<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2017 tzportfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum

# Family website: http://www.templaza.com

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

class TZ_Portfolio_PlusViewAcls extends JViewLegacy{

    protected $items    = null;
    protected $sidebar  = null;

    public function display($tpl = null){

        TZ_Portfolio_PlusHelper::addSubmenu('acls');

        $this -> addToolbar();

        if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
            $this->sidebar = JHtmlSidebar::render();
        }

        $this -> items  = $this -> get('Items');

        parent::display($tpl);
    }

    protected function addToolbar(){

        $user   = TZ_Portfolio_PlusUser::getUser();

        // Get the results for each action.
        $canDo = TZ_Portfolio_PlusHelper::getActions('com_tz_portfolio_plus');

        JToolbarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_ACL_MANAGER'), 'lock');

        if ($canDo->get('core.edit' ) || $canDo -> get('core.edit.own')) {
            JToolBarHelper::editList('acl.edit');
        }

        if ($user->authorise('core.admin', 'com_tz_portfolio_plus')
            || $user->authorise('core.options', 'com_tz_portfolio_plus')) {
            JToolBarHelper::preferences('com_tz_portfolio_plus');
        }

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');
    }
}