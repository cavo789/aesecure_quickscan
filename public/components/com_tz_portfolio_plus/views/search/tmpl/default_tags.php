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
    echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_TAGS','');
?>
    <?php foreach($this -> item -> tags as $i => $item): ?>
        <a href="<?php echo $item ->link; ?>"><?php echo $item -> title;?></a><?php if($i != count($this -> item -> tags) - 1):?><span><?php echo ','?></span><?php endif;?>
    <?php endforeach;?>
<?php endif;?>