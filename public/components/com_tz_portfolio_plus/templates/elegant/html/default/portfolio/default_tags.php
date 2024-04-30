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
 defined('_JEXEC') or die();

$params     = $this -> item -> params;
if($params -> get('show_cat_tags',0) && $this -> item && isset($this -> item -> tags)):
?>
<div class="tpp-item-tags">
    <i class="tp tp-tags"></i>
    <?php foreach($this -> item -> tags as $i => $item): ?>
        <?php if($params -> get('cat_link_tag', 1)){ ?>
            <a href="<?php echo $item ->link; ?>"><?php echo $item -> title;?></a>
        <?php }else{ ?>
            <span><?php echo $item -> title;?></span>
        <?php }?>
        <?php if($i != count($this -> item -> tags) - 1):?><span><?php echo ','?></span><?php endif;?>
    <?php endforeach;?>
</div>
<?php endif;?>