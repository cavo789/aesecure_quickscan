<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

class TZ_Portfolio_PlusTableContent_Category_Map extends JTable
{
    public function __construct(JDatabaseDriver $db)
    {
        parent::__construct('#__tz_portfolio_plus_content_category_map', 'id', $db);
    }

    public function resetAll()
    {
        // Get the default values for the class from the table.
        foreach ($this->getFields() as $k => $v)
        {
            // If the property is not the primary key or private, reset it.
            if ((strpos($k, '_') !== 0))
            {
                $this->$k = $v->Default;
            }
        }

        // Reset table errors
        $this->_errors = array();
    }
}