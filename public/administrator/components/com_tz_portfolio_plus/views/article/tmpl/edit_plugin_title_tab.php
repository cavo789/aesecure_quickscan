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
$lang   = JFactory::getLanguage();
$lang -> load('com_plugins');

// Load Tabs's title from plugin group tz_portfolio_plus_mediatype
if($this -> pluginsMediaTypeTab && count($this -> pluginsMediaTypeTab)){
    foreach($this -> pluginsMediaTypeTab as $media){
        ?>
        <li class="nav-item">
            <a class="nav-link" href="#tztabsaddonsplg_mediatype<?php echo $media -> type -> value;?>" data-toggle="tab">
                <?php echo $media -> type -> text;?>
            </a>
        </li>
        <?php
    }
}
 
