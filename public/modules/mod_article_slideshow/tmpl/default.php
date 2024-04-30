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

defined('_JEXEC') or die;
?>
<div class="uk-position-relative uk-visible-toggle" data-uk-slideshow<?php echo $sw_params; ?>>
    <ul class="uk-slideshow-items">
			<?php foreach ($list as $item) : ?>
				<?php 
					
					$itemTags = $tagsList['items'][$item->id];
					$itemClass = ' ';
					foreach ($itemTags as $tag) {
						$itemClass .= $tagsList['index'][$tag] .' ';
					}					
				?>
				
		<li>
			<?php if ($params->get('img_intro_full') !== 'none' && !empty($item->imageSrc)) : ?>	
					<img src="<?php echo $item->imageSrc; ?>" alt="<?php echo $item->imageAlt; ?>" uk-cover>
			<?php endif; ?>
			
			<div uk-scrollspy="cls:uk-animation-fade; delay: 700; repeat:true" style="background:<?php echo $content_bg; ?>;margin:<?php echo $content_margin; ?>;width:<?php echo $content_width; ?>" class="p-3 <?php echo $content_text_align; ?> <?php echo $content_pos; ?>">
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
					<br/><p class="<?php echo $readmore_class; ?>" ><?php echo '<a class="readmore" href="' . $item->link . '">' . $readmoretext . '</a>'; ?></p>
				<?php endif; ?>
			</div>
        </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($slidenav) { ?>
	
    <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" data-uk-slidenav-previous data-uk-slideshow-item="previous"></a>
    <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" data-uk-slidenav-next data-uk-slideshow-item="next"></a>
    <?php } ?>

    <?php if ($dotnav) { ?>
	<div class="<?php if ($dotnav_pos) { ?>uk-position-bottom-center<?php } ?> uk-position-small">
	<ul class="uk-slideshow-nav uk-dotnav uk-flex-center uk-margin"></ul>
	</div>
    <?php } ?>

</div>







