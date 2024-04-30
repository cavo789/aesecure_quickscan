<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_article_grid
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
$content_before_class       = $params->get('content_before_class');
$item_class       = $params->get('item_class');
$content_before    = $params->get('content_before');

defined('_JEXEC') or die;
?>

<?php if ($content_before) { ?>
<div class="<?php echo $content_before_class; ?>">
<?php echo $content_before; ?>
</div>
<?php } ?>
<div uk-filter="target: .js-filter-<?php echo $module->id; ?>; duration: 1000" animation="delayed-fade">
	<?php if ($params->get('show_tags')) : ?>
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
		<?php
		foreach ($items as $key => $item)
		{
			
			$tags = $tagsList['items'][$key];
			$itemClass = ' ';
			foreach ($tags as $tag) {
				$itemClass .= $tagsList['index'][$tag] .' ';
			}
		?>
				
				<li class="uk-transition-toggle <?php echo $itemClass; ?>" >
					
					<div class="uk-inline-clip uk-transition-toggle" >
						<a class="uk-inline"  href="#<?php $str = "$item->title"; $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str); echo $str; ?>" uk-toggle >
						
						<img src="<?php echo $item->img; ?>" alt="<?php echo $item->title; ?>" >
						<div class="<?php echo $item->overlay_style; ?>">
							<span class="uk-transition-fade">
							<h3><?php echo $item->title; ?></h3>
							</span>
						</div>
						
						</a>
					</div>						
						
					<?php if ($item->modal_typ) { ?>
					<!-- This is the modal -->
					<div id="<?php $str = "$item->title"; $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str); echo $str; ?>" class="uk-modal-full" uk-modal>
						<div class="uk-modal-dialog">
							<button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
							
							<div class="uk-grid-collapse uk-child-width-1-2@s uk-flex-middle" uk-grid>
								<div class="uk-background-cover" style="background-image: url('<?php echo $item->img; ?>');" uk-height-viewport></div>
								<div class="uk-padding-large">
									
									<?php echo $item->modal_content; ?>
								</div>
							</div>
	
						</div>
					</div>		
					<!-- This is the modal -->		
					<?php } else { ?> 
					<!-- This is the modal -->
					<div id="<?php $str = "$item->title"; $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str); echo $str; ?>" class="uk-flex-top" uk-modal>
						<div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical" uk-overflow-auto>
						<button class="uk-modal-close-default" type="button" uk-close></button>
							
							
							
							<div><?php echo $item->modal_content; ?></div>
						</div>
					</div>	
					<!-- This is the modal -->
					<?php } ?>
					
					


				
				
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
</div>

