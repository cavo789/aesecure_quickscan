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

JLoader::import('com_tz_portfolio_plus.helpers.tz_portfolio_plus', JPATH_SITE.'/components');

if($item && $image && isset($image -> url) && !empty($image -> url)):
    ?>
    <?php
    $href   = null;
    $class  = null;
    $rel    = null;

    $orgImage   = $item -> media -> image;
    ?>
    <div class="tz_portfolio_plus_image">
        <?php if(isset($image -> url_detail) && trim($image -> url_detail)){
            $orgUrlDetail     = TZ_Portfolio_PlusFrontHelper::getImageURLBySize($orgImage -> url_detail);
            $propertiesDetail = JImage::getImageFileProperties(JPATH_SITE.'/'.$orgUrlDetail);
//            var_dump($image -> url_detail);
//            var_dump($orgImage -> url_detail);
//            var_dump($propertiesDetail); die();
            ?>
            <amp-img src="<?php echo $image -> url_detail;?>"
                     width="<?php echo $propertiesDetail -> width;?>"
                     height="<?php echo $propertiesDetail -> height;?>"
                     alt="<?php echo ($image -> caption)?($image -> caption):$item -> title;?>"
                     layout="responsive"></amp-img>
        <?php }else{

            $orgUrl     = TZ_Portfolio_PlusFrontHelper::getImageURLBySize($orgImage -> url);
            $properties = JImage::getImageFileProperties(JPATH_SITE.'/'.$orgUrl); ?>
            <amp-img src="<?php echo $image -> url;?>"
                     width="<?php echo $properties -> width;?>"
                     height="<?php echo $properties -> height;?>"
                     alt="<?php echo ($image -> caption)?($image -> caption):$item -> title;?>"
                     layout="responsive"></amp-img>
        <?php } ?>
    </div>
<?php endif;