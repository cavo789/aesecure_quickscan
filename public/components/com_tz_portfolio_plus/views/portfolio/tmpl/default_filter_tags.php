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

?>
<?php if($this -> itemTags):?>
    <?php foreach($this -> itemTags as $item):?>
        <a href="#<?php echo $item -> alias; ?>"
           class="btn btn-default btn-outline-secondary btn-sm"
           data-option-value=".<?php echo $item -> alias; ?>">
            <?php echo $item -> title;?>
        </a>
    <?php endforeach;?>
<?php endif;?>