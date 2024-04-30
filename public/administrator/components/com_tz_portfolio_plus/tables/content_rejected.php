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

use Joomla\CMS\Factory;

class TZ_Portfolio_PlusTableContent_Rejected extends JTable
{
    public function __construct(JDatabaseDriver $db)
    {
        parent::__construct('#__tz_portfolio_plus_content_rejected', 'id', $db);
    }

    public function store($updateNulls = false)
    {
        $date = Factory::getDate();
        $user = Factory::getUser();

//        if (!$this->created)
//        {
            $this->created = $date->toSql();
//        }
//        if (empty($this->created_by))
//        {
            $this->created_by = $user->get('id');
//        }

        return parent::store($updateNulls);
    }

}
