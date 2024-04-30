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

$this -> setAttribute('class', 'custom-select', 'input');
//if ($options) {
    echo JHtml::_('select.genericlist', $options, $this->getName(), $this->getAttribute(null, null, "input"), 'value', 'text', $value, $this->getId());
//}