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

if($item && $image && isset($image -> url) && !empty($image -> url)):
    $doc    = JFactory::getDocument();
    $doc -> addStyleSheet(TZ_Portfolio_PlusUri::base().'/addons/mediatype/image/css/style.css', array('version' => 'auto'));
    if($params -> get('mt_show_image',1)):
        ?>
<div class="tz_portfolio_plus_image">
    <a href="<?php echo $item -> link;?>">
        <img src="<?php echo $image -> url;?>"
             alt="<?php echo isset($image -> caption)?$image -> caption:$item -> title;?>"
             title="<?php echo isset($image -> caption)?$image -> caption:$item -> title;?>" itemprop="image"/>
    </a>
</div>
    <?php endif;?>
<?php endif;?>
