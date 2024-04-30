<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_skills
 * @copyright   Copyright (C) Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

$dotnav_pos    = $params->get('dotnav_pos');
$dotnav_pos_top   = $params->get('dotnav_pos_top');
$tooltip   = $params->get('tooltip');
$dotnav_link   = $params->get('dotnav_link');
$dotnav_bg   = $params->get('dotnav_bg');
$dot_border_color   = $params->get('dot_border_color');
$dotnav_bg_hover   = $params->get('dotnav_bg_hover');
$dotnav_size   = $params->get('dotnav_size');
$icon   = $params->get('icon');
$hide   = $params->get('hide');


$link_target     		= $params->get('link_target');
?>

<style type="text/css">
#dotnav_menu{<?php echo $dotnav_pos; ?>:10px;top:<?php echo $dotnav_pos_top; ?>; position:fixed;z-index:9999}
#dotnav_menu ul li a{width:<?php echo $dotnav_size; ?>;height:<?php echo $dotnav_size; ?>;background:<?php echo $dotnav_bg; ?>;border:1px solid <?php echo $dot_border_color; ?>}
#dotnav_menu ul li a:hover,#dotnav_menu ul li a:active{background:<?php echo $dotnav_bg_hover; ?>}
.tooltip-arrow {
    display: none !important;
}
</style>




<div id="dotnav_menu" class="<?php echo $hide; ?>">
    
        <ul class="uk-dotnav uk-dotnav-vertical ">
            <?php
            foreach ($items as $item) {?>
            <li>
				<a data-toggle="tooltip" target="<?php echo $item->link_target; ?>" href="<?php echo $item->dotnav_link; ?>" title="<?php echo $item->tooltip; ?>"></a>
				
				

            </li>
            <?php } ?>
        </ul>
		
    
</div>

