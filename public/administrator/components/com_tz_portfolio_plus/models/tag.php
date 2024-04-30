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
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

jimport('joomla.application.component.modeladmin');
JLoader::import('tags', COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH);

class TZ_Portfolio_PlusModelTag extends JModelAdmin
{

    public function __construct($config = array(), MVCFactoryInterface $factory = null)
    {
        parent::__construct($config, $factory);

        // Set the model dbo
        if (array_key_exists('dbo', $config))
        {
            $this->_db = $config['dbo'];
        }
        else
        {
            $this->_db = TZ_Portfolio_PlusDatabase::getDbo();
        }
    }

    public function populateState(){
        parent::populateState();
    }

    public function getTable($type = 'Tags', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    function getForm($data = array(), $loadData = true){
        $form = $this->loadForm('com_tz_portfolio_plus.tag', 'tag', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        if (empty($data)) {
            $data = $this->getItem();
            if(isset($data -> params) && $data -> params){
                $params         = new JRegistry($data -> params);
                $data -> params = $params -> toArray();
            }
            $data -> articles_assignment = $this -> getArticlesAssignment();
        }

        return $data;
    }

    function getItem($pk = null){
        // Initialise variables.
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        $table  = $this -> getTable();

        if ($pk > 0)
        {
            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());
                return false;
            }
        }

        // Convert to the JObject before adding other data.
        $properties = $table->getProperties(1);
        $item = ArrayHelper::toObject($properties, 'JObject');

        if (property_exists($item, 'params'))
        {
            $registry = new JRegistry;
            if($item -> params) {
                $registry->loadString($item->params);
            }
            $item->params = $registry->toArray();
        }

        return $item;
    }

    public function getArticlesAssignment($pk = null){
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        if($pk > 0){
            $db     = $this -> getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('contentid');
            $query -> from('#__tz_portfolio_plus_tag_content_map');
            $query -> where('tagsid = '.$pk);
            $db -> setQuery($query);
            if($rows = $db -> loadColumn()){
                return implode(',',$rows);
            }
        }
        return null;
    }

    public function save($data){
        $app    = Factory::getApplication();
        $input = $app->input;
        $articlesAssignment = null;

        if(isset($data['articles_assignment']) && count($data['articles_assignment'])){
            $articlesAssignment  = $data['articles_assignment'];
            unset($data['articles_assignment']);
        }
        // Automatic handling of alias for empty fields
        if (in_array($input->get('task'), array('apply', 'save', 'save2new')))
        {
            if((!isset($data['id']) || (int) $data['id'] == 0)){
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


                    $table = JTable::getInstance('Tags', 'TZ_Portfolio_PlusTable');
                    if ($table->load(array('alias' => $data['alias'])))
                    {
                        $msg = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_ALIAS_SAVE_WARNING', $input -> get('view'));
                    }

                    list($title, $alias) = $this->generateNewTitle(0, $data['alias'], $data['title']);
                    $data['alias']  = $alias;

                    if (isset($msg))
                    {
                        $app->enqueueMessage($msg, 'warning');
                    }
                }
            }

            // Check tag's alias
            $alias_check    = TZ_Portfolio_PlusHelperTags::getTagByKey(array('alias' => $data['alias'], 'id' => (int) $data['id']),
                array('id' => true));
            if($alias_check && count($alias_check)){
                $msg    = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_ALIAS_SAVE_WARNING', $input -> get('view'));
                $this -> setError($msg);
                return false;
            }
            // Check tag's title
            $title_check    = TZ_Portfolio_PlusHelperTags::getTagByKey(array('title' => $data['title'], 'id' => (int) $data['id']),
                array('id' => true));

            if($title_check && count($title_check)){
                $msg    = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_TITLE_SAVE_WARNING', $input -> get('view'));
                $this -> setError($msg);
                return false;
            }
        }

        if(parent::save($data)){
            $db     = $this -> getDbo();
            $query  = $db->getQuery(true);
            $id     = $this->getState($this->getName() . '.id');

            // Assign articles with this tag;
            if(!empty($articlesAssignment) && count($articlesAssignment)){

                $query -> select('DISTINCT contentid');
                $query -> from($db -> quoteName('#__tz_portfolio_plus_tag_content_map'));
                $query->where('tagsid = ' . (int) $id);
                $db -> setQuery($query);

                if(!$updateIds = $db -> loadColumn()){
                    $updateIds  = array();
                }

                // Insert article items with this tag if they were created in
                if($insertIds  = array_diff($articlesAssignment,$updateIds)){
                    $query -> clear();
                    $query -> insert($db -> quoteName('#__tz_portfolio_plus_tag_content_map'));
                    $query ->columns('contentid,tagsid');
                    foreach($insertIds as $cid){
                        $query -> values($cid.','.$id);
                    }
                    $db -> setQuery($query);
                    $db -> execute();
                }
            }

            // Remove tags mappings for article items this tag is NOT assigned to.
            // If unassigned then all existing maps will be removed.

            if (!empty($articlesAssignment) && count($articlesAssignment))
            {
                $query -> clear();
                $query -> delete('#__tz_portfolio_plus_tag_content_map');
                $query->where('contentid NOT IN (' . implode(',', $articlesAssignment) . ')');
                $query->where('tagsid = ' . (int) $id);

                $db->setQuery($query);
                $db->execute();
            }
            return true;
        }
        return true;
    }

    public function delete(&$pks)
    {
        $_pks = (array)$pks;
        $result = parent::delete($pks);
        if($result){
            if ($_pks && count($_pks)) {
                $db     = $this->getDbo();
                $query  = $db->getQuery(true);

                // Remove tag map to content
                $query -> clear();
                $query -> delete('#__tz_portfolio_plus_tag_content_map');
                $query -> where('tagsid IN(' . implode(',', $_pks) . ')');

                $db -> setQuery($query);
                $db -> execute();
            }
        }
        return $result;
    }

    protected function prepareTable($table){
        if(isset($table -> title) && $table -> title){
//            $table -> title   = str_replace(array(',',';','\'','"','.','?'
//            ,'/','\\','<','>','(',')','*','&','^','%','$','#','@','!','-','+','|','`','~'),' ',$table -> title);
            $table -> title  = trim($table -> title);
        }
        if(is_array($table -> params)){
            $attribs            = new JRegistry($table -> params);
            $table -> params    = $attribs -> toString();
        }
    }

    protected function generateNewTitle($category_id, $alias, $title)
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(array('alias' => $alias)))
        {
            $title = StringHelper::increment($title);
            $alias = StringHelper::increment($alias, 'dash');
        }

        return array($title, $alias);
    }

    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            $user = Factory::getUser();

            if(isset($record -> asset_id) && !empty($record -> asset_id)) {
                $state = $user->authorise('core.delete', $this->option . '.tag.' . (int)$record->id);
            }else{
                $state = $user->authorise('core.delete', $this->option . '.tag');
            }
            return $state;
        }

        return parent::canDelete($record);
    }

    protected function canEditState($record)
    {
        $user = Factory::getUser();

        // Check for existing tag.
        if (!empty($record->id))
        {
            if(isset($record -> asset_id) && !empty($record -> asset_id)) {
                return $user->authorise('core.edit.state', $this->option . '.tag.' . (int)$record->id);
            }else{
                return $user->authorise('core.edit.state', $this->option.'.tag');
            }
        }
        return parent::canEditState($record);
    }
}