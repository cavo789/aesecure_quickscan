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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Filesystem\File;
use Joomla\Utilities\ArrayHelper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

jimport('joomla.filesytem.file');
jimport('joomla.application.component.modeladmin');
JLoader::import('com_tz_portfolio_plus.helpers.addons', JPATH_ADMINISTRATOR.'/components');

class TZ_Portfolio_PlusModelAddon extends JModelAdmin
{
    protected $type         = 'tz_portfolio_plus-plugin';
    protected $accept_types = array();
    protected $_cache;
    protected $folder       = 'addons';
    protected $cache;
    protected $limit;
    protected $filterFormName = null;
    protected $filter_fields    = array();

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this -> accept_types   = array('tz_portfolio_plus-plugin', 'tz_portfolio_plus-template');

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

    protected function populateState()
    {
        parent::populateState();

        $app    = Factory::getApplication();

        $filters = $app->getUserStateFromRequest($this->option . '.'.$this -> getName().'.filter', 'filter', array(), 'array');
        $this -> setState('filters', $filters);

        $limitstart  = $app -> getUserStateFromRequest($this->option . '.'.$this -> getName().'.limitstart', 'limitstart', 0,'int');
        $this -> setState('list.start', $limitstart);

        $search      = isset($filters['search'])?$filters['search']:null;
        $search  = $app -> getUserStateFromRequest($this->option . '.'.$this -> getName().'.filter_search', 'filter_search', $search,'string');
        $this -> setState('filter.search',$search);

        $type  = $app -> getUserStateFromRequest($this->option . '.'.$this -> getName().'.filter_type', 'filter_type',
            (isset($filters['type'])?$filters['type']:null), 'string');
        $this -> setState('filter.type',$type);

        if ($list = $app->getUserStateFromRequest($this->option . '.'.$this -> getName() . '.list', 'list', array(), 'array'))
        {
            $ordering   = 'rdate';
            if(isset($list['fullordering'])) {
                $ordering = $list['fullordering'];
            }
            $this->setState('list.ordering', $ordering);
        }

        if($listSubmit  = $app -> input -> get('list', array(), 'array')){
            if(isset($list['form_submited'])) {
                $this->setState('list.form_submited', $list['form_submited']);
            }
        }

        // Support old ordering field
        $oldOrdering = $app->input->get('filter_order', 'rdate');

        if (!empty($oldOrdering) && in_array($oldOrdering, $this->filter_fields))
        {
            $this->setState('list.ordering', $oldOrdering);
        }

        $this -> setState('cache.filename', $this -> getName().'_list');

    }

    public function getTable($type = 'Extensions', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true){
        $input  = Factory::getApplication() -> input;
        // The folder and element vars are passed when saving the form.
        if (empty($data))
        {
            $item		= $this->getItem();
            $folder		= $item->folder;
            $element	= $item->element;
        }
        else
        {
            $folder		= ArrayHelper::getValue($data, 'folder', '', 'cmd');
            $element	= ArrayHelper::getValue($data, 'element', '', 'cmd');
        }

        // These variables are used to add data from the plugin XML files.
        $this->setState('item.folder',	$folder);
        $this->setState('item.element',	$element);

        $control    = 'jform';
        if($input -> getCmd('layout') == 'upload'){
            $loadData   = false;
            $control    = '';
        }

        $form = $this->loadForm('com_tz_portfolio_plus.'.$this -> getName(), $this -> getName(),
            array('control' => $control, 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }
        return $form;
    }
    protected function loadFormData()
    {
        $input  = Factory::getApplication() -> input;
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_tz_portfolio_plus.edit.addon.data', array());

        if (empty($data))
        {
            $data = $this->getItem();
        }

        // Pre-fill the list options
        if (!property_exists($data, 'list'))
        {
            $data->list = array(
                'fullordering'  => $this->getState('list.ordering')
            );
        }

        $this->preprocessData('com_tz_portfolio_plus.'.$input -> getCmd('view'), $data);

        return $data;
    }


    protected function preprocessForm(JForm $form, $data, $group = 'content')
    {
        $input  = Factory::getApplication() -> input;

        if($input -> getCmd('layout') != 'upload'){
            jimport('joomla.filesystem.path');


            $folder		= $this->getState('item.folder');
            $element	= $this->getState('item.element');
            $lang		= Factory::getApplication() -> getLanguage();

            // Load the core and/or local language sys file(s) for the ordering field.
            $db     = $this -> getDbo();
            $query  = $db->getQuery(true)
                ->select($db->quoteName('element'))
                ->from($db->quoteName('#__tz_portfolio_plus_extensions'))
                ->where($db->quoteName('type') . ' = ' . $db->quote($this -> type))
                ->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
            $db->setQuery($query);

            if (empty($folder) || empty($element))
            {
                $app = Factory::getApplication();
                $app->redirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=addons', false));
            }

            $formFile = JPath::clean(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $folder . '/' . $element . '/' . $element . '.xml');

            if (!file_exists($formFile))
            {
                throw new Exception(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_ADDONS_ERROR_FILE_NOT_FOUND', $element . '.xml'));
            }

            // Load the core and/or local language file(s).
            TZ_Portfolio_PlusPluginHelper::loadLanguage($element, $folder);

            if (file_exists($formFile))
            {
                // Get the plugin form.
                if (!$form->loadFile($formFile, false, '//config'))
                {
                    throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
                }
            }

            if($form -> getField('rules')){
                if($data) {
                    if(isset($folder) && $folder && isset($element) && $element) {
                        $form -> setFieldAttribute('title', 'value', JText::_('PLG_' . strtoupper($folder . '_' . $element)));
                        if(!$form -> getFieldAttribute('rules', 'group')) {
                            $form->setFieldAttribute('rules', 'group', $folder);
                        }
                        if(!$form -> getFieldAttribute('rules', 'addon')) {
                            $form -> setFieldAttribute('rules', 'addon', $element);
                        }
                    }
                }
            }

            if($addonId = $input -> getInt('id')){

                $user       = TZ_Portfolio_PlusUser::getUser();

                if(!$user->authorise('core.edit', 'com_tz_portfolio_plus.addon.'.$addonId)){
                    $form -> setFieldAttribute('folder', 'type', 'hidden');
                    $form -> setFieldAttribute('element', 'type', 'hidden');
                    $form -> removeField('access');
                    $form -> removeField('published');
                }
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
        }

        // Insert parameter from extrafield
        JLoader::import('extrafields', COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH);
        TZ_Portfolio_PlusHelperExtraFields::prepareForm($form, $data);

        // Trigger the default form events.
        parent::preprocessForm($form, $data, $group);
    }

    public function getExtension($name, $type){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_extensions'));
        $query -> where($db -> quoteName('type').'='.$db -> quote($type));
        $query -> where($db -> quoteName('name').'='.$db -> quote($name));
        $db -> setQuery($query);
        if($data = $db -> loadObject()){
            return $data;
        }
        return false;
    }

    public function install()
    {
        $app = Factory::getApplication();
        $input = $app->input;

        // Load installer plugins for assistance if required:
        JPluginHelper::importPlugin('installer');

        $package = null;

        // This event allows an input pre-treatment, a custom pre-packing or custom installation.
        // (e.g. from a JSON description).
        $results = $app->triggerEvent('onInstallerBeforeInstallation', array($this, &$package));

        /* phan code working */
        if (in_array(true, $results, true)) {
            return true;
        }

        if (in_array(false, $results, true)) {
            return false;
        }
        /* end phan code working */

        if ($input->get('task') == 'ajax_install') {
            $url = $input->post->get('pProduceUrl', null, 'string');
            $package = $this->_getPackageFromUrl($url);
        } else {
            $package = $this->_getPackageFromUpload();
        }

        $result = true;
        $msg = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INSTALL_SUCCESS', JText::_('COM_TZ_PORTFOLIO_PLUS_' . $input->getCmd('view')));

        // This event allows a custom installation of the package or a customization of the package:
        $results = $app->triggerEvent('onInstallerBeforeInstaller', array($this, &$package));

        if (in_array(true, $results, true)) {
            return true;
        }

        if (in_array(false, $results, true)) {
            return false;
        }

        // Was the package unpacked?
        if (!$package || !$package['type']) {
            JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

            $this->setError(JText::_('COM_TZ_PORTFOLIO_PLUS_UNABLE_TO_FIND_INSTALL_PACKAGE'));

            return false;
        }

        // Get an installer instance.
        $installer  = JInstaller::getInstance($package['dir']);
        $installer -> setPath('source',$package['dir']);


        if($manifest = $installer ->getManifest()){
            $attrib = $manifest -> attributes();

            $name   = (string) $manifest -> name;
            $type   = (string) $attrib -> type;

            if(!in_array($type, $this -> accept_types) || (in_array($type, $this -> accept_types)
                    && $type != $this -> type)){
                $this -> setError(JText::_('COM_TZ_PORTFOLIO_PLUS_UNABLE_TO_FIND_INSTALL_PACKAGE'));
                return false;
            }


            $_type  = str_replace('tz_portfolio_plus-','',$type);

            // Install for add-ons to update version
            JLoader::import('com_tz_portfolio_plus.libraries.adapter.plugin',JPATH_ADMINISTRATOR
                .DIRECTORY_SEPARATOR.'components');

            $class  = 'TZ_Portfolio_Plus\Installer\Adapter\TZ_Portfolio_PlusInstaller'.ucfirst($_type).'Adapter';

            if(!class_exists($class)){
                JLoader::register($class, JPath::clean(COM_TZ_PORTFOLIO_PLUS_LIBRARIES.'/adapter/'.$_type.'.php'));
            }

            $tzinstaller    = new $class($installer,$installer -> getDbo());
            $tzinstaller -> setRoute('install');
            $tzinstaller -> setManifest($installer -> getManifest());

            if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
                $tzinstaller -> setProperties(array('type' => $type));
            }

            if(!$tzinstaller -> install()){
                // There was an error installing the package.
                $msg = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INSTALL_ERROR', $input -> getCmd('view'));
                $result = false;
                $this -> setError($msg);
            }

            if(method_exists($this, 'afterInstall')) {
                $this -> afterInstall($manifest);
            }

            // This event allows a custom a post-flight:
            $app->triggerEvent('onInstallerAfterInstaller', array($this, &$package, $installer, &$result, &$msg));
        }

        JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

        return $result;
    }

    public function uninstall($eid = array())
    {
        $user   = TZ_Portfolio_PlusUser::getUser();
        $app    = Factory::getApplication();
        $view   = $app -> input -> getCmd('view');

        if (!$user->authorise('core.delete', 'com_tz_portfolio_plus.addon'))
        {
            \JLog::add(\JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), \JLog::WARNING, 'jerror');
            return false;
        }

        /*
         * Ensure eid is an array of extension ids in the form id => client_id
         * TODO: If it isn't an array do we want to set an error and fail?
         */
        if (!is_array($eid))
        {
            $eid = array($eid => 0);
        }

        // Get an installer object for the extension type
        $table = $this -> getTable();

        // Uninstall the chosen extensions
        $msgs = array();
        $result = false;

        // Get an installer instance.
        $installer  = JInstaller::getInstance();

        foreach ($eid as $id)
        {
            $id = trim($id);
            $table->load($id);

            $langstring = 'COM_TZ_PORTFOLIO_PLUS_' . strtoupper($table->type);
            $rowtype = JText::_($langstring);

            if (strpos($rowtype, $langstring) !== false)
            {
                $rowtype = $table->type;
            }

            if ($table->type && $table->type == 'tz_portfolio_plus-plugin')
            {

                // Is the template we are trying to uninstall a core one?
                // Because that is not a good idea...
                if ($table->protected)
                {
                    JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_PLG_UNINSTALL_WARNCOREPLUGIN',
                        JText::_('COM_TZ_PORTFOLIO_PLUS_'.$view)), JLog::WARNING, 'jerror');

                    return false;
                }

                $_type  = str_replace('tz_portfolio_plus-','',$table->type);
                tzportfolioplusimport('adapter.'.$_type);
                $class  = 'TZ_Portfolio_Plus\Installer\Adapter\TZ_Portfolio_PlusInstaller'.$_type.'Adapter';

                $tzinstaller    = new $class($installer,$installer -> getDbo());

                $result = $tzinstaller->uninstall($id);

                // Build an array of extensions that failed to uninstall
                if ($result === false)
                {
                    // There was an error in uninstalling the package
                    $msgs[] = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_UNINSTALL_ERROR', JText::_('COM_TZ_PORTFOLIO_PLUS_'.$view));

                    continue;
                }

                // Package uninstalled sucessfully
                $msgs[] = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_UNINSTALL_SUCCESS', JText::_('COM_TZ_PORTFOLIO_PLUS_'.$view));
                $result = true;
            }
        }

        $msg = implode("<br />", $msgs);
        $app->enqueueMessage($msg);

        return $result;
    }

    public function afterSave($data){}

    protected function _getPackageFromUpload()
    {
        // Get the uploaded file information.
        $input    = Factory::getApplication()->input;
        // Do not change the filter type 'raw'. We need this to let files containing PHP code to upload. See JInputFiles::get.
        $userfile = $input->files->get('install_package', null, 'raw');

        // Make sure that file uploads are enabled in php.
        if (!(bool) ini_get('file_uploads'))
        {
            JError::raiseWarning('', JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_INSTALL_WARNINSTALLFILE'));

            return false;
        }

        // Make sure that zlib is loaded so that the package can be unpacked.
        if (!extension_loaded('zlib'))
        {
            JError::raiseWarning('', JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_INSTALL_WARNINSTALLZLIB'));

            return false;
        }

        // If there is no uploaded file, we have a problem...
        if (!is_array($userfile))
        {
            JError::raiseWarning('', JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_INSTALL_NO_FILE_SELECTED'));

            return false;
        }

        // Is the PHP tmp directory missing?
        if ($userfile['error'] && ($userfile['error'] == UPLOAD_ERR_NO_TMP_DIR))
        {
            JError::raiseWarning('', JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_WARNINGS_PHPUPLOADNOTSET'));

            return false;
        }

        // Is the max upload size too small in php.ini?
        if ($userfile['error'] && ($userfile['error'] == UPLOAD_ERR_INI_SIZE))
        {
            JError::raiseWarning('', JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_WARNINGS_SMALLUPLOADSIZE'));

            return false;
        }

        // Check if there was a different problem uploading the file.
        if ($userfile['error'] || $userfile['size'] < 1)
        {
            JError::raiseWarning('', JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_INSTALL_WARNINSTALLUPLOADERROR'));

            return false;
        }

        // Build the appropriate paths.
        $tmp_dest	= JPATH_ROOT . '/tmp/tz_portfolio_plus_install/' . $userfile['name'];
        $tmp_src	= $userfile['tmp_name'];

        if(!File::exists(JPATH_ROOT . '/tmp/tz_portfolio_plus_install/index.html')){
            File::write(JPATH_ROOT . '/tmp/tz_portfolio_plus_install/index.html',
                htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
        }

        // Move uploaded file.
        jimport('joomla.filesystem.file');
        File::upload($tmp_src, $tmp_dest, false, true);

        // Unpack the downloaded package file.
        $package = JInstallerHelper::unpack($tmp_dest, true);

        return $package;
    }

    public function getItem($pk = null)
    {
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName().'.id');

        if (!isset($this->_cache[$pk]))
        {
            $false	= false;

            // Get a row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());

                return $false;
            }

            // Convert to the JObject before adding other data.
            $properties = $table->getProperties(1);
            $this->_cache[$pk] = ArrayHelper::toObject($properties, 'JObject');

            // Convert the params field to an array.
            $registry = new Registry;
            if($table -> params) {
                $registry->loadString($table->params);
            }
            $this->_cache[$pk]->params = $registry->toArray();

            $plugin = TZ_Portfolio_PlusPluginHelper::getInstance($this->_cache[$pk] -> folder, $this->_cache[$pk] -> element);

            $this->_cache[$pk] -> data_manager        = false;
            if(is_object($plugin) && method_exists($plugin, 'getDataManager')){
                $this->_cache[$pk] -> data_manager    = $plugin -> getDataManager();
            }

            // Get the plugin XML.
            $path = JPath::clean(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $table->folder . '/'
                . $table->element . '/' . $table->element . '.xml');

            if (file_exists($path))
            {
                $xml                    = simplexml_load_file($path);
                $this->_cache[$pk]->xml = $xml;
            }
            else
            {
                $this->_cache[$pk]->xml = null;
            }
        }

        return $this->_cache[$pk];
    }

    public function getAddOnItem($pk = null){
        $pk         = (!empty($pk)) ? $pk : (int) $this->getState($this->getName().'.id');
        $storeId    = __METHOD__.'::' .$pk;

        if (!isset($this->_cache[$storeId]))
        {
            $false	= false;

            // Get a row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());

                return $false;
            }

            // Convert to the JObject before adding other data.
            $properties = $table->getProperties(1);
            $this->_cache[$storeId] = ArrayHelper::toObject($properties, 'JObject');

            // Convert the params field to an array.
            $registry = new Registry;
            $registry->loadString($table->params);
            $this->_cache[$storeId]->params = $registry->toArray();

            $dispatcher     = TZ_Portfolio_PlusPluginHelper::getDispatcher();
            $plugin         = TZ_Portfolio_PlusPluginHelper::getInstance($table -> folder,
                $table -> element, false, $dispatcher);
            if(method_exists($plugin, 'onAddOnDisplayManager')) {
                $this->_cache[$storeId]->manager = $plugin->onAddOnDisplayManager();
            }
        }

        return $this->_cache[$storeId];
    }

    public function getReturnLink(){
        $input  = Factory::getApplication() -> input;
        if($return = $input -> get('return', null, 'base64')){
            return $return;
        }
        return false;
    }

    public function prepareTable($table){
        if(isset($table -> params) && is_array($table -> params)){
            $registry   = new Registry;
            $registry -> loadArray($table -> params);
            $table -> params    = $registry -> toString();
        }
    }

    protected function getReorderConditions($table)
    {
        $condition = array();
        $condition[] = 'type = ' . $this->_db->quote($table->type);
        $condition[] = 'folder = ' . $this->_db->quote($table->folder);

        return $condition;
    }

    protected function cleanCache($group = null, $client_id = 0)
    {
        parent::cleanCache('com_tz_portfolio_plus', 0);
        parent::cleanCache('com_tz_portfolio_plus', 1);
    }

    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            $user = Factory::getUser();

            if(isset($record -> asset_id) && !empty($record -> asset_id)) {
                $state = $user->authorise('core.delete', $this->option . '.addon.' . (int)$record->id);
            }else{
                $state = $user->authorise('core.delete', $this->option . '.addon');
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
                $state = $user->authorise('core.edit.state', $this->option . '.addon.' . (int)$record->id);
            }else{
                $state = $user->authorise('core.edit.state', $this->option . '.addon');
            }
            return $state;
        }

        return parent::canEditState($record);
    }
    public function validate($form, $data, $group = null)
    {
        if($data && isset($data['rules'])){
            unset($data['rules']);
        }
        return parent::validate($form, $data, $group);
    }



    public function getFilterForm($data = array(), $loadData = true)
    {
        $form = null;

        // Try to locate the filter form automatically. Example: ContentModelArticles => "filter_articles"
        if (empty($this->filterFormName))
        {
            $classNameParts = explode('Model', get_called_class());

            if (count($classNameParts) == 2)
            {
                $this->filterFormName = 'filter_' . strtolower($classNameParts[1]);
            }
        }

        if (!empty($this->filterFormName))
        {
            // Get the form.
            $form = $this->loadForm($this->option . '.'.$this -> getName().'.filter', $this->filterFormName, array('control' => '', 'load_data' => $loadData));

            // Check the session for previously entered form data.
            $filters = Factory::getApplication()->getUserState($this -> option.'.'.$this -> getName().'.filter', new \stdClass);

            $data   = new stdClass();
            $data -> filter = $filters;

            $form -> bind($data);
        }

        return $form;
    }

    public function getItemsFromServer(){

        $data           = false;
        $value          = $this -> getState('list.start');
        $limitstart     = $value;
        $search         = $this -> getState('filter.search');
        $type           = $this -> getState('filter.type');
        $params         = $this -> getState('params');
        $filters        = $this -> getState('filters');
        $cacheFileName  = $this -> getState('cache.filename');
        $ordering       = $this -> getState('list.ordering');
        $formSubmited   = $this -> getState('list.form_submited');

        // Cache time is 1 day
        $cacheTime      = 24 * 60 * 60;
        $cacheFilters   = null;
        $hasCache       = true;
        $cacheFolder    = JPATH_CACHE.'/'.$this -> option;
        $cacheFile      = $cacheFolder.'/'. $cacheFileName .'.json';


        // Get data from cache
        if(File::exists($cacheFile) && (filemtime($cacheFile) > (time() - $cacheTime ))){
            $items  = file_get_contents($cacheFile);
            $items  = trim($items);
            if(!empty($items)){
                $data   = json_decode($items);
                if($data && isset($data -> filters) && $data -> filters){
                    $cacheFilters   = $data -> filters;
                }else{
                    $hasCache  = false;
                }
            }
        }

        if($cacheFilters && count((array) $cacheFilters) && $filters){
            foreach($cacheFilters as $k => $v){
                if(isset($filters[$k]) && $filters[$k] != $v){
                    $hasCache  = false;
                    $limitstart = 0;
                    break;
                }
            }
        }

        if($formSubmited){
            $hasCache   = false;
        }

        if($hasCache && $data && isset($data -> start) && $data -> start != $limitstart){
            $hasCache  = false;
        }

        if(!$data && $hasCache) {
            $hasCache = false;
        }

        $needUpdate = $this -> __get_extensions_installed();

        if(!empty($needUpdate)){
            $hasCache   = false;
        }

        if(!$hasCache) {

            $url    = $this -> getUrlFromServer();

            if(!$url){
                return false;
            }

            // Get data from server
            $edition = '';
            if (COM_TZ_PORTFOLIO_PLUS_EDITION == 'commercial' && $apiKey = $params->get('token_key')) {
                $edition = '&token_key=' . $apiKey;
            }

            $url .= ($limitstart ? '&start=' . $limitstart : '') . ($type ? '&type=' . urlencode($type) : '')
                . ($search ? '&search=' . urlencode($search) : '') . $edition;

            if($ordering){
                $url    .= '&order='.$ordering;
            }

            if(!empty($needUpdate)){
//                $needUpdate[]   = 43;
                $order_list = http_build_query(array('order_list'=>$needUpdate));
                $order_list = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $order_list);
                $url    .= '&'.$order_list;
            }

            $response = TZ_Portfolio_PlusHelper::getDataFromServer($url, 'post');

            if(!$response){
                return false;
            }

            $data   = json_decode($response -> body);

            if(!$data){
                return false;
            }

            $items  = $data -> items;
            unset($data -> items);

            $data -> start      = $limitstart;
            $data -> filters    = $filters;
            $data -> items      = $items;

            $_data   = json_encode($data);

            File::write($cacheFile, $_data);
        }

        if(!$data || ($data && !isset($data -> items) )){
            return false;
        }


        if($data -> items){
            foreach($data -> items as &$item){
                $item -> pProduce           = null;
                $item -> installedVersion   = null;

                $editionName    = 'pProduce';

                if($item -> pElement && isset($item -> pType)){
                    if($extension = $this -> getManifest_Cache($item -> pElement, $item -> pType)){
                        if(isset($extension -> edition) && $extension -> edition) {
                            $editionName = $extension->edition;
                        }
                        $item -> installedVersion   = $extension -> version;
                    }
                }

                if($pProduces = $item -> pProduces) {
                    if(isset($pProduces -> {$editionName}) && $pProduces -> {$editionName}) {
                        $item->pProduce = $pProduces->{$editionName};
                    }
                }
            }
        }

        $this -> setState('list.dataserver', true);

        $limit  = $data -> limit;

        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);

        $this -> setState('list.start', $limitstart);
        $this -> setState('list.limit', $limit);
        $this -> setState('list.total', $data -> total);

        return $data -> items;
    }

    protected function getManifest_Cache($element, $folder = null, $type = 'tz_portfolio_plus-plugin', $key = null){

        if(!$element){
            return false;
        }

        if(!$type){
            $type   = 'tz_portfolio_plus-plugin';
        }

        $option = array('element' => $element, 'type' => $type);

        if($folder){
            $option['folder']   = $folder;
        }

        $table  = $this -> getTable();

        if(!$table -> load($option)){
            return false;
        }


        if (empty($key))
        {
            $key = $table->getKeyName();
        }

        if(!$table -> $key){
            return false;
        }

        $manifestCache  = false;
        if(isset($table -> manifest_cache) && $table -> manifest_cache && is_string($table -> manifest_cache)){
            $manifestCache    = json_decode($table -> manifest_cache);
        }


        return $manifestCache;
    }

    public function getUrlFromServer($xmlTag = 'addonurl'){

        if(!$xmlTag){
            return false;
        }

        $url    = false;

        // Get update data
        $xmlPath    = COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/tz_portfolio_plus.xml';

        $xml        = simplexml_load_file($xmlPath, 'SimpleXMLElement', LIBXML_NOCDATA);
        if($updateServer = $xml -> updateservers){
            if(isset($updateServer -> server) && $updateServer -> server){
                foreach ($updateServer -> server as $server){
                    if($responseUpdate = TZ_Portfolio_PlusHelper::getDataFromServer((string) $server)){
                        $xmlUpdate  = simplexml_load_string( $responseUpdate -> body);

                        if($update = $xmlUpdate -> xpath('update['.((int) $server['pirority']).']')) {
                            $update = $update[0];
                            if(isset($update -> listupdate)) {
                                $listUpdate = $update -> listupdate;
                                if(isset($listUpdate -> {$xmlTag}) && $listUpdate -> {$xmlTag}){
                                    $url    = (string) $listUpdate -> {$xmlTag};
                                }
                            }
                        }
                        break;
                    }
                }
            }
        }
        return $url;
    }

    public function getStart()
    {
        $store = __METHOD__;

        // Try to load the data from internal storage.
        if (isset($this->cache[$store]))
        {
            return $this->cache[$store];
        }

        $start = $this->getState('list.start');

        if ($start > 0)
        {
            $limit = $this->getState('list.limit', 0);
            $total = $this -> getState('list.total', 0);

            if ($limit > 0 && $start > $total - $limit)
            {
                $start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
            }
        }

        // Add the total to the internal cache.
        $this->cache[$store] = $start;

        return $this->cache[$store];
    }

    public function getPaginationFromServer()
    {
        // Get a storage key.
        $store  = __METHOD__;

        // Try to load the data from internal storage.
        if (isset($this->cache[$store]))
        {
            return $this->cache[$store];
        }

        $limit = (int) $this->getState('list.limit');

        // Create the pagination object and add the object to the internal cache.
        $this->cache[$store] = new \JPagination($this -> getState('list.total'), $this->getStart(), $limit);

        return $this->cache[$store];
    }

    protected function _getPackageFromUrl($url)
    {
        // Capture PHP errors
        $track_errors = ini_get('track_errors');
        ini_set('track_errors', true);

        // Load installer plugins, and allow URL and headers modification
        $headers = array();
        \JPluginHelper::importPlugin('installer');
        Factory::getApplication() -> triggerEvent('onInstallerBeforePackageDownload', array(&$url, &$headers));

        $response   = TZ_Portfolio_PlusHelper::getDataFromServer($url);

        // Was the package downloaded?
        if (!$response)
        {
            JError::raiseWarning('', JText::sprintf('COM_INSTALLER_PACKAGE_DOWNLOAD_FAILED', $url));

            return false;
        }

        $target     = null;

        // Parse the Content-Disposition header to get the file name
        $contentDisposition = false;
        if(isset($response->headers['Content-Disposition'])){
            $contentDisposition = 'Content-Disposition';
        }elseif(isset($response -> headers['CONTENT-DISPOSITION'])){
            $contentDisposition = 'CONTENT-DISPOSITION';
        }if(isset($response -> headers['content-disposition'])){
            $contentDisposition = 'content-disposition';
        }

        if ($contentDisposition && ($content = $response->headers[$contentDisposition])) {
            if (is_array($content)) {
                $content = array_shift($content);
            }
            if (preg_match("/\s*filename\s?=\s?(.*)/", $content, $parts)) {
                $flds = explode(';', $parts[1]);
                $target = trim($flds[0], '"');
            }
        }

        if(!$target){
            return false;
        }

        $tmp_dest	= JPATH_ROOT . '/tmp/tz_portfolio_plus_install/' . $target;

        if(!File::exists(JPATH_ROOT . '/tmp/tz_portfolio_plus_install/index.html')){
            $html   = htmlspecialchars_decode('<!DOCTYPE html><title></title>');
            File::write(JPATH_ROOT . '/tmp/tz_portfolio_plus_install/index.html', $html);
        }

        $resbody   = $response -> body;

        // Write buffer to file
        File::write($tmp_dest, $resbody);

        // Restore error tracking to what it was before
        ini_set('track_errors', $track_errors);

        // Bump the max execution time because not using built in php zip libs are slow
        @set_time_limit(ini_get('max_execution_time'));

        // Unpack the downloaded package file
        $package = JInstallerHelper::unpack($tmp_dest, true);

        return $package;
    }

    protected function __get_extensions_installed(&$update = array(), $model_type = 'AddOns',
                                                  $model_prefix = 'TZ_Portfolio_PlusModel', &$limit_start = 0){
        $limit  = 9;
        $total  = 0;
        $items  = false;

        if(strtolower($model_type) == 'extensions'){
            // Get update data
            $xmlPath    = COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/tz_portfolio_plus.xml';

            $xml        = simplexml_load_file($xmlPath, 'SimpleXMLElement', LIBXML_NOCDATA);
            $modules_core   = $xml -> xpath('modules/module/@module');

            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__extensions')
                ->where('state = 0')
                ->where('type='.$db -> quote('module'))
                -> where('element LIKE '.$db -> quote('%mod_tz%'));
            if(!empty($modules_core)){
                $query -> where('element NOT IN('.$db -> quote(implode($db -> quote(','),$modules_core), false).')');
            }
            $db -> setQuery($query);

            $items  = $db -> loadObjectList();

            $query -> clear('select');
            $query -> select('COUNT(extension_id)');
            $db -> setQuery($query);
            $total  = $db -> loadResult();
        }else {
            $model = JModelLegacy::getInstance($model_type, $model_prefix, array('ignore_request' => true));

            $model->setState('filter.status', 3);
            $model->setState('list.start', $limit_start);
            $model->setState('list.limit', $limit);
            $items = $model -> getItems();

            $total  = $model -> getTotal();
        }

        if(!empty($items)){

            $url    = $this -> getUrlFromServer();

            if(!$url){
                return false;
            }

            $params         = $this -> getState('params');

            // Get data from server
            $edition = '';
            if (COM_TZ_PORTFOLIO_PLUS_EDITION == 'commercial' && $apiKey = $params->get('token_key')) {
                $edition = '&token_key=' . $apiKey;
            }
            $url .= $edition;

            $url    = str_replace('format=list', 'format=item', $url);

            foreach($items as $item){

                $_url   = $url;
                if(isset($item -> folder) && !empty($item -> folder)) {
                    $_url .= '&type=' . $item->folder;
                }
                $_url  .= '&element='.$item -> element;
                $response = TZ_Portfolio_PlusHelper::getDataFromServer($_url);

                if(!$response){
                    continue;
                }

                $data   = json_decode($response -> body);

                if(!$data){
                    continue;
                }

                $sitem  = $data -> item;

                $pProduct   = '';
                if(isset($sitem -> pProduces) && !empty($sitem -> pProduces) && isset($sitem -> pProduces -> pProduce)) {
                    $pProduct = $sitem -> pProduces -> pProduce;
                }

                $version    = '';
                if(isset($item -> version) && !empty($item -> version)){
                    $version    = $item -> version;
                }else{
                    if (strlen($item -> manifest_cache))
                    {
                        $manifest = json_decode($item -> manifest_cache);
                        if(!empty($manifest) && isset($manifest -> version) && !empty($manifest -> version)) {
                            $version = $manifest->version;
                        }
                    }
                }

                // Extension has update
                if(!empty($pProduct) && version_compare( $pProduct -> pVersion, $version, '>')){
                    $update[]   = $sitem -> id;
                }

            }

            $limit_start    += $limit;
            if($limit_start < $total){
                $this -> __get_extensions_installed($update, $model_type, $model_prefix, $limit_start);
            }

            return $update;
        }

        return array();

    }

}