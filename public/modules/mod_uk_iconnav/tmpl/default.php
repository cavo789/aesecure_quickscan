<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_iconnav
 * @copyright   Copyright (C) Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

$iconnav_pos    = $params->get('iconnav_pos');
$iconnav_pos_top   = $params->get('iconnav_pos_top');
$iconnav_tooltip   = $params->get('iconnav_tooltip');
$iconnav_link   = $params->get('iconnav_link');
$icon   = $params->get('icon');
$hide   = $params->get('hide');
$link_target   = $params->get('link_target');
$iconnav_bg    = $params->get('iconnav_bg');
$iconnav_color    = $params->get('iconnav_color');
$iconnav_bg_hover    = $params->get('iconnav_bg_hover');
$iconnav_color_hover    = $params->get('iconnav_color_hover');

?>

<style type="text/css">
#iconnav_menu {<?php echo $iconnav_pos; ?>:10px;padding-left:0;top:<?php echo $iconnav_pos_top; ?>; position:fixed; z-index:9999}

a.icon-button {
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
  box-sizing: border-box;
  display: inline-block;
  font-size: 14px;
  height: 30px;
  line-height: 30px;
  text-align: center;
  width: 30px;
  margin-bottom:8px;
  background: <?php echo $iconnav_bg; ?>!important;
  color: <?php echo $iconnav_color; ?>!important;
}
a.icon-button:hover {
  background: <?php echo $iconnav_bg_hover; ?>!important;
  color: <?php echo $iconnav_color_hover; ?>!important;
}
.tooltip-arrow {
    display: none !important;
}
</style>

<div id="iconnav_menu" class="<?php echo $hide; ?>">
           
	<?php foreach ($items as $item) {?>
		<a data-toggle="tooltip" title="<?php echo $item->iconnav_tooltip; ?>" class="icon-button" target="<?php echo $item->link_target; ?>"  href="<?php echo $item->iconnav_link; ?>" ><i class="<?php echo $item->iconnav_icon; ?>"></i></a>
		<br/>
	<?php } ?>
       
	
    
</div>
