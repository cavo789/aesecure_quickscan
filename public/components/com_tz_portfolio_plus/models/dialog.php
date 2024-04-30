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

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

tzportfolioplusimport('model.dialog');

class TZ_Portfolio_PlusModelDialog extends TZ_Portfolio_PlusModelDialogBase
{
    public function __construct()
    {
        $lang   = JFactory::getLanguage();
        $lang -> load('com_tz_portfolio_plus', JPATH_ADMINISTRATOR);

        parent::__construct();
    }
}
