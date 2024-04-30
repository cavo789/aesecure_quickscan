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

jimport('joomla.application.component.view');

/**
 * HTML Article View class for the Content component.
 */
class TZ_Portfolio_PlusViewArticle extends TZ_Portfolio_PlusViewLegacy
{
    protected $item;
    protected $params;
    protected $print;
    protected $state;
    protected $user;
    protected $generateLayout;
    protected $listMedia;
    protected $authorParams;
    protected $itemsRelated;
    protected $listTags;

    public function display($tpl = null)
    {
        $this -> setLayout('amp');

        JLoader::import('com_tz_portfolio_plus.includes.framework',JPATH_ADMINISTRATOR.'/components');


        // Initialise variables.
        $app		= JFactory::getApplication();

        $tmpl   = $app -> input -> getString('tmpl');
        if($tmpl){
            JHtml::_('bootstrap.framework');
            JHtml::_('jquery.framework');
        }

        $user		= JFactory::getUser();
        $dispatcher	= TZ_Portfolio_PlusPluginHelper::getDispatcher();

        $this->state	= $this->get('State');
        $params	        = $this->state->get('params');
        $this->item		= $this->get('Item');
        $offset         = $this->state->get('list.offset');
        $related        = $this -> get('ItemRelated');

        // Merge article params. If this is single-article view, menu params override article params
        // Otherwise, article params override menu item params
        $this->params	= $this->state->get('params');

        $active	= $app->getMenu()->getActive();
        $temp	= clone ($this->params);
        $tempR	= clone ($this->params);

        JPluginHelper::importPlugin('content');
        TZ_Portfolio_PlusPluginHelper::importPlugin('mediatype');
        TZ_Portfolio_PlusPluginHelper::importPlugin('content');

        if($this -> item -> id && $params -> get('show_tags',1)) {
            $this -> listTags = TZ_Portfolio_PlusFrontHelperTags::getTagsByArticleId($this -> item -> id, array(
                    'orderby' => 'm.contentid',
                    'menuActive' => $params -> get('menu_active', 'auto')
                )
            );
        }


        $mediatypes = array();
        if($results    = $app -> triggerEvent('onAddMediaType')){
            foreach($results as $result){
                if(isset($result -> special) && $result -> special) {
                    $mediatypes[] = $result -> value;
                }
            }
        }

        if($tmpl){
            $tmpl   = '&amp;tmpl='.$tmpl;
        }

        if($params -> get('tz_use_lightbox', 0) && !$tmpl){
            $tmpl   = '&amp;tmpl=component';
        }

        $this->print = $app->input->getBool('print');
        $this->user		= $user;

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            $app -> enqueueMessage(implode("\n", $errors), 'warning');
            return false;
        }

        // Create a shortcut for $item.
        $item = &$this->item;

        // Get second categories
        $second_categories  = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesByArticleId($item -> id,
            array('main' => false, 'reverse_contentid' => false));
        $item -> second_categories  = $second_categories;

        // Add router helpers.
        $item->slug			= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
        $item->catslug		= $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
        $item->parent_slug	= $item->category_alias ? ($item->parent_id.':'.$item->parent_alias) : $item->parent_id;

        // TODO: Change based on shownoauth
        $item->readmore_link = null;

        // Check to see which parameters should take priority
        if ($active) {
            $currentLink = $active->link;
            // If the current view is the active item and an article view for this article, then the menu item params take priority
            if (strpos($currentLink, 'view=article') && (strpos($currentLink, '&id='.(string) $item->id))) {
                // $item->params are the article params, $temp are the menu item params
                // Merge so that the menu item params take priority
                $item->params->merge($temp);
                // Load layout from active query (in case it is an alternative menu item)
                if (isset($active->query['layout'])) {
                    $this->setLayout($active->query['layout']);
                }
            }
            else {
                // Current view is not a single article, so the article params take priority here
                // Merge the menu item params with the article params so that the article params take priority
                $temp->merge($item->params);
                $item->params = $temp;

                // Check for alternative layouts (since we are not in a single-article menu item)
                // Single-article menu item layout takes priority over alt layout for an article
                if ($layout = $item->params->get('article_layout')) {
                    $this->setLayout($layout);
                }
            }
        }
        else {
            // Merge so that article params take priority
            $temp->merge($item->params);
            $item->params = $temp;
            // Check for alternative layouts (since we are not in a single-article menu item)
            // Single-article menu item layout takes priority over alt layout for an article
            if ($layout = $item->params->get('article_layout')) {
                $this->setLayout($layout);
            }
        }

        $item -> params -> set('show_cat_icons', $item -> params -> get('show_icons'));

        // Create "link" and "fullLink" for article object
        $tmpl   = null;
        if($item -> params -> get('tz_use_lightbox',0)){
            $tmpl   = '&amp;tmpl=component';
        }

        $config = JFactory::getConfig();
        $ssl    = 2;
        if($config -> get('force_ssl')){
            $ssl    = $config -> get('force_ssl');
        }
        $uri    = JUri::getInstance();
        if($uri -> isSsl()){
            $ssl    = 1;
        }

        $item ->link        = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item -> slug,$item -> catid).$tmpl);
        $item -> fullLink   = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item -> slug,$item -> catid), true, $ssl);

        $item->parent_link = JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($item->parent_slug));
        $item -> category_link  = JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($item->catslug));

        $item -> author_link    = JRoute::_(TZ_Portfolio_PlusHelperRoute::getUserRoute($item -> created_by,
            $params -> get('user_menu_active','auto')));

        // Check the view access to the article (the model has already computed the values).
        if ($item->params->get('access-view') != true && (($item->params->get('show_noauth') != true &&  $user->get('guest') ))) {
            throw new \Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        //
        // Process the content plugins.
        //

        $app -> triggerEvent('onAlwaysLoadDocument', array('com_tz_portfolio_plus.portfolio'));
        $app -> triggerEvent('onLoadData', array('com_tz_portfolio_plus.portfolio', $this -> item, $params));

        if ($item->params->get('show_intro', 1)) {
            $item->text = $item->introtext.' '.$item->fulltext;
        }
        elseif ($item->fulltext) {
            $item->text = $item->fulltext;
        }
        else  {
            $item->text = $item->introtext;
        }

        if ($item->params->get('show_intro', 1)) {
            $text = $item->introtext.' '.$item->fulltext;
        }
        elseif ($item->fulltext) {
            $text = $item->fulltext;
        }
        else  {
            $text = $item->introtext;
        }

        if($item -> introtext && !empty($item -> introtext)) {
            $item->text = $item->introtext;
            $results = $app -> triggerEvent('onContentPrepare', array('com_tz_portfolio_plus.article', &$item, &$this->params, $offset));
            $results = $app -> triggerEvent('onContentAfterTitle', array('com_tz_portfolio_plus.article', &$item, &$this->params, $offset));
            $results = $app -> triggerEvent('onContentBeforeDisplay', array('com_tz_portfolio_plus.article', &$item, &$this->params, $offset));
            $results = $app -> triggerEvent('onContentAfterDisplay', array('com_tz_portfolio_plus.article', &$item, &$this->params, $offset));

            $item->introtext = $item->text;
        }
        if($item -> fulltext && !empty($item -> fulltext)) {
            $item->text = $item->fulltext;
            $results = $app -> triggerEvent('onContentPrepare', array('com_tz_portfolio_plus.article', &$item, &$this->params, $offset));
            $results = $app -> triggerEvent('onContentAfterTitle', array('com_tz_portfolio_plus.article', &$item, &$this->params, $offset));
            $results = $app -> triggerEvent('onContentBeforeDisplay', array('com_tz_portfolio_plus.article', &$item, &$this->params, $offset));
            $results = $app -> triggerEvent('onContentAfterDisplay', array('com_tz_portfolio_plus.article', &$item, &$this->params, $offset));

            $item->fulltext = $item->text;
        }

        $item -> text   = $text;
        $results = $app -> triggerEvent('onContentPrepare', array ('com_tz_portfolio_plus.article', &$item, &$this->params, $offset));

        $item->event = new stdClass();
        $results = $app -> triggerEvent('onContentDisplayAuthorAbout',
            array('com_tz_portfolio_plus.article', $item -> author_id, &$this->params, &$item, $offset));
        $item->event->authorAbout = trim(implode("\n", $results));

        $results = $app -> triggerEvent('onContentAfterTitle',
            array('com_tz_portfolio_plus.article', &$item, &$this->params, $offset));
        $item->event->afterDisplayTitle = trim(implode("\n", $results));

        $results = $app -> triggerEvent('onContentBeforeDisplay',
            array('com_tz_portfolio_plus.article', &$item, &$this->params, $offset));
        $item->event->beforeDisplayContent = trim(implode("\n", $results));

        $results = $app -> triggerEvent('onContentAfterDisplay',
            array('com_tz_portfolio_plus.article', &$item, &$this->params, $offset));
        $item->event->afterDisplayContent = trim(implode("\n", $results));

        // Trigger portfolio's plugin
        $results = $app -> triggerEvent('onContentDisplayCommentCount',
            array('com_tz_portfolio_plus.article',&$item,&$item -> params,$offset));
        $item -> event -> contentDisplayCommentCountCount  = trim(implode("\n",$results));

        $results = $app -> triggerEvent('onContentDisplayVote',
            array('com_tz_portfolio_plus.article', &$item, &$item -> params, $offset));
        $item->event->contentDisplayVote = trim(implode("\n", $results));

        $results    = $app -> triggerEvent('onBeforeDisplayAdditionInfo',
            array('com_tz_portfolio_plus.article',
                &$item, &$item -> params, $offset));
        $item -> event -> beforeDisplayAdditionInfo   = trim(implode("\n", $results));

        $results    = $app -> triggerEvent('onAfterDisplayAdditionInfo',
            array('com_tz_portfolio_plus.article',
                &$item, &$item -> params, $offset));
        $item -> event -> afterDisplayAdditionInfo   = trim(implode("\n", $results));

        $results    = $app -> triggerEvent('onContentDisplayMediaType',
            array('com_tz_portfolio_plus.article',
                &$item, &$item -> params, $offset, 'amp'));
        $item -> event -> onContentDisplayMediaType    = trim(implode("\n", $results));

        if($template   = TZ_Portfolio_PlusTemplate::getTemplate(true)){
            $tplparams  = $template -> params;
            if(!$tplparams -> get('use_single_layout_builder',1)){
                $results = $app -> triggerEvent('onContentDisplayArticleView',
                    array('com_tz_portfolio_plus.article',
                        &$item, &$item->params, $offset));
                $item->event->contentDisplayArticleView = trim(implode("\n", $results));
            }
        }

        // Increment the hit counter of the article.
        if (!$this->params->get('intro_only') && $offset == 0) {
            $model = $this->getModel();
            $model->hit();
        }

        foreach($related as $i => &$itemR){
            $itemR -> link   = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($itemR -> slug, $itemR -> catid).$tmpl);

            $media      = $itemR -> media;
            $registry   = new JRegistry;
            $registry -> loadString($media);

            $media              = $registry -> toObject();
            $itemR -> media     = $media;

            $itemR -> event = new stdClass();
            $results    = $app -> triggerEvent('onContentDisplayMediaType',array('com_tz_portfolio_plus.article',
                &$itemR, &$item -> params, $offset, 'related'));

            if($itemR) {
                $itemR->event->onContentDisplayMediaType = trim(implode("\n", $results));

                $itemR->mediatypes = $mediatypes;
            }else{

                unset($related[$i]);
            }
        }

        $this -> itemsRelated   = $related;

        // Get article's extrafields
        JLoader::import('extrafields', COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH);
        $extraFields    = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraFields($this -> item, $params,
            false, array('filter.detail_view' => true));
        $this -> item -> extrafields    = $extraFields;

        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->item->params->get('pageclass_sfx'));

//        $this->_prepareDocument();

        $this -> generateLayout($item,$params,$dispatcher);

//        if($this -> generateLayout){
//            $this -> document -> addStyleSheet(TZ_Portfolio_PlusUri::base(true)
//                . '/bootstrap/css/bootstrap.min.css', array('version' => 'auto'));
//        }
//
//        $this -> document -> addStyleSheet('components/com_tz_portfolio_plus/css/tzportfolioplus.min.css'
//            , array('version' => 'auto'));

        parent::display($tpl);

    }

//    /**
//     * Prepares the document
//     */
//    protected function _prepareDocument()
//    {
//        $app	= JFactory::getApplication();
//        $menus	= $app->getMenu();
//        $pathway = $app->getPathway();
//        $title = null;
//
//        // Because the application sets a default page title,
//        // we need to get it from the menu item itself
//        $menu = $menus->getActive();
//        if ($menu)
//        {
//            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
//        }
//        else
//        {
//            $this->params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
//        }
//
//        $title = $this->params->get('page_title', '');
//
//        $id = null;
//        if($menu && isset($menu -> query) && isset($menu -> query['id'])) {
//            $id = (int)@$menu->query['id'];
//        }
//
//        // if the menu item does not concern this article
//        if ($menu && ($menu->query['option'] != 'com_tz_portfolio_plus' || $menu->query['view'] != 'article' || $id != $this->item->id))
//        {
//            // If this is not a single article menu item, set the page title to the article title
//            if ($this->item->title) {
//                $title = $this->item->title;
//            }
//            $path = array(array('title' => $this->item->title, 'link' => ''));
//            $category = JCategories::getInstance('Content')->get($this->item->catid);
//            while ($category && ($menu->query['option'] != 'com_tz_portfolio_plus' || $menu->query['view'] == 'article' || $id != $category->id) && $category->id > 1)
//            {
//                $path[] = array('title' => $category->title, 'link' => TZ_Portfolio_PlusHelperRoute::getCategoryRoute($category->id));
//                $category = $category->getParent();
//            }
//            $path = array_reverse($path);
//            foreach($path as $item)
//            {
//                $pathway->addItem($item['title'], $item['link']);
//            }
//        }
//
//        // Check for empty title and add site name if param is set
//        if (empty($title)) {
//            $title = $app->getCfg('sitename');
//        }
//        elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
//            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
//        }
//        elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
//            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
//        }
//        if (empty($title)) {
//            $title = $this->item->title;
//        }
//        if(!empty($title)){
//            $title  = htmlspecialchars($title);
//        }
//        $this->document->setTitle($title);
//
//        $description    = null;
//        if ($this->item->metadesc){
//            $description    = $this -> item -> metadesc;
//        }elseif(!empty($this -> item -> introtext)){
//            $description    = strip_tags($this -> item -> introtext);
//            $description    = explode(' ',$description);
//            $description    = array_splice($description,0,25);
//            $description    = trim(implode(' ',$description));
//            if(!strpos($description,'...'))
//                $description    .= '...';
//        }elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
//        {
//            $description    = $this -> params -> get('menu-meta_description');
//        }
//
//        if($description){
//            $description    = htmlspecialchars($description);
//            $this -> document -> setDescription($description);
//        }
//
//        $tags   = null;
//
//        if ($this->item->metakey)
//        {
//            $tags   = $this->item->metakey;
//        }elseif($this -> listTags){
//            foreach($this -> listTags as $tag){
//                $tags[] = $tag -> alias;
//            }
//            $tags   = implode(',',$tags);
//        }
//        elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
//        {
//            $tags   = $this->params->get('menu-meta_keywords');
//        }
//
//        if ($this->params->get('robots'))
//        {
//            $this->document->setMetadata('robots', $this->params->get('robots'));
//        }
//
//        if ($app->getCfg('MetaAuthor') == '1')
//        {
//            $this->document->setMetaData('author', $this->item->author);
//        }
//
//        // Set metadata tags with prefix property "og:"
//        $this -> document -> addCustomTag('<meta property="og:title" content="'.$title.'"/>');
//        $this -> document -> addCustomTag('<meta property="og:url" content="'.$this -> item -> fullLink.'"/>');
//        $this -> document -> addCustomTag('<meta property="og:type" content="article"/>');
//        if($description){
//            $this -> document -> addCustomTag('<meta property="og:description" content="'.$description.'"/>');
//        }
//        //// End set meta tags with prefix property "og:" ////
//
//        // Set meta tags with prefix property "article:"
//        $this -> document -> addCustomTag('<meta property="article:author" content="'.$this->item->author.'"/>');
//        $this -> document -> addCustomTag('<meta property="article:published_time" content="'
//            .$this->item->created.'"/>');
//        $this -> document -> addCustomTag('<meta property="article:modified_time" content="'
//            .$this->item->modified.'"/>');
//        $this -> document -> addCustomTag('<meta property="article:section" content="'
//            .$this->escape($this->item->category_title).'"/>');
//        if($tags){
//            $tags   = htmlspecialchars($tags);
//            $this -> document-> setMetaData('keywords',$tags);
//            $this -> document -> addCustomTag('<meta property="article:tag" content="'.$tags.'"/>');
//        }
//        ///// End set meta tags with prefix property "article:" ////
//
//        $mdata = $this->item->metadata->toArray();
//        foreach ($mdata as $k => $v)
//        {
//            if ($v)
//            {
//                $this->document->setMetadata($k, $v);
//            }
//        }
//
//        // If there is a pagebreak heading or title, add it to the page title
//        if (!empty($this->item->page_title))
//        {
//            $this->item->title = $this->item->title . ' - ' . $this->item->page_title;
//            $this->document->setTitle($this->item->page_title . ' - ' . JText::sprintf('PLG_CONTENT_PAGEBREAK_PAGE_NUM', $this->state->get('list.offset') + 1));
//        }
//
//        if ($this->print)
//        {
//            $this->document->setMetaData('robots', 'noindex, nofollow');
//        }
//    }

    protected function FindUserItemId($_userid=null){
        $app		= JFactory::getApplication();
        $menus		= $app->getMenu('site');
        $active     = $menus->getActive();
        if($_userid){
            $userid    = intval($_userid);
        }

        $component	= JComponentHelper::getComponent('com_tz_portfolio_plus');
        $items		= $menus->getItems('component_id', $component->id);

        if($this -> params -> get('user_menu_active') && $this -> params -> get('user_menu_active') != 'auto'){
            return $this -> params -> get('user_menu_active');
        }

        foreach ($items as $item)
        {
            if (isset($item->query) && isset($item->query['view'])) {
                $view = $item->query['view'];

                if (isset($item -> query['created_by'])) {
                    if ($item->query['created_by'] == $userid) {
                        return $item -> id;
                    }
                }
                else{
                    if($item -> home == 1){
                        $homeId = $item -> id;
                    }
                }
            }
        }

        if(!isset($active -> id)) {
            if(isset($homeId)){
                return $homeId;
            } else {
                return 0;
            }
        }

        return $active -> id;
    }
}
