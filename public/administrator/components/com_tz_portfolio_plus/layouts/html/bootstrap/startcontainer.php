<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

$sidebar        = $displayData['sidebar'];
$gridColumn     = $displayData['gridColumn'];
$attribute      = isset($displayData['attribute'])?$displayData['attribute']:'';
$responsive     = isset($displayData['responsive'])?$displayData['responsive']:'';
$containerclass = isset($displayData['containerclass'])?$displayData['containerclass']:'j-main-container';
?>

<?php if (COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) { ?>
<div class="<?php if ($sidebar && !empty($sidebar)) {
        echo $gridColumn?'col-md-'.$gridColumn.$responsive:'';
    } else { echo 'col-md-12'; }
?>"<?php echo $attribute;?>>
<?php } ?>

    <div id="j-main-container" class="<?php echo $containerclass;
    if (!empty($this->sidebar) &&
        !COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
        echo $gridColumn?' span'.$gridColumn:'';
    } ?>">

    <?php
//    if($sidebar) {
        // Display message update to pro (it only use for free version)
        echo JLayoutHelper::render('message');
//    }
    ?>
