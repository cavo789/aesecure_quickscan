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
use Joomla\Filesystem\Folder;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

jimport('joomla.filesytem.folder');
jimport('joomla.application.component.modeladmin');
JLoader::import('addon', COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR.'models');

class TZ_Portfolio_PlusModelExtension extends TZ_Portfolio_PlusModelAddon
{
    protected $type         = 'module';
    protected $folder       = 'modules';

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

        $this -> accept_types   = array('module');
    }

    protected function populateState(){
        parent::populateState();

        $this -> setState($this -> getName().'.id',Factory::getApplication() -> input -> getInt('id'));

//        $this -> setState('cache.filename', $this -> getName().'_list');
    }

    public function getTable($type = 'Extension', $prefix = 'JTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
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

        $installer  = JInstaller::getInstance();
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

            if(!$installer -> install($package['dir'])){
                // There was an error installing the package.
                $msg = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INSTALL_ERROR', $input -> getCmd('view'));
                $result = false;
                $this -> setError($msg);
            }

            // This event allows a custom a post-flight:
            $app->triggerEvent('onInstallerAfterInstaller', array($this, &$package, $installer, &$result, &$msg));
        }

        JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

        return $result;
    }

    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            $user = Factory::getUser();

            if(isset($record -> asset_id) && !empty($record -> asset_id)) {
                $state = $user->authorise('core.delete', $this->option . '.template.' . (int)$record->id);
            }else{
                $state = $user->authorise('core.delete', $this->option . '.template');
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
                $state = $user->authorise('core.edit.state', $this->option . '.template.' . (int)$record->id);
            }else{
                $state = $user->authorise('core.edit.state', $this->option . '.template');
            }
            return $state;
        }

        return parent::canEditState($record);
    }

    public function getUrlFromServer($xmlTag = 'extensionurl'){
        return parent::getUrlFromServer($xmlTag);
    }

    protected function getManifest_Cache($element, $folder = null, $type = 'module', $key = null){
        return parent::getManifest_Cache($element, $folder, $type, $key);
    }

    protected function __get_extensions_installed(&$update = array(), $model_type = 'Extensions',
                                                  $model_prefix = 'TZ_Portfolio_PlusModel', &$limit_start = 0){
        return parent::__get_extensions_installed($update, $model_type, $model_prefix, $limit_start);
    }
}