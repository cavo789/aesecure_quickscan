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

class TZ_Portfolio_PlusViewDialog extends JViewLegacy{

    protected $state;
    protected $formReject;

    public function display($tpl = null)
    {
        $this -> state      = $this -> get('State');
        $this -> formReject = $this -> get('FormReject');

        ob_start();
            parent::display($tpl);
        $html   = ob_get_contents();
        ob_end_clean();
        $json   = new JResponseJson();
        $json ->data    = $html;



        echo json_encode($json);

        JFactory::getApplication()->close();
    }
}