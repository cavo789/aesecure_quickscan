<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class JHtmlTZPaddingMargin{

    protected static $loaded = array();

    public static function addPadding( $name, $id, $value){
        $html   =   self::generation('padding', $name, $id, $value);
        return $html;
    }

    public static function addMargin($name, $id, $value){
        $html   =   self::generation('margin', $name, $id, $value);
        return $html;
    }

    public static function generation($type  =   'margin', $name, $id, $value) {
        $document = Factory::getApplication() -> getDocument();
        $document->addStyleSheet(JUri::root().'/administrator/components/com_tz_portfolio_plus/css/addon-admin.css', array('version' => 'auto'));
        $document->addScript(JUri::root().'/administrator/components/com_tz_portfolio_plus/js/addon-admin.js', array('version' => 'auto'));
        $val      = (isset($value) && $value) ? json_decode($value): json_decode('{"md": {"top": "", "right": "", "bottom": "", "left": ""}, "sm": {"top":"", "right":"", "bottom":"", "left":""}, "xs": {"top":"", "right":"", "bottom":"", "left":""}}');
        $html   =   '<div class="tz'.$type.'-container tzportfolio-box-responsive">';
        $html   .=  '<label class="clearfix">';
        $html   .=  '<span class="tzportfolio-box-label">'.$type.'</span>';
        $html   .=  '<span class="tzportfolio-lock">';
        $html   .=  '<span class="md active"><i class="icon-locked"></i></span>';
        $html   .=  '<span class="sm"><i class="icon-locked"></i></span>';
        $html   .=  '<span class="xs"><i class="icon-locked"></i></span>';
        $html   .=  '</span>';
        $html   .=  '<ul class="tzportfolio-device-tab">';
        $html   .=  '<li class="tzportfolio-device md active"><i class="icon-screen"></i></li>';
        $html   .=  '<li class="tzportfolio-device sm"><i class="icon-tablet"></i></li>';
        $html   .=  '<li class="tzportfolio-device xs"><i class="icon-mobile"></i></li>';
        $html   .=  '</ul>';
        $html   .=  '</label>';
        $html   .=  '<div class="clearfix tzportfolio-tabcontent-container">';
        $html   .=  '<div class="tzportfolio-box md row-fluid active">';
        $html   .=  '<div class="tzportfolio-box-item col-md-3 span3"><input type="text" class="pm-top" placeholder="Top" value="'.$val->md->top.'" /></div>';
        $html   .=  '<div class="tzportfolio-box-item col-md-3 span3"><input type="text" class="pm-right" placeholder="Right" value="'.$val->md->right.'" /></div>';
        $html   .=  '<div class="tzportfolio-box-item col-md-3 span3"><input type="text" class="pm-bottom" placeholder="Bottom" value="'.$val->md->bottom.'" /></div>';
        $html   .=  '<div class="tzportfolio-box-item col-md-3 span3"><input type="text" class="pm-left" placeholder="Left" value="'.$val->md->left.'" /></div>';
        $html   .=  '</div>';
        $html   .=  '<div class="tzportfolio-box sm row-fluid">';
        $html   .=  '<div class="tzportfolio-box-item col-md-3 span3"><input type="text" class="pm-top" placeholder="Top" value="'.$val->sm->top.'" /></div>';
        $html   .=  '<div class="tzportfolio-box-item col-md-3 span3"><input type="text" class="pm-right" placeholder="Right" value="'.$val->sm->right.'" /></div>';
        $html   .=  '<div class="tzportfolio-box-item col-md-3 span3"><input type="text" class="pm-bottom" placeholder="Bottom" value="'.$val->sm->bottom.'" /></div>';
        $html   .=  '<div class="tzportfolio-box-item col-md-3 span3"><input type="text" class="pm-left" placeholder="Left" value="'.$val->sm->left.'" /></div>';
        $html   .=  '</div>';
        $html   .=  '<div class="tzportfolio-box xs row-fluid">';
        $html   .=  '<div class="tzportfolio-box-item col-md-3 span3"><input type="text" class="pm-top" placeholder="Top" value="'.$val->xs->top.'" /></div>';
        $html   .=  '<div class="tzportfolio-box-item col-md-3 span3"><input type="text" class="pm-right" placeholder="Right" value="'.$val->xs->right.'" /></div>';
        $html   .=  '<div class="tzportfolio-box-item col-md-3 span3"><input type="text" class="pm-bottom" placeholder="Bottom" value="'.$val->xs->bottom.'" /></div>';
        $html   .=  '<div class="tzportfolio-box-item col-md-3 span3"><input type="text" class="pm-left" placeholder="Left" value="'.$val->xs->left.'" /></div>';
        $html   .=  '</div>';
        $html   .=  '</div>';
        $html   .= '<input type="hidden" name="'.$name.'" id="'.$id.'" class="pm-data" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" />';
        $html   .=  '</div>';
        return $html;
    }
}