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

// No direct access.
defined('_JEXEC') or die;

$form   = $this -> form;
$group  = 'media.'.$this -> _name;
$image  = null;

if($this -> item && isset($this -> item -> media)){
    $image  = $this -> item -> media;
    if(isset($image[$this -> _name])) {
        $image = $image[$this -> _name];
    }
}
?>

<div class="control-group">
    <div class="control-label"><?php echo $form -> getLabel('url',$group);?></div>
    <div class="controls">
        <?php echo $form -> getInput('url',$group);?>
        <?php
        if($image && isset($image['url']) && !empty($image['url'])){
            ?>
            <div class="control-group">
                <?php
                echo $form -> getInput('url_remove',$group,$image['url']);
                ?>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $form -> getLabel('url_detail',$group);?></div>
    <div class="controls">
        <?php echo $form -> getInput('url_detail',$group);?>
        <?php
        if($image && isset($image['url_detail']) && !empty($image['url_detail'])){
            ?>
            <div class="control-group">
                <?php
                echo $form -> getInput('url_detail_remove',$group);
                ?>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $form -> getLabel('caption',$group);?></div>
    <div class="controls">
        <?php echo $form -> getInput('caption',$group);?>
    </div>
</div>