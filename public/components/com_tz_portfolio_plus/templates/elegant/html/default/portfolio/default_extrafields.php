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

if($item = $this -> item):
    if(isset($item -> extrafields) && !empty($item -> extrafields)):
        $params         = $item -> params;
?>
<ul class="tz-extrafields">
<?php foreach($item -> extrafields as $field):?>
    <li class="tz_extrafield-item">
        <?php if($params -> get('show_cat_field_image', $field -> hasImage())){ ?>
        <span class="tpp-extrafield__image pull-left float-left mr-1 mt-1">
            <img src="<?php echo $field -> getImage();?>" alt="<?php echo $field -> getTitle();?>"/></span>
        <?php }?>
        <?php if($field -> hasTitle()):?>
        <div class="tz_extrafield-label"><?php echo $field -> getTitle();?></div>
        <?php endif;?>
        <div class="tz_extrafield-value pull-left">
            <?php echo $field -> getListing();?>
        </div>
    </li>
<?php endforeach;?>
</ul>
<?php
    endif;
endif;