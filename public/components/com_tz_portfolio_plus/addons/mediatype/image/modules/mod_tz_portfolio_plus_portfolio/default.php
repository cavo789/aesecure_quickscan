<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015-2018 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;

if($item && $image && isset($image -> url) && !empty($image -> url)):
    $doc    = JFactory::getDocument();
    $doc -> addStyleSheet(TZ_Portfolio_PlusUri::base().'/addons/mediatype/image/css/style.css', array('version' => 'auto'));
    $image_uikit  =   $params->get('mt_image_uikit',0);
    if($params -> get('mt_show_image',1)):
        ?>
        <div class="tz_portfolio_plus_image">
            <a href="<?php echo $item -> link;?>">
                <?php
                $imagesrc   =   $image -> url;
                if ($image_uikit) :
                    ?>
                    <img data-src="<?php echo $imagesrc; ?>" alt="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>"
                         title="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>"<?php
                        if ($image_properties && is_array($image_properties)) echo ' data-width="'.$image_properties[0].'" data-height="'.$image_properties[1].'" ';
                        ?> itemprop="image" uk-img />
                <?php else: ?>
                    <img src="<?php echo $imagesrc; ?>" alt="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>"
                         title="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>"
                         itemprop="image" />
                <?php endif; ?>
            </a>
        </div>
    <?php endif;?>
<?php endif;?>
