<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_grid
 * @copyright   © 2020 Theme-Point by Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */
$content_before_class       = $params->get('content_before_class');
$content_before    = $params->get('content_before');

?>
<style type="text/css">

.grid-hover img { transform: scale(1); transform-origin: 50% 50% 0px; transition: all 0.3s ease 0s; }
.grid-hover a:hover img { transform: scale(1.0); }
.grid-hover .item-image-backdrop { position: absolute; top: 0px; left: 0px; z-index: 10; width: 100%; height: 100%; background-color: rgb(0, 0, 0); visibility: hidden; opacity: 0; transition: all 0.3s ease 0s; }
.grid-hover a:hover .item-image-backdrop { visibility: visible; opacity: 0.7; }
.grid-hover .item-image-backdrop::before { content: ""; z-index: 11; display: block; width: 25px; height: 1px; background-color: rgb(255, 255, 255); position: absolute; top: 50%; left: 50%; margin-left: -12px; transition: all 0.3s ease 0s; transform: translateX(-150px); }
.grid-hover .item-image-backdrop::after { content: ""; z-index: 12; display: block; width: 1px; height: 25px; background-color: rgb(255, 255, 255); position: absolute; top: 50%; left: 50%; margin-top: -12px; transition: all 0.3s ease 0s; transform: translateY(-150px); }
.grid-hover a:hover .item-image-backdrop::before { transform: translateX(0px); }
.grid-hover a:hover .item-image-backdrop::after { transform: translateY(0px); }

</style>
<?php if ($content_before) { ?>
<div class="<?php echo $content_before_class; ?>">
<?php echo $content_before; ?>
</div>
<?php } ?>

<div uk-filter="target: .js-filter">

    <ul class="uk-subnav <?php echo $show_tags; ?>">
		<li class="uk-active" uk-filter-control><a href="#"><?php echo JText::_('MOD_UK_GRID_ALL'); ?></a></li>
		<?php foreach($tagsList['index'] as $tag => $tagClass):?>
		<li uk-filter-control="filter: .<?php echo $tagClass; ?>"><a href="#"><?php echo $tag; 'UTF-8'?></a></li>
		<?php endforeach; ?>
    </ul>

	<div class="js-filter mod_uk_grid<?php echo $grid_class, $classes; ?>" uk-grid<?php echo $grid_params, $hm_param; ?>>
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
			<div class="grid-hover <?php echo $item_style; ?>">
				<?php if ($item->img) { ?>
					<?php if ($item->img_popup) { ?>
					<div uk-lightbox class="uk-inline-clip">
						<a href="<?php echo $item->img; ?>">
						<img src="<?php echo $item->img; ?>" alt="<?php echo $item->title; ?>">
						<span class="item-image-backdrop"></span>
						</a>
					</div>
					<?php } else { ?> 			
				
					<div><img src="<?php echo $item->img; ?>" alt="<?php echo $item->title; ?>"></div>
					<?php } ?>
				<?php } ?>
				<div class="px-3">
				<?php if ($item->title) { ?>
				<h4 class="pt-3 <?php echo $item->al_title; ?>"><?php echo $item->title; ?></h4>
				<?php } ?>
				
				<?php if ($item->content) { ?>
				<p class="py-2 <?php echo $item->al_content; ?>"><?php echo $item->content; ?></p>
				<?php } ?>
				
				<?php if ($item->link) { ?>
					<div class="pb-3 <?php echo trim(' ' . $item->al_content); ?>">
						<a target="<?php echo $item->link_target; ?>" class="btn btn-sm btn-primary" href="<?php echo $item->link; ?>"><?php echo $item->link_text; ?></a>
					</div>
					
				<?php } ?>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
