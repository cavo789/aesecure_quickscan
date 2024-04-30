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

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Filesystem\Folder;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

jimport('joomla.filesytem.file');
jimport('joomla.filesytem.folder');
jimport('joomla.application.component.modeladmin');

class TZ_Portfolio_PlusModelTemplate_Style extends JModelAdmin
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

    protected function populateState(){
        parent::populateState();

        $input  = Factory::getApplication() -> input;
        $this -> setState('template.id',$input -> getInt('id'));
        $this -> setState('content.id',null);
        $this -> setState('category.id',null);
        $this -> setState('template.template',null);
        $this -> setState('template.rowincolumn',$input -> get('rowincolumn', false, 'bool'));
    }

    public function getTable($type = 'Templates', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    function getForm($data = array(), $loadData = true){
        // The folder and element vars are passed when saving the form.
        if (empty($data))
        {
            $item	   = $this -> getItem();
            $template  = $item -> template;
        }
        else
        {
            $template  = ArrayHelper::getValue($data, 'template');
        }

        // These variables are used to add data from the plugin XML files.
        $this->setState('template.template', $template);

        //        JForm::addFormPath(COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.)
        $form = $this->loadForm('com_tz_portfolio_plus.template_style', 'template_style', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }
        return $form;
    }

    protected function loadFormData()
    {
        if (empty($data)) {
            $data = $this->getItem();
            $data -> categories_assignment = $this -> getCategoriesAssignment();
            $data -> articles_assignment = $this -> getArticlesAssignment();
        }

        return $data;
    }

    protected function preprocessForm(JForm $form, $data, $group = 'content')
    {
        $template       = $this->getState('template.template');
        $lang           = Factory::getApplication() -> getLanguage();

        $template_path  = COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'templates'
            .DIRECTORY_SEPARATOR.$template;

        jimport('joomla.filesystem.path');

        $formFile = JPath::clean($template_path.DIRECTORY_SEPARATOR.'template.xml');

        // Load the core and/or local language file(s).
        TZ_Portfolio_PlusTemplate::loadLanguage($template);

        $default_directory  = 'components'.DIRECTORY_SEPARATOR.'com_tz_portfolio_plus'.DIRECTORY_SEPARATOR.'templates';
        $directory          = $default_directory.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html';
        if(Folder::exists(JPATH_SITE.DIRECTORY_SEPARATOR.$directory)) {
            $form->setFieldAttribute('layout', 'directory', $directory, 'params');
        }elseif ((is_array($data) && array_key_exists('protected', $data) && $data['protected'] == 1)
            || ((is_object($data) && isset($data->protected) && $data->protected == 1)))
        {
            $form -> removeField('layout','params');
        }else{
            $form -> removeField('layout','params');
        }

        if (file_exists($formFile))
        {
            // Get the template form.
            if (!$form->loadFile($formFile, false, '//config'))
            {
                throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
            }
        }

        // Disable home field if it is default style

        if ((is_array($data) && array_key_exists('home', $data) && $data['home'] == '1')
            || ((is_object($data) && isset($data->home) && $data->home == '1')))
        {
            $form->setFieldAttribute('home', 'readonly', 'true');
        }

        // Attempt to load the xml file.
        if (!$xml = simplexml_load_file($formFile))
        {
            throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
        }

        // Get the help data from the XML file if present.
        $help = $xml->xpath('/extension/help');

        if (!empty($help))
        {
            $helpKey = trim((string) $help[0]['key']);
            $helpURL = trim((string) $help[0]['url']);

            $this->helpKey = $helpKey ? $helpKey : $this->helpKey;
            $this->helpURL = $helpURL ? $helpURL : $this->helpURL;
        }

        // Trigger the default form events.
        parent::preprocessForm($form, $data, $group);
    }

    public function getCategoriesAssignment($pk = null){
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        if($pk > 0){
            $db     = $this -> getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('id');
            $query -> from('#__tz_portfolio_plus_categories');
            $query -> where('template_id = '.$pk);
            $db -> setQuery($query);
            if($rows = $db -> loadColumn()){
                return implode(',',$rows);
            }
        }
        return null;
    }

    public function getArticlesAssignment($pk = null){
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        if($pk > 0){
            $db     = $this -> getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('id');
            $query -> from('#__tz_portfolio_plus_content');
            $query -> where('template_id = '.$pk);
            $db -> setQuery($query);
            if($rows = $db -> loadColumn()){
                return implode(',',$rows);
            }
        }
        return null;
    }

    public function getItem($pk = null){
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

        if (property_exists($item, 'layout'))
        {
            $item->layout = json_decode($item -> layout);
        }
        if (property_exists($item, 'params'))
        {
            $item->params = json_decode($item -> params);
        }

        // Set default for preset if the style has layout default
        if(!isset($item -> layout) || (isset($item -> layout) && !$item -> layout)){
            $defaultLayout  = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.'/'.$item -> template.'/config/default.json';
            if(File::exists($defaultLayout)){
                $config = file_get_contents($defaultLayout);
                $config = json_decode($config);
                if($config){
                    if(isset($config -> params) && $config -> params){
                        $defParams  = json_decode($config -> params);
                        $defParams  = array_intersect_key((array) $defParams, (array) $item -> params);
                        $itemParams = array_merge((array) $item -> params, $defParams);
                        $item -> params = (object) $itemParams;
                    }
                    if(isset($config -> layout) && $config -> layout) {
                        $item->layout = json_decode($config->layout);
                    }
                }
//                $item -> preset = 'default';

            }
        }

        return $item;
    }

    protected function generateNewTitle($category_id, $alias, $title)
    {
        // Alter the title
        $table = $this->getTable();

        while ($table->load(array('title' => $title)))
        {
            $title = StringHelper::increment($title);
        }

        return $title;
    }

    public function save($data)
    {
        $app        = Factory::getApplication();

        $table 		= $this->getTable();
        $post   	= $app -> input -> post;
		$post		= $post -> getArray();
		
        $articlesAssignment         = null;
        $articlesAssignmentOld      = null;
        $categoriesAssignment       = null;
        $categoriesAssignmentOld    = null;

        $presets                    = null;

        if($data && isset($data['presets'])){
            $presets    = $data['presets'];
            unset($data['presets']);
        }

        $data['layout'] = '';
        if(isset($post['jform']['attrib']) && $attrib = $post['jform']['attrib']){
            $data['layout'] = json_encode($attrib);
        }else{
            $pathfile   = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/views/template_style/tmpl/default.json';
            if(File::exists($pathfile)){
                $data['layout'] = file_get_contents($pathfile);
            }
        }
        if(!$data['id'] || $data['id'] == 0){
            $data['title']  = $this ->generateNewTitle(null,null,$data['title']);
        }

        if(!$table -> hasHome()){
            $data['home']   = '1';
        }

        if ($app->input->get('task') == 'save2copy')
        {
            $data['id'] = 0;
            $data['home']   = 0;
            unset($data['articles_assignment']);
            unset($data['categories_assignment']);
            unset($post['menus_assignment_old']);
            unset($data['menus_assignment']);

        }

        if(isset($data['articles_assignment'])){
            $articlesAssignment  = $data['articles_assignment'];
            unset($data['article_assignment']);
        }

        if(isset($data['categories_assignment']) && count($data['categories_assignment'])){
            $categoriesAssignment  = $data['categories_assignment'];
            unset($data['categories_assignment']);
        }

        if($data['params']){
            $data['params'] = json_encode($data['params']);
        }

        $key = $table->getKeyName();
        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
        $isNew = true;

        // Include the content plugins for the on save events.
        JPluginHelper::importPlugin('content');

        // Allow an exception to be thrown.
        try
        {
            // Load the row if saving an existing record.
            if ($pk > 0)
            {
                $table->load($pk);
                $isNew = false;
            }

            // Bind the data.
            if (!$table->bind($data))
            {
                $this->setError($table->getError());

                return false;
            }

            // Prepare the row for saving
            $this->prepareTable($table);

            // Check the data.
            if (!$table->check())
            {
                $this->setError($table->getError());
                return false;
            }

            // Trigger the onContentBeforeSave event.
            $result = Factory::getApplication() -> triggerEvent($this->event_before_save,
                array($this->option . '.' . $this->name, $table, $isNew, $data));

            if (in_array(false, $result, true))
            {
                $this->setError($table->getError());
                return false;
            }

            // Store the data.
            if (!$table->store())
            {
                $this->setError($table->getError());
                return false;
            }

            if($data['home'] == '1'){
                $this -> setHome($table -> id);
            }

            $db     = $this -> getDbo();

            $user = Factory::getUser();

            // Assign template style for menu
            if ($user->authorise('core.edit', 'com_menus'))
            {
                $n    = 0;
                $db   = $this -> getDbo();
                $user = Factory::getUser();

                // Assign menu items with this template;
                if(!empty($post['jform']['menus_assignment_old']) && count($post['jform']['menus_assignment_old'])){
                    $query  = $db -> getQuery(true)
                        -> select('*')
                        -> from('#__menu')
                        ->where('id IN (' . implode(',', $post['jform']['menus_assignment_old']) . ')')
                        ->where('(checked_out IS NULL OR checked_out IN (0,' . (int) $user->id . '))');
                    $db -> setQuery($query);
                    if($menu = $db -> loadObjectList()){
                        foreach($menu as $item){
                            $params         = new JRegistry($item -> params);
                            $params -> set('tz_template_style_id',0);
                            $update_query = 'UPDATE '.$db -> quoteName('#__menu').' SET params='
                                .$db -> quote($params -> toString()).
                                ' WHERE id='.$item -> id;
                            if(!empty($update_query)){
                                $db -> setQuery($update_query);
                                $db ->execute();
                            }
                        }
                    }
                }

                if (!empty($data['menus_assignment']) && is_array($data['menus_assignment']))
                {

                    $data['menus_assignment']   = ArrayHelper::toInteger($data['menus_assignment']);

                    $query  = $db -> getQuery(true)
                        -> select('*')
                        -> from('#__menu')
                        ->where('id IN (' . implode(',', $data['menus_assignment']) . ')')
                        ->where('(checked_out IS NULL OR checked_out IN (0,' . (int) $user->id . '))');

                    $db -> setQuery($query);

                    if($menus = $db -> loadObjectList()){
                        foreach($menus as $menu){
                            $params         = new JRegistry($menu -> params);
                            $params -> set('tz_template_style_id',$table -> id);

                            $update_query = 'UPDATE '.$db -> quoteName('#__menu').' SET params='
                                .$db -> quote($params -> toString()).
                                ' WHERE id='.$menu -> id;
                            if(!empty($update_query)){
                                $db -> setQuery($update_query);
                                $db ->execute();
                            }
                        }
                    }
                }

            }


            // Assign article with this template;
            if(!empty($articlesAssignment) && count($articlesAssignment)){

                // Update the mapping for article items that this style IS assigned to.
                $query = $db->getQuery(true)
                    ->update('#__tz_portfolio_plus_content')
                    ->set('template_id = ' . (int) $table->id)
                    ->where('id IN (' . implode(',', $articlesAssignment) . ')')
                    ->where('template_id != ' . (int) $table->id);
                $db->setQuery($query);
                $db->execute();

                $query  = $db -> getQuery(true);
                $query -> select('id');
                $query -> from($db -> quoteName('#__tz_portfolio_plus_content'));
                $query -> where($db -> quoteName('id').' IN('
                    .implode(',',$articlesAssignment).')');
                $db -> setQuery($query);

                if(!$updateIds = $db -> loadColumn()){
                    $updateIds  = array();
                }

                // Insert article items with this template if they were created in com_content
                if($insertIds  = array_diff($articlesAssignment,$updateIds)){
                    $query  = $db -> getQuery(true);
                    $query -> insert($db -> quoteName('#__tz_portfolio_plus_content'));
                    $query ->columns('id,type,link_attribs,template_id');
                    foreach($insertIds as $cid){
                        $query -> values($cid.','.$db -> quote('none').','
                            .$db -> quote('{"link_target":"_blank","link_follow":"nofollow"}')
                            .','.$table -> id);
                    }
                    $db -> setQuery($query);
                    $db -> execute();
                }
            }

            // Remove style mappings for article items this style is NOT assigned to.
            // If unassigned then all existing maps will be removed.
            $query = $db->getQuery(true)
                ->update('#__tz_portfolio_plus_content')
                ->set('template_id = 0');

            if (!empty($articlesAssignment) && count($articlesAssignment))
            {
                $query->where('id NOT IN (' . implode(',', $articlesAssignment) . ')');
            }

            $query->where('template_id = ' . (int) $table->id);
            $db->setQuery($query);
            $db->execute();



            // Assign categories with this template;
            if(!empty($categoriesAssignment) && count($categoriesAssignment)){

                // Update the mapping for category items that this style IS assigned to.
                $query = $db->getQuery(true)
                    ->update('#__tz_portfolio_plus_categories')
                    ->set('template_id = ' . (int) $table->id)
                    ->where('id IN (' . implode(',', $categoriesAssignment) . ')')
                    ->where('template_id != ' . (int) $table->id);
                $db->setQuery($query);
                $db->execute();

                $query  = $db -> getQuery(true);
                $query -> select('id');
                $query -> from($db -> quoteName('#__tz_portfolio_plus_categories'));
                $query -> where($db -> quoteName('id').' IN('
                    .implode(',',$categoriesAssignment).')');
                $db -> setQuery($query);

                if(!$updateIds = $db -> loadColumn()){
                    $updateIds  = array();
                }

                // Insert category items with this template if they were created in com_content
                if($insertIds  = array_diff($categoriesAssignment,$updateIds)){
                    $query  = $db -> getQuery(true);
                    $query -> insert($db -> quoteName('#__tz_portfolio_plus_categories'));
                    $query ->columns('id,groupid,template_id');
                    foreach($insertIds as $cid){
                        $query -> values($cid.',0,'.$table -> id);
                    }
                    $db -> setQuery($query);
                    $db -> execute();
                }
            }

            // Remove style mappings for category items this style is NOT assigned to.
            // If unassigned then all existing maps will be removed.
            $query = $db->getQuery(true)
                ->update('#__tz_portfolio_plus_categories')
                ->set('template_id = 0');

            if (!empty($categoriesAssignment) && count($categoriesAssignment))
            {
                $query->where('id NOT IN (' . implode(',', $categoriesAssignment) . ')');
            }

            $query->where('template_id = ' . (int) $table->id);
            $db->setQuery($query);
            $db->execute();

            // Save preset
            if($presets){
                if(is_array($presets) && count($presets)){
                    $presets    = array_filter($presets);
                    if(count($presets) && isset($presets['name'])){
                        $name   = trim($presets['name']);
                        if($name && !empty($name)) {

                            $preset_name            = JApplicationHelper::stringURLSafe($name);
                            $tpl_base_path          = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH
                                .DIRECTORY_SEPARATOR. $table -> template;

                            while(File::exists($tpl_base_path.DIRECTORY_SEPARATOR.'config'
                                .DIRECTORY_SEPARATOR.$preset_name.'.json')){
                                $preset_name    = StringHelper::increment($preset_name,'dash');
                            }

                            if(isset($presets['image']) && $presets['image']){
                                $image_path = JPATH_ROOT.DIRECTORY_SEPARATOR.$presets['image'];
                                if(File::exists($image_path)){
                                    $image_name = $preset_name.'.'.File::getExt($image_path);
                                    $folder     = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH
                                        .DIRECTORY_SEPARATOR. $table -> template.DIRECTORY_SEPARATOR
                                        .'images'.DIRECTORY_SEPARATOR.'presets';
                                    if(!Folder::exists($folder)){
                                        Folder::create($folder);
                                    }
                                    if(File::copy($image_path, $folder.DIRECTORY_SEPARATOR.$image_name)){
                                        $presets['image']   = 'templates/'.$table -> template
                                            .'/images/presets/'.$image_name;
                                    }
                                }
                            }

                            $presets['name']        = $preset_name;
                            $preset_value           = new stdClass();
                            $preset_value->layout   = $table->layout;
                            $preset_value->params   = $table->params;
                            $preset_value->presets  = $presets;

                            $path   = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH
                                .'/'.$table -> template.'/config/'.$preset_name.'.json';
                            $preset_value   = json_encode($preset_value);
                            if(File::write($path,$preset_value)){
                                $query  = $db -> getQuery(true)
                                    -> update('#__tz_portfolio_plus_templates')
                                    -> set('preset='.$db -> quote($preset_name))
                                    -> where('id='.$table -> id);
                                $db -> setQuery($query);
                                $db -> execute();
                            }
                        }
                    }
                }
            }

            // Clean the cache.
            $this->cleanCache();

            // Trigger the onContentAfterSave event.
            Factory::getApplication() -> triggerEvent($this->event_after_save,
                array($this->option . '.' . $this->name, $table, $isNew, $data));
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        $pkName = $table->getKeyName();

        if (isset($table->$pkName))
        {
            $this->setState($this->getName() . '.id', $table->$pkName);
        }
        $this->setState($this->getName() . '.new', $isNew);

        return true;
    }

    public function getTZLayout(){
        $item   = $this -> getItem();
        $layout = $item -> layout;
        if(empty($layout)){
            $pathfile   = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/views/template_style/tmpl/default.json';

            // If the default.json config file exists in style
            $defaultLayout  = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.'/'.$item -> template.'/config/default.json';
            if(File::exists($defaultLayout)){
//                $pathfile   = $defaultLayout;
            }

            if(File::exists($pathfile)){
                $configLayout   = file_get_contents($pathfile);
                $configLayout   = json_decode($configLayout);

                // With the default.json of style has layout option key
                if(is_object($configLayout) && isset($configLayout -> layout) && $configLayout -> layout){
                    if(is_string($configLayout -> layout)){
                        return json_decode($configLayout -> layout);
                    }
                    return (array) $configLayout -> layout;
                }

                return $configLayout;
            }
        }
        return $layout;
    }

    public function setHome($id = 0)
    {
        $user = Factory::getUser();
        $db   = $this->getDbo();

        // Access checks.
        if (!$user->authorise('core.edit.state', 'com_tz_portfolio_plus.style'))
        {
            throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
        }

        $style = $this -> getTable();

        if (!$style->load((int) $id))
        {
            throw new Exception(JText::_('COM_TEMPLATES_ERROR_STYLE_NOT_FOUND'));
        }

        // Reset the home fields for the client_id.
        $db->setQuery(
            'UPDATE #__tz_portfolio_plus_templates' .
            ' SET home = \'0\'' .
            ' WHERE home = \'1\''
        );
        $db->execute();

        // Set the new home style.
        $db->setQuery(
            'UPDATE #__tz_portfolio_plus_templates' .
            ' SET home = \'1\'' .
            ' WHERE id = ' . (int) $id
        );
        $db->execute();

        // Clean the cache.
        $this->cleanCache();

        return true;
    }

    public function unsetHome($id = 0)
    {
        $user = Factory::getUser();
        $db   = $this->getDbo();

        // Access checks.
        if (!$user->authorise('core.edit.state', 'com_tz_portfolio_plus.style'))
        {
            throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
        }

        // Lookup the client_id.
        $db->setQuery(
            'SELECT home' .
            ' FROM #__tz_portfolio_plus_templates' .
            ' WHERE id = ' . (int) $id
        );
        $style = $db->loadObject();

        if ($style->home == '1')
        {
            throw new Exception(JText::_('COM_TEMPLATES_ERROR_CANNOT_UNSET_DEFAULT_STYLE'));
        }

        // Set the new home style.
        $db->setQuery(
            'UPDATE #__tz_portfolio_plus_templates' .
            ' SET home = \'0\'' .
            ' WHERE id = ' . (int) $id
        );
        $db->execute();

        // Clean the cache.
        $this->cleanCache();

        return true;
    }

    public function delete(&$pks)
    {
        $pks	= (array) $pks;
        $user	= Factory::getUser();
        $table	= $this->getTable();

        // Iterate the items to delete each one.
        foreach ($pks as $pk)
        {
            if ($table->load($pk))
            {
                // Access checks.
                if (!$user->authorise('core.delete', 'com_tz_portfolio_plus.style'))
                {
                    \JLog::add(\JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), \JLog::WARNING, 'jerror');

                    return false;
                }

                // You should not delete a default style
                if ($table->home != '0')
                {
                    JError::raiseWarning(SOME_ERROR_NUMBER, Jtext::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE_STYLE_CANNOT_DELETE_DEFAULT_STYLE'));
                    return false;
                }

                if (!$table->delete($pk))
                {
                    $this->setError($table->getError());

                    return false;
                }
            }
            else
            {
                $this->setError($table->getError());

                return false;
            }
        }

        // Clean cache
        $this->cleanCache();

        return true;
    }

    public function deleteTemplate(&$template)
    {
        $user	= Factory::getUser();
        $table	= $this->getTable();

        // Access checks.
        if (!$user->authorise('core.delete', 'com_tz_portfolio_plus'))
        {
            throw new Exception(JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
        }

        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);

        $query -> delete($db -> quoteName('#__tz_portfolio_plus_templates'));
        $query -> where($db -> quoteName('template').'='.$db -> quote($template));
        $db -> setQuery($query);
        if(!$db -> execute()){
            $this -> setError($db -> getError());
            return false;
        }

        // Clean cache
        $this->cleanCache();

        return true;
    }

    public function duplicate(&$pks)
    {
        $user	= Factory::getUser();

        // Access checks.
        if (!$user->authorise('core.create', 'com_tz_portfolio_plus.style'))
        {
            throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
        }

        $table = $this->getTable();

        foreach ($pks as $pk)
        {
            if ($table->load($pk, true))
            {
                // Reset the id to create a new record.
                $table->id = 0;

                // Reset the home (don't want dupes of that field).
                $table->home = 0;

                // Alter the title.
                $m = null;
                $table->title = $this -> generateNewTitle(null,null,$table -> title);

                if (!$table->check() || !$table->store())
                {
                    throw new Exception($table->getError());
                }
            }
            else
            {
                throw new Exception($table->getError());
            }
        }

        // Clean cache
        $this->cleanCache();

        return true;
    }

    public function getItemTemplate($artId = null,$catId = null){
        $_artId = !empty($artId)?$artId:$this -> getState('content.id');
        $_catId = !empty($catId)?$catId:$this -> getState('category.id');

        $db         = $this -> getDbo();
        $templateId = null;

        if($_catId){
            $query  = $db -> getQuery(true);
            $query -> select($db -> quoteName('template_id'));
            $query -> from($db -> quoteName('#__tz_portfolio_plus_categories'));
            $query -> where($db -> quoteName('id').'='.$_catId);
            $db -> setQuery($query);
            if($crow = $db -> loadObject()){
                if($crow -> template_id){
                    $templateId = $crow -> template_id;
                }
            }
        }
        if($_artId){
            $query  = $db -> getQuery(true);
            $query -> select($db -> quoteName('template_id'));
            $query -> from($db -> quoteName('#__tz_portfolio_plus_content'));
            $query -> where($db -> quoteName('id').'='.$_artId);
            $db -> setQuery($query);
            if($row = $db -> loadObject()){
                if($row -> template_id){
                    $templateId = $row -> template_id;
                }
            }
        }
        return (int) $templateId;
    }

    public function getPresets(){
        if($item   = $this -> getItem()){
            $path   = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.DIRECTORY_SEPARATOR.$item -> template
                .DIRECTORY_SEPARATOR.'config';
            if(Folder::exists($path)){
                $files  = Folder::files($path,'.json',true,false,array('.json'));
                if(count($files)){
                    $items  = array();
                    foreach($files as $i => $file){
                        if($data       = file_get_contents($path.DIRECTORY_SEPARATOR.$file)) {
                            $config = json_decode($data);
                            $items[] = $config->presets;
                        }

                    }
                    if(count($items)) {
                        return $items;
                    }
                }
            }
        }
    }

    public function loadPreset($data){
        if($data && isset($data['preset'])){
            $preset_name    = $data['preset'];
            $path           = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.DIRECTORY_SEPARATOR.$data['template']
                .DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$preset_name.'.json';
            if(File::exists($path)){
                $table      = $this->getTable();
                $config     = file_get_contents($path);
                $config     = json_decode($config);
                $key        = $table->getKeyName();
                $pk         = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');

                $data['layout'] = $config -> layout;
                $data['params'] = $config -> params;

                // Allow an exception to be thrown.
                try
                {
                    // Load the row if saving an existing record.
                    if ($pk > 0)
                    {
                        $table->load($pk);
                        $isNew = false;
                    }

                    // Bind the data.
                    if (!$table->bind($data))
                    {
                        $this->setError($table->getError());

                        return false;
                    }

                    // Prepare the row for saving
                    $this->prepareTable($table);

                    // Check the data.
                    if (!$table->check())
                    {
                        $this->setError($table->getError());
                        return false;
                    }

                    // Trigger the onContentBeforeSave event.
                    $result = Factory::getApplication() -> triggerEvent($this->event_before_save,
                        array($this->option . '.' . $this->name, $table, $isNew, $data));

                    if (in_array(false, $result, true))
                    {
                        $this->setError($table->getError());
                        return false;
                    }

                    // Store the data.
                    if (!$table->store())
                    {
                        $this->setError($table->getError());
                        return false;
                    }
                }
                catch (Exception $e)
                {
                    $this->setError($e->getMessage());

                    return false;
                }
            }
        }
        return true;
    }

    public function removePreset($data){
        if($data && isset($data['preset'])){
            $preset_name    = $data['preset'];
            $path           = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.DIRECTORY_SEPARATOR.$data['template']
                .DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$preset_name.'.json';
            if(File::exists($path)){
                // Remove file
                if(File::delete($path)){
                    $table      = $this->getTable();
                    $key        = $table->getKeyName();
                    $pk         = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');

                    $_data['id']        = $pk;
                    $_data['preset']    = '';

                    // Allow an exception to be thrown.
                    try
                    {

                        // Load the row if saving an existing record.
                        if ($pk > 0)
                        {
                            $table->load($pk);
                            $isNew  = false;
                        }

                        // Bind the data.
                        if (!$table->bind($_data))
                        {
                            $this->setError($table->getError());

                            return false;
                        }

                        // Prepare the row for saving
                        $this->prepareTable($table);

                        // Check the data.
                        if (!$table->check())
                        {
                            $this->setError($table->getError());
                            return false;
                        }

                        // Trigger the onContentBeforeSave event.
                        $result = Factory::getApplication() -> triggerEvent($this->event_before_save,
                            array($this->option . '.' . $this->name, $table, $isNew, $data));

                        if (in_array(false, $result, true))
                        {
                            $this->setError($table->getError());
                            return false;
                        }

                        // Store the data.
                        if (!$table->store())
                        {
                            $this->setError($table->getError());
                            return false;
                        }
                    }
                    catch (Exception $e)
                    {
                        $this->setError($e->getMessage());

                        return false;
                    }
                }
            }
        }
        return true;
    }

    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            $user = Factory::getUser();

            if(isset($record -> asset_id) && !empty($record -> asset_id)) {
                $state = $user->authorise('core.delete', $this->option . '.style.' . (int)$record->id);
            }else{
                $state = $user->authorise('core.delete', $this->option . '.style');
            }
            return $state;
        }

        return parent::canDelete($record);
    }

    protected function canEditState($record)
    {
        $user = Factory::getUser();

        // Check for existing group.
        if (!empty($record->id))
        {
            if(isset($record -> asset_id) && $record -> asset_id) {
                $state = $user->authorise('core.edit.state', $this->option . '.style.' . (int)$record->id);
            }else{
                $state = $user->authorise('core.edit.state', $this->option . '.style');
            }
            return $state;
        }

        return parent::canEditState($record);
    }
}