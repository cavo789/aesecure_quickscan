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
        $params = $item -> params;
?>
<div class="tpp-extrafield">
    <ul class="tpp-extrafield__list list-group list-group-flush border-top border-bottom">
    <?php foreach($item -> extrafields as $i => $field){?>
        <li class="tpp-extrafield__item d-flex list-group-item<?php echo $i%2==0?' list-group-item-light':''; ?>">
            <?php if($params -> get('show_field_image', $field -> hasImage())){ ?>
                <span class="tpp-extrafield__image pull-left float-left mr-1 mt-1">
            <img src="<?php echo $field -> getImage();?>" alt="<?php echo $field -> getTitle();?>"/></span>
            <?php }?>
            <?php if($field -> hasTitle()):?>
            <div class="tpp-extrafield__label flex-fill"><?php echo $field -> getTitle();?></div>
            <?php endif;?>
            <div class="tpp-extrafield__value pull-left">
                <?php echo $field -> getOutput();?>
            </div>
        </li>
    <?php }?>
    </ul>
</div>
<?php
    endif;
endif;