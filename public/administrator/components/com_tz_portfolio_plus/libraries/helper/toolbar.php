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

use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Toolbar\Toolbar;

JLoader::import('toolbar', JPATH_ADMINISTRATOR.'/includes');

class TZ_Portfolio_PlusToolbarHelper extends JToolbarHelper {

    public static function customHelp($url, $title, $icon = null, $id=null, $width=800, $height=500){

        $bar    = JToolbar::getInstance('toolbar');
        $text   = $title?JText::_($title):JText::_('JTOOLBAR_HELP');
        $doTask = 'Joomla.popupWindow(\''.$url.'\',\''.$text.'\', '.$width.', '.$height.', 1)';
        $id     = $id?$id:'customhelp';

        $layout = new JLayoutFile('toolbar.customhelp');
        $html   = $layout->render(array('doTask' => $doTask, 'text' => $text, 'icon' => $icon, 'id' => $id));
        $bar -> appendButton('Custom', $html, $id);
    }

    public static function draft($task = 'draft', $alt = 'COM_TZ_PORTFOLIO_PLUS_SAVE_DRAFT', $check = false)
    {
        $bar = Toolbar::getInstance('toolbar');

        // Add a publish button.
        $bar->appendButton('Standard', 'pencil-2 text-success', $alt, $task, $check);
    }

    public static function approve($task = 'approve', $alt = 'COM_TZ_PORTFOLIO_PLUS_APPROVE', $check = false)
    {
        $bar = Toolbar::getInstance('toolbar');

        // Add a publish button.
        $bar->appendButton('Standard', 'checkmark-2 text-success', $alt, $task, $check);
    }

    public static function reject($task = 'reject', $alt = 'COM_TZ_PORTFOLIO_PLUS_REJECT', $check = false)
    {
        $bar = Toolbar::getInstance('toolbar');

        // Add a publish button.
        $bar->appendButton('Standard', 'minus text-error text-danger', $alt, $task, $check);
    }

    public static function preferencesAddon($addonId, $height = '550', $width = '875', $alt = 'JToolbar_Options')
    {
        $bar = JToolbar::getInstance('toolbar');

        $uri = (string) JUri::getInstance();
        $return = urlencode(base64_encode($uri));

        // Add a button linking to config for component.
        $bar->appendButton(
            'Link',
            'options',
            $alt,
            'index.php?option=com_tz_portfolio_plus&task=addon.edit&id=' . $addonId . '&amp;return=' . $return
        );
    }

    public static function addonDataManager($alt='COM_TZ_PORTFOLIO_PLUS_ADDONS_MANAGER', $icon = 'puzzle'){

        $bar = JToolbar::getInstance('toolbar');

        // Add a button linking to config for component.
        $bar->appendButton(
            'Link',
            $icon,
            $alt,
            'index.php?option=com_tz_portfolio_plus&view=addons'
        );
    }

//    public static function customScript($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
//    {
//    }
}