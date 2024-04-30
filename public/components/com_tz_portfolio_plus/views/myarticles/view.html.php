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

JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/helpers/html');

class TZ_Portfolio_PlusViewMyArticles extends JViewLegacy
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
    protected $itemCategories   = null;

    function __construct($config = array()){
        $this -> item           = new stdClass();
        parent::__construct($config);
    }

    public function display($tpl=null){

        $this->items		    = $this->get('Items');
        $this->pagination	    = $this->get('Pagination');
        $this->state		    = $this->get('State');
        $this->authors		    = $this->get('Authors');
        $this -> filterForm     = $this -> get('FilterForm');
        $this -> activeFilters  = $this -> get('ActiveFilters');

        $params = $this -> state -> get('params');


//        $user   = JFactory::getUser();
//        $app    = JFactory::getApplication();
//
//        $filterPublished    = $this -> state -> get('filter.published');

//        if(!is_array($filterPublished) && $filterPublished == 3
//            && !$user -> authorise('core.approve', 'com_tz_portfolio_plus')){
//            $app->enqueueMessage(JText::_('COM_TZ_PORTFOLIO_PLUS_NO_PERMISSION_TO_MODERATE_ARTICLE'), 'error');
//            $app->setHeader('status', 500, true);
//            return false;
//        }

        $this -> params = $params;

        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx', ''));


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
    }
}