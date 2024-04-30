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
use Joomla\CMS\Filesystem\File;

jimport('joomla.filesytem.file');

class TZ_Portfolio_PlusHelperAddon_Datas{
    public static function getRootURL($addon_id,$root_view = 'addon_datas'){
        if($addon_id){
            return 'index.php?option=com_tz_portfolio_plus&view='.$root_view.'&addon_id='.$addon_id;
        }
        return false;
    }

    public static function getActions($id, $section = 'addon',$parent_section = '')
    {
        $component  = 'com_tz_portfolio_plus';
        $user	    = Factory::getUser();
        $result	    = new JObject;

        $path       = JPATH_ADMINISTRATOR . '/components/com_tz_portfolio_plus/access.xml';

        if($addon  = TZ_Portfolio_PlusPluginHelper::getPluginById($id)){
            $_path   = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$addon -> type.'/'. $addon -> name.'/access.xml';
            if(File::exists($_path)){
                $path   = $_path;
            }
        }

        $assetName = $component;

        if ($section && $id)
        {
            $assetName = $component . '.' . $section . '.' . (int) $id;

            $tblAsset   = JTable::getInstance('Asset', 'JTable');
            if(!$tblAsset -> loadByName($assetName)){
                $assetName  = $component . '.' . $parent_section;
            }
        }elseif (empty($id))
        {
            $assetName = $component . '.' . $section;
        }

        $actions = JAccess::getActionsFromFile($path, "/access/section[@name='addon']/");

        foreach ($actions as $action)
        {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }

        return $result;
    }
}