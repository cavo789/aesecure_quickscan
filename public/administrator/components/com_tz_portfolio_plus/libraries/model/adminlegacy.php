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

use Joomla\CMS\Form\Form;

if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
    class TZ_Portfolio_PlusModelAdmin extends JModelAdmin
    {
        public function getForm($data = array(), $loadData = true)
        {
            return parent::getForm($data, $loadData);
        }

        protected function preprocessForm(\JForm $form, $data, $group = 'content')
        {
            parent::preprocessForm($form, $data, $group);
        }
    }
}else{
    class TZ_Portfolio_PlusModelAdmin extends JModelAdmin
    {
        public function getForm($data = array(), $loadData = true)
        {
            return parent::getForm($data, $loadData);
        }

        protected function preprocessForm(Form $form, $data, $group = 'content')
        {
            parent::preprocessForm($form, $data, $group);
        }
    }

}