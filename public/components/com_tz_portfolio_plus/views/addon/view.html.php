<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2011-2017 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

class TZ_Portfolio_PlusViewAddOn extends JViewLegacy
{
	function display($tpl = null)
	{
        $html = $this -> get('RenderAddonView');
        echo $html;
	}
}
