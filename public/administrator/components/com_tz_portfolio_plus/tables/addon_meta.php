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

// No direct access
defined('JPATH_PLATFORM') or die;

use Joomla\Registry\Registry;

class TZ_Portfolio_PlusTableAddon_Meta extends JTable
{
    public function __construct(JDatabaseDriver $db)
    {
        parent::__construct('#__tz_portfolio_plus_addon_meta', 'id', $db);
    }
    public function check()
    {
        if(empty($this -> meta_key)){
            $this -> setError(JText::_('Invaild Meta Key data. Please provide data for it'));
                return false;
        }
        return true;
    }
}
