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
 
//no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.event.dispatcher');

//JHtml::addIncludePath(COM_TZ_PORTFOLIO_PLUS_PATH_SITE . '/helpers/html');

class TZ_Portfolio_PlusViewPortfolio extends JViewLegacy
{
    protected $char             = null;
    protected $item             = null;
    protected $items            = null;
    protected $media            = null;
    protected $state            = null;
    protected $params           = null;
    protected $Itemid           = null;
    protected $lang_sef         = '';
    protected $tagAbout         = null;
    protected $ajaxLink         = null;
    protected $itemTags         = null;
    protected $pagination       = null;
    protected $authorAbout      = null;
    protected $availLetter      = null;
    protected $categoryAbout    = null;
    protected $itemCategories   = null;

    protected $categories       = array();
    protected $parentCategory;
    protected $filterSubCategory;

    function __construct($config = array()){
        $this -> item           = new stdClass();
        parent::__construct($config);
    }

    function display($tpl=null){

        $app        = JFactory::getApplication('site');
        $input      = $app -> input;
        $config     = JFactory::getConfig();
        if($config -> get('sef')){
            $language   = JLanguageHelper::getLanguages('lang_code');
        }else{
            $language   = JLanguageHelper::getLanguages('sef');
        }

        $menus		= JMenu::getInstance('site');
        $active     = $menus->getActive();

        $doc            = JFactory::getDocument();

        $params         = null;
        $state          = $this -> get('State');

        $this -> state  = $state;
        $params         = $state -> get('params');

        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
            $params -> set('show_cat_email_icon', 0);
        }

        // Get filter tag information
        if($tagId = $state -> get('filter.tagId')) {
            $this -> tagAbout   = TZ_Portfolio_PlusFrontHelperTags::getTagById($tagId);
        }

        // Get filter category information
        $categoryId = $state -> get('filter.category_id');

        if(!$categoryId && ($param_catIds = $params -> get('catid'))){
            $param_catIds   = array_filter($param_catIds);
            $countCat       = count($param_catIds);
            if($countCat && $countCat == 1){
                $categoryId = $param_catIds[0];
            }
        }

        if($categoryId) {
            $this -> categoryAbout   = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesById($categoryId);
        }

        if($params -> get('tz_show_filter', 1) && $params -> get('show_all_filter', 1)
            && $params -> get('tz_filter_type', 'categories') == 'categories'){
            $categories	= JCategories::getInstance('TZ_Portfolio_Plus', array(
                'countItems'    => true,
                'filter.id'     => $params -> get('catid', array())));
            $parent    = $categories->get('root');

            if($parent) {
                $this->categories = array($parent->id => $parent->getChildren());
                $this->parentCategory = $parent;
                $this->maxLevelcat = $parent;
            }
        }

        // Get filter user information
        if(($authorId = $state -> get('filter.userId')) &&
            ($author = JFactory::getUser($state -> get('filter.userId')))){

            TZ_Portfolio_PlusPluginHelper::importPlugin('users');
            $results = $app -> triggerEvent('onContentDisplayAuthorAbout', array(
                'com_tz_portfolio_plus.portfolio',
                $authorId,
                &$params));
            $this -> authorAbout    = trim(implode("\n", $results));
        }

        // Create ajax link
        $this -> ajaxLink   = JURI::root().'index.php?option=com_tz_portfolio_plus&amp;view=portfolio&amp;task=portfolio.ajax'
            .'&amp;layout=default:item'.(($state -> get('filter.char'))?'&amp;char='.$state -> get('filter.char'):'')
            .($state -> get('filter.category_id')?'&amp;id='.$state -> get('filter.category_id'):'')
            .(($uid = $state -> get('filter.userId'))?'&amp;uid='.$uid:'')
            .(($tid = $state -> get('filter.tagId'))?'&amp;tid='.$tid:'')
            .(($searchword = $state -> get('filter.searchword'))?'&amp;searchword='.$searchword:'');

        if($active) {
            $this->ajaxLink .= '&amp;Itemid=' . $active->id;
        }
        $this -> ajaxLink   .=  '&amp;page=2';

        $doc -> addStyleSheet('components/com_tz_portfolio_plus/css/isotope.min.css', array('version' => 'auto'));
        $this -> document -> addScript('components/com_tz_portfolio_plus/js/jquery.isotope.min.js', array('version' => 'auto', 'relative' => true));

        if($params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'ajaxButton'
            || $params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'ajaxInfiScroll'){
            $this -> document -> addScript('components/com_tz_portfolio_plus/js/jquery.infinitescroll.min.js', array('version' => 'auto', 'relative' => true));

            if($params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'ajaxButton'){
                $doc->addStyleDeclaration('
                    #infscr-loading {
                        position: absolute;
                        padding: 0;
                        left: 35%;
                        bottom:0;
                        background:none;
                    }
                    #infscr-loading div,#infscr-loading img{
                        display:inline-block;
                    }
                ');
            }
            if($params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'ajaxInfiScroll'){
                $doc->addStyleDeclaration('
                    #tz_append{
                        cursor: auto;
                    }
                    #tz_append a{
                        color:#000;
                        cursor:auto;
                    }
                    #tz_append a:hover{
                        color:#000 !important;
                    }
                    #infscr-loading {
                        position: absolute;
                        padding: 0;
                        left: 38%;
                        bottom:-35px;
                    }

                ');
            }
        }

        $availableItem  =   $this->get('AvailableItem');
        $availableItem  ?   $doc -> addScriptDeclaration('var tzItemAvailable = 1;') : $doc -> addScriptDeclaration('var tzItemAvailable = 0;');

        $total  = $this -> get('Total');

	    $doc -> addScriptDeclaration('
	        (function($, window){
                window.TZ_Portfolio_Plus.infiniteScroll    = $.extend({},TZ_Portfolio_Plus.infiniteScroll, {
                    displayNoMorePageLoad: '.$params->get('tz_show_no_more_page', 0).',
                    noMorePageLoadText: "'.$params->get('tz_no_more_page_text', 'No more items to load').'",
                    countItems: '.($total?$total:0).'
                });
            })(jQuery, window);
		');

        $this -> document -> addScript('components/com_tz_portfolio_plus/js/tz_portfolio_plus.min.js',
            array('version' => 'auto', 'relative' => true));

        $list   = $this -> get('Items');
        
        if($params -> get('show_all_filter',0) && $params -> get('tz_portfolio_plus_layout', 'ajaxButton') != 'default'){
            if(!$this -> itemTags) {
                $this->itemTags = $this->get('AllTags');
            }
            if(!$this -> itemCategories) {
                $this->itemCategories = $this->get('AllCategories');
            }
        }
        else{
            if(!$this -> itemTags) {
                $this -> itemTags       = $this -> get('TagsByArticle');
            }
            if(!$this -> itemCategories) {
                $this->itemCategories = $this->get('CategoriesByArticle');
            }
        }
        //Escape strings for HTML output
        $this->pageclass_sfx = $params->get('pageclass_sfx') ? htmlspecialchars($params->get('pageclass_sfx', '')) : '';

        if ($active)
        {
            $params->def('page_heading', $params->get('page_title', $active->title));
        }
        else
        {
            $params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
        }

        $lang   = JFactory::getLanguage();
        if($lang -> isRtl()){
            $doc -> addStyleDeclaration('
            .isotope .isotope-item {
                -webkit-transition-property: right, top, -webkit-transform, opacity;
                -moz-transition-property: right, top, -moz-transform, opacity;
                -ms-transition-property: right, top, -ms-transform, opacity;
                -o-transition-property: right, top, -o-transform, opacity;
                transition-property: right, top, transform, opacity;
            }');
        }

        $this -> items          = $list;
        $this -> params         = $params;
        $this -> pagination     = $this -> get('Pagination');
        $this->Itemid           = $active?$active->id:$input -> getInt('Itemid');
        $this -> char           = $state -> get('filter.char');
        $this -> availLetter    = $this -> get('AvailableLetter');

        $this -> _prepareDocument();

        // Add feed links
		if ($params->get('show_feed_link', 1)) {
			$link = '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$doc->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$doc->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
		}

        parent::display($tpl);
    }

    protected function _prepareDocument()
    {
        $app    = JFactory::getApplication();
        $title  = $this->params->get('page_title', '');

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

        if ($this->params->get('menu-meta_description'))
        {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords'))
        {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots'))
        {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }

        $app        = \JFactory::getApplication();
        $menus      = $app->getMenu();
        $menu       = $menus->getActive();
        $pathway    = $app->getPathway();

        $id = $this -> state -> get('filter.category_id');

        $catIds = $this -> params -> get('catid', array());
        $catIds = array_filter($catIds);
        $catIds = array_values($catIds);

        if ($menu && ($menu->query['option'] !== 'com_tz_portfolio_plus' || $menu->query['view'] === 'article'
                || ($id && count($catIds) && !in_array($id, $catIds)) ))
        {
            $mcategory  = JCategories::getInstance('TZ_Portfolio_Plus')->get($id);
            $path       = array(array('title' => $mcategory -> title, 'link' => ''));
            $category   = $mcategory -> getParent();

            while (!empty($category) &&
                ($menu->query['option'] !== 'com_tz_portfolio_plus' || $menu->query['view'] === 'article' || $id != $category->id)
                && $category->id > 1)
            {
                $path[] = array('title' => $category->title, 'link' => TZ_Portfolio_PlusHelperRoute::getCategoryRoute($category->id));
                $category = $category->getParent();
            }

            $path = array_reverse($path);

            foreach ($path as $item)
            {
                $pathway -> addItem($item['title'], $item['link']);
            }
        }
    }
}