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
JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/helpers');

class TZ_Portfolio_PlusViewLicense extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;

    public function display($tpl=null){

        $this -> state          = $this -> get('State');
        $this -> items          = $this -> get('Items');
        $this -> pagination     = $this -> get('Pagination');

        TZ_Portfolio_PlusHelper::addSubmenu('license');

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this -> addToolbar();
        }

        $this -> sidebar    = JHtmlSidebar::render();

        parent::display($tpl);

    }

    protected function addToolbar(){

        $user   = TZ_Portfolio_PlusUser::getUser();

        // Get the results for each action.
        $canDo = TZ_Portfolio_PlusHelper::getActions('com_tz_portfolio_plus', 'license');

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_TAGS_MANAGER'),'credit');

//        if($canDo -> get('core.create')) {
//            JToolBarHelper::addNew('tag.add');
//        }

//        if ($canDo->get('core.edit' )) {
//            JToolBarHelper::editList('tag.edit');
//        }
//
//        $canEditState   = $canDo->get('core.edit.state');
//        $canDelete      = $canDo->get('core.delete');
//        if($canEditState) {
//            JToolBarHelper::publish('tags.publish', 'JTOOLBAR_PUBLISH', true);
//            JToolBarHelper::unpublish('tags.unpublish', 'JTOOLBAR_UNPUBLISH', true);
//        }
//        if($canDelete) {
//            JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_PLUS_QUESTION_DELETE'), 'tags.delete');
//        }

        if ($user->authorise('core.admin', 'com_tz_portfolio_plus')
            || $user->authorise('core.options', 'com_tz_portfolio_plus')) {
            JToolBarHelper::preferences('com_tz_portfolio_plus');
        }

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document/administration/54-how-to-create-tags-in-tz-portfolio-plus.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');

    }
}