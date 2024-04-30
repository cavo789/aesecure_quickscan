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

if($tag = $this -> tagAbout){
    $params = $this -> params;
?>
<div class="tpp-tag-about">
    <?php if($params -> get('show_tag_title_heading', 1)){?>
        <span class="h2"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_TAG_HEADING', ''); ?></span>
        <h2 class="title"><?php echo '#'.$tag -> title; ?></h2>
    <?php } ?>
</div>
<?php
}