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
?>
<div class="tpButton">
    <a href="<?php echo $this->button['link']; ?>">
        <?php echo JHtml::_('image', $this->button['image'], null, null, false); ?>
        <div>
            <?php echo $this->button['text']; ?>
        </div>
    </a>
</div>


