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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
JHtml::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH.DIRECTORY_SEPARATOR.'html');

class TZ_Portfolio_PlusViewFields extends JViewLegacy
{
    protected $items        = null;
    protected $state        = null;
    protected $pagination   = null;
    protected $sidebar      = null;

    public function display($tpl = null){

        $this -> items          = $this -> get('Items');
        $this -> state          = $this -> get('State');
        $this -> pagination     = $this -> get('Pagination');
        $this -> filterForm     = $this -> get('FilterForm');
        $this -> activeFilters  = $this -> get('ActiveFilters');

        TZ_Portfolio_PlusHelper::addSubmenu('fields');

        $this -> addToolbar();

        if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
            $this->sidebar = JHtmlSidebar::render();
        }

        parent::display($tpl);
    }

    protected function addToolbar(){

        $user   = TZ_Portfolio_PlusUser::getUser();

        // Get the results for each action.
        $canDo = TZ_Portfolio_PlusHelper::getActions('com_tz_portfolio_plus','group',
            $this->state->get('com_tz_portfolio_plus.filter.group'));

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_FIELDS_MANAGER'),'file-2');

        $canCreate  = $canDo -> get('core.create') || count($user->getAuthorisedFieldGroups('core.create')) > 0;
        $canEdit    = $canDo->get('core.edit' ) || $canDo -> get('core.edit.own');
        if($canCreate) {
            JToolBarHelper::addNew('field.add');
        }
        if ($canEdit) {
            JToolBarHelper::editList('field.edit');
        }

        $canEditState   = $canDo->get('core.edit.state') || $canDo->get('core.edit.state.own');
        $canDelete      = $canDo->get('core.delete') || $canDo->get('core.delete.own');

        if($canEditState) {
            JToolBarHelper::publish('fields.publish', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::unpublish('fields.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            JToolbarHelper::checkin('fields.checkin');
        }

        if($canDelete) {
            JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_PLUS_QUESTION_DELETE'), 'fields.delete');
        }

        if ($user->authorise('core.admin', 'com_tz_portfolio_plus')
            || $user->authorise('core.options', 'com_tz_portfolio_plus')) {
            JToolBarHelper::preferences('com_tz_portfolio_plus');
        }

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,'https://www.tzportfolio.com/document/administration/30-how-to-use-fields-in-tz-portfolio-plus.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');
    }
}