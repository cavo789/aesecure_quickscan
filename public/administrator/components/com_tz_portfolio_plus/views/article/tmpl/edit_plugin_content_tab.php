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

// Load Tabs's title from plugin group tz_portfolio_plus_mediatype
if($this -> pluginsMediaTypeTab && count($this -> pluginsMediaTypeTab)){
    foreach($this -> pluginsMediaTypeTab as $media){
        ?>
        <div class="tab-pane" id="tztabsaddonsplg_mediatype<?php echo $media -> type -> value;?>">
            <?php echo $media -> html;?>
        </div>
        <?php
    }
}