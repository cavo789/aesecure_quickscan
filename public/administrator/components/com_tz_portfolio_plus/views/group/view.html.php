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

jimport('joomla.application.components.view');

class TZ_Portfolio_PlusViewGroup extends JViewLegacy
{
    protected $item     = null;
    protected $form     = null;
    protected $canDo    = null;

    function display($tpl = null){
        $this -> form   = $this -> get('Form');
        $this -> item   = $this -> get('Item');
        $canDo	        = TZ_Portfolio_PlusHelper::getActions(COM_TZ_PORTFOLIO_PLUS, 'group'
            , $this -> item -> id);
        $this -> canDo	= $canDo;

        $this -> addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(){
        Factory::getApplication()->input->set('hidemainmenu', true);

        $user	    = TZ_Portfolio_PlusUser::getUser();
        $canDo      = $this -> canDo;
        $userId     = $user -> id;
        $isNew      = ($this -> item -> id == 0);
        $checkedOut = !($this -> item -> checked_out == 0 || $this -> item -> checked_out == $userId);

        JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_GROUP_FIELDS_MANAGER_TASK',
            JText::_(($isNew)?'COM_TZ_PORTFOLIO_PLUS_PAGE_ADD_GROUP_FIELD':'COM_TZ_PORTFOLIO_PLUS_PAGE_EDIT_GROUP_FIELD')),'folder-plus-2');

        if($isNew){
            if ($canDo->get('core.create')) {
                JToolBarHelper::apply('group.apply');
                JToolBarHelper::save('group.save');
                JToolbarHelper::save2new('group.save2new');
                JToolBarHelper::cancel('group.cancel');
            }
        }else{
            if(!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.edit.own')
                        && $this -> item -> created_by == $userId))){
                JToolbarHelper::apply('group.apply');
                JToolbarHelper::save('group.save');

                if ($canDo->get('core.create'))
                {
                    JToolbarHelper::save2new('group.save2new');
                }
            }
            JToolBarHelper::cancel('group.cancel', JText::_('JTOOLBAR_CLOSE'));
        }

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document/administration/29-how-to-use-group-fields-in-tz-portfolio-plus.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');
    }
}