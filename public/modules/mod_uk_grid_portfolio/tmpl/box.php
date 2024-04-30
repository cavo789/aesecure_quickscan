<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_grid
 * @copyright   © 2020 Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */
$content_before_class       = $params->get('content_before_class');
$content_before    = $params->get('content_before');



?>
<style type="text/css">

</style>
<?php if ($content_before) { ?>
<div class="<?php echo $content_before_class; ?>">
<?php echo $content_before; ?>
</div>
<?php } ?>
<div uk-filter="target: .js-filter-<?php echo $module->id; ?>;duration: 1200" animation="delayed-fade">
	<?php if ($params->get('show_tags')) : ?>
    <ul class="uk-subnav">
		<li class="uk-active" uk-filter-control><a href="#"><?php echo JText::_('MOD_UK_GRID_ALL'); ?></a></li>
		<?php foreach($tagsList['index'] as $tag => $tagClass):?>
		<li uk-filter-control="filter: .<?php echo $tagClass; ?>"><a href="#"><?php echo $tag; 'UTF-8'?></a></li>
		<?php endforeach; ?>
    </ul>
	<?php endif; ?>

	<div uk-scrollspy="target: > div > .uk-inline-clip; cls: uk-animation-scale-up; delay: 200" class="js-filter-<?php echo $module->id; ?> <?php echo $grid_class, $classes; ?>" uk-grid<?php echo $grid_params, $hm_param; ?>>
    <?php
		foreach ($items as $key => $item)
		{
			
			$tags = $tagsList['items'][$key];
			$itemClass = ' ';
			foreach ($tags as $tag) {
			$itemClass .= $tagsList['index'][$tag] .' ';
			}
		?>


		<div class="<?php echo $itemClass; ?>">
			<div class="uk-inline-clip uk-transition-toggle">
				<a href="#<?php echo $item->modal_id; ?>" uk-toggle>
				<img class="" src="<?php echo $item->img; ?>" alt="<?php echo $item->title; ?>">
				<?php if ($item->title) { ?>
					<div class="<?php echo $item->overlay_style; ?>">
						<h4 class="uk-margin-remove uk-text-center"><?php echo $item->title; ?></h4>
					</div>
				<?php } else { ?>
				<span class="item-image-backdrop"></span>	
				<?php } ?>	
				</a>				
			</div>
			
		
		
		<?php if ($item->modal_typ) { ?>
		<!-- This is the full modal -->
		<div id="<?php echo $item->modal_id; ?>" class="uk-modal-full" uk-modal>
			<div class="uk-modal-dialog">
				<button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
				<?php if ($item->modal_img) { ?>
				<div class="uk-grid-collapse uk-child-width-1-2@s uk-flex-middle" uk-grid>
					<div class="uk-background-cover" style="background-image: url('<?php echo $item->modal_img; ?>');" uk-height-viewport></div>
					<div class="uk-padding-large">
						
						<?php echo $item->content; ?>
					</div>
				</div>
				<?php } else { ?>
				<div class="uk-grid-collapse uk-child-width-1-1@s uk-flex-middle" uk-grid uk-height-viewport>
					<div class="uk-padding-large text-center">
						<p><?php echo $item->content; ?></p>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>		
		<!-- This is the modal -->		
		<?php } else { ?> 
		
		<!-- This is the modal -->
		<div id="<?php echo $item->modal_id; ?>" class="uk-flex-top" uk-modal>
			<div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical" uk-overflow-auto>
			<button class="uk-modal-close-default" type="button" uk-close></button>
				 
				<?php echo $item->content; ?>
			</div>
		</div>	
		<!-- This is the modal -->
		<?php } ?>

		
    </div>
    <?php } ?>
</div>
</div>

















	