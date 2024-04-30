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

use Joomla\CMS\Factory;

class TZ_Portfolio_PlusViewAcl extends JViewLegacy{

    protected $item     = null;
    protected $form     = null;
    protected $state    = null;

    public function display($tpl = null){

        TZ_Portfolio_PlusHelper::addSubmenu('acls');

        $this -> state = $this -> get('State');
        $this -> item  = $this -> get('Item');
        $this -> form  = $this -> get('Form');

        $this -> addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(){

        Factory::getApplication()->input->set('hidemainmenu', true);

        // Get the results for each action.
        $canDo      = TZ_Portfolio_PlusHelper::getActions('com_tz_portfolio_plus');

        $section    = $this -> state -> get('acl.section');

        switch ($section){
            default:
                $text   = JText::_('COM_TZ_PORTFOLIO_PLUS_'.strtoupper($section).'S');
                break;
            case 'category':
                $text   = JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES');
                break;
            case 'group':
                $text   = JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_GROUPS');
                break;
            case 'style':
                $text   = JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE_STYLES');
                break;

        }

        JToolbarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_ACL_MANAGER_TASK', JText::_($text)), 'lock');

        if ($canDo->get('core.edit' ) || $canDo -> get('core.edit.own')) {
            JToolbarHelper::apply('acl.apply');
            JToolbarHelper::save('acl.save');
            JToolBarHelper::cancel('acl.cancel');
        }

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');
    }
}