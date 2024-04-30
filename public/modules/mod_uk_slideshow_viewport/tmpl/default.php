<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_slider
 * @copyright   © 2020 Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */
$content_pos       = $params->get('content_pos');
$content_animation       = $params->get('content_animation');
$animation_delay       = $params->get('animation_delay');
$content_class       = $params->get('content_class');
$content_before    = $params->get('content_before');
$dotnav_pos    = $params->get('dotnav_pos');
$link    = $params->get('link');
$link_text    = $params->get('link_text');
$pause_on_hover    = $params->get('pause_on_hover');
$imgorvideo    = $params->get('imgorvideo');


?>
<style type="text/css">

.uk-slidenav {
    color: rgba(255,255,255,.9);
    background: rgba(0,0,0,.3);
	transition:background .1s ease-in-out
	
}
.uk-slidenav:focus, .uk-slidenav:hover {
    color: rgba(255,255,255,1);
    background: rgba(0,0,0,.5);
}
@media only screen and (max-width: 768px) {
  .jp-slide {max-width:90%!important;}
}

</style>
<?php if ($content_before) { ?>
<div class="<?php echo $content_class; ?>">
<?php echo $content_before; ?>
</div>
<?php } ?>
<div class="uk-position-relative uk-visible-toggle uk-dark " uk-slideshow<?php echo $sw_params; ?>>
    <ul class="uk-slideshow-items"  uk-height-viewport="offset-top: true; offset-bottom: 0">
        <?php
        foreach ($items as $item){?>
        <li>
		
            <?php if ($item->img_bg) { ?>
            <img src="<?php echo $item->img_bg; ?>" alt="" uk-cover  >
            <?php } ?>
			


		<?php if ($item->content) { ?>
			<div uk-scrollspy="cls:<?php echo $item->content_animation; ?>; delay: <?php echo $item->animation_delay; ?>; repeat:true" style="background:<?php echo $item->content_bg; ?>;margin:<?php echo $item->content_margin; ?>; max-width:<?php echo $item->content_width; ?>" class="<?php echo $item->content_text_align; ?> <?php echo $item->hide; ?>  <?php echo $item->content_pos; ?> jp-slide p-4" >
			<div class="<?php echo $item->content_color;?>"><?php echo $item->content; ?></div>
			<?php if ($item->link) { ?>
				<div class="pb-3 text-white">
					<a class="mt-2 btn btn-sm btn-primary" href="<?php echo $item->link; ?>"><?php echo $item->link_text; ?></a>
				</div>
				
			<?php } ?>
			</div>
		
		<?php } ?>
        </li>
        <?php } ?>
    </ul>

    <?php if ($slidenav) { ?>
	
    <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" data-uk-slidenav-previous data-uk-slideshow-item="previous"></a>
    <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" data-uk-slidenav-next data-uk-slideshow-item="next"></a>
    <?php } ?>

    <?php if ($dotnav) { ?>
	<div class="<?php if ($dotnav_pos) { ?>uk-position-bottom-center<?php } ?> uk-position-small">
	<ul class="uk-slideshow-nav uk-dotnav uk-flex-center uk-margin"></ul>
	</div>
    <?php } ?>

</div>
