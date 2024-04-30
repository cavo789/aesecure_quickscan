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

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

jimport('joomla.application.component.view');

class TZ_Portfolio_PlusViewField extends JViewLegacy
{
    protected $state    = null;
    protected $form     = null;
    protected $item     = null;
    protected $canDo    = null;
    protected $groups   = null;

    public function display($tpl = null){
        $this -> state  = $this -> get('State');
        $this -> form   = $this -> get('Form');
        $this -> item   = $this -> get('Item');
        $this -> canDo	= TZ_Portfolio_PlusHelper::getActions(COM_TZ_PORTFOLIO_PLUS, 'field'
            , $this -> item -> id);

        BaseDatabaseModel::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR.'models','TZ_Portfolio_PlusModel');
        $groupModel = BaseDatabaseModel::getInstance('Groups','TZ_Portfolio_PlusModel',array('ignore_request' => true));
        if($groupModel) {
            $groupModel->setState('filter_order', 'name');
            $groupModel->setState('filter_order_Dir', 'ASC');

            $this->groups = $groupModel->getItems();
        }

        if($this -> item -> id == 0){
            $this -> item -> published = 'P';
        }
        else{
            if($this -> item -> published == 1){
                $this -> item -> published  = 'P';
            }
            else{
                $this -> item -> published  = 'U';
            }
        }

        $this -> addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(){

        Factory::getApplication()->input->set('hidemainmenu', true);

        $user	    = TZ_Portfolio_PlusUser::getUser();
        $userId     = $user -> id;
        $isNew      = ($this -> item -> id == 0);
        $canDo      = $this -> canDo;
        $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

        JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_FIELDS_MANAGER_TASK',
            JText::_(($isNew)?'COM_TZ_PORTFOLIO_PLUS_PAGE_ADD_FIELD':'COM_TZ_PORTFOLIO_PLUS_PAGE_EDIT_FIELD')),'file-plus');

        if($isNew) {
            if(count($user->getAuthorisedFieldGroups('core.create')) > 0) {
                JToolBarHelper::apply('field.apply');
                JToolBarHelper::save('field.save');
                JToolBarHelper::save2new('field.save2new');
            }
            JToolBarHelper::cancel('field.cancel');
        }else{
            $canEdit	    = $user->authorise('core.edit',		  'com_tz_portfolio_plus.field.'
                    .$this -> item->id)
                && (count($user -> getAuthorisedFieldGroups('core.edit', $this -> item -> groupid)) > 0);
            $canEditOwn	    = $user->authorise('core.edit.own', 'com_tz_portfolio_plus.field.'.$this -> item->id)
                && $this -> item -> created_by == $user -> id && (count($user -> getAuthorisedFieldGroups('core.edit.own', $this -> item -> groupid)) > 0);

            if (!$checkedOut && ($canEdit || $canEditOwn)) {
                JToolBarHelper::apply('field.apply');
                JToolBarHelper::save('field.save');

                // We can save this record, but check the create permission to see if we can return to make a new one.
                if ($canDo->get('core.create')) {
                    JToolBarHelper::save2new('field.save2new');
                }
            }
            JToolBarHelper::cancel('field.cancel', JText::_('JTOOLBAR_CLOSE'));
        }

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,'https://www.tzportfolio.com/document/administration/30-how-to-use-fields-in-tz-portfolio-plus.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');
    }
}