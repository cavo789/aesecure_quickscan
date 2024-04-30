<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_slider
 * @copyright   Copyright (C) Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */
$content_before_class       = $params->get('content_before_class');
$content_before    = $params->get('content_before');

?>
<style type="text/css">
img.hover-effect {
  vertical-align: top;
  transition: opacity 0.3s; /* Transition should take 0.3s */
  -webkit-transition: opacity 0.3s; /* Transition should take 0.3s */
  opacity: 1; /* Set opacity to 1 */
}

img.hover-effect:hover {
  opacity: 0.5; /* On hover, set opacity to 2 */
}

</style>
<?php if ($content_before) { ?>
<div class="<?php echo $content_before_class; ?>">
<?php echo $content_before; ?>
</div>
<?php } ?>

<div uk-slider<?php echo $sw_params; ?> >
    <div>
        <ul class="uk-slider-items uk-child-width-1-<?php echo $item_count; ?>@m uk-grid-match" uk-grid >
            <?php
            foreach ($items as $item)
            {
            ?>
            <li>
				<div class="<?php echo $item_style; ?> <?php echo $item_space; ?> uk-grid-collapse uk-child-width-1-2@m" uk-grid >
					<?php if ($item->img) { ?>
					<div class="<?php echo $media_align; ?> uk-cover-container">
						<?php if ($item->img_popup) { ?>
							<div uk-lightbox class="img-container">
								<a href="<?php echo $item->img; ?>">
									<img class="hover-effect" src="<?php echo $item->img; ?>" uk-cover >
									<canvas width="<?php echo $img_width; ?>" height="<?php echo $img_height; ?>"></canvas>
								</a>	
							</div>
						<?php } else { ?> 
						<img src="<?php echo $item->img; ?>" alt="<?php echo $item->title; ?>" uk-cover>
						<canvas width="<?php echo $img_width; ?>" height="<?php echo $img_height; ?>"></canvas>
						<?php } ?>
					<?php } ?>	
					</div>
					
					
					<div>
						<div class="uk-card-body">
						
							<?php if ($item->title) { ?>
							<h4 class="uk-card-title"><?php echo $item->title; ?></h4>
							<?php } ?>
						
							<?php if ($item->content) { ?><?php echo $item->content; ?><?php } ?>
						
							<?php if ($item->link) { ?>
							<div class="uk-margin-small-top <?php echo trim(' ' . $item->al_content); ?>">
								<a class="btn btn-sm btn-primary" href="<?php echo $item->link; ?>"><?php echo $item->link_text; ?></a>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			
            </li>
            <?php } ?>
        </ul>
    </div>
    

    
    <?php if ($dotnav) { ?>
    <div class="uk-flex uk-flex-center uk-margin-small-top mt-3">
        <ul class="uk-slider-nav uk-dotnav"></ul>
    </div>
    <?php } ?>
    
</div>
