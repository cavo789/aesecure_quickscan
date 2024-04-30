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

class JHtmlTZBootstrap{

    protected static $loaded = array();

    public static function addRow($options = array()){
        $options['gridrow'] = COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE?'row':'row-fluid';

        if(isset($options['attribute']) && is_array($options['attribute'])){
            $options['attribute']   = implode(' ', $options['attribute']);
        }

        return JLayoutHelper::render('html.bootstrap.addrow', $options);
    }

    public static function endRow(){
        return JLayoutHelper::render('html.bootstrap.endrow', null);
    }

    public static function startContainer($gridColumn, $sidebar = false, $options = array()){
        $opt['sidebar']     = $sidebar;
        $opt['gridColumn']  = $gridColumn;

        if(isset($options['attribute']) && is_array($options['attribute'])){
            $opt['attribute']   = implode(' ', $options['attribute']);
        }

        if(isset($options['responsive'])){
            $opt['responsive']   = $options['responsive'];
        }

        if(isset($options['containerclass'])){
            $opt['containerclass']   = $options['containerclass'];
        }

        return JLayoutHelper::render('html.bootstrap.startcontainer', $opt);
    }

    public static function endContainer(){
        return JLayoutHelper::render('html.bootstrap.endcontainer', null);
    }
}