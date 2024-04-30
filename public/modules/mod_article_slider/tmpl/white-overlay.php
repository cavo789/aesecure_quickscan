<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_article_grid
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');
$tags = new JHelperTags;
$taglayout = new JLayoutFile('joomla.content.tags');
$introtext_limit   = $params->get('introtext_limit');
$item_style   = $params->get('item_style');

defined('_JEXEC') or die;
?>
<div uk-filter="target: .js-filter-<?php echo $module->id; ?>; duration: 1000" animation="delayed-fade">
	<?php if ($params->get('show_tag_filter')) : ?>
    <ul class="uk-subnav">
		<li class="uk-active" uk-filter-control><a href="#"><?php echo JText::_('MOD_UK_GRID_ALL'); ?></a></li>
		<?php foreach($tagsList['index'] as $tag => $tagClass):?>
			<li uk-filter-control="filter: .<?php echo $tagClass; ?>"><a href="#"><?php echo $tag; ?></a></li>
		<?php endforeach; ?>

    </ul>
	<?php endif; ?>
	
	<div uk-slider<?php echo $sw_params; ?>>
		<div class="uk-position-relative uk-visible-toggle">
			<ul class="js-filter-<?php echo $module->id; ?> uk-slider-items uk-grid <?php echo $classes; ?>">
			<?php foreach ($list as $item) : ?>
				<?php 
					$itemTags = $tagsList['items'][$item->id];
					$itemClass = ' ';
					foreach ($itemTags as $tag) {
						$itemClass .= $tagsList['index'][$tag] .' ';
					}					
				?>
				
				<li class="uk-transition-toggle <?php echo $itemClass; ?>" >
					
					<?php if ($params->get('readmore') == '1' ) : ?>
					<div class="uk-inline-clip uk-transition-toggle" >
					

					<?php if ($params->get('readmore_popup') == '2' ) : ?>
						<span uk-lightbox> 
						<a data-type="iframe" class="uk-inline"  href="<?php echo $item->link; ?>?tmpl=component" >
					
						<?php if ($params->get('img_intro_full') !== 'none' && !empty($item->imageSrc)) : ?>
						<img src="<?php echo $item->imageSrc; ?>" alt="<?php echo $item->imageAlt; ?>" >
						<div class="uk-transition-fade uk-position-cover uk-overlay uk-overlay-default uk-dark uk-flex uk-flex-center uk-flex-middle">
							<span class="uk-transition-fade">
						<?php if ($params->get('item_title')) : ?>
							<?php $item_heading = $params->get('item_heading', 'h4'); ?>
							<<?php echo $item_heading; ?> class="newsflash-title<?php echo $params->get('moduleclass_sfx'); ?>">

								<?php echo $item->title; ?>
							</<?php echo $item_heading; ?>>
						<?php endif; ?>
								<?php if ($params->get('show_introtext', 1)) : ?>
							<?php echo JHtml::_('string.truncate', strip_tags($item->introtext), $introtext_limit); ?>
							<br/>
									

								<?php endif; ?>
							</span>
						</div>
						<?php endif; ?>
						</a>
						</span>
						<?php endif; ?>
						
					<?php if ($params->get('readmore_popup') == '1' ) : ?>
						<span > 
						<a class="uk-inline"  href="<?php echo $item->link; ?>" >
					
						<?php if ($params->get('img_intro_full') !== 'none' && !empty($item->imageSrc)) : ?>
						<img src="<?php echo $item->imageSrc; ?>" alt="<?php echo $item->imageAlt; ?>" >
						<div class="uk-transition-fade uk-position-cover uk-overlay uk-overlay-default uk-dark uk-flex uk-flex-center uk-flex-middle">
							<span class="uk-transition-fade">
						<?php if ($params->get('item_title')) : ?>
							<?php $item_heading = $params->get('item_heading', 'h4'); ?>
							<<?php echo $item_heading; ?> class="newsflash-title<?php echo $params->get('moduleclass_sfx'); ?>">

								<?php echo $item->title; ?>
							</<?php echo $item_heading; ?>>
						<?php endif; ?>
								<?php if ($params->get('show_introtext', 1)) : ?>
							<?php echo JHtml::_('string.truncate', strip_tags($item->introtext), $introtext_limit); ?>
							<br/>
									

								<?php endif; ?>
							</span>
						</div>
						<?php endif; ?>
						</a>
						</span>
						<?php endif; ?>
						
						
					</div>						
						
					<?php endif; ?>
					
					<?php if ($params->get('readmore') == '0' ) : ?>
					<div class="uk-inline-clip uk-transition-toggle" >
						
						<?php if ($params->get('img_intro_full') !== 'none' && !empty($item->imageSrc)) : ?>
						<img src="<?php echo $item->imageSrc; ?>" alt="<?php echo $item->imageAlt; ?>" >
						<div class="uk-transition-fade uk-position-cover uk-overlay uk-overlay-default uk-flex uk-flex-center uk-flex-middle">
							<span class="uk-transition-fade">
								<?php if ($params->get('item_title')) : ?>
								<h3><?php echo $item->title; ?></h3>
								<?php endif; ?>
								<?php if ($params->get('show_introtext', 1)) : ?>
								<?php echo JHtml::_('string.truncate', strip_tags($item->introtext), $introtext_limit); ?>
								<?php endif; ?>
							</span>
						</div>
						<?php endif; ?>
						
					</div>						
						
					<?php endif; ?>

				
				
				</li>
				<?php endforeach; ?>
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
</div>
<style type="text/css">
.uk-lightbox-iframe {
    width: 800px;
    height: 60%;
}
.none .p-3 {padding:0!important;padding-top:1rem!important}
.uk-dark {color:#000 !important}


</style>
