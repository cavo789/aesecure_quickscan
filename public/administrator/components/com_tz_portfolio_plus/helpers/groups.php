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
use Joomla\Utilities\ArrayHelper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class TZ_Portfolio_PlusHelperGroups{

    protected static $cache	= array();
    
    public static function getGroups($option = null){
        $storeId    = __METHOD__;
        if($option){
            if(is_array($option)) {
                $storeId .= '::'.implode(',', $option);
            }else{
                $storeId    .= '::'.$option;
            }
        }

        if(!isset(self::$cache[$storeId])){

            $user       = Factory::getUser();
            $viewlevels = ArrayHelper::toInteger($user->getAuthorisedViewLevels());
            $db         = TZ_Portfolio_PlusDatabase::getDbo();
            $query      = $db -> getQuery(true);

            $query -> select('*');
            $query -> from('#__tz_portfolio_plus_fieldgroups');
            $query -> where('access IN (' . implode(',', $viewlevels) . ')');
            if($option) {
                if(isset($option['filter.published'])) {
                    if($option['filter.published']) {
                        $query->where('published = 1');
                    }else{
                        $query->where('published = 0');
                    }
                }
            }
            $db -> setQuery($query);
            if($data = $db -> loadObjectList()){
                self::$cache[$storeId]  = $data;
                return $data;
            }
            self::$cache[$storeId]  = false;
        }
        return self::$cache[$storeId];
    }
}