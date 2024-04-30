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
defined('_JEXEC') or die('Restricted access');

if($item = $this -> item) {
    if($item->event->onContentDisplayMediaType && !empty($item->event->onContentDisplayMediaType)){
?>
<div class="TzArticleMedia">
    <?php echo $item->event->onContentDisplayMediaType; ?>
</div>
<?php }
}
?>