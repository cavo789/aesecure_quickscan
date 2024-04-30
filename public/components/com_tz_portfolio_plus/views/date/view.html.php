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

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\BaseModel;

jimport('joomla.application.component.view');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JLoader::import('com_tz_portfolio_plus.helpers.article', JPATH_SITE.DIRECTORY_SEPARATOR.'components');
JLoader::import('com_tz_portfolio_plus.helpers.extrafields', JPATH_SITE.DIRECTORY_SEPARATOR.'components');

class TZ_Portfolio_PlusViewDate extends JViewLegacy{

    protected $items    = null;
    protected $item     = null;
    protected $state    = null;
    protected $params   = null;
    protected $pagination;

    protected $lead_items   = array();
    protected $intro_items  = array();
    protected $link_items   = array();
    protected $columns      = 1;
    protected $category;
    protected $char         = null;
    protected $availLetter  = null;

    function __construct($config = array()){
        parent::__construct($config);
    }

    public function display($tpl = null){
        $app	= JFactory::getApplication();

        // Get some data from the models
        $state		= $this->get('State');
        $params		= $state->params;

        // Set value again for option tz_portfolio_plus_redirect
        if($params -> get('tz_portfolio_plus_redirect') == 'default'){
            $params -> set('tz_portfolio_plus_redirect','article');
        }

        $items		= $this->get('Items');
        $parent		= $this->get('Parent');
        $pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        // Check whether category access level allows access.
        $user	= JFactory::getUser();

        $content_ids    = array();
        if($items) {
            $content_ids    = ArrayHelper::getColumn($items, 'id');
        }

        $tags   = null;
        if(count($content_ids) && $params -> get('show_tags',1)) {
            $tags = TZ_Portfolio_PlusFrontHelperTags::getTagsByArticleId($content_ids, array(
                    'orderby' => 'm.contentid',
                    'reverse_contentid' => true
                )
            );
        }

        $_params    = null;

        $mainCategories     = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesByArticleId($content_ids,
            array('main' => true));
        $second_categories  = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesByArticleId($content_ids,
            array('main' => false));

        JPluginHelper::importPlugin('content');
        TZ_Portfolio_PlusPluginHelper::importPlugin('content');
        TZ_Portfolio_PlusPluginHelper::importPlugin('mediatype');

        $app -> triggerEvent('onAlwaysLoadDocument', array('com_tz_portfolio_plus.date'));
        $app -> triggerEvent('onLoadData', array('com_tz_portfolio_plus.date', $items, $params));

        $mediatypes = array();
        if($results    = $app -> triggerEvent('onAddMediaType')){
            foreach($results as $result){
                if(isset($result -> special) && $result -> special) {
                    $mediatypes[] = $result -> value;
                }
            }
        }

        $groups	= $user->getAuthorisedViewLevels();

        for ($i = 0, $n = count($items); $i < $n; $i++)
        {
            $item = &$items[$i];

            $item->params   = clone($params);

            $app -> triggerEvent('onTPContentBeforePrepare', array('com_tz_portfolio_plus.date',
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
                $item -> category_link      = TZ_Portfolio_PlusHelperRoute::getCategoryRoute($item -> catid);

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

            /*** New source ***/
            $tmpl   = null;
            if($item -> params -> get('tz_use_lightbox',0)){
                $tmpl   = '&tmpl=component';
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

            // Create article link
            $item ->link        = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item -> slug, $item -> catid).$tmpl);
            $item -> fullLink   = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item -> slug, $item -> catid), true, $ssl);

            // Create author link
            $item -> author_link    = JRoute::_(TZ_Portfolio_PlusHelperRoute::getUserRoute($item -> created_by,
                $params -> get('user_menu_active','auto')));

            // No link for ROOT category
            if ($item->parent_alias == 'root') {
                $item->parent_slug = null;
            }

            $item->event = new stdClass();

            // Old plugins: Ensure that text property is available
            if (!isset($item->text))
            {
                $item->text = $item->introtext;
            }
            //Call trigger in group content
            $results = $app -> triggerEvent('onContentPrepare', array ('com_tz_portfolio_plus.date',
                &$item, &$item->params, $state -> get('list.start')));
            $item->introtext = $item->text;

            $results = $app -> triggerEvent('onContentAfterTitle', array('com_tz_portfolio_plus.date',
                &$item, &$item->params, $state -> get('list.start')));
            $item->event->afterDisplayTitle = trim(implode("\n", $results));

            $results = $app -> triggerEvent('onContentBeforeDisplay', array('com_tz_portfolio_plus.date',
                &$item, &$item->params, $state -> get('list.start')));
            $item->event->beforeDisplayContent = trim(implode("\n", $results));

            $results = $app -> triggerEvent('onContentAfterDisplay', array('com_tz_portfolio_plus.date',
                &$item, &$item->params, $state -> get('list.start')));
            $item->event->afterDisplayContent = trim(implode("\n", $results));

            // Process the tz portfolio's content plugins.
            $results    = $app -> triggerEvent('onContentDisplayVote',array('com_tz_portfolio_plus.date',
                &$item, &$item->params, $state -> get('list.start')));
            $item -> event -> contentDisplayVote   = trim(implode("\n", $results));

            $results    = $app -> triggerEvent('onBeforeDisplayAdditionInfo',array('com_tz_portfolio_plus.date',
                &$item, &$item->params, $state -> get('list.start')));
            $item -> event -> beforeDisplayAdditionInfo   = trim(implode("\n", $results));

            $results    = $app -> triggerEvent('onAfterDisplayAdditionInfo',array('com_tz_portfolio_plus.date',
                &$item, &$item->params, $state -> get('list.start')));
            $item -> event -> afterDisplayAdditionInfo   = trim(implode("\n", $results));

            $results    = $app -> triggerEvent('onContentDisplayListView',array('com_tz_portfolio_plus.date',
                &$item, &$item->params, $state -> get('list.start')));
            $item -> event -> contentDisplayListView   = trim(implode("\n", $results));

            //Call trigger in group tz_portfolio_plus_mediatype
            if($item) {
                $results = $app -> triggerEvent('onContentDisplayMediaType', array('com_tz_portfolio_plus.date',
                    &$item, &$item->params, 0));
                if($item) {
                    $item->event->onContentDisplayMediaType = trim(implode("\n", $results));
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

            // Get article's extrafields
            $extraFields    = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraFields($item, $item -> params,
                false, array('filter.list_view' => true, 'filter.group' => $params -> get('order_fieldgroup', 'rdate')));
            $item -> extrafields    = $extraFields;

            $access = $state -> get('filter.access');

            if ($access) {
                // If the access filter has been set, we already have only the articles this user can view.
                $item->params->set('access-view', true);
            }
            else {
                // If no access filter is set, the layout takes some responsibility for display of limited information.
                if ($item->catid == 0 || $item->category_access === null) {
                    $item->params->set('access-view', in_array($item->access, $groups));
                }
                else {
                    $item->params->set('access-view', in_array($item->access, $groups) && in_array($item->category_access, $groups));
                }
            }

            $app -> triggerEvent('onTPContentAfterPrepare', array('com_tz_portfolio_plus.date',
                &$item, &$item -> params, $state -> get('list.start')));
        }

        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx', ''));

        $this -> state      = $state;
        $this -> params     = $params;
        $this -> items      = $items;
        $this -> pagination = $pagination;

        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
            BaseModel::addIncludePath(COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'models', 'TZ_Portfolio_PlusModel');
            $model  = BaseModel::getInstance('Portfolio','TZ_Portfolio_PlusModel',array('ignore_request' => true));
        }else{
            JModelLegacy::addIncludePath(COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'models', 'TZ_Portfolio_PlusModel');
            $model  = JModelLegacy::getInstance('Portfolio','TZ_Portfolio_PlusModel',array('ignore_request' => true));
        }

        $pParams    = clone($params);
        $pParams -> set('tz_catid',$params -> get('tz_catid',array()));
        $model -> setState('params',$pParams);
        $model -> setState('filter.year',$state -> get('filter.year'));
        $model -> setState('filter.month',$state -> get('filter.month'));
        $this -> char           = $state -> get('filter.char');
        $this -> availLetter    = $model -> getAvailableLetter();

        // Add feed links
        if ($this->params->get('show_feed_link', 1)) {
            $link = '&format=feed&limitstart=';
            $attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
            $this->document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
            $attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
            $this->document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
        }

        parent::display($tpl);
    }

    protected function _prepareDocument()
    {
        $app		= JFactory::getApplication();
        $menus		= $app->getMenu();
        $pathway	= $app->getPathway();
        $title		= null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();

        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }
        else {
            $this->params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
        }

        $id = (int) @$menu->query['id'];

        if ($menu && ($menu->query['option'] != 'com_tz_portfolio_plus' || $menu->query['view'] == 'article' || $id != $this->category->id)) {
            $path = array(array('title' => $this->category->title, 'link' => ''));
            $category = $this->category->getParent();

            while (($menu->query['option'] != 'com_tz_portfolio_plus' || $menu->query['view'] == 'article' || $id != $category->id) && $category->id > 1)
            {
                $path[] = array('title' => $category->title, 'link' => TZ_Portfolio_PlusHelperRoute::getCategoryRoute($category->id));
                $category = $category->getParent();
            }

            $path = array_reverse($path);

            foreach ($path as $item)
            {
                $pathway->addItem($item['title'], $item['link']);
            }
        }

        $title = $this->params->get('page_title', '');

        if (empty($title)) {
            $title = $app->getCfg('sitename');
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }

        $this->document->setTitle($title);

        if ($this->category->metadesc)
        {
            $this->document->setDescription($this->category->metadesc);
        }
        elseif (!$this->category->metadesc && $this->params->get('menu-meta_description'))
        {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->category->metakey)
        {
            $this->document->setMetadata('keywords', $this->category->metakey);
        }
        elseif (!$this->category->metakey && $this->params->get('menu-meta_keywords'))
        {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots'))
        {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }

        if ($app->getCfg('MetaAuthor') == '1') {
            $this->document->setMetaData('author', $this->category->getMetadata()->get('author'));
        }

        $mdata = $this->category->getMetadata()->toArray();

        foreach ($mdata as $k => $v)
        {
            if ($v) {
                $this->document->setMetadata($k, $v);
            }
        }

        // Add feed links
        if ($this->params->get('show_feed_link', 1)) {
            $link = '&format=feed&limitstart=';
            $attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
            $this->document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
            $attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
            $this->document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
        }
    }

    protected function FindUserItemId($_userid=null){
        $app		= JFactory::getApplication();
        $menus		= $app->getMenu('site');
        $active     = $menus->getActive();
        $homeId     = null;
        $userid     = null;
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

        if(!isset($active -> id) && $homeId){
            return $homeId;
        }

        if($active && isset($active -> id))
            return $active -> id;
        return null;
    }
}