<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_slideportfolio2
 * @copyright   Copyright (C) Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */
$content_before_class       = $params->get('content_before_class');
$content_before    = $params->get('content_before');
$modal_content    = $params->get('modal_content');
$modal_link    = $params->get('modal_link');

?>

<style type="text/css">
.jp-portfolio img { transform: scale(1); transform-origin: 50% 50% 0px; transition: all 0.3s ease 0s; }
.jp-portfolio a:hover img { transform: scale(1.0); }
.jp-portfolio .item-image-backdrop { position: absolute; top: 0px; left: 0px; z-index: 10; width: 100%; height: 100%; background-color: rgb(0, 0, 0); visibility: hidden; opacity: 0; transition: all 0.3s ease 0s; }
.jp-portfolio a:hover .item-image-backdrop { visibility: visible; opacity: 0.7; }
.jp-portfolio .item-image-backdrop::before { content: ""; z-index: 11; display: block; width: 25px; height: 1px; background-color: rgb(255, 255, 255); position: absolute; top: 50%; left: 50%; margin-left: -12px; transition: all 0.3s ease 0s; transform: translateX(-150px); }
.jp-portfolio .item-image-backdrop::after { content: ""; z-index: 12; display: block; width: 1px; height: 25px; background-color: rgb(255, 255, 255); position: absolute; top: 50%; left: 50%; margin-top: -12px; transition: all 0.3s ease 0s; transform: translateY(-150px); }
.jp-portfolio a:hover .item-image-backdrop::before { transform: translateX(0px); }
.jp-portfolio a:hover .item-image-backdrop::after { transform: translateY(0px); }
</style>
<?php if ($content_before) { ?>
<div class="<?php echo $content_before_class; ?>">
<?php echo $content_before; ?>
</div>
<?php } ?>

	<div uk-slider<?php echo $sw_params; ?>>
		<div class="uk-position-relative uk-visible-toggle">
			<ul class="uk-grid-match uk-slider-items <?php echo $classes; ?>" uk-grid>
				<?php foreach ($items as $item){?>
				<li>
					<div class="<?php echo $item_style; ?> <?php echo $center; ?>">
						<div class="jp-portfolio uk-card uk-card-media-top" >
							<?php if ($item->img) { ?>
								<div class="">
									<a href="#<?php echo $item->modal_link; ?>" uk-toggle>
									<img class="<?php echo $circle; ?>" src="<?php echo $item->img; ?>" alt="<?php echo $item->title; ?>">
									<span class="item-image-backdrop"></span>
									</a>
								</div>
							<?php } ?>
						</div>
					
						<?php if (($item->content) or ($item->title)): ?>
						<div class="p-3">
							<?php if ($item->title) { ?>
							<h4 class="uk-card-title"><?php echo $item->title; ?></h4>
							<?php } ?>
							
							<?php if ($item->content) { ?>
								<?php echo $item->content; ?>
							<?php } ?>
						</div>
						<?php endif; ?>
					</div>
					
					<!-- This is the modal -->
					<div id="<?php echo $item->modal_link; ?>" class="uk-modal-full" uk-modal >
						<div class="uk-modal-dialog">
							<button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
							<div class="uk-grid-collapse uk-child-width-1-2@s uk-flex-middle" uk-grid>
								<div class="uk-background-cover" style="background-image: url('<?php echo $item->img; ?>');" uk-height-viewport></div>
								<div class="uk-padding-large">
									<div><?php echo $item->modal_content; ?></div>
								</div>
							</div>
						</div>
					</div>		
					<!-- This is the modal -->
					
					<?php } ?>
				</li>
			</ul>
		</div>	
		<?php if ($slidenav) { ?>
		<a class="uk-position-center-left uk-position-small" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
		<a class="uk-position-center-right uk-position-small" href="#" uk-slidenav-next uk-slider-item="next"></a>    
		<?php } ?>
		<?php if ($dotnav) { ?>
		<div class="uk-flex uk-flex-center uk-margin-small-top">
			<ul class="uk-slider-nav uk-dotnav"></ul>
		</div>
		<?php } ?>
		
	</div>
