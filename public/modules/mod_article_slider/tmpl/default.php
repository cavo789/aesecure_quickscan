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
$readmoretext   = $params->get('readmoretext');
$readmore_class   = $params->get('readmore_class');
$strip_tags   = $params->get('strip_tags');

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
	
	<div class="uk-slider-container-offset" uk-slider<?php echo $sw_params; ?>>
		<div class="uk-position-relative uk-visible-toggle">
			<ul class="js-filter-<?php echo $module->id; ?> uk-slider-items <?php echo $classes; ?>" uk-grid uk-height-match="target: > .uk-card">
			<?php foreach ($list as $item) : ?>
				<?php 
					$itemTags = $tagsList['items'][$item->id];
					$itemClass = ' ';
					foreach ($itemTags as $tag) {
						$itemClass .= $tagsList['index'][$tag] .' ';
					}					
				?>
				
				<li class="uk-card <?php echo $itemClass; ?>" >
					<div class="<?php echo $item_style; ?> <?php echo $center; ?>">
						<div class="uk-card-media-top" >
							<?php if ($params->get('img_intro_full') !== 'none' && !empty($item->imageSrc)) : ?>	
								
									<img src="<?php echo $item->imageSrc; ?>" alt="<?php echo $item->imageAlt; ?>" >
								
							<?php endif; ?>
						</div>
					
						<div class="p-4" >
						<?php if ($params->get('item_title')) : ?>
							<?php $item_heading = $params->get('item_heading', 'h4'); ?>
							<<?php echo $item_heading; ?> class="newsflash-title<?php echo $params->get('moduleclass_sfx'); ?>">
							<?php if ($item->link !== '' && $params->get('link_titles')) : ?>
								<a href="<?php echo $item->link; ?>">
									<?php echo $item->title; ?>
								</a>
							<?php else : ?>
								<?php echo $item->title; ?>
							<?php endif; ?>
							</<?php echo $item_heading; ?>>
						<?php endif; ?>
						
						<?php if ($params->get('displayDate')) : ?>
							<p class="uk-text-small uk-margin-remove"><?php echo JHtml::_('date',$item->created, JText::_('DATE_FORMAT_LC1')); ?></p>
						<?php endif; ?>

						<?php if ($params->get('show_tags')) : ?>
							<?php $tags->getItemTags('com_content.article', $item->id)?>
							<p>Tags:
							<?php foreach ($tags->itemTags as $tag) : ?>
							<span class="badge badge-primary d-inline"><?php echo $tag->title;?></span>
							<?php endforeach; ?>
							</p>
						<?php endif; ?>
							
				<?php if ($params->get('show_introtext', 1)) : ?>
					<?php if ($params->get('strip_tags') == '1' ) : ?>
					
						<?php echo JHtml::_('string.truncate', strip_tags($item->introtext), $introtext_limit); ?>
					<br/>
					<?php endif; ?>


					<?php if ($params->get('strip_tags') == '2' ) : ?>
						
						<?php echo JHtml::_('string.truncate', $item->introtext, $introtext_limit); ?>
					<br/>
					<?php endif; ?>
					
				<?php endif; ?>

							
				<?php echo $item->afterDisplayContent; ?>

						<?php if ($params->get('readmore') == '1' ) : ?>
							<br/>
							<?php if ($params->get('readmore_popup') == '1' ) : ?>
								<p class="<?php echo $readmore_class; ?>" ><?php echo '<a class="readmore" href="' . $item->link . '">' . $readmoretext . '</a>'; ?></p>
							<?php endif; ?>
							
							<?php if ($params->get('readmore_popup') == '2' ) : ?>
							<p uk-lightbox class="<?php echo $readmore_class; ?>" ><?php echo '<a data-type="iframe" class="readmore" href="' . $item->link . '?tmpl=component">' . $readmoretext . '</a>'; ?></p>
							<?php endif; ?>
							
						<?php endif; ?>
							
							
						</div>
					</div>
				
				</li>
				<?php endforeach; ?>
			</ul>
			<?php if ($slidenav) { ?>
			<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
			<a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>    
			<?php } ?>
					<?php if ($dotnav) { ?>
		<div class="uk-flex uk-flex-center uk-margin-small-top">
			<ul class="uk-slider-nav uk-dotnav"></ul>
		</div>
		<?php } ?>
		</div>
		

		
	</div>
</div>
<style type="text/css">
.uk-lightbox-iframe {
    width: 800px;
    height: 60%;
}
.none .p-4 {padding:0!important;padding-top:1rem!important}
</style>