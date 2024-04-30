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
defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

JLoader::import('com_tz_portfolio_plus.helpers.route', JPATH_SITE . '/components');
JLoader::import('com_tz_portfolio_plus.helpers.tags', JPATH_SITE . '/components');
JLoader::import('com_tz_portfolio_plus.helpers.categories', JPATH_SITE . '/components');
JLoader::import('com_tz_portfolio_plus.libraries.plugin.helper', JPATH_ADMINISTRATOR.'/components');
JLoader::import('com_tz_portfolio_plus.helpers.query', JPATH_SITE . '/components');

class modTZ_Portfolio_PlusPortfolioHelper
{
    protected static $cache;

    public static function getList(&$params, $module = null)
    {
        $storeId    = __METHOD__;
        $storeId   .= ':'.serialize($params);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        // Get the dbo
        $app    = JFactory::getApplication();
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);

        $query->select('c.*, c.id as content_id, m.catid AS catid, u.name as user_name, u.id as user_id');
        $query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
        $query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
        $query->select('CASE WHEN CHAR_LENGTH(c.fulltext) THEN c.fulltext ELSE null END as readmore');

        $query->from('#__tz_portfolio_plus_content AS c');

        $query->join('INNER', $db->quoteName('#__tz_portfolio_plus_content_category_map') . ' AS m ON m.contentid=c.id');
        $query->join('LEFT', $db->quoteName('#__tz_portfolio_plus_categories') . ' AS cc ON cc.id=m.catid');
        $query->join('LEFT', $db->quoteName('#__tz_portfolio_plus_tag_content_map') . ' AS x ON x.contentid=c.id');
        $query->join('LEFT', $db->quoteName('#__tz_portfolio_plus_tags') . ' AS t ON t.id=x.tagsid');
        $query->join('LEFT', $db->quoteName('#__users') . ' AS u ON u.id=c.created_by');

        $query->where('c.state= 1');

        if($params -> get('category_filter', 2) == 2){
            $query -> where('(m.main = 0 OR m.main = 1)');
        }elseif($params -> get('category_filter',2) == 1){
            $query -> where('m.main = 1');
        }else{
            $query -> where('m.main = 0');
        }


        $nullDate = $db->Quote($db->getNullDate());
        $nowDate = $db->Quote(JFactory::getDate()->toSQL());

        $query->where('(c.publish_up = ' . $nullDate . ' OR c.publish_up <= ' . $nowDate . ')');
        $query->where('(c.publish_down = ' . $nullDate . ' OR c.publish_down >= ' . $nowDate . ')');

        if($types = $params -> get('media_types',array())){
            $types  = array_filter($types);
            if(count($types)) {
                $media_conditions   = array();
                foreach($types as $type){
                    $media_conditions[] = 'c.type='.$db -> quote($type);
                }
                if(count($media_conditions)){
                    $query -> where('('.implode(' OR ', $media_conditions).')');
                }
            }
        }

        if (!$params->get('show_featured', 1)) {
            $query -> where('c.featured = 0');
        } elseif ($params->get('show_featured', 1) == 2) {
            $query -> where('c.featured = 1');
        }

        $catids = $params->get('catid');
        if (is_array($catids)) {
            $catids = array_filter($catids);
            if (count($catids)) {
                $query->where('m.catid IN(' . implode(',', $catids) . ')');
            }
        } else {
            $query->where('m.catid IN(' . $catids . ')');
        }

        $primary    = TZ_Portfolio_PlusHelperQuery::orderbyPrimary($params -> get('orderby_pri'));
        $secondary  = TZ_Portfolio_PlusHelperQuery::orderbySecondary($params -> get('orderby_sec', 'rdate'),
            $params -> get('order_date', 'created'));

        if ($params->get('random_article', 0)) {
            $query->order('RAND()');
        }

        $orderby = $primary . ' ' . $secondary;

        $query->order($orderby);
        $query->group('c.id');

        $db->setQuery($query, 0, $params->get('article_limit', 5));
        $items = $db->loadObjectList();

        if ($items) {
            JPluginHelper::importPlugin('content');
            TZ_Portfolio_PlusPluginHelper::importPlugin('content');
            TZ_Portfolio_PlusPluginHelper::importPlugin('mediatype');

            $app -> triggerEvent('onAlwaysLoadDocument', array('modules.mod_tz_portfolio_plus_portfolio'));
            $app -> triggerEvent('onLoadData', array('modules.mod_tz_portfolio_plus_portfolio', $items, $params));

            foreach ($items as $i => &$item) {
                $item -> params = clone($params);

                $app -> triggerEvent('onTPContentBeforePrepare', array('modules.mod_tz_portfolio_plus_portfolio',
                    &$item, &$item -> params));

                $config = JFactory::getConfig();
                $ssl    = 2;
                if($config -> get('force_ssl')){
                    $ssl    = $config -> get('force_ssl');
                }
                $uri    = JUri::getInstance();
                if($uri -> isSsl()){
                    $ssl    = 1;
                }

                $item->link = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->language));
                $item->fullLink = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->language), true, $ssl);
                $item->author_link = JRoute::_(TZ_Portfolio_PlusHelperRoute::getUserRoute($item->user_id, $params->get('usermenuitem', 'auto')));

                $media      = $item -> media;
                if(!empty($media)) {
                    $registry = new Registry($media);

                    $media = $registry->toObject();
                    $item->media = $media;
                }

                $item -> mediatypes = array();


                // Old plugins: Ensure that text property is available
                if (!isset($item->text))
                {
                    $item->text = $item->introtext;
                }
                $item -> event  = new stdClass();

                //Call trigger in group content
                $results = $app -> triggerEvent('onContentPrepare', array ('modules.mod_tz_portfolio_plus_portfolio', &$item, &$item -> params, 0));
                $item->introtext = $item->text;

                if($introtext_limit = $item -> params -> get('introtext_limit')){
                    $item -> introtext  = '<p>'.JHtml::_('string.truncate', $item->introtext, $introtext_limit, true, false).'</p>';
                }

                $results = $app -> triggerEvent('onContentBeforeDisplay', array('modules.mod_tz_portfolio_plus_portfolio',
                    &$item, &$item -> params, 0, $params->get('layout', 'default'), $module));
                if(is_array($results)){
                    $results    = array_unique($results);
                }
                $item->event->beforeDisplayContent = trim(implode("\n", $results));

                $results = $app -> triggerEvent('onContentAfterDisplay', array('modules.mod_tz_portfolio_plus_portfolio',
                    &$item, &$item -> params, 0, $params->get('layout', 'default'), $module));
                if(is_array($results)){
                    $results    = array_unique($results);
                }
                $item->event->afterDisplayContent = trim(implode("\n", $results));

                // Process the tz portfolio's content plugins.
                $results    = $app -> triggerEvent('onBeforeDisplayAdditionInfo',array('modules.mod_tz_portfolio_plus_portfolio',
                    &$item, &$item -> params, 0, $params->get('layout', 'default'), $module));
                if(is_array($results)){
                    $results    = array_unique($results);
                }
                $item -> event -> beforeDisplayAdditionInfo   = trim(implode("\n", $results));

                $results    = $app -> triggerEvent('onAfterDisplayAdditionInfo',array('modules.mod_tz_portfolio_plus_portfolio',
                    &$item, &$item -> params, 0, $params->get('layout', 'default'), $module));
                if(is_array($results)){
                    $results    = array_unique($results);
                }
                $item -> event -> afterDisplayAdditionInfo   = trim(implode("\n", $results));

                $results    = $app -> triggerEvent('onContentDisplayListView',array('modules.mod_tz_portfolio_plus_portfolio',
                    &$item, &$item -> params, 0, $params->get('layout', 'default'), $module));
                if(is_array($results)){
                    $results    = array_unique($results);
                }
                $item -> event -> contentDisplayListView   = trim(implode("\n", $results));

                //Call trigger in group tz_portfolio_plus_mediatype
                $results    = $app -> triggerEvent('onContentDisplayMediaType',array('modules.mod_tz_portfolio_plus_portfolio',
                    &$item, &$item -> params, 0, $params->get('layout', 'default'), $module));
                if(is_array($results)){
                    $results    = array_unique($results);
                }
                if(isset($item) && $item){
                    $item -> event -> onContentDisplayMediaType    = trim(implode("\n", $results));
                    if($results    = $app -> triggerEvent('onAddMediaType')){
                        $mediatypes = array();
                        foreach($results as $result){
                            if(isset($result -> special) && $result -> special) {
                                $mediatypes[] = $result -> value;
                            }
                        }
                        $item -> mediatypes = $mediatypes;
                    }
                }else{
                    unset($items[$i]);
                }

                $app -> triggerEvent('onTPContentAfterPrepare', array('modules.mod_tz_portfolio_plus_portfolio',
                    &$item, &$item -> params, 0, $params->get('layout', 'default'), $module));
            }
            self::$cache[$storeId]  = $items;
            return $items;
        }
        return false;
    }

    protected static function __getArticleByKey($article, $key = 'id')
    {
        $contentId	= ArrayHelper::getColumn($article, $key);
        $storeId = md5(__METHOD__ . '::' . $key.'::'.implode(',',$contentId));
        if (!isset(self::$cache[$storeId])) {
            self::$cache[$storeId] = ArrayHelper::getColumn($article, $key);
            return self::$cache[$storeId];
        }
        return self::$cache[$storeId];
    }

    public static function getCategoriesByArticle($params)
    {
        if ($articles = self::getList($params)) {
            $contentId = self::__getArticleByKey($articles, 'content_id');
            return TZ_Portfolio_PlusFrontHelperCategories::getCategoriesByArticleId($contentId, array('reverse_contentid' => true));
        }
        return false;
    }

    public static function getCategoriesGroupByArticle($params)
    {
        if ($articles = self::getList($params)) {
            $contentId = self::__getArticleByKey($articles, 'content_id');
            return TZ_Portfolio_PlusFrontHelperCategories::getCategoriesByArticleId($contentId, array('reverse_contentid' => true, 'groupby' => 'c.id'));
        }
        return false;
    }

    public static function getTagsByArticle($params)
    {
        if ($articles = self::getList($params)) {
            $contentId = self::__getArticleByKey($articles, 'content_id');
            return TZ_Portfolio_PlusFrontHelperTags::getTagsByArticleId($contentId, array(
                    'orderby' => 'm.contentid',
                    'menuActive' => $params->get('tagmenuitem', 'auto'),
                    'reverse_contentid' => true
                )
            );
        }
    }

    public static function getTagsByCategory($params)
    {
        $catids = $params->get('catid');
        if(isset($catids)) {
            $tags = TZ_Portfolio_PlusFrontHelperTags::getTagsByCategoryId($catids);
            return $tags;
        }else {
            return false;
        }
    }

    public static function getTagsFilterByArticle($params)
    {
        if ($articles = self::getList($params)) {
            $contentId = self::__getArticleByKey($articles, 'content_id');
            return TZ_Portfolio_PlusFrontHelperTags::getTagsFilterByArticleId($contentId);
        }
        return false;
    }

    public static function getCategoriesFilterByArticle($params)
    {
        if ($articles = self::getList($params)) {
            $contentId = self::__getArticleByKey($articles, 'content_id');
            return TZ_Portfolio_PlusFrontHelperCategories::getCategoriesByArticleId($contentId, array('reverse_contentid' => false, 'groupby' => 'c.id'));
        }
        return false;
    }
}
