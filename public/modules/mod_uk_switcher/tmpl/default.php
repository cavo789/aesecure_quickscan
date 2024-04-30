<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_switcher
 * @copyright   Copyright (C) Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */


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
<ul class="uk-tab" uk-switcher="animation: uk-animation-slide-left-medium, uk-animation-slide-right-medium">
    <?php
            foreach ($items as $item)
            { ?>
				<li><a href="#"><?php echo $item->title; ?></a></li>
	<?php } ?>	
</ul>


<ul class="uk-switcher uk-margin">
    <?php
    foreach ($items as $item)
    {
       
    ?>
	<li>
	
	<div class="<?php echo $item_style; ?> <?php if ($item->item_space) { ?>uk-card-large<?php } else { ?>uk-card-small<?php } ?> <?php if ($item->img) { ?>uk-grid-collapse uk-child-width-1-2@m<?php } else { ?>uk-grid-collapse uk-child-width-1-1@m<?php } ?>" uk-grid>
		<?php if ($item->img) { ?>
		<div class="<?php if ($item->media_align) { ?>uk-card-media-left<?php } else { ?>uk-flex-last@m uk-card-media-right<?php } ?> uk-cover-container">
			<?php if ($item->img_popup) { ?>
				<div uk-lightbox class="img-container">
					<a href="<?php echo $item->img; ?>">
						<img class="hover-effect" title="<?php echo $item->title; ?>" src="<?php echo $item->img; ?>" uk-cover >
						<canvas width="auto" height="auto"></canvas>
					</a>	
				</div>
			<?php } else { ?> 
			
			<img title="<?php echo $item->title; ?>" src="<?php echo $item->img; ?>" alt="<?php echo $item->title; ?>" uk-cover>
			<canvas width="auto" height="<?php echo $img_height; ?>"></canvas>
			<?php } ?>
		</div>
		<?php } ?>	
	
	
	
		<div style="padding-left:5%;padding-right:5%" class="uk-card-body">
			<?php if ($item->title) { ?>
				<h4 class="uk-card-title"><?php echo $item->title; ?></h4>
			<?php } ?>
			
			<?php if ($item->content) { ?>
				<p><?php echo $item->content; ?></p>
			<?php } ?>
		
			<?php if ($item->link) { ?>
			<div class="uk-margin-small-top <?php echo trim(' ' . $item->al_content); ?>">
				<a class="btn btn-sm btn-primary" href="<?php echo $item->link; ?>"><?php echo $item->link_text; ?></a>
			</div>
			<?php } ?>
		</div>
	</div>	
		

</li>	
<?php } ?>
</ul>
	
	
	
	
	
	
	
	
	
	
	
