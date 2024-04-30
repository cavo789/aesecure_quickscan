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

use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class TZ_Portfolio_PlusHelperTemplates{

    protected static $cache = array();

    public static function getTemplateOptions()
    {
        // Build the filter options.
        $db     = TZ_Portfolio_PlusDatabase::getDbo();
        $query  = $db->getQuery(true);

        $query->select('element as value, name as text, id as e_id')
            ->from('#__tz_portfolio_plus_extensions')
            ->where('type = ' . $db->quote('tz_portfolio_plus-template'))
            ->where('published = 1')
            ->order('name');
        $db->setQuery($query);
        $options = $db->loadObjectList();

        return $options;
    }

    public static function getStyles($options = array()){
        $storeId    = __METHOD__;
        $storeId   .= ':'.serialize($options);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $db     = TZ_Portfolio_PlusDatabase::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('e.*');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_extensions').' AS e');

        $query -> where('type = '.$db -> quote('tz_portfolio_plus-template'));

        if(count($options)){
            if(isset($options['published'])){
                if(is_array($options['published'])) {
                    $query->where('published IN('.implode($options['published']).')');
                }else{
                    $query -> where('published='.$options['published']);
                }
            }else{
                $query -> where('(published = 0 OR published = 1)');
            }
            if(isset($options['protected'])){
                if(is_array($options['protected'])) {
                    $query->where('protected IN('.implode($options['protected']).')');
                }else{
                    $query -> where('protected='.$options['protected']);
                }
            }
            if(isset($options['folder']) && $options['folder']){
                $query -> where('folder='.$db -> quote($options['folder']));
            }
        }
        $db -> setQuery($query);
        if($data = $db -> loadObjectList()){
            self::$cache[$storeId]  = $data;
            return $data;
        }
        return false;
    }

    public static function getTotal($options = array()){
        $db     = TZ_Portfolio_PlusDatabase::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('COUNT(e.id)');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_extensions').' AS e');

        $query -> where('type = '.$db -> quote('tz_portfolio_plus-template'));

        if(count($options)){
            if(isset($options['published'])){
                if(is_array($options['published'])) {
                    $query->where('e.published IN('.implode($options['published']).')');
                }else{
                    $query -> where('e.published='.$options['published']);
                }
            }else{
                $query -> where('(e.published = 0 OR e.published = 1)');
            }
            if(isset($options['protected'])){
                if(is_array($options['protected'])) {
                    $query->where('e.protected IN('.implode($options['protected']).')');
                }else{
                    $query -> where('e.protected='.$options['protected']);
                }
            }
            if(isset($options['folder']) && $options['folder']){
                $query -> where('e.folder='.$db -> quote($options['folder']));
            }
        }
        $db -> setQuery($query);
        if($data = $db -> loadResult()){
            return $data;
        }

        return 0;
    }
}