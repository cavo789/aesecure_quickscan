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
defined('_JEXEC') or die;

class TZ_Portfolio_PlusViewAddon_Datas extends JViewLegacy{

    protected $state;
    protected $addonItem;

    public function display($tpl=null){

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this -> state      = $this -> get('State');
        $this -> addonItem  = $this -> get('AddonItem');

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(){
        JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_ADDONS_MANAGER_TASK','Manager Data'),'puzzle');
        TZ_Portfolio_PlusToolbarHelper::addonDataManager();
    }
}