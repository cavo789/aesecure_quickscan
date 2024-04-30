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
defined('_JEXEC') or die;

$params = $this -> item -> params;
if($params -> get('show_title',1)) {
    $htag    = $this->params->get('show_page_heading', 1) ? 'h2' : 'h1';
    ?>
    <<?php echo $htag; ?> class="tpTitle reset-heading" itemprop="name headline">
    <?php echo $this->escape($this->item->title); ?>
    </<?php echo $htag; ?>>
    <?php
}
//Call event onContentAfterTitle on plugin
echo $this->item->event->afterDisplayTitle;
?>