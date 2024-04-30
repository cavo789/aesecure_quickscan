<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_slider
 * @copyright   Copyright (C) Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */
$content_before_class       = $params->get('content_before_class');
$content_before    = $params->get('content_before');
$img_popup    = $params->get('img_popup');
$media_top    = $params->get('media_top');

?>

<style type="text/css">

.uk-card img { transform: scale(1); transform-origin: 50% 50% 0px; transition: all 0.3s ease 0s; }
.uk-card a:hover img { transform: scale(1.0); }
.uk-card .item-image-backdrop { position: absolute; top: 0px; left: 0px; z-index: 10; width: 100%; height: 100%; background-color: rgb(0, 0, 0); visibility: hidden; opacity: 0; transition: all 0.3s ease 0s; }
.uk-card a:hover .item-image-backdrop { visibility: visible; opacity: 0.7; }
.uk-card .item-image-backdrop::before { content: ""; z-index: 11; display: block; width: 25px; height: 1px; background-color: rgb(255, 255, 255); position: absolute; top: 50%; left: 50%; margin-left: -12px; transition: all 0.3s ease 0s; transform: translateX(-150px); }
.uk-card .item-image-backdrop::after { content: ""; z-index: 12; display: block; width: 1px; height: 25px; background-color: rgb(255, 255, 255); position: absolute; top: 50%; left: 50%; margin-top: -12px; transition: all 0.3s ease 0s; transform: translateY(-150px); }
.uk-card a:hover .item-image-backdrop::before { transform: translateX(0px); }
.uk-card a:hover .item-image-backdrop::after { transform: translateY(0px); }

</style>
<?php if ($content_before) { ?>
<div class="<?php echo $content_before_class; ?>">
<?php echo $content_before; ?>
</div>
<?php } ?>

<div class="uk-slider-container-offset" uk-slider<?php echo $sw_params; ?>>
    <div class="uk-position-relative uk-visible-toggle">
        <ul class="uk-grid-match uk-slider-items <?php echo $classes; ?>" uk-grid>
            <?php
            foreach ($items as $item)
            {
            ?>
			
			
            <li>
				<div class="<?php echo $item_style; ?> <?php echo $center; ?>">
                    <div class="uk-card uk-card-media-top" >
                        <?php if ($item->img) { ?>
							<?php if ($item->img_popup) { ?>
							<div uk-lightbox class="uk-inline-clip">
								<a href="<?php echo $item->img; ?>">
								<img  title="<?php echo $item->title; ?>" class="<?php echo $circle; ?>" src="<?php echo $item->img; ?>" alt="<?php echo $item->title; ?>">
								<span class="item-image-backdrop"></span>
								</a>
							</div>
							<?php } else { ?> 
							<img title="<?php echo $item->title; ?>" class="<?php echo $circle; ?> " src="<?php echo $item->img; ?>" alt="<?php echo $item->title; ?>">
							<?php } ?>
						<?php } ?>
                    </div>
                
					<?php if (($item->content) or ($item->title) or ($item->link)): ?>
					<div class="p-3">
						<?php if ($item->title) { ?>
						<h4 class="uk-card-title"><?php echo $item->title; ?></h4>
						<?php } ?>
						
						<?php if ($item->content) { ?>
							<?php echo $item->content; ?>
						<?php } ?>
						
						<?php if ($item->link) { ?>
							<div class="uk-margin-small-top">
								<a class="btn btn-sm btn-primary" target="<?php echo $item->link_target; ?>" href="<?php echo $item->link; ?>"><?php echo $item->link_text; ?></a>
							</div>
						<?php } ?>
					</div>
					<?php endif; ?>
				</div>
			
            </li>
            <?php } ?>
        </ul>
		<?php if ($slidenav) { ?>
		<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
        <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>    
		<?php } ?>
    </div>
    
   
    
    <?php if ($dotnav) { ?>
    <div class="uk-flex uk-flex-center uk-margin-small-top">
        <ul class="uk-slider-nav uk-dotnav"></ul>
    </div>
    <?php } ?>
    
</div>
