<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

tzportfolioplusimport('plugin.modelitem');
JLoader::register('TZ_Portfolio_PlusHelper', COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH
    .DIRECTORY_SEPARATOR.'tz_portfolio_plus.php');
JLoader::register('TZ_Portfolio_PlusFrontHelperExtraFields', COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH
    .DIRECTORY_SEPARATOR.'extrafields.php');
JLoader::import('com_tz_portfolio_plus.helpers.tags',JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components');
JLoader::import('com_tz_portfolio_plus.helpers.association',JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components');


/**
 * Item Model for an Article.
 */
class TZ_Portfolio_PlusModelArticle extends JModelAdmin
{
    protected $text_prefix = 'COM_CONTENT';
    public $typeAlias = 'com_tz_portfolio_plus.article';
    protected $associationsContext = 'com_tz_portfolio_plus.article.item';

    /**
     * The event to trigger after call trigger after save.
     *
     * @var    string
     * @since  1.2.7
     */
    protected $event_addon_after_save = 'onAddOnAfterSave';

    protected function batchCopy($value, $pks, $contexts)
    {
        $categoryId = (int) $value;

        $newIds = array();

        if (!parent::checkCategoryId($categoryId))
        {
            return false;
        }

        // Parent exists so we let's proceed
        while (!empty($pks))
        {
            // Pop the first ID off the stack
            $pk = array_shift($pks);

            $this->table->reset();

            // Check that the row actually exists
            if (!$this->table->load($pk))
            {
                if ($error = $this->table->getError())
                {
                    // Fatal error
                    $this->setError($error);

                    return false;
                }
                else
                {
                    // Not fatal error
                    $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
                    continue;
                }
            }

            // Alter the title & alias
            $data = $this->generateNewTitle($categoryId, $this->table->alias, $this->table->title);
            $this->table->title = $data['0'];
            $this->table->alias = $data['1'];

            // Reset the ID because we are making a copy
            $this->table->id = 0;

            // Reset hits because we are making a copy
            $this->table->hits = 0;

            // Unpublish because we are making a copy
            $this->table->state = 0;

            // New category ID
            $this->table->catid = $categoryId;

            // TODO: Deal with ordering?
            // $table->ordering	= 1;

            // Get the featured state
            $featured = $this->table->featured;

            // Check the row.
            if (!$this->table->check())
            {
                $this->setError($this->table->getError());
                return false;
            }

            parent::createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);

            // Store the row.
            if (!$this->table->store())
            {
                $this->setError($this->table->getError());
                return false;
            }

            // Get the new item ID
            $newId = $this->table->get('id');

            // Add the new ID to the array
            $newIds[$pk] = $newId;

            // Check if the article was featured and update the #__tz_portfolio_plus_content_featured_map table
            if ($featured == 1)
            {
                $db = $this->getDbo();
                $query = $db->getQuery(true)
                    ->insert($db->quoteName('#__tz_portfolio_plus_content_featured_map'))
                    ->values($newId . ', 0');
                $db->setQuery($query);
                $db->execute();
            }
        }

        // Clean the cache
        $this->cleanCache();

        return $newIds;
    }

    protected function generateNewTitle($category_id, $alias, $title)
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(array('alias' => $alias, 'catid' => $category_id)))
        {
            $title = StringHelper::increment($title);
            $alias = StringHelper::increment($alias, 'dash');
        }

        return array($title, $alias);
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     *
     * @since   1.6
     */
    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            if ($record->state != -2)
            {
                return false;
            }
            $user = Factory::getUser();

            $state  = $user->authorise('core.delete', 'com_tz_portfolio_plus.article.' . (int) $record->id)
                || ($user->authorise('core.delete.own', 'com_tz_portfolio_plus.article.' . (int) $record->id)
                    && $record -> created_by == $user -> id);
            return $state;
        }

        return false;
    }

    public function delete(&$pks)
    {
        $_pks = (array)$pks;
        $result = parent::delete($pks);
        if($result){
            if ($_pks && count($_pks)) {
                $db     = $this->getDbo();
                $query  = $db->getQuery(true);

                // Remove content map to category
                $query -> delete('#__tz_portfolio_plus_content_category_map');
                $query -> where('contentid IN(' . implode(',', $_pks) . ')');

                $db -> setQuery($query);
                $db -> execute();

                // Remove tag map to content
                $query -> clear();
                $query -> delete('#__tz_portfolio_plus_tag_content_map');
                $query -> where('contentid IN(' . implode(',', $_pks) . ')');

                $db -> setQuery($query);
                $db -> execute();

                // Remove field map to content
                $query -> clear();
                $query -> delete('#__tz_portfolio_plus_field_content_map');
                $query -> where('contentid IN(' . implode(',', $_pks) . ')');

                $db -> setQuery($query);
                $db -> execute();

                // Remove content rejected from tz_portfolio_plus_content_rejected
                $query -> clear();
                $query -> delete('#__tz_portfolio_plus_content_rejected');
                $query -> where('content_id IN(' . implode(',', $_pks) . ')');

                $db -> setQuery($query);
                $db -> execute();
            }
        }
        return $result;
    }



    protected function canApprove($record)
    {
        return TZ_Portfolio_PlusHelperACL::allowApprove($record);
    }

    /**
     * Method to test whether a record can have its state edited.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
     *
     * @since   1.6
     */
    protected function canEditState($record)
    {
        $user = Factory::getUser();

        // Check for existing article.
        if (!empty($record->id))
        {
            $state = $user->authorise('core.edit.state', 'com_tz_portfolio_plus.article.' . (int)$record->id)
                || ($user->authorise('core.edit.state.own', 'com_tz_portfolio_plus.article.' . (int)$record->id)
                    && $record->created_by == $user->id);


            if($this -> canApprove($record)){
                return true;
            }

            return $state;
        }
        // New article, so check against the category.
        elseif (!empty($record->catid))
        {
            $state  = $user->authorise('core.edit.state', 'com_tz_portfolio_plus.category.' . (int) $record->catid)
                || ($user->authorise('core.edit.state.own', 'com_tz_portfolio_plus.category.' . (int) $record->catid)
                    && $record -> created_by == $user -> id);
            return $state;
        }
        // Default to component settings if neither article nor category known.
        else
        {
            $state  = parent::canEditState($record) ||
                ($user->authorise('core.edit.state.own', 'com_tz_portfolio_plus')
                    && $record -> created_by == $user -> id);
            return $state;
        }
    }

    /**
     * Prepare and sanitise the table data prior to saving.
     *
     * @param   JTable  $table  A JTable object.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function prepareTable($table)
    {
        // Set the publish date to now
        $db = $this->getDbo();

        if ($table->state == 1 && (int) $table->publish_up == 0)
        {
            $table->publish_up = Factory::getDate()->toSql();
        }

        if ($table->state == 1 && intval($table->publish_down) == 0)
        {
            $table->publish_down = $db->getNullDate();
        }

        // Increment the content version number.
        $table->version++;

        // Reorder the articles within the category so the new article is first
        if (empty($table->id))
        {
            $table->reorder('m.catid = ' . (int) $table->catid . ' AND c.state >= 0');
        }
    }

    /**
     * Returns a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  JTable    A database object
     */
    public function getTable($type = 'Content', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed  Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk))
        {
            // Convert the params field to an array.
            if($item -> attribs && is_string($item -> attribs)) {
                $registry = new Registry;
                $registry->loadString($item->attribs);
                $item->attribs = $registry->toArray();
            }

            // Convert the metadata field to an array.
            if($item -> metadata && is_string($item -> metadata)) {
                $registry = new Registry;
                $registry->loadString($item->metadata);
                $item->metadata = $registry->toArray();
            }

            // Convert the images field to an array.
            if($item -> images && is_string($item -> images)) {
                $registry = new Registry;
                $registry->loadString($item->images);
                $item->images = $registry->toArray();
            }

            // Convert the urls field to an array.
            if($item -> urls && is_string($item -> urls)) {
                $registry = new Registry;
                $registry->loadString($item->urls);
                $item->urls = $registry->toArray();
            }

            $item->articletext = !empty($item -> fulltext) && trim($item->fulltext) != '' ? $item->introtext
                . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;

            if(isset($item -> media) && !empty($item -> media)){
                $media = new Registry;
                $media -> loadString($item -> media);
                $item -> media  = $media -> toArray();
            }

            if (!empty($item->id))
            {
                $item -> tags   = null;
                $tags   = TZ_Portfolio_PlusHelperTags::getTagsByArticleId($item -> id);
                if($tags && count($tags)) {
                    $tags = ArrayHelper::getColumn($tags, 'id');
                    $tags = implode(',', $tags);
                    $item->tags = $tags;
                }
            }
        }

        // Load associated content items
        $assoc = JLanguageAssociations::isEnabled();

        if ($assoc)
        {
            $item->associations = array();

            if ($item->id != null)
            {
                $associations    = TZ_Portfolio_PlusBackEndHelperAssociation::getArticleAssociations($item->id);

                foreach ($associations as $tag => $association)
                {
                    $item->associations[$tag] = $association->id;
                }
            }
        }
        return $item;
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed  A JForm object on success, false on failure
     *
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this -> option.'.'.$this -> getName(), 'article', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form))
        {
            return false;
        }
        $jinput = Factory::getApplication()->input;

        // The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
        if ($jinput->get('a_id'))
        {
            $id = $jinput->get('a_id', 0);
        }
        // The back end uses id so we use that the rest of the time and set it to 0 by default.
        else
        {
            $id = $jinput->get('id', 0);
        }
        // Determine correct permissions to check.
        if ($this->getState('article.id'))
        {
            $id = $this->getState('article.id');

            // Existing record. Can only edit in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit');

            // Existing record. Can only edit own articles in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit.own');
        }
        else
        {
            // New record. Can only create in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.create');
        }

        $user = Factory::getUser();

        // Check for existing article.
        // Modify the form based on Edit State access controls.
        if ($id != 0 && (!$user->authorise('core.edit.state', 'com_tz_portfolio_plus.article.' . (int) $id))
            || ($id == 0 && !$user->authorise('core.edit.state', 'com_tz_portfolio_plus')))
        {
            // Disable fields for display.
            $form->setFieldAttribute('featured', 'disabled', 'true');
            $form->setFieldAttribute('ordering', 'disabled', 'true');
            $form->setFieldAttribute('publish_up', 'disabled', 'true');
            $form->setFieldAttribute('publish_down', 'disabled', 'true');
            $form->setFieldAttribute('state', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is an article you can edit.
            $form->setFieldAttribute('featured', 'filter', 'unset');
            $form->setFieldAttribute('ordering', 'filter', 'unset');
            $form->setFieldAttribute('publish_up', 'filter', 'unset');
            $form->setFieldAttribute('publish_down', 'filter', 'unset');
            $form->setFieldAttribute('state', 'filter', 'unset');
        }

        if(!TZ_Portfolio_PlusHelperACL::allowApprove()){
            $form -> setFieldAttribute('state', 'type', 'hidden');
            $form -> setFieldAttribute('state', 'default', '3');
        }

        // Prevent messing with article language and category when editing existing article with associations
        $app = Factory::getApplication();
        $assoc = JLanguageAssociations::isEnabled();

        // Check if article is associated
        if ($this->getState('article.id') && $app->isClient('site') && $assoc)
        {
            $associations = TZ_Portfolio_PlusBackEndHelperAssociation::getArticleAssociations($id);

            // Make fields read only
            if (!empty($associations))
            {
                $form->setFieldAttribute('language', 'readonly', 'true');
                $form->setFieldAttribute('catid', 'readonly', 'true');
                $form->setFieldAttribute('language', 'filter', 'unset');
                $form->setFieldAttribute('catid', 'filter', 'unset');
            }
        }

        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
            $form -> removeField('show_email_icon', 'attribs');
            $form -> removeField('show_cat_email_icon', 'attribs');
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app = Factory::getApplication();
        $data = $app->getUserState($this -> option. '.edit.'.$this -> getName().'.data', array());

        if (empty($data))
        {
            $data               = $this->getItem();
            if($second_categories  = TZ_Portfolio_PlusHelperCategories::getCategoriesByArticleId($data -> id, 0)) {
                if (is_array($second_categories)) {
                    $catids = ArrayHelper::getColumn($second_categories, 'id');
                } else {
                    $catids = $second_categories->id;
                }

                $data->set('second_catid', $catids);
            }

            if($main_category      = TZ_Portfolio_PlusHelperCategories::getCategoriesByArticleId($data -> id, 1)) {
                if (is_array($main_category)) {
                    $catid = ArrayHelper::getColumn($main_category, 'id');
                } else {
                    $catid = $main_category->id;
                }
                $data->set('catid', $catid);
            }

            // Pre-select some filters (Status, Category, Language, Access) in edit form if those have been selected in Article Manager: Articles
            if ($this->getState($this -> getName().'.id') == 0)
            {
                $filters = (array) $app->getUserState('com_tz_portfolio_plus.articles.filter');
                $data->set('state', $app->input->getInt('state', (!empty($filters['published']) ? $filters['published'] : null)));
                $data->set('catid', $app->input->get('catid', (!empty($filters['category_id']) ? $filters['category_id'] : array())));
                $data->set('language', $app->input->getString('language', (!empty($filters['language']) ? $filters['language'] : null)));
                $data->set('access', $app->input->getInt('access', (!empty($filters['access']) ? $filters['access'] : Factory::getConfig()->get('access'))));
            }
        }

        $this->preprocessData('com_tz_portfolio_plus.article', $data);

        return $data;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success.
     *
     * @since   1.6
     */
    public function save($data)
    {
        $user       = Factory::getUser();
        $input      = Factory::getApplication()->input;
        $filter     = JFilterInput::getInstance();

        if (isset($data['metadata']) && isset($data['metadata']['author']))
        {
            $data['metadata']['author'] = $filter->clean($data['metadata']['author'], 'TRIM');
        }

        if (isset($data['created_by_alias']))
        {
            $data['created_by_alias'] = $filter->clean($data['created_by_alias'], 'TRIM');
        }

        if (isset($data['urls']) && is_array($data['urls']))
        {
            foreach ($data['urls'] as $i => $url)
            {
                if ($url != false && ($i == 'urla' || $i == 'urlb' || $i == 'urlc'))
                {
                    $data['urls'][$i] = JStringPunycode::urlToPunycode($url);
                }
            }

            $registry = new Registry;
            $registry->loadArray($data['urls']);
            $data['urls'] = (string) $registry;
        }

        // Alter the title for save as copy
        if ($input->get('task') == 'save2copy')
        {
            $origTable = clone $this->getTable();
            $origTable->load($input->getInt('id'));

            if ($data['title'] == $origTable->title)
            {
                list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
                $data['title'] = $title;
                $data['alias'] = $alias;
            }
            else
            {
                if ($data['alias'] == $origTable->alias)
                {
                    $data['alias'] = '';
                }
            }

            $data['state'] = 0;
        }

        // Automatic handling of alias for empty fields
        if (in_array($input->get('task'), array('apply', 'save', 'save2new')) && (!isset($data['id']) || (int) $data['id'] == 0))
        {
            if ($data['alias'] == null)
            {
                if (Factory::getConfig()->get('unicodeslugs') == 1)
                {
                    $data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['title']);
                }
                else
                {
                    $data['alias'] = JFilterOutput::stringURLSafe($data['title']);
                }

                // Verify that the alias is unique
                if(!$this -> verifyAlias($data['id'], $data['alias'], $data['catid'])){
                    $msg    = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_ALIAS_SAVE_WARNING', $input -> get('view'));
                }

                list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
                $data['alias'] = $alias;

                if (isset($msg))
                {
                    Factory::getApplication()->enqueueMessage($msg, 'warning');
                }
            }
        }

        $tags   = null;
        if(isset($data['tags'])){
            $tags   = $data['tags'];
        }

        $mCatid     = (isset($data['catid']) && $data['catid'])?(int) $data['catid']:null;
        $sCatIds    = isset($data['second_catid'])?$data['second_catid']:array();

        // Permission can publish
        $canApprove = $user -> authorise('core.approve', 'com_tz_portfolio_plus');
        if(!$canApprove){
            $data['state']  = 3;
        }

        if($input -> get('task') == 'draft' || $input -> get('task') == 'reject'){
            $data['state']  = -3;
        }

        if (parent::save($data))
        {
            $context    = $this->option . '.' . $this->name;
            $isNew      = $this->getState($this->getName() . '.new');
            $artId      = $this->getState($this->getName() . '.id');

            try {
                // Save categories
                if ($this->saveArticleCategories($artId, $mCatid, $sCatIds)) {
                    unset($data['catid']);
                    unset($data['second_catid']);
                }

                $table = $this->getTable();
                $table->load($this->getState($this->getName() . '.id'));

                // Save extrafields
                if (isset($data['extrafields'])) {
                    $this->saveArticleFields($data['extrafields'], $table);
                } else {
                    $this->saveArticleFields(array(), $table);
                }

                if (isset($data['featured'])) {
                    $this->featured($this->getState($this->getName() . '.id'), $data['featured']);
                }

                $assoc = JLanguageAssociations::isEnabled();
                if ($assoc) {
                    $id = (int)$this->getState($this->getName() . '.id');
                    $item = $this->getItem($id);

                    // Adding self to the association
                    $associations = $data['associations'];

                    foreach ($associations as $tag => $id) {
                        if (empty($id)) {
                            unset($associations[$tag]);
                        }
                    }

                    // Detecting all item menus
                    $all_language = $item->language == '*';

                    if ($all_language && !empty($associations)) {
                        JError::raiseNotice(403, JText::_('COM_CONTENT_ERROR_ALL_LANGUAGE_ASSOCIATED'));
                    }

                    $associations[$item->language] = $item->id;

                    try
                    {
                        // Deleting old association for these items
                        $db = Factory::getDbo();
                        $query = $db->getQuery(true)
                            ->delete('#__associations')
                            ->where('context=' . $db->quote('com_tz_portfolio_plus.item'))
                            ->where('id IN (' . implode(',', $associations) . ')');
                        $db->setQuery($query);
                        $db->execute();
                    }
                    catch (\InvalidArgumentException $e)
                    {
                        $this->setError($e->getMessage());
                        return false;
                    }

                    if (!$all_language && count($associations)) {
                        // Adding new association for these items
                        $key = md5(json_encode($associations));
                        $query->clear()
                            ->insert('#__associations');

                        foreach ($associations as $id) {
                            $query->values($id . ',' . $db->quote('com_tz_portfolio_plus.item') . ',' . $db->quote($key));
                        }

                        try {
                            $db->setQuery($query);
                            $db->execute();
                        }
                        catch (\InvalidArgumentException $e)
                        {
                            $this->setError($e->getMessage());
                            return false;
                        }
                    }
                }

                $articleId = $this->getState($this->getName() . '.id');
                if (isset($articleId) && $articleId) {
                    // Tags
                    if (!TZ_Portfolio_PlusHelperTags::insertTagsByArticleId($articleId, $tags)) {
                        $this->setError(TZ_Portfolio_PlusHelperTags::getError());
                        return false;
                    }
                    // Reject article
                    if($input -> get('task') == 'reject'){
                        $tblReject  = $this -> getTable('Content_Rejected');
                        $_data['id']    = 0;
                        $_data['content_id'] = $articleId;
                        if($tblReject -> load(array('content_id' => $articleId))) {
                            $_data['id'] = $tblReject->id;
                        }
                        if($tblReject -> bind($_data)){
                            $tblReject -> store();
                        }
                    }

                }


                // Trigger the addon after save event.
                Factory::getApplication()->triggerEvent($this->event_addon_after_save, array($context, $table, $isNew, $data));

            }
            catch (\Exception $e)
            {
                $this->setError($e->getMessage());

                return false;
            }

            return true;
        }

        return false;
    }

    public function saveArticleCategories($artId, $mainCatid, $secondCatids = array()){
        // Insert categories
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);

        if(!$artId || !$mainCatid){
            return false;
        }

        $catIds = array($mainCatid);

        $table  = $this -> getTable('Content_Category_Map', 'TZ_Portfolio_PlusTable');

        // Check contentid map catid and store it
        $table -> set('id', 0);
        if($table -> load(array('contentid' => $artId, 'catid' => $mainCatid))){
            if(!$table -> main){
                $table -> main  = 1;
                if(!$table -> store()){
                    $this->setError($table->getError());
                    return false;
                }
            }
        }else{
            if(!$table -> bind(array('contentid' => $artId, 'catid' => $mainCatid, 'main' => 1))){
                $this->setError($table->getError());
                return false;
            }
            if(!$table -> store()){
                $this->setError($table->getError());
                return false;
            }
        }

        // Check and store second category map to content
        if($secondCatids && count($secondCatids)){
            $catIds = array_merge($catIds, $secondCatids);
            foreach($secondCatids as $sCatid){
                if(!$table -> load(array('contentid' => $artId, 'catid' => $sCatid))){
                    $table -> resetAll();
                }
                if(!$table -> bind(array('contentid' => $artId, 'catid' => $sCatid, 'main' => 0))){
                    $this->setError($table->getError());
                    return false;
                }
                if(!$table -> store()){
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        // Delete all categories did not map article
        $query -> delete('#__tz_portfolio_plus_content_category_map');
        $query -> where('contentid = '.$artId);
        $query -> where('catid NOT IN('.implode(',', $catIds).')');

        try {
            $db->setQuery($query);
            $db->execute();
        }
        catch (\InvalidArgumentException $e)
        {
            $this->setError($e->getMessage());
            return false;
        }

        return true;
    }

    public function saveArticleFields($fieldsData, $table, $isNew = true){
//        if($fieldsData){
        if($fields = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraFields($table, null, true)){
            if(count($fields) >= count($fieldsData)){
                foreach($fields as $field){
                    $fieldObj   = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraField($field, $table);
                    $defValue   = $field -> getDefaultValues();
                    $fieldValue = isset($fieldsData[$field->id]) ? $fieldsData[$field->id] : "";
                    if((!$fieldValue || empty($fieldValue)) && isset($defValue) && !empty($defValue)){
                        $fieldValue = $defValue;
                    }
                    $fieldObj -> onSaveArticleFieldValue($fieldValue);
                }
                return true;
            }
        }
        if($fieldsData){
            foreach($fieldsData as $id => $fieldValue){
                $fieldObj   = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraField($id, $table);
                $fieldObj -> onSaveArticleFieldValue($fieldValue);
            }
        }
        return false;
    }

    protected function verifyAlias($articleId, $alias, $catid){
        if(!empty($alias)){
            $db     = $this -> getDbo();
            $query  = $db -> getQuery(true);

            $query -> select('c.*');
            $query -> from('#__tz_portfolio_plus_content AS c');
            $query -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = c.id');
            $query -> join('LEFT', '#__tz_portfolio_plus_categories AS cc ON cc.id = m.catid');
            if(is_array($catid)){
                $query -> where('m.catid IN('.implode(',',$catid).')');
            }else{
                $query -> where('m.catid = '.$catid);
            }
            $query -> where('c.alias = '. $db -> quote($alias));
            $query -> where('c.id <> '.$articleId);
            $db -> setQuery($query);
            if($db -> loadResult()){
                return false;
            }
        }
        return true;
    }

    /**
     * Method to toggle the featured setting of articles.
     *
     * @param   array    $pks    The ids of the items to toggle.
     * @param   integer  $value  The value to toggle to.
     *
     * @return  boolean  True on success.
     */
    public function featured($pks, $value = 0)
    {
        // Sanitize the ids.
        $pks    = (array) $pks;
        $pks    = ArrayHelper::toInteger($pks);

        if (empty($pks))
        {
            $this->setError(JText::_('COM_CONTENT_NO_ITEM_SELECTED'));

            return false;
        }

        $table = $this->getTable('Featured', 'TZ_Portfolio_PlusTable');

        try
        {
            $db = $this->getDbo();
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__tz_portfolio_plus_content'))
                ->set('featured = ' . (int) $value)
                ->where('id IN (' . implode(',', $pks) . ')');
            $db->setQuery($query);
            $db->execute();

            if ((int) $value == 0)
            {
                // Adjust the mapping table.
                // Clear the existing features settings.
                $query = $db->getQuery(true)
                    ->delete($db->quoteName('#__tz_portfolio_plus_content_featured_map'))
                    ->where('content_id IN (' . implode(',', $pks) . ')');
                $db->setQuery($query);
                $db->execute();
            }
            else
            {
                // First, we find out which of our new featured articles are already featured.
                $query = $db->getQuery(true)
                    ->select('f.content_id')
                    ->from('#__tz_portfolio_plus_content_featured_map AS f')
                    ->where('content_id IN (' . implode(',', $pks) . ')');
                $db->setQuery($query);

                $old_featured = $db->loadColumn();

                // We diff the arrays to get a list of the articles that are newly featured
                $new_featured = array_diff($pks, $old_featured);

                // Featuring.
                $tuples = array();

                foreach ($new_featured as $pk)
                {
                    $tuples[] = $pk . ', 0';
                }

                if (count($tuples))
                {
                    $db = $this->getDbo();
                    $columns = array('content_id', 'ordering');
                    $query = $db->getQuery(true)
                        ->insert($db->quoteName('#__tz_portfolio_plus_content_featured_map'))
                        ->columns($db->quoteName($columns))
                        ->values($tuples);
                    $db->setQuery($query);
                    $db->execute();
                }
            }
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());
            return false;
        }

        $table->reorder();

        $this->cleanCache();

        return true;
    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param   object  $table  A record object.
     *
     * @return  array  An array of conditions to add to add to ordering queries.
     *
     * @since   1.6
     */
    protected function getReorderConditions($table)
    {
        $tblContentCatMap   = $this -> getTable('Content_Category_Map');

        $condition = array();

        if($tblContentCatMap -> load(array('contentid' => ($table -> id), 'main' => 1))) {
            $condition[] = 'catid = ' . (int)$tblContentCatMap->catid;
        }

        return $condition;
    }

    /**
     * Auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   JForm   $form   The form object
     * @param   array   $data   The data to be merged into the form object
     * @param   string  $group  The plugin group to be executed
     *
     * @return  void
     *
     * @since    3.0
     */
    protected function preprocessForm(JForm $form, $data, $group = 'content')
    {
        // Association content items
        $app = Factory::getApplication();
        $assoc = JLanguageAssociations::isEnabled();

        if ($assoc)
        {
            $languages = JLanguageHelper::getContentLanguages(false, true, null, 'ordering', 'asc');

            $addform = new SimpleXMLElement('<form />');
            $fields = $addform->addChild('fields');
            $fields->addAttribute('name', 'associations');
            $fieldset = $fields->addChild('fieldset');
            $fieldset->addAttribute('name', 'item_associations');
            $fieldset->addAttribute('description', 'COM_CONTENT_ITEM_ASSOCIATIONS_FIELDSET_DESC');
            $add = false;

            foreach ($languages as $language)
            {
                if (empty($data->language) || $language->lang_code != $data->language)
                {
                    $add = true;
                    $field = $fieldset->addChild('field');
                    $field->addAttribute('name', $language->lang_code);
                    $field->addAttribute('type', 'modal_article');
                    $field->addAttribute('language', $language->lang_code);
                    $field->addAttribute('label', $language->title);
                    $field->addAttribute('translate_label', 'false');
                    $field->addAttribute('edit', 'true');
                    $field->addAttribute('clear', 'true');
                }
            }
            if ($add)
            {
                $form->load($addform, false);
            }
        }

        // Insert Mediatype from plugins
        TZ_Portfolio_PlusPluginHelper::importPlugin('mediatype');
        if($mediaType  = Factory::getApplication()->triggerEvent('onAddMediaType')){
            if(count($mediaType)){
                $xml        = $form -> getXml();
                $field_type = $xml -> xpath('//field[@name="type"]');
                $field_type = $field_type[0];

                foreach($mediaType as $type){
                    if(is_object($type)) {
                        $field_type->addChild('option', $type->text)->addAttribute('value', $type->value);
                    }elseif(is_array($type)){
                        $field_type->addChild('option', $type['text'])->addAttribute('value', $type['value']);
                    }
                }
            }
        }

        // Insert parameter from extrafield
        JLoader::import('extrafields', COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH);
        TZ_Portfolio_PlusHelperExtraFields::prepareForm($form, $data);

        parent::preprocessForm($form, $data, $group);
    }


    public function getExtraFields()
    {
        $app        = Factory::getApplication();
        $jinput     = $app -> input;

        $articleId  = $jinput->get('a_id', $jinput->get('id', 0));
        $db         = $this -> getDbo();
        $query      = $db -> getQuery(true);

        if($fieldGroups = TZ_Portfolio_PlusFrontHelperExtraFields::getFieldGroupsByArticleId($articleId)){
            $fieldsCache    = array();
            foreach($fieldGroups as $i => $fieldGroup){
                $fieldGroup->fields = array();

                $query -> clear();
                $query->select("field.*, m.groupid");
                $query->from("#__tz_portfolio_plus_fields AS field");
                $query -> join('LEFT','#__tz_portfolio_plus_field_fieldgroup_map AS m ON field.id = m.fieldsid');
                $query -> join('INNER', '#__tz_portfolio_plus_fieldgroups AS fg ON fg.id = m.groupid');

                $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = field.type')
                    -> where('e.type = '.$db -> quote('tz_portfolio_plus-plugin'))
                    -> where('e.folder = '.$db -> quote('extrafields'))
                    -> where('e.published = 1');

                $query->where("field.published = 1");
                $query->where("m.groupid = " . $fieldGroup->id);

                // Implement View Level Access
                $user       = Factory::getUser();
                $viewlevels = ArrayHelper::toInteger($user->getAuthorisedViewLevels());
                $viewlevels = implode(',', $viewlevels);
                $subquery   = $db -> getQuery(true);

                $subquery -> select('subg.id');
                $subquery -> from('#__tz_portfolio_plus_fieldgroups AS subg');
                $subquery -> where('subg.access IN('.$viewlevels.')');

                $query -> where('field.access IN('.$viewlevels.')');
                $query -> where('fg.id IN('.((string) $subquery).')');
                $query -> where('e.access IN('.$viewlevels.')');

                // Ordering by default : core fields, then extra fields
                $query -> order('IF(fg.field_ordering_type = 2, '.$db -> quoteName('m.ordering')
                    .',IF(fg.field_ordering_type = 1,'.$db -> quoteName('field.ordering').',NULL))');

                $db->setQuery($query);
                $_fields = $db->loadObjectList();
                if ($_fields)
                {
                    foreach ($_fields AS $field)
                    {
                        if(!in_array($field -> id, $fieldsCache)) {
                            $fieldObj               = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraField($field, $articleId);
                            $fieldGroup->fields[]   = $fieldObj;
                            $fieldsCache[]          = $field->id;
                        }
                    }
                }

                if(!count($fieldGroup -> fields)){
                    unset($fieldGroups[$i]);
                }
            }
            return $fieldGroups;
        }

        return false;
    }

    public function savepriority($pks = array(), $order = null)
    {
        // Initialize re-usable member properties
        $this->initBatch();

        $conditions = array();

        if (empty($pks))
        {
            return \JError::raiseWarning(500, \JText::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'));
        }

        $orderingField = $this->table->getColumnAlias('priority');

        // Update ordering values
        foreach ($pks as $i => $pk)
        {
            $this->table->load((int) $pk);

            $tblContentCatMap   = $this -> getTable('Content_Category_Map');

            if($tblContentCatMap -> load(array('contentid' => $pk, 'main' => 1))){
                $this -> table -> set('catid', $tblContentCatMap -> catid);
            }

            // Access checks.
            if (!$this->canEditState($this->table))
            {
                // Prune items that you can't change.
                unset($pks[$i]);
                \JLog::add(\JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), \JLog::WARNING, 'jerror');
            }
            elseif ($this->table->$orderingField != $order[$i])
            {
                $this->table->$orderingField = $order[$i];

                if (!$this->table->store())
                {
                    $this->setError($this->table->getError());

                    return false;
                }

                // Remember to reorder within position and client_id
                $condition = array();
                $found = false;

                foreach ($conditions as $cond)
                {
                    if ($cond[1] == $condition)
                    {
                        $found = true;
                        break;
                    }
                }

                if (!$found)
                {
                    $key = $this->table->getKeyName();
                    $conditions[] = array($this->table->$key, $condition);
                }
            }
        }

        // Execute reorder for each articles.
        foreach ($conditions as $cond)
        {
            $this->table->load($cond[0]);
            $this->table->repriority($cond[1]);
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }


    public function repriority($pks, $delta = 0)
    {
        $table = $this->getTable();
        $pks = (array) $pks;
        $result = true;

        $allowed = true;

        foreach ($pks as $i => $pk)
        {
            $table->reset();

            if ($table->load($pk) && $this->checkout($pk))
            {
                // Access checks.
                if (!$this->canEditState($table))
                {
                    // Prune items that you can't change.
                    unset($pks[$i]);
                    $this->checkin($pk);
                    \JLog::add(\JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), \JLog::WARNING, 'jerror');
                    $allowed = false;
                    continue;
                }

                if (!$table->movepriority($delta, array()))
                {
                    $this->setError($table->getError());
                    unset($pks[$i]);
                    $result = false;
                }

                $this->checkin($pk);
            }
            else
            {
                $this->setError($table->getError());
                unset($pks[$i]);
                $result = false;
            }
        }

        if ($allowed === false && empty($pks))
        {
            $result = null;
        }

        // Clear the component's cache
        if ($result == true)
        {
            $this->cleanCache();
        }

        return $result;
    }

    /**
     * Custom clean the cache of com_content and content modules
     *
     * @param   string   $group      The cache group
     * @param   integer  $client_id  The ID of the client
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function cleanCache($group = null, $client_id = 0)
    {
        parent::cleanCache('com_tz_portfolio_plus');
        parent::cleanCache('mod_tz_portfolio_plus_archive');
        parent::cleanCache('mod_tz_portfolio_plus_categories');
        parent::cleanCache('mod_tz_portfolio_plus_articles');
    }

}
