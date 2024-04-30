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

if($item && $image && isset($image -> url) && !empty($image -> url)):
?>
    <?php
    $href   = null;
    $class  = null;
    $rel    = null;
    $image_uikit  =   $params->get('mt_image_uikit',0);
    ?>
    <div class="tz_portfolio_plus_image">
        <?php if($params -> get('image_lightbox_enable', 1)){ ?>
        <a class="image-title" data-thumb="<?php
        echo (isset($image -> url_detail) && $image -> url_detail)?$image -> url_detail:$image -> url;
        ?>" data-id="image<?php echo time();?>"<?php echo $params -> get('image_lightbox_caption_enable', 1)?' data-caption="'.(isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title).'
        "':'';?> href="<?php
        echo (isset($image -> url_detail) && $image -> url_detail)?$image -> url_detail:$image -> url; ?>">
        <?php } ?>
        <?php if(isset($image -> url_detail) && trim($image -> url_detail)):
                $imagesrc   =   $image -> url_detail;
            else:
                $imagesrc   =   $image -> url;
            endif;
            ?>
            <?php if ($image_uikit) :
                $image_properties   =   $this->image_properties;
                ?>
                <img data-src="<?php echo $imagesrc; ?>" alt="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>" title="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>"<?php
                    if ($image_properties && is_array($image_properties)) echo ' data-width="'.$image_properties[0].'" data-height="'.$image_properties[1].'" ';
                    ?>itemprop="image" uk-img />
            <?php else: ?>
                <img src="<?php echo $imagesrc; ?>" alt="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>" title="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>" itemprop="image" />
            <?php endif; ?>
        <?php if($params -> get('image_lightbox_enable', 1)){ ?>
        </a>
        <?php } ?>
    </div>
<?php endif;