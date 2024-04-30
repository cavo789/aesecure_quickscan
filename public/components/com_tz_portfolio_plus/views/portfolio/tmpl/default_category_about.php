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

$params = $this -> params;
if(($category = $this -> categoryAbout) && (($params -> get('show_cat_about_title', 0) && $category -> title)
        || ($params -> get('show_cat_about_image', 0) && $category -> images)
        || ($params -> get('show_cat_about_description', 0) && $category -> description)) ){
?>
<div class="tpp-category-about">
    <?php if($params -> get('show_cat_about_title', 0) && $category -> title){?>
        <h2 class="title"><?php echo $category -> title; ?></h2>
    <?php } ?>
    <?php if($params -> get('show_cat_about_image', 0) && $category -> images){?>
    <img src="<?php echo JUri::root(true).'/'.$category -> images;?>" alt="<?php
    echo $category -> title; ?>" class="mb-2"/>
    <?php } ?>
    <?php if($params -> get('show_cat_about_description', 0) && $category -> description){?>
    <div class="description"><?php echo $category -> description; ?></div>
    <?php } ?>
</div>
<?php
}