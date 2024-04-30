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

$item   = $this -> item;
$image  = $this -> image;
$params = $this -> params;

if($params -> get('mt_image_show_image_category', 1) && $item && $image && isset($image -> url) && !empty($image -> url)):
    $image_uikit  =   $params->get('mt_image_uikit',0);
?>
<div class="tz_portfolio_plus_image">
    <a href="<?php echo $item -> link;?>">
        <?php if ($image_uikit) :
            $image_properties   =   $this->image_properties;
            ?>
            <img data-src="<?php echo $image -> url; ?>" alt="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>" title="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>"<?php
                if ($image_properties && is_array($image_properties)) echo ' data-width="'.$image_properties[0].'" data-height="'.$image_properties[1].'" ';
                ?>itemprop="thumbnailUrl" uk-img />
        <?php else: ?>
            <img src="<?php echo $image -> url; ?>" alt="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>" title="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>" itemprop="thumbnailUrl" />
        <?php endif; ?>
    </a>
</div>
<?php endif;?>
