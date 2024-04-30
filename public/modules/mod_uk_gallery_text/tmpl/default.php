<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_gallery_text
 * @copyright   © 2022 Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */
$content_before_class       = $params->get('content_before_class');
$content_before    = $params->get('content_before');
$content_class    = $params->get('content_class');

?>


<?php if ($content_before) { ?>
<div class="<?php echo $content_before_class; ?>">
<?php echo $content_before; ?>
</div>
<?php } ?>

<div uk-scrollspy="target: > div > .gal_con; cls: uk-animation-scale-up; delay: 300" uk-filter="target: .js-filter">

    <ul class="uk-subnav <?php echo $show_tags; ?>">
		<li class="uk-active" uk-filter-control><a href="#"><?php echo JText::_('MOD_UK_GALLERY_ALL'); ?></a></li>
		<?php foreach($tagsList['index'] as $tag => $tagClass):?>
		<li uk-filter-control="filter: .<?php echo $tagClass; ?>"><a href="#"><?php echo $tag; 'UTF-8'?></a></li>
		<?php endforeach; ?>
    </ul>

	<div uk-lightbox="animation: <?php echo $animation; ?>" class="js-filter mod_uk_gallery<?php echo $grid_class, $classes; ?>" uk-grid<?php echo $grid_params, $hm_param; ?> >
		<?php
		foreach ($items as $key => $item)
		{
			
			$tags = $tagsList['items'][$key];
			$itemClass = ' ';
			foreach ($tags as $tag) {
				$itemClass .= $tagsList['index'][$tag] .' ';
			}
		?>
			<div class="gal_con <?php echo $itemClass; ?>">
				<div class="uk-transition-toggle uk-inline " >
					<a data-caption="<?php echo $item->title; ?>" href="<?php echo $item->img; ?>">
					<img src="<?php echo $item->img; ?>" alt="<?php echo $item->title; ?>">
					<div class="uk-transition-fade uk-position-cover uk-overlay-default uk-position-cover"></div>
                        <div class="uk-position-center">
						<span class="uk-transition-fade" uk-icon="icon: plus; ratio: 2"></span>
					</div>

				</div>
				<?php if ($item->content) { ?>
				<div class="<?php echo $item->content_class; ?>"><?php echo $item->content; ?></div>
				<?php } ?>


				</a>
							
			</div>

		<?php } ?>	
		
	</div>
</div>


