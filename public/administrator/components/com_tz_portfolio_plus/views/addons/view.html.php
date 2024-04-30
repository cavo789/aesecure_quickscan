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

use Joomla\CMS\Factory;

jimport('joomla.application.component.view');

JLoader::register('TZ_Portfolio_PlusHelperAddon_Datas', COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH
    .DIRECTORY_SEPARATOR.'addon_datas.php');
JLoader::import('com_tz_portfolio_plus.helpers.addons', JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components');

class TZ_Portfolio_PlusViewAddons extends JViewLegacy
{
    protected $state;
    protected $items;
    protected $templates;
    protected $form;
    protected $sidebar;
    protected $pagination;

    public function display($tpl=null){

        $this->state            = $this->get('State');
        $this->items            = $this->get('Items');
        $this->pagination       = $this->get('pagination');
        $this -> filterForm     = $this->get('FilterForm');
        $this -> activeFilters  = $this->get('ActiveFilters');
        $input                  = Factory::getApplication() -> input;

        TZ_Portfolio_PlusHelper::addSubmenu($this -> getName());

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal' && $this->getLayout() !== 'upload') {
            $this -> addToolbar();
        }
//        elseif($input -> get('ismultiple')) {
//            var_dump($this -> filterForm); die();
//        }

        if ($this->getLayout() !== 'modal') {
            if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
                $this->sidebar = JHtmlSidebar::render();
            }
        }

        parent::display($tpl);
    }

    protected function addToolbar(){


        $user   = TZ_Portfolio_PlusUser::getUser();

        // Get the results for each action.
        $canDo  = TZ_Portfolio_PlusHelper::getActions('com_tz_portfolio_plus', 'addon');


        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_ADDONS_MANAGER'), 'puzzle');

        if ($canDo->get('core.create')) {
            JToolbarHelper::addNew('addon.upload', 'COM_TZ_PORTFOLIO_PLUS_INSTALL_UPDATE');
        }

        if ($canDo->get('core.edit' )) {
            JToolBarHelper::editList('addon.edit');
        }

        if ($canDo->get('core.delete')){
            JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_PLUS_QUESTION_DELETE'),'addon.uninstall','JTOOLBAR_UNINSTALL');
        }

        if ($canDo->get('core.edit.state')) {
            JToolBarHelper::publish($this -> getName().'.publish','JENABLED', true);
            JToolBarHelper::unpublish($this -> getName().'.unpublish','JDISABLED', true);
        }

        if($user->authorise('core.admin', 'com_tz_portfolio_plus')
            || $user->authorise('core.options', 'com_tz_portfolio_plus')){
            JToolBarHelper::preferences('com_tz_portfolio_plus');
        }

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');

        JToolbarHelper::link('javascript:', JText::_('COM_TZ_PORTFOLIO_PLUS_INTRO_GUIDE'), 'support');
    }
}