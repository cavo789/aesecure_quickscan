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
defined('_JEXEC') or die();

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.modellist');
jimport('joomla.html.pagination');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class TZ_Portfolio_PlusModelPortfolio extends JModelList
{
    protected $pagNav                   = null;
    protected $rowsTag                  = null;
    protected $categories               = null;

    public function __construct($config = array()){
        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null){
        parent::populateState($ordering,$direction);

        $app    = JFactory::getApplication('site');
        $params = $app -> getParams('com_tz_portfolio_plus');

        $global_params    = JComponentHelper::getParams('com_tz_portfolio_plus');

        if($layout_type = $params -> get('layout_type',array())){
            if(empty($layout_type) || (is_array($layout_type) && !count($layout_type))){
                $params -> set('layout_type',$global_params -> get('layout_type',array()));
            }
        }else{
            $params -> set('layout_type',$global_params -> get('layout_type',array()));
        }

        $user		= JFactory::getUser();

        $offset = $app -> input -> getUInt('limitstart',0);

        if($params -> get('show_limit_box',0)  && $params -> get('tz_portfolio_plus_layout') == 'default'){
            $limit  = $app->getUserStateFromRequest('com_tz_portfolio_plus.portfolio.limit','limit',$params -> get('tz_article_limit',10));
        }
        else{
            $limit  = (int) $params -> get('tz_article_limit',10);
        }

        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio_plus')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio_plus'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $this->setState('filter.language', JLanguageMultilang::isEnabled());

        $this -> setState('params',$params);
        $this -> setState('list.start', $offset);
        $this -> setState('Itemid',$params -> get('id'));
        $this -> setState('list.limit',$limit);
        $this -> setState('catid',$params -> get('catid'));
        $this -> setState('filter.char',$app -> input -> getString('char',null));
        $this -> setState('filter.tagId', $app -> input -> getInt('tid'));
        $this -> setState('filter.tagAlias', $app -> input -> getString('tagAlias'));
        $this -> setState('filter.userId', $app -> input -> getInt('uid'));
        $this -> setState('filter.featured',null);
        $this -> setState('filter.year',null);
        $this -> setState('filter.month',null);
        $this -> setState('filter.category_id',$app -> input -> getInt('id'));

        $this -> setState('filter.searchword', $app->input->getString('searchword'));
        $this -> setState('filter.fields', $app -> input -> get('fields', array(), 'array'));

        $this -> setState('filter.shownIds', $app -> input -> get('shownIds', array(), 'array'));

        $articleOrderDate	= $params->get('order_date', 'created');

        $orderby    = '';
        $secondary  = TZ_Portfolio_PlusHelperQuery::orderbySecondary($params -> get('orderby_sec', 'rdate'), $articleOrderDate);
        $primary    = TZ_Portfolio_PlusHelperQuery::orderbyPrimary($params -> get('orderby_pri'));

        $orderby .= $primary . ' ' . $secondary;

        $this -> setState('list.ordering', $orderby);
        $this -> setState('list.direction', null);
    }

    protected function getListQuery(){
        $params = $this -> getState('params');
        $user	= JFactory::getUser();
        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);

        $query -> select(
            $this->getState(
                'list.select',
                'c.*, m.catid AS catid ,cc.title AS category_title'.
                ',CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug'.
                ',CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
                ',CASE WHEN CHAR_LENGTH(c.fulltext) THEN c.fulltext ELSE null END as readmore'
//                .
//                ',parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias'
            )
        );

        $query -> from($db -> quoteName('#__tz_portfolio_plus_content').' AS c');

        $query -> join('INNER',$db -> quoteName('#__tz_portfolio_plus_content_category_map').' AS m ON m.contentid=c.id');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_plus_categories').' AS cc ON cc.id=m.catid');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_plus_tag_content_map').' AS x ON x.contentid=c.id');

        $query -> select('t.title AS tagName');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_plus_tags').' AS t ON t.id=x.tagsid');

        // Filter by tag id
        if($tagId = $this -> getState('filter.tagId')) {
            $query->where('t.id =' .$tagId);
        }

        // Filter by tag alias
        if($tagAlias = $this -> getState('filter.tagAlias')) {
//            $query -> select('t2.title AS tagName');
//            $query -> join('INNER',$db -> quoteName('#__tz_portfolio_plus_tags').' AS t2 ON t2.id=x.tagsid');
            $query->where('t.alias =' .$db -> quote($tagAlias));
        }

        $query -> select(' u.name AS author');
        $query -> select('u.email AS author_email');
        $query -> join('LEFT',$db -> quoteName('#__users').' AS u ON u.id=c.created_by');

        // Filter by user id
        if($userId = $this -> getState('filter.userId')) {
            $query->where('u.id =' .$userId);
        }

        // Join over the categories to get parent category titles
        $query -> select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
        $query->join('LEFT', '#__tz_portfolio_plus_categories as parent ON parent.id = cc.parent_id');

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published)) {
            // Use article state if badcats.id is null, otherwise, force 0 for unpublished
            $query->where('c.state = ' . (int) $published);
        }
        elseif (is_array($published)) {
            $published  = ArrayHelper::toInteger($published);
            $published  = implode(',', $published);
            // Use article state if badcats.id is null, otherwise, force 0 for unpublished
            $query->where('c.state IN ('.$published.')');
        }

        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio_plus')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio_plus'))){
            // Filter by start and end dates.
            $nullDate = $db->Quote($db->getNullDate());
            $nowDate = $db->Quote(JFactory::getDate()->toSQL());

            $query->where('(c.publish_up = ' . $nullDate . ' OR c.publish_up <= ' . $nowDate . ')');
            $query->where('(c.publish_down = ' . $nullDate . ' OR c.publish_down >= ' . $nowDate . ')');
        }

        // Filter by access level.
        if (!$params->get('show_noauth')) {
            $groups	= implode(',', $user->getAuthorisedViewLevels());
            $query->where('c.access IN ('.$groups.')');
            $query->where('cc.access IN ('.$groups.')');
        }

        $catids = $params -> get('catid');

        if($this -> getState('filter.category_id')){
            $catids = $this -> getState('filter.category_id');
        }

        if(is_array($catids)){
            $catids = array_filter($catids);
            if(count($catids)){
                $query -> where('m.catid IN('.implode(',',$catids).')');
            }
        }
        elseif(!empty($catids)){
            $query -> where('m.catid IN('.$catids.')');
        }

        if($types = $params -> get('media_types',array())){
            $types  = array_filter($types);
            if(count($types)) {
                $media_conditions   = array();
                foreach($types as $type){
                    $media_conditions[] = 'type='.$db -> quote($type);
                }
                if(count($media_conditions)){
                    $query -> where('('.implode(' OR ', $media_conditions).')');
                }
            }
        }

        if($char = $this -> getState('filter.char')){
            $query -> where('c.title LIKE '.$db -> quote(urldecode(mb_strtolower($char)).'%'));
            $query -> where('ASCII(SUBSTR(LOWER(c.title),1,1)) = ASCII('.$db -> quote(mb_strtolower($char)).')');
        }

        // Filter by shownids
        $shownIds = $this -> getState('filter.shownIds', array());
        if(count($shownIds)){
            $query -> where('c.id NOT IN( '.implode(',', $shownIds).')');
        }

        // Filter by word from filter module
        if ($searchWord = $this->getState('filter.searchword')) {
            $searchWord = $db->quote('%' . $db->escape($searchWord, true) . '%', true);
            $query->where('(c.title LIKE ' . $searchWord . ' OR c.introtext LIKE ' . $searchWord.')');
        }

        // Filter by extrafields from filter module
        if ($fields = $this->getState('filter.fields')) {
            if (count($fields)) {
                $fields = array_filter($fields);
                $fieldIds = array_keys($fields);
                $fieldIds = array_unique($fieldIds);

                JLoader::import('extrafields', JPATH_SITE . '/components/com_tz_portfolio_plus/helpers');
                if ($extraFields = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraFieldObjectById($fieldIds)) {
                    $where = array();
                    if (count($extraFields)) {
                        foreach ($extraFields as $field) {
                            $field->onSearch($query, $where, $fields[$field->id]);
                        }
                    }
                    if (count($where)) {
                        $query->where('(' . implode(' AND ', $where) . ')');
                    }
                }
            }
        }


        $query->order($this->getState('list.ordering', 'c.created') . ' ' . $this->getState('list.direction', null));

        // Filter by language
        if ($this->getState('filter.language')) {
            $query->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
        }

        $query -> group('c.id');

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){

            $app            = JFactory::getApplication();
            $user	        = TZ_Portfolio_PlusUser::getUser();
            $userId	        = $user->get('id');
            $guest	        = $user->get('guest');

            $params         = $this -> getState('params');

            JLoader::import('category',COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'helpers');

            $_params        = null;

            $threadLink     = null;
            $comments       = null;

            if(count($items)>0){
                $content_ids        = ArrayHelper::getColumn($items, 'id');
                $mainCategories     = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesByArticleId($content_ids,
                    array('main' => true));
                $second_categories  = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesByArticleId($content_ids,
                    array('main' => false));

                $tags   = null;
                if(count($content_ids) && $params -> get('show_tags',1)) {
                    $tags = TZ_Portfolio_PlusFrontHelperTags::getTagsByArticleId($content_ids, array(
                            'orderby' => 'm.contentid',
                            'menuActive' => $params -> get('menu_active', 'auto'),
                            'reverse_contentid' => true
                        )
                    );
                }

                JPluginHelper::importPlugin('content');
                TZ_Portfolio_PlusPluginHelper::importPlugin('mediatype');
                TZ_Portfolio_PlusPluginHelper::importPlugin('content');

                $app -> triggerEvent('onAlwaysLoadDocument', array('com_tz_portfolio_plus.portfolio'));
                $app -> triggerEvent('onLoadData', array('com_tz_portfolio_plus.portfolio', $items, $params));

                // Get the global params
                $globalParams = JComponentHelper::getParams('com_tz_portfolio_plus', true);

                JLoader::import('extrafields', COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH);

                foreach($items as $i => &$item){

                    $_params        = clone($params);

                    $item->params   = clone($_params);

                    $app -> triggerEvent('onTPContentBeforePrepare', array('com_tz_portfolio_plus.portfolio',
                        &$item, &$item -> params));

                    $articleParams = new JRegistry;
                    $articleParams->loadString($item->attribs);

                    if($mainCategories && isset($mainCategories[$item -> id])){
                        $mainCategory   = $mainCategories[$item -> id];
                        if($mainCategory){
                            $item -> catid          = $mainCategory -> id;
                            $item -> category_title = $mainCategory -> title;
                            $item -> catslug        = $mainCategory -> id.':'.$mainCategory -> alias;
                            $item -> category_link  = $mainCategory -> link;
                            $item -> cat_alias      = $mainCategory -> alias.'_'.$mainCategory -> id;

                            // Merge main category's params to article
                            $catParams  = new JRegistry($mainCategory ->  params);
                            if($inheritFrom = $catParams -> get('inheritFrom', 0)){
                                if($inheritCategory    = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesById($inheritFrom)) {
                                    $inheritCatParams   = new JRegistry($inheritCategory->params);
                                    $catParams          = clone($inheritCatParams);
                                }
                            }
                            $item -> params -> merge($catParams);
                        }
                    }else {
                        // Create main category's link
                        $item -> category_link      = JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($item -> catid));

                        // Merge main category's params to article
                        if($mainCategory = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesById($item -> catid)) {
                            $catParams = new JRegistry($mainCategory->params);
                            if ($inheritFrom = $catParams->get('inheritFrom', 0)) {
                                if ($inheritCategory = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesById($inheritFrom)) {
                                    $inheritCatParams = new JRegistry($inheritCategory->params);
                                    $catParams = clone($inheritCatParams);
                                }
                            }
                            $item->params->merge($catParams);
                        }
                    }

                    // Merge with article params
                    $item -> params -> merge($articleParams);

                    // Disable email icon with joomla 4.x
                    if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE && !empty($item -> params)) {
                        $item->params->set('show_email_icon', 0);
                    }

                    // Get all second categories
                    $item -> second_categories  = null;
                    if(isset($second_categories[$item -> id])) {
                        $item->second_categories = $second_categories[$item -> id];
                    }

                    // Get article's tags
                    $item -> tags   = null;
                    if($tags && count($tags) && isset($tags[$item -> id])){
                        $item -> tags   = $tags[$item -> id];
                    }

                    /*** Start New Source ***/
                    $tmpl   = null;

                    $config = JFactory::getConfig();
                    $ssl    = 2;
                    if($config -> get('force_ssl')){
                        $ssl    = $config -> get('force_ssl');
                    }
                    $uri    = JUri::getInstance();
                    if($uri -> isSsl()){
                        $ssl    = 1;
                    }

                    // Create Article Link
                    $item ->link        = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item -> slug, $item -> catid, $item->language).$tmpl);
                    $item -> fullLink   = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item -> slug, $item -> catid, $item->language), true, $ssl);

                    // Create author Link
                    $item -> author_link    = JRoute::_(TZ_Portfolio_PlusHelperRoute::getUserRoute($item -> created_by,
                        $params -> get('user_menu_active','auto')));

                    // Compute the asset access permissions.
                    // Technically guest could edit an article, but lets not check that to improve performance a little.
                    if (!$guest) {
                        $asset	= 'com_tz_portfolio_plus.article.'.$item->id;

                        // Check general edit permission first.
                        if ($user->authorise('core.edit', $asset)) {
                            $item->params->set('access-edit', true);
                        }
                        // Now check if edit.own is available.
                        elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
                            // Check for a valid user and that they are the owner.
                            if ($userId == $item->created_by) {
                                $item->params->set('access-edit', true);
                            }
                        }
                    }

                    $media      = $item -> media;
                    if($item -> media && !empty($item -> media)) {
                        $registry   = new JRegistry($item -> media);
                        $obj        = $registry->toObject();
                        $item->media = clone($obj);
                    }

                    $item -> mediatypes = array();

                    // Add feed links
                    if ($app -> input -> getCmd('format',null) != 'feed') {

                        // Old plugins: Ensure that text property is available
                        if (!isset($item->text))
                        {
                            $item->text = $item->introtext;
                        }

                        //
                        // Process the content plugins.
                        //

                        $app -> triggerEvent('onTPContentPrepare', array (
                            'com_tz_portfolio_plus.portfolio',
                            &$item,
                            &$item -> params,
                            $this -> getState('list.start')));

                        $app -> triggerEvent('onContentPrepare', array (
                            'com_tz_portfolio_plus.portfolio',
                            &$item,
                            &$item -> params,
                            $this -> getState('list.start')));
                        $item->introtext = $item->text;

                        $item->event = new stdClass();
                        $results = $app -> triggerEvent('onContentAfterTitle', array(
                            'com_tz_portfolio_plus.portfolio',
                            &$item,
                            &$item -> params,
                            $this -> getState('list.start')));
                        $item->event->afterDisplayTitle = trim(implode("\n", $results));

                        $results = $app -> triggerEvent('onContentBeforeDisplay', array(
                            'com_tz_portfolio_plus.portfolio',
                            &$item,
                            &$item -> params,
                            $this -> getState('list.start')));
                        $item->event->beforeDisplayContent = trim(implode("\n", $results));

                        $results = $app -> triggerEvent('onContentAfterDisplay', array(
                            'com_tz_portfolio_plus.portfolio',
                            &$item,
                            &$item -> params,
                            $this -> getState('list.start')));
                        $item->event->afterDisplayContent = trim(implode("\n", $results));

                        // Process the tz portfolio's content plugins.
                        $results    = $app -> triggerEvent('onContentDisplayVote',array(
                            'com_tz_portfolio_plus.portfolio',
                            &$item,
                            &$item -> params,
                            $this -> getState('list.start')));
                        $item -> event -> contentDisplayVote   = trim(implode("\n", $results));

                        $results    = $app -> triggerEvent('onBeforeDisplayAdditionInfo',array(
                            'com_tz_portfolio_plus.portfolio',
                            &$item,
                            &$item -> params,
                            $this -> getState('list.start')));
                        $item -> event -> beforeDisplayAdditionInfo   = trim(implode("\n", $results));

                        $results    = $app -> triggerEvent('onAfterDisplayAdditionInfo',array(
                            'com_tz_portfolio_plus.portfolio',
                            &$item,
                            &$item -> params,
                            $this -> getState('list.start')));
                        $item -> event -> afterDisplayAdditionInfo   = trim(implode("\n", $results));

                        $results = $app -> triggerEvent('onContentDisplayListView', array(
                            'com_tz_portfolio_plus.portfolio',
                            &$item,
                            &$item -> params,
                            $this->getState('list.start')));
                        $item->event->contentDisplayListView = trim(implode("\n", $results));

                        // Process the tz portfolio's mediatype plugins.
                        $results    = $app -> triggerEvent('onContentDisplayMediaType',array(
                            'com_tz_portfolio_plus.portfolio',
                            &$item,
                            &$item -> params,
                            $this -> getState('list.start')));
                        if($item){
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
                    }

                    if($item && strlen(trim($item -> introtext)) && $introLimit = $params -> get('tz_article_intro_limit')){
                        $item -> introtext   = '<p>'.JHtml::_('string.truncate', $item->introtext, $introLimit, true, false).'</p>';
                    }

                    // Get article's extrafields
                    $extraFields    = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraFields($item, $item -> params,
                        false, array('filter.list_view' => true, 'filter.group' => $params -> get('order_fieldgroup', 'rdate')));
                    $item -> extrafields    = $extraFields;

                    $app -> triggerEvent('onTPContentAfterPrepare', array('com_tz_portfolio_plus.portfolio',
                        &$item, &$item -> params, $this -> getState('list.start')));

                }
                return $items;
            }
        }
        return false;
    }

    public function ajax($data = null){

        $list   = null;

        $params = JComponentHelper::getParams('com_tz_portfolio_plus');

        $input		= JFactory::getApplication() -> input;
        $Itemid     = $data['Itemid'];
        $page       = $data['page'];
        $layout     = $data['layout'];
        $char       = $data['char'];
        $catid      = $data['id'];
        $uid        = $data['uid'];
        $tagid      = $data['tid'];
        $searchword = $data['searchword'];

        $tags       = stripslashes($input -> getString('tags', ''));
        $tags       = json_decode($tags);

        $menu       = JMenu::getInstance('site');
        $menuParams = $menu -> getParams($Itemid);

        $params -> merge($menuParams);

        $limit  = (int) $params -> get('tz_article_limit', 10);

        $offset = $limit * ($page - 1);

        $user   = JFactory::getUser();
        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio_plus')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio_plus'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $app    = JFactory::getApplication();

        $this->setState('filter.language', $app->getLanguageFilter());

        $this -> setState('list.limit',$limit);
        $this -> setState('list.start',$offset);
        $this -> setState('params',$params);
        $this -> setState('filter.char',$char);
        $this -> setState('filter.category_id',$catid);
        $this -> setState('filter.userId',$uid);
        $this -> setState('filter.tagId',$tagid);
        $this -> setState('filter.tagAlias',$data['tagAlias']);
        $this -> setState('filter.shownIds',$data['shownIds']);
        $this -> setState('filter.fields',$data['fields']);
        $this -> setState('filter.searchword',$searchword);


        $articleOrderDate	= $params->get('order_date', 'created');

        $orderby    = '';
        $secondary  = TZ_Portfolio_PlusHelperQuery::orderbySecondary($params -> get('orderby_sec', 'rdate'), $articleOrderDate);
        $primary    = TZ_Portfolio_PlusHelperQuery::orderbyPrimary($params -> get('orderby_pri'));

        $orderby .= $primary . ' ' . $secondary;

        $this -> setState('list.ordering', $orderby);
        $this -> setState('list.direction', null);

        return true;
    }

    protected function __getArticleByKey($article, $key = 'id'){
        $storeId    = md5(__METHOD__.'::'.$key);
        if(!isset($this -> cache[$storeId])){
            $this -> cache[$storeId]    = ArrayHelper::getColumn($article, $key);
            return $this -> cache[$storeId];
        }
        return $this -> cache[$storeId];
    }

    public function getCategoriesByArticle(){
        if(isset($this -> cache[$this->getStoreId()]) && ($articles = $this -> cache[$this->getStoreId()])){
            $contentId  = $this -> __getArticleByKey($articles, 'id');

            $params     = $this -> getState('params');
            $orderby    = null;
            // Order by artilce
            switch ($params -> get('orderby_pri')){
                case 'alpha' :
                    $orderby    = 'title';
                    break;

                case 'ralpha' :
                    $orderby    = 'title DESC';
                    break;

                case 'order' :
                    $orderby    = 'lft';
                    break;
            }

            $options    = array('orderby' => $orderby, 'reverse_contentid' => false, 'groupby' => 'c.id');
            if(!$params -> get('filter_second_category', 1)){
                return TZ_Portfolio_PlusFrontHelperCategories::getMainCategoriesByArticleId($contentId);
            }

            return TZ_Portfolio_PlusFrontHelperCategories::getCategoriesByArticleId($contentId, $options);
        }
        return false;
    }

    public function getAllCategories(){
        $params     = $this -> getState('params');
        $orderby    = null;

        // Order by artilce
        switch ($params -> get('orderby_pri')){
            case 'alpha' :
                $orderby    = 'c.title';
                break;

            case 'ralpha' :
                $orderby    = 'c.title DESC';
                break;

            case 'order' :
                $orderby    = 'c.lft';
                break;
        }

        $catid = $params -> get('catid', array());
        $catid  = array_unique($catid);
        $catid  = array_filter($catid);

        $options    = array('second_by_article' => true, 'orderby' => $orderby);
        if(!$params -> get('filter_second_category', 1)){
            $options['second_by_article']   = false;
        }

        if(count($catid) && $categories = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesById($catid, $options)){
            return $categories;
        }
        return TZ_Portfolio_PlusFrontHelperCategories::getAllCategories($options);
    }

    public function getTagsByArticle($filterAlias = null){
        if(isset($this -> cache[$this->getStoreId()]) && ($articles = $this -> cache[$this->getStoreId()])){
            $contentId  = $this -> __getArticleByKey($articles, 'id');
            $tags   = TZ_Portfolio_PlusFrontHelperTags::getTagsFilterByArticleId($contentId, $filterAlias);
            return $tags;
        }
        return false;
    }

    public function getAllTags(){
        $params = $this -> getState('params');
        return TZ_Portfolio_PlusFrontHelperTags::getTagsByCategoryId($params -> get('catid'));
    }

    public function getAvailableLetter(){
        $params = $this -> getState('params');
        if($params -> get('use_filter_first_letter',0)){
            if($letters = $params -> get('tz_letters','a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z')){
                $letters = explode(',',$letters);
                $arr    = null;

                $filters    = array();
                if($catids = $params -> get('catid')){
                    $filters['catid']   = $catids;
                }

                if($featured = $this -> getState('filter.featured')){
                    $filters['featured']   = $featured;
                }

                if($tagId = $this -> getState('filter.tagId')){
                    $filters['tagId']   = $tagId;
                }

                if($userId = $this -> getState('filter.userId')){
                    $filters['userId']   = $userId;
                }

                if($year = $this -> getState('filter.year')){
                    $filters['year']   = $year;
                }

                if($month = $this -> getState('filter.month')){
                    $filters['month']   = $month;
                }

                $lettersArt = TZ_Portfolio_PlusContentHelper::getLetters($filters);

                foreach($letters as $i => &$letter){
                    $letter = trim($letter);
                    $letterKey  = ord($letter);

                    $arr[$i]    = false;
                    if(in_array($letterKey, $lettersArt)){
                        $arr[$i]    = true;
                    }
                }

                return $arr;

            }
        }
        return false;
    }

    public function getAvailableItem() {
        if (isset($_COOKIE["tppLatestItem"])  && $_COOKIE["tppLatestItem"] !='undefined' && $tppLatestItem  =   $_COOKIE["tppLatestItem"]) {
            $tppLatestItem  =   (int)str_replace('tzelement','', $tppLatestItem);
            if ($tppLatestItem) {
                $query  =   $this->getListQuery();
                $query->where ('c.id = '.$tppLatestItem);
                $db = JFactory::getDbo();
                $db->setQuery($query);
                $data   =   $db->loadObject();
                if ($data) {
                    return $data;
                }
            }
        }
        return false;
    }

    public function ajaxComments(){
        $input	= JFactory::getApplication() -> input;
        $data   = json_decode(base64_decode($input -> getString('url')));
        $id     = json_decode(base64_decode($input -> getString('id')));
        if($data){
            require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'
                .DIRECTORY_SEPARATOR.'phpclass'.DIRECTORY_SEPARATOR.'http_fetcher.php');
            require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'
                .DIRECTORY_SEPARATOR.'phpclass'.DIRECTORY_SEPARATOR.'readfile.php');

            $params     = JComponentHelper::getParams('com_tz_portfolio_plus');

            $Itemid     = $input -> getInt('Itemid');

            $menu       = JMenu::getInstance('site');
            $menuParams = $menu -> getParams($Itemid);

            $params -> merge($menuParams);

            $threadLink = null;

            $_id    = null;

            if(is_array($data) && count($data)){
                foreach($data as $i => &$contentUrl){
                    if(!preg_match('/http\:\/\//i',$contentUrl)){
                        $uri    = JUri::getInstance();
                        $contentUrl    = $uri -> getScheme().'://'.$uri -> getHost().$contentUrl;
                    }

                    if(preg_match('/(.*?)(\?tmpl\=component)|(\&tmpl\=component)/i',$contentUrl)){
                        $contentUrl = preg_replace('/(.*?)(\?tmpl\=component)|(\&tmpl\=component)/i','$1',$contentUrl);
                    }

                    $_id[$contentUrl]  = $id[$i];

                    if($params -> get('tz_comment_type','disqus') == 'facebook'){
                        $threadLink .= '&urls[]='.$contentUrl;
                    }elseif($params -> get('tz_comment_type','disqus') == 'disqus'){
                        $threadLink .= '&thread[]=link:'.$contentUrl;
                    }
                }
            }

            if(!is_array($data)){
                $threadLink = $data;
            }

            $fetch       = new Services_Yadis_Plainhttp_fetcher();
            $comments    = null;

            if($params -> get('tz_show_count_comment',1) == 1){
                // From Facebook
                if($params -> get('tz_comment_type','disqus') == 'facebook'){
                    if($threadLink){
                        $url        = 'http://api.facebook.com/restserver.php?method=links.getStats'
                            .$threadLink;
                        $content    = $fetch -> get($url);

                        if($content){
                            if($bodies = $content -> body){
                                if(preg_match_all('/\<link_stat\>(.*?)\<\/link_stat\>/ims',$bodies,$matches)){
                                    if(isset($matches[1]) && !empty($matches[1])){
                                        foreach($matches[1]as $val){
                                            $match  = null;
                                            if(preg_match('/\<url\>(.*?)\<\/url\>.*?\<comment_count\>(.*?)\<\/comment_count\>/msi',$val,$match)){
                                                if(isset($match[1]) && isset($match[2])){
                                                    if(in_array($match[1],$data)){
                                                        $comments[$_id[$match[1]]]    = $match[2];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // Disqus Comment count
                if($params -> get('tz_comment_type','disqus') == 'disqus'){

                    $url        = 'https://disqus.com/api/3.0/threads/list.json?api_secret='
                        .$params -> get('disqusApiSecretKey','4sLbLjSq7ZCYtlMkfsG7SS5muVp7DsGgwedJL5gRsfUuXIt6AX5h6Ae6PnNREMiB')
                        .'&forum='.$params -> get('disqusSubDomain','templazatoturials')
                        .$threadLink.'&include=open';

                    if($_content = $fetch -> get($url)){

                        $body    = json_decode($_content -> body);
                        if(isset($body -> response)){
                            if($responses = $body -> response){
                                foreach($responses as $response){
                                    if(in_array($response ->link,$data)){
                                        $comments[$_id[$response ->link]]    = $response -> posts;
                                    }
                                }
                            }

                        }
                    }
                }

                if($comments){
                    if(is_array($comments)){
                        return json_encode($comments);
                    }
                    return 0;
                }
                return 0;
            }
        }
    }
}
?>