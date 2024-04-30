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
						<a class="uk-inline"  href="<?php echo $item->link; ?>" >
						<?php if ($params->get('img_intro_full') !== 'none' && !empty($item->imageSrc)) : ?>
						<img src="<?php echo $item->imageSrc; ?>" alt="<?php echo $item->imageAlt; ?>" >
						<div class="uk-transition-fade uk-position-cover uk-overlay uk-overlay-primary uk-flex uk-flex-center uk-flex-middle">
							<span class="uk-transition-fade">
							<?php if ($params->get('item_title')) : ?><?php echo $item->title; ?><?php endif; ?>
								<?php if ($params->get('show_introtext', 1)) : ?>
									<?php
									$limit =$introtext_limit; if (strlen($item->introtext) > $limit) {
									echo (substr($item->introtext, 0, $limit)) . " ... ";
									}
									else {
									echo $item->introtext;
									}
									?>
									<?php else : ?>
									<span class="uk-transition-fade" uk-icon="icon: plus; ratio: 2"></span>

								<?php endif; ?>
							</span>
						</div>
						<?php endif; ?>
						</a>
					</div>						
						
					<?php endif; ?>
					
					<?php if ($params->get('readmore') == '0' ) : ?>
					<div class="uk-inline-clip uk-transition-toggle" >
						
						<?php if ($params->get('img_intro_full') !== 'none' && !empty($item->imageSrc)) : ?>
						<img src="<?php echo $item->imageSrc; ?>" alt="<?php echo $item->imageAlt; ?>" >
						<div class="uk-transition-fade uk-position-cover uk-overlay uk-overlay-primary uk-flex uk-flex-center uk-flex-middle">
							<span class="uk-transition-fade">
								<?php if ($params->get('item_title')) : ?><?php echo $item->title; ?><?php endif; ?>
								<?php if ($params->get('show_introtext', 1)) : ?>
									<?php
									$limit =$introtext_limit; if (strlen($item->introtext) > $limit) {
									echo (substr($item->introtext, 0, $limit)) . " ... ";
									}
									else {
									echo $item->introtext;
									}
									?>
									<?php else : ?>
									<span class="uk-transition-fade" uk-icon="icon: plus; ratio: 2"></span>

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
</style>
