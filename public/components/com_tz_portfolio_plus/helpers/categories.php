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

use Joomla\Registry\Registry;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class TZ_Portfolio_PlusFrontHelperCategories{
    protected static $cache = array();

    public static function getMainCategoriesByArticleId($articleId, $options = array()){
        if($articleId) {
            $_options   = '';
            $config     = array('condition' => null, 'reverse_contentid' => false);
            if(count($options)) {
                $config     = array_merge($config,$options);
                $_options   = serialize($config);
            }
            if(is_array($articleId)) {
                $storeId = md5(__METHOD__ . '::'.serialize($articleId).'::'.$_options);
            }else{
                $storeId = md5(__METHOD__ . '::'.$articleId.'::'.$_options);
            }

            if(!isset(self::$cache[$storeId])){
                $db     =  TZ_Portfolio_PlusDatabase::getDbo();
                $query  =  $db -> getQuery(true);

                $query  -> select('c.id, c.groupid, c.images, c.template_id, c.asset_id, c.parent_id');
                $query  -> select('c.lft, c.rgt, c.level, c.path, c.extension, c.title, c.alias');
                $query  -> select('c.note, c.description, c.published, c.checked_out, c.checked_out_time');
                $query  -> select('c.access, c.params, c.metadesc, c.metakey, c.metadata');
                $query  -> select('c.created_user_id, c.created_time, c.modified_user_id');
                $query  -> select('c.modified_time, c.hits, c.language, c.version');
                $query  -> select('m.contentid AS contentid');
                $query  -> from('#__tz_portfolio_plus_categories AS c');
                $query  -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.catid = c.id AND m.main = 1');
                $query  -> join('INNER', '#__tz_portfolio_plus_content AS cc ON cc.id = m.contentid');
                $query -> group('c.id');


                if(is_array($articleId)) {
                    $query -> where('cc.id IN('.implode(',', $articleId) .')');
                }else{
                    $query -> where('cc.id = '.$articleId);
                }

                if(count($config)){
                    if(isset($config['condition']) && $config['condition']){
                        $query -> where($config['condition']);
                    }
                    if(isset($config['orderby']) && $config['orderby']){
                        $query->order($config['orderby']);
                    }
                }

                $db -> setQuery($query);
                if($data = $db -> loadObjectList()){
                    $categories     = array();
                    $categoryIds    = array();
                    foreach($data as $i => &$item){
                        $item -> order	= $i;
                        $item -> link	= JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($item -> id));

                        // Create article's id is array's key with value are tags
                        if(count($config) && (isset($config['reverse_contentid']) && $config['reverse_contentid'])){
                            if(!isset($categories[$item -> contentid])){
                                $categories[$item -> contentid]    = array();
                            }
                            if(!isset($categoryIds[$item -> contentid])){
                                $categoryIds[$item -> contentid]    = array();
                            }
                            if(!in_array($item -> id, $categoryIds[$item -> contentid])) {
                                $categories[$item->contentid][] = $item;
                                $categoryIds[$item -> contentid][]  = $item -> id;
                            }
                        }
                    }
                    if(!count($categories)){
                        $categories   = $data;
                    }

                    self::$cache[$storeId]  = $categories;
                    return $categories;
                }

                self::$cache[$storeId]  = false;
            }

            return self::$cache[$storeId];
        }
        return false;
    }

    public static function getCategoriesByArticleId($articleId, $options =
                            array('main' => null, 'condition' => null, 'reverse_contentid' => true)){

        if($articleId) {
            $_options   = '';
            $config     = array('main' => null, 'condition' => null, 'reverse_contentid' => true);
            if(count($options)) {
                $config     = array_merge($config,$options);
                $_options   = serialize($config);
            }
            if(is_array($articleId)) {
                $storeId = md5(__METHOD__ . '::'.serialize($articleId).'::'.$_options);
            }else{
                $storeId = md5(__METHOD__ . '::'.$articleId.'::'.$_options);
            }

            if(!isset(self::$cache[$storeId])){
                $db     =  TZ_Portfolio_PlusDatabase::getDbo();
                $query  =  $db -> getQuery(true);

                $query  -> select('c.*, m.contentid AS contentid');
                $query  -> from('#__tz_portfolio_plus_categories AS c');

                $query -> select('m.main');
                $query  -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.catid = c.id');
                $query  -> join('INNER', '#__tz_portfolio_plus_content AS cc ON cc.id = m.contentid');
                $query  -> where('extension = '.$db -> quote('com_tz_portfolio_plus'));

                if(is_array($articleId)) {
                    $query -> where('cc.id IN('.implode(',', $articleId) .')');
                }else{
                    $query -> where('cc.id = '.$articleId);
                }

                if(count($config)){
                    if ($config['main'] === true) {
                        $query->where('m.main = 1');
                    } elseif ($config['main'] === false) {
                        $query->where('m.main = 0');
                    }
                    if(isset($config['condition']) && $config['condition']){
                        $query -> where($config['condition']);
                    }
                    if(isset($config['orderby']) && $config['orderby']){
                        $query->order($config['orderby']);
                    }
                    if(isset($config['groupby']) && $config['groupby']){
                        $query->group($config['groupby']);
                    }
                }

                $db -> setQuery($query);
                if($data = $db -> loadObjectList()){
                    $categories     = array();
                    $categoryIds    = array();
                    foreach($data as $i => &$item){
                        $item -> order	= $i;
                        $item -> link	= JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($item -> id));

                        // Create article's id is array's key with value are tags
                        if(count($config) && (!isset($config['reverse_contentid'])
                                || (isset($config['reverse_contentid']) && $config['reverse_contentid']))){
                            if(!isset($categories[$item -> contentid])){
                                $categories[$item -> contentid]    = array();
                            }
                            if(!isset($categoryIds[$item -> contentid])){
                                $categoryIds[$item -> contentid]    = array();
                            }
                            if(!in_array($item -> id, $categoryIds[$item -> contentid])) {
                                if(count($config) && $config['main'] === true) {
                                    $categories[$item->contentid]     = $item;
                                }else {
                                    $categories[$item->contentid][] = $item;
                                }
                                $categoryIds[$item -> contentid][]  = $item -> id;
                            }
                        }
                    }

                    if(!count($categories)){
                        $categories   = $data;
                        if(count($config) && $config['main'] === true) {
                            $categories = array_shift($data);
                        }
                    }

                    self::$cache[$storeId]  = $categories;

                    return $categories;
                }
                self::$cache[$storeId]  = false;
            }
            return self::$cache[$storeId];
        }
        return false;
    }

    public static function getCategoriesById($id, $options = array('second_by_article' => false, 'orderby' => null)){
        if($id){
            if(is_array($id)){
                $storeId    = md5(__METHOD__ . '::' . serialize($id));
            }else {
                $storeId    = md5(__METHOD__ . '::' . $id);
            }
            if(!isset(self::$cache[$storeId])){
                $db         = TZ_Portfolio_PlusDatabase::getDbo();
                $query      = $db -> getQuery(true);
                $subquery   = $db -> getQuery(true);

                $query -> select('c.*');
                $query -> from('#__tz_portfolio_plus_categories AS c');

                $query -> where('c.published = 1');
                $query -> where('extension = '.$db -> quote('com_tz_portfolio_plus'));
                $query->join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.catid = c.id');
                $query->join('INNER', '#__tz_portfolio_plus_content AS cc ON cc.id = m.contentid');

                if(count($options) && isset($options['second_by_article']) && $options['second_by_article']) {
                    $query -> where('m.main = 0 OR m.main = 1');

                    $subquery->select('DISTINCT c2.id');
                    $subquery->from('#__tz_portfolio_plus_content AS c2');
                    $subquery->join('INNER', '#__tz_portfolio_plus_content_category_map AS m2 ON m2.contentid = c2.id');
                    $subquery->join('INNER', '#__tz_portfolio_plus_categories AS cc2 ON cc2.id = m2.catid');

                    if (is_array($id)) {
                        $subquery->where('cc2.id IN(' . implode(',', $id) . ')');
                    } else {
                        $subquery->where('cc2.id = ' . $id);
                    }

                    $query->where('cc.id IN(' . $subquery . ')');

                    $query->group('c.id');
                }else{
                    $query -> where('m.main = 1');
                    if (is_array($id)) {
                        $query -> where('c.id IN(' . implode(',', $id) . ')');
                    } else {
                        $query -> where('c.id = ' . $id);
                    }
                }

                if(count($options) && isset($options['orderby']) && $options['orderby']){
                    $query -> order($options['orderby']);
                }

                $query->group('c.id');

                $db -> setQuery($query);

                $categories = null;

                if(is_array($id)){
                    $categories = $db -> loadObjectList();
                    foreach($categories as $i => &$category){
                        $category -> order  = $i;
                        $category -> link   = JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($category -> id));
                    }
                }else{
                    if($categories = $db -> loadObject()) {
                        $categories -> order    = 0;
                        $categories->link = JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($categories->id));
                    }
                }
                if($categories){
                    self::$cache[$storeId]  = $categories;
                    return $categories;
                }
                self::$cache[$storeId]  = false;
            }
            return self::$cache[$storeId];

        }
        return false;
    }

    public static function getSubCategoriesByParentId($parentid){
        if($parentid){
            if(is_array($parentid)){
                $storeId    = md5(__METHOD__ . '::' . serialize($parentid));
            }else {
                $storeId    = md5(__METHOD__ . '::' . $parentid);
            }

            if(!isset(self::$cache[$storeId])){
                $db         = TZ_Portfolio_PlusDatabase::getDbo();
                $query      = $db -> getQuery(true);

                $query -> select('c.*');
                $query -> from('#__tz_portfolio_plus_categories AS c, #__tz_portfolio_plus_categories AS parent');
                $query -> where('c.lft BETWEEN parent.lft AND parent.rgt');
                if (is_array($parentid)) {
                    $query->where('parent.id IN(' . implode(',', $parentid) . ')');
                } else {
                    $query->where('parent.id = ' . $parentid);
                }
                $query -> where('c.published = 1');
                $query -> order('c.lft ASC');

                $db -> setQuery($query);

                $categories = null;

                $categories = $db -> loadObjectList();
                foreach($categories as &$category){
                    $category -> link   = JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($category -> id));
                }

                if($categories){
                    self::$cache[$storeId]  = $categories;
                    return $categories;
                }
                self::$cache[$storeId]  = false;
            }
            return self::$cache[$storeId];

        }
        return false;
    }

    public static function getAllCategories($options = array('second_by_article' => false, 'orderby' => null)){
        $storeId    = __METHOD__;
        $storeId   .= ':'.serialize($options);
        $storeId    = md5($storeId);

        if(!isset(self::$cache[$storeId])){
            $db     =  TZ_Portfolio_PlusDatabase::getDbo();
            $query  =  $db -> getQuery(true);
            $query -> select('c.*');
            $query -> from('#__tz_portfolio_plus_categories AS c');
            $query -> where('c.published = 1');
            $query -> where('extension = '.$db -> quote('com_tz_portfolio_plus'));
            $query -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.catid = c.id');
            $query -> join('INNER', '#__tz_portfolio_plus_content AS cc ON cc.id = m.contentid');
            if(count($options)){
                if(isset($options['second_by_article']) && $options['second_by_article']){
                    $query -> where('m.main = 0 OR m.main = 1');
                }else{
                    $query -> where('m.main = 1');
                }
                if(isset($options['filter.parent']) && $options['filter.parent']){
                    $query->join('LEFT', $db->quoteName('#__tz_portfolio_plus_categories') . ' AS p ON p.id = ' . (int) $options['filter.parent'])
                        ->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');
                }
                if(isset($options['orderby']) && $options['orderby']){
                    $query -> order($options['orderby']);
                }

                $query -> group('id');
            }
            $db -> setQuery($query);
            if($categories = $db -> loadObjectList()){
                foreach($categories as $i => $item){
                    $item -> order	= $i;
                }
                self::$cache[$storeId]  = $categories;
                return $categories;
            }
            return self::$cache[$storeId];
        }
        return false;
    }
}