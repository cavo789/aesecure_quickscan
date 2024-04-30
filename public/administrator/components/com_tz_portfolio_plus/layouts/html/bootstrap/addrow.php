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

$gridrow    = $displayData['gridrow'];
$class      = isset($displayData['class'])?$displayData['class']:'';
$attribute  = isset($displayData['attribute'])?$displayData['attribute']:'';
?>
<div class="<?php echo $gridrow.($class?' '.$class:''); ?>"<?php echo $attribute?' '.$attribute:'';?>>
