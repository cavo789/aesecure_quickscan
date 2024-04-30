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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;

jimport('joomla.filesytem.file');
jimport('joomla.filesytem.folder');
tzportfolioplusimport('fields.extrafield');

abstract class JHtmlFields
{
    public static function options($arr, $optKey = 'value', $optText = 'text', $selected = null){
        if(!$arr) {
            $arr = self::_getFieldTypes();
        }
        return JHtml::_('select.options', $arr, $optKey, $optText, $selected);
    }

    protected static function _getFieldTypes(){
        $data       = array();
        $core_path  = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.'extrafields';
        if(Folder::exists($core_path)){
            if($core_folders = Folder::folders($core_path)){
                $lang   = Factory::getApplication() -> getLanguage();

                foreach($core_folders as $i => $folder){

                    $core_f_xml_path    = $core_path.DIRECTORY_SEPARATOR.$folder
                        .DIRECTORY_SEPARATOR.$folder.'.xml';
                    if(File::exists($core_f_xml_path)){
                        $core_class         = 'TZ_Portfolio_PlusExtraField'.ucfirst($folder);
                        if(!class_exists($core_class)){
                            JLoader::import('com_tz_portfolio_plus.addons.extrafields.'.$folder.'.'.$folder,
                                JPATH_SITE.DIRECTORY_SEPARATOR.'components');
                        }
                        $core_class         = new $core_class($folder);

                        $data[$i]           = new stdClass();
                        $data[$i] -> value  = $folder;
                        $core_class -> loadLanguage($folder);
                        $key_lang           = 'PLG_EXTRAFIELDS_'.strtoupper($folder).'_TITLE';
                        if($lang ->hasKey($key_lang)) {
                            $data[$i]->text = JText::_($key_lang);
                        }else{
                            $data[$i]->text = (string)$folder;
                        }
                    }
                }
            }
        }
        return $data;
    }
}
 
