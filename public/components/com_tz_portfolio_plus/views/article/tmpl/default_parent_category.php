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

// no direct access
defined('_JEXEC') or die('Restricted access');

$params = $this -> item -> params;
if($params -> get('show_parent_category',1)) {
    ?>
    <?php if ($this->item->parent_slug != '1:root'){ ?>
        <span class="tpp-item-parent-category">
    <?php
    $title = $this->escape($this->item->parent_title);
    $url = $title;
    $target = '';
    if (isset($tmpl) AND !empty($tmpl)) {
        $target = ' target="_blank"';
    }
    $url = '<a href="' . $this->item->parent_link . '"' . $target . ' itemprop="genre">' . $title . '</a>';
    ?>
    <?php if ($params->get('link_parent_category', 1) and $this->item->parent_slug){ ?>
        <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_PARENT', $url); ?>
    <?php }else{ ?>
        <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_PARENT', '<span itemprop="genre">' . $title . '</span>'); ?>
    <?php } ?>
</span>
    <?php }
}