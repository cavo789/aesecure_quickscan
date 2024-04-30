<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_accordion
 * @copyright   © 2020 Joomlaplates. All rights reserved.
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
.uk-accordion > :nth-child(n+2) {
    margin-top: 0;
}
.uk-accordion-content {
  margin-top: 0;
}
img.hover-effect:hover {
  opacity: 0.5; /* On hover, set opacity to 2 */
}

.uk-open > .uk-accordion-title::before {
  content: "-";
  background-image: none;
  font-size: <?php echo $titlesize; ?>;

}
.uk-accordion-title::before {
  content: "+";
  background-image: none;
  font-size: <?php echo $titlesize; ?>;

}
@media (max-width: 768px) {
		img.accordion-img {width:100% !important}
		}

</style>

<ul class="mod_uk_accordion <?php echo $accordeon_class; ?>" data-uk-accordion<?php echo $accordion_params; ?>>
    <?php
    foreach ($items as $item)
    {
           ?>
    <li class="p-0">
        <a style="font-size:<?php echo $titlesize; ?>; line-height:<?php echo $titlesize; ?>; color:<?php echo $titlecolor; ?>; background:<?php echo $titlebg; ?>" href="#" class="uk-accordion-title p-3 <?php echo $titlespace; ?>" ><?php echo $item->title; ?></a>
		
		
        <div style="background:<?php echo $itembg; ?>" class="<?php echo $itemspace; ?> uk-accordion-content">
		<?php if ($item->img) { ?>
			<?php if ($item->img_popup) { ?>
			<div uk-lightbox>
				<a href="<?php echo $item->img; ?>">
				<img class="accordion-img uk-float-left hover-effect mb-2" style="margin-right:2em;width:<?php echo $item->img_width; ?>px;max-width:100%" src="<?php echo $item->img; ?>" alt="<?php echo $item->title; ?>">
				</a>
				
			</div>
			<?php } else { ?> 
			<img title="<?php echo $item->title; ?>" class="accordion-img uk-float-left mb-2" style="width:<?php echo $item->img_width; ?>px;max-width:100%;margin-right:2em" src="<?php echo $item->img; ?>" alt="<?php echo $item->title; ?>">
			
			<?php } ?>
		<?php } ?>
		<p><?php echo $item->content; ?></p>
		<?php if ($item->link) { ?>
		<a class="btn btn-sm btn-primary" href="<?php echo $item->link; ?>"><?php echo $item->link_text; ?></a>
		<?php } ?>
		</div>
    </li>
    <?php } ?>
</ul>
