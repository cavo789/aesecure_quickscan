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

class TZ_Portfolio_PlusFrontHelperTags{
    protected static $cache = array();

    public static function getTagById($tagId){
        if(!$tagId){
            return false;
        }

        $storeId = md5(__METHOD__ . '::'.$tagId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $db     = TZ_Portfolio_PlusDatabase::getDbo();
        $query  = $db -> getQuery(true);

        $query -> select('*');
        $query -> from('#__tz_portfolio_plus_tags');
        $query -> where('id = '.(int) $tagId);
        $db -> setQuery($query);
        if($data = $db -> loadObject()){
            self::$cache[$storeId] = $data;
            return $data;
        }
        return false;
    }

    public static function getTagsFilterByArticleId($contentId, $filterAlias = null){
        if($contentId) {
            if(is_array($contentId)) {
                $storeId = md5(__METHOD__ . '::'.implode(',', $contentId));
            }else{
                $storeId = md5(__METHOD__ . '::'.$contentId);
            }

            if(!isset(self::$cache[$storeId])){
                $db     = TZ_Portfolio_PlusDatabase::getDbo();
                $query  = $db -> getQuery(true);
                $query -> select('t.*, c.id AS contentid');
                $query -> from('#__tz_portfolio_plus_tags AS t');
                $query -> join('INNER', '#__tz_portfolio_plus_tag_content_map AS m ON m.tagsid = t.id');
                $query -> join('INNER', '#__tz_portfolio_plus_content AS c ON c.id = m.contentid');
                $query -> where('t.published = 1');

                if(is_array($contentId)) {
                    if(count($contentId)) {
                        $query->where('c.id IN(' . implode(',', $contentId) . ')');
                    }
                }else{
                    $query -> where('c.id = '.$contentId);
                }

                if($filterAlias){
                    if(is_array($filterAlias)) {
                        if(count($filterAlias)) {
                            foreach($filterAlias as $alias){
                                $query -> where('t.alias <> '.$db -> quote($alias));
                            }
                        }
                    }else{
                        $query -> where('t.alias <> '.$db -> quote($filterAlias));
                    }
                }

                $query -> group('t.id');

                $db -> setQuery($query);
                if($tags = $db -> loadObjectList()){
                    $articleTags    = array();
                    foreach($tags as &$item){
                        if(isset($item -> params) && !empty($item -> params)){
                            $item -> params  = new JRegistry($item -> params);
                        }

                        if(!isset($articleTags[$item -> contentid]) ||
                            (isset($articleTags[$item -> contentid])
                                && isset($articleTags[$item -> contentid] -> id)
                                && $articleTags[$item -> contentid] -> id != $item -> id)) {
                            $articleTags[$item -> contentid][] = $item;
                        }
                    }

                    self::$cache[$storeId]  = $tags;
                    return $tags;
                }
                self::$cache[$storeId]  = false;
            }

            return self::$cache[$storeId];
        }

        return false;
    }

    public static function getTagsByCategoryId($catid, $options = array()){

        if($catid) {
            if (is_array($catid)) {
                $catid      = array_unique($catid);
                $catid      = array_filter($catid);
                $storeId    = md5(__METHOD__ . '::' . implode(',', $catid));
            } else {
                $storeId    = md5(__METHOD__ . '::' . $catid);
            }
        }else{
            $storeId    = md5(__METHOD__);
        }
        if (!isset(self::$cache[$storeId])) {
            $db     = TZ_Portfolio_PlusDatabase::getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('t.*');
            $query -> from('#__tz_portfolio_plus_tags AS t');
            $query -> join('INNER', '#__tz_portfolio_plus_tag_content_map AS m ON m.tagsid = t.id');
            $query -> join('INNER', '#__tz_portfolio_plus_content AS c ON c.id = m.contentid');
            $query -> where('t.published = 1');

            if($catid){
                $subquery   = $db -> getQuery(true);
                $subquery -> select(' c.id');
                $subquery -> from('#__tz_portfolio_plus_content AS c');
                $subquery -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = c.id');
                $subquery -> join('INNER', '#__tz_portfolio_plus_categories AS cc ON cc.id = m.catid');
                if (is_array($catid)) {
                    $subquery -> where('cc.id IN('. implode(',', $catid).')');
                } else {
                    $subquery -> where('cc.id = '. $catid);
                }
                $subquery -> group('c.id');
                $query -> where('c.id IN('. $subquery .')');
            }

            $query -> group('t.id');

            $db -> setQuery($query);
            if($tags = $db -> loadObjectList()){
                self::$cache[$storeId]  = $tags;
                return $tags;
            }
            self::$cache[$storeId]  = false;
        }
        return self::$cache[$storeId];
    }

    public static function getTagsByArticleId($contentId, $options = array('orderby' => null,
        'condition' => null,
        'reverse_contentid' => true,
        'menuActive' => null)){
        if($contentId) {
            if(is_array($contentId)) {
                $storeId = md5(__METHOD__ . '::'.implode(',', $contentId));
            }else{
                $storeId = md5(__METHOD__ . '::'.$contentId);
            }

            if(!isset(self::$cache[$storeId])){
                $db     = TZ_Portfolio_PlusDatabase::getDbo();
                $query  = $db -> getQuery(true);
                $query -> select('t.*, c.id AS contentid');
                $query -> select('CASE WHEN CHAR_LENGTH(t.alias) THEN CONCAT_WS(":", t.id, t.alias) ELSE t.id END as slug');
                $query -> from('#__tz_portfolio_plus_tags AS t');
                $query -> join('INNER', '#__tz_portfolio_plus_tag_content_map AS m ON m.tagsid = t.id');
                $query -> join('INNER', '#__tz_portfolio_plus_content AS c ON c.id = m.contentid');
                $query -> where('t.published = 1');

                if(is_array($contentId)) {
                    $query -> where('c.id IN('.implode(',', $contentId).')');
                }else{
                    $query -> where('c.id = '.$contentId);
                }

                if(count($options)){
                    if(isset($options['condition']) && $options['condition']){
                        $query -> where($options['condition']);
                    }
                    if(isset($options['orderby']) && $options['orderby']){
                        $query -> order($options['orderby']);
                    }
                }

                $db -> setQuery($query);
                if($data = $db -> loadObjectList()){
                    $tags       = array();
                    $tagIds     = array();
                    $menuActive = null;
                    if(isset($options['menuActive']) && !empty($options['menuActive'])){
                        $menuActive = $options['menuActive'];
                    }

                    foreach($data as &$tag){
                        // Create Tag Link
                        $tag -> link    = JRoute::_(TZ_Portfolio_PlusHelperRoute::getTagRoute($tag -> slug, 0, $menuActive));

                        // Create article's id is array's key with value are tags
                        if(count($options) && isset($options['reverse_contentid']) && $options['reverse_contentid']){
                            if(!isset($tags[$tag -> contentid])){
                                $tags[$tag -> contentid]    = array();
                            }
                            if(!isset($tagIds[$tag -> contentid])){
                                $tagIds[$tag -> contentid]    = array();
                            }
                            if(!in_array($tag -> id, $tagIds[$tag -> contentid])) {
                                $tags[$tag->contentid][]        = $tag;
                                $tagIds[$tag -> contentid][]    = $tag -> id;
                            }
                        }
                    }

                    if(!count($tags)){
                        $tags   = $data;
                    }
                    self::$cache[$storeId]  = $tags;
                    return $tags;
                }
                self::$cache[$storeId]  = false;
            }

            return self::$cache[$storeId];
        }

        return false;
    }
}