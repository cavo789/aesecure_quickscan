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

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

JLoader::register('TZ_Portfolio_PlusPlugin',JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'
                    .DIRECTORY_SEPARATOR.'com_tz_portfolio_plus'.DIRECTORY_SEPARATOR.'libraries'
                    .DIRECTORY_SEPARATOR.'plugin'.DIRECTORY_SEPARATOR.'plugin.php');

class PlgSystemTZ_Portfolio_Plus extends JPlugin {

    public function __construct(&$subject, $config = array())
    {
        JLoader::import('com_tz_portfolio_plus.includes.framework',JPATH_ADMINISTRATOR.'/components');

        JLoader::import('com_tz_portfolio_plus.libraries.plugin.helper', JPATH_ADMINISTRATOR.'/components');

        parent::__construct($subject,$config);
    }

    public function onAfterRoute(){
        $app    = Factory::getApplication();
        $option = $app -> input -> get('option');
        $task   = $app -> input -> get('task');
        if(class_exists('TZ_Portfolio_PlusPluginHelper') && $this -> _tppAllowImport()) {
            if(Folder::exists(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH)){
                $plgGroups  = Folder::folders(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH);
                if(count($plgGroups)){

                    foreach($plgGroups as $group){
                        if($group != 'extrafields') {
                            if($app ->isClient('administrator')  || ($app ->isClient('site')
                                    && ($group != 'user' ||($group == 'user'
                                            && $option == 'com_users' && $task != 'user.login'
                                            && $task != 'user.logout')))) {
                                TZ_Portfolio_PlusPluginHelper::importPlugin($group);
                            }
                        }
                    }
                }
            }
        }

    }

    public function onContentPrepareForm($form, $data){
        if(version_compare(JVERSION, '3.10', '<')) {
            $form_name = $form->getName();
            list($options, $view) = explode('.', $form_name);
            $is_my_module  = false;
            if($form_name == 'com_modules.module'){
                $module     	= (!empty($data) && isset($data -> module) && !empty($data -> module))?$data -> module:false;
                $is_my_module  	= ($module && preg_match('/^mod_tz_portfolio_plus/i', $module))?true:$is_my_module;
            }

            if ($options == 'com_tz_portfolio_plus' || $is_my_module) {
                $fieldsets   = $form -> getFieldsets();
                if($fieldsets && count($fieldsets)){
                    foreach($fieldsets as $fsname => $fieldset){
                        $fields = $form -> getFieldset($fsname);
                        if($fields && count($fields)){
                            foreach($fields as &$field){
                                $f_type = strtolower($field -> __get('type'));
                                if($f_type != 'radio'){
                                    continue;
                                }
                                if($field -> __get('layout') == 'joomla.form.field.radio.switcher') {
                                    $form->setFieldAttribute($field->__get('fieldname'), 'layout', '', $field->__get('group'));
                                }

                            }
                        }
                    }
                }
            }
        }
    }

    protected function _tppAllowImport(){

        $app    = JFactory::getApplication();
        $option = $app -> input -> get('option');
        $task   = $app -> input -> get('task');
        $view   = $app -> input -> get('view');

        if($app -> isClient('administrator')){
            $optionAllows   = array(
                'com_config',
                'com_login',
                'com_checkin',
                'com_cache',
                'com_admin',
                'com_installer',
                'com_plugins'
            );
            if(!$option || ($option && in_array($option, $optionAllows))){
                return false;
            }
            elseif($option == 'com_menus' && ($view == 'menus' || $view == 'items')){
                return false;
            }
            elseif($option == 'com_users' && (!in_array($view, array('user')) && !$task)){
                return false;
            }
        }

        return true;
    }
}
?>