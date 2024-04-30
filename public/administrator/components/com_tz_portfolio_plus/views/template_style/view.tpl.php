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

class TZ_Portfolio_PlusViewTemplate_Style extends JViewLegacy
{
    protected $state;
    protected $rowItem      = null;
    protected $rowOuter     = null;
    protected $columnItem   = null;
    protected $rowInColumn  = false;

    public function display($tpl=null)
    {
        $this -> state  = $this -> get('State');

        parent::display($tpl);
        $app    = Factory::getApplication();
        $app -> close();
    }
    protected function get_value($item, $method){
        if (!isset($item -> $method)) {
            if (preg_match('/offset/', $method)) {
                return isset($item -> offset) ? $item -> offset : '';
            }
            if (preg_match('/col/', $method)) {
                return isset($item -> span) ? $item -> span : '12';
            }
        }
        return isset($item -> $method) ? $item -> $method : '';
    }

    protected function get_color($item, $method){
        return isset($item -> $method) ? $item -> $method : 'rgba(255, 255, 255, 0)';
    }
}