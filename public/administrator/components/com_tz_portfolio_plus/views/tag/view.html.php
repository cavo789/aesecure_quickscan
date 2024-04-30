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

use Joomla\CMS\Factory;

jimport('joomla.application.component.view');

class TZ_Portfolio_PlusViewTag extends JViewLegacy
{
    protected $item     = null;
    protected $form     = null;
    protected $canDo    = null;

    function display($tpl = null){
        $this -> item   = $this -> get('Item');
        $this -> form   = $this -> get('Form');

        $this -> canDo  = TZ_Portfolio_PlusHelper::getActions(COM_TZ_PORTFOLIO_PLUS, 'tag');

        $this -> addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(){

        Factory::getApplication()->input->set('hidemainmenu', true);

        $canDo  = $this -> canDo;

        $isNew  = ($this -> item -> id == 0);

        JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_TAGS_MANAGER_TASK',
            JText::_(($isNew)?'COM_TZ_PORTFOLIO_PLUS_PAGE_ADD_TAG':'COM_TZ_PORTFOLIO_PLUS_PAGE_EDIT_TAG')), 'tag');
        if($isNew) {
            if($canDo -> get('core.create')) {
                JToolBarHelper::apply('tag.apply');
                JToolBarHelper::save('tag.save');
                JToolBarHelper::save2new('tag.save2new');
                JToolBarHelper::cancel('tag.cancel');
            }
        }else {
            if($canDo -> get('core.edit')) {
                JToolBarHelper::apply('tag.apply');
                JToolBarHelper::save('tag.save');
            }
            if($canDo -> get('core.create')) {
                JToolBarHelper::save2new('tag.save2new');
            }
            JToolBarHelper::cancel('tag.cancel', JText::_('JTOOLBAR_CLOSE'));
        }

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document/administration/54-how-to-create-tags-in-tz-portfolio-plus.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');
    }
}
