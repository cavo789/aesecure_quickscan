<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_skills
 * @copyright   Copyright (C) Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */
$document = JFactory::getDocument();
$modulePath = JURI::base() . 'modules/mod_uk_skills/';
$document->addStyleSheet($modulePath.'css/mod_uk_skills.css');
$document->addScript($modulePath.'js/mod_uk_skills.js');
$circle_color     = $params->get('circle_color');
$circle_bg        = $params->get('circle_bg');
$circle_value     = $params->get('circle_value');
$circle_width    = $params->get('circle_width');
$circle_speed    = $params->get('circle_speed');
$circle_thickness    = $params->get('circle_thickness');
$content_before_class       = $params->get('content_before_class');
$content_before    = $params->get('content_before');

?>

<?php if ($content_before) { ?>
<div class="<?php echo $content_before_class; ?>">
<?php echo $content_before; ?>
</div>
<?php } ?>

<div class="hide-tox-progress" uk-slider<?php echo $sw_params; ?>>
    <div class="uk-position-relative uk-visible-toggle">
        <ul class="uk-grid-match uk-slider-items <?php echo $classes; ?>" uk-grid >
            <?php
            foreach ($items as $item)
            {
                
               
            ?>
            <li>
				<div class="<?php echo $item_style; ?> <?php echo $center; ?>">
					<div style="margin:10px auto" class="hidden pt-2 tox-progress" data-size="<?php echo $item->circle_width; ?>" data-thickness="<?php echo $item->circle_thickness; ?>" data-color="<?php echo $item->circle_color; ?>" data-background="<?php echo $item->circle_bg; ?>" data-progress="<?php echo $item->circle_value; ?>" data-speed="<?php echo $item->circle_speed; ?>">
						<div class="tox-progress-content" data-vcenter="true">
						 <h2 class="text-center" style="width: 100%"><?php echo $item->circle_value; ?>%</h2>
						</div>
					</div>
                    <?php if (($item->content) or ($item->title)): ?>
                    <div class="p-3">
						<?php if ($item->title) { ?>
						<h4 class="uk-card-title"><?php echo $item->title; ?></h4>
						<?php } ?>
						<?php if ($item->content) { ?>
						<p><?php echo $item->content; ?></p>
						<?php } ?>
					</div>
					<?php endif; ?>
                </div>
            </li>
            <?php } ?>
        </ul>
		<?php if ($slidenav) { ?>
		<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
        <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>    <?php } ?>

    </div>
    
    
    <?php if ($dotnav) { ?>
    <div class="uk-flex uk-flex-center uk-margin-small-top">
        <ul class="uk-slider-nav uk-dotnav"></ul>
    </div>
    <?php } ?>
    
</div>


<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        ToxProgress.create();
        ToxProgress.animate();
    });
</script>







