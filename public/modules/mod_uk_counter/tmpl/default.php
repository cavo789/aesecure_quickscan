<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_skills
 * @copyright   Copyright (C) Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

$content_before_class       = $params->get('content_before_class');
$content_before    = $params->get('content_before');
$count_icon     		= $params->get('count_icon');
$count_icon_size     		= $params->get('count_icon_size');
$count_icon_color     		= $params->get('count_icon_color');
$count_number     		= $params->get('count_number');
$count_number_color     		= $params->get('count_number_color');
$count_number_size     		= $params->get('count_number_size');
$content_bg     		= $params->get('content_bg');
$counter_delay     		= $params->get('counter_delay');
$counter_time     		= $params->get('counter_time');

?>
<script type="text/javascript" src="<?php echo JUri::base(); ?>modules/mod_uk_counter/js/jquery.js"></script>



<script>
	jQuery(function($){
		$('.counter').counterUp({
			delay: <?php echo $counter_delay; ?>,
			time: <?php echo $counter_time; ?>
		});
	});
</script>
	
	



<?php if ($content_before) { ?>
<div class="<?php echo $content_before_class; ?>">
<?php echo $content_before; ?>
</div>
<?php } ?>


<div uk-slider<?php echo $sw_params; ?>>
    <div class="uk-position-relative">
        <ul class="uk-grid-match uk-slider-items <?php echo $classes; ?>" uk-grid >
            <?php
            foreach ($items as $item) {?>
            <li>
				<div style="background:<?php echo $item->content_bg; ?>" class="p-3 <?php echo $item_style; ?> <?php echo $center; ?>">
					<?php if ($item->count_icon) { ?>
						<p style="font-size:<?php echo $item->count_icon_size; ?>;color:<?php echo $item->count_icon_color; ?>" class="pb-3 <?php echo $item->count_icon; ?>"></p>
					<?php } ?>
					
					<div class="counter " style="font-size:<?php echo $item->count_number_size; ?>;color:<?php echo $item->count_number_color; ?>;"><?php echo $item->count_number; ?></div>

					
					<?php if ($item->content) { ?>
						<div class="p-3 mt-3"><?php echo $item->content; ?></div>
					<?php } ?>
					
				</div>
            </li>
            <?php } ?>
        </ul>


	</div>
	    <?php if ($dotnav) { ?>
    <div class="uk-flex uk-flex-center uk-margin-small-top">
        <ul class="uk-slider-nav uk-dotnav"></ul>
    </div>
    <?php } ?>
</div>

<script type="text/javascript" src="<?php echo JUri::base(); ?>modules/mod_uk_counter/js/waypoints.js"></script>
<script type="text/javascript" src="<?php echo JUri::base(); ?>modules/mod_uk_counter/js/counterup.js"></script>






