<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_pricetable
 * @copyright   Copyright (C) Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */
$content_before_class       = $params->get('content_before_class');
$content_before    = $params->get('content_before');

?>
<style type="text/css">
.bg-dark .border-bottom{border-color:#333 !important}

</style>
<?php if ($content_before) { ?>
<div class="<?php echo $content_before_class; ?>">
<?php echo $content_before; ?>
</div>
<?php } ?>
<div uk-slider <?php echo $sw_params; ?>>
    <div class="uk-position-relative uk-visible-toggle">
        <ul class="uk-slider-items <?php echo $classes; ?>" uk-grid>
            <?php
            foreach ($items as $item)
            {
               
            ?>
            <li>
				<div class="<?php echo $item_style; ?> <?php echo $center; ?> uk-card-hover ">
                    
                        <?php if ($item->img) { ?>
							<img src="<?php echo $item->img; ?>" alt="<?php echo $item->title; ?>">
						<?php } ?>
                    
						<?php if ($item->price) { ?>
						<div class="p-3 border-bottom <?php echo $item->background; ?>"><h2><?php echo $item->price; ?></h2>
						</div>
						<?php } ?>
						
						<?php if ($item->title1) { ?>
						<div class="p-3 border-bottom"><h4><?php echo $item->title1; ?></h4>
						</div>
						<?php } ?>
						
						<?php if ($item->title2) { ?>
						<div class="p-3 border-bottom"><h4><?php echo $item->title2; ?></h4>
						</div>
						<?php } ?>
						
						<?php if ($item->title3) { ?>
						<div class="p-3 border-bottom"><h4><?php echo $item->title3; ?></h4>
						</div>
						<?php } ?>
						<?php if ($item->content) { ?>
						<div class="p-3 border-bottom"><p><?php echo $item->content; ?></p>
						</div>
						<?php } ?>
						
						

						<?php if ($item->link) { ?>
							
							<div class="p-3 bg-primary text-white <?php echo trim(' ' . $item->al_content); ?>">
							
								<h3><a target="<?php echo $item->link_target; ?>" href="<?php echo $item->link; ?>"><?php echo $item->link_text; ?></a></h3>
							</div>
							
						<?php } ?>
				</div>
            </li>
            <?php } ?>
        </ul>
    
    
    <?php if ($slidenav) { ?>
        
 <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
        <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>    <?php } ?>
    
    <?php if ($dotnav) { ?>
    <div class="uk-flex uk-flex-center uk-margin-small-top">
        <ul class="uk-slider-nav uk-dotnav"></ul>
    </div>
    <?php } ?>
    
</div>
</div>
