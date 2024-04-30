<?php
/**
 * @version      4.2
 * @package      Simple Image Gallery (plugin)
 * @author       JoomlaWorks - https://www.joomlaworks.net
 * @copyright    Copyright (c) 2006 - 2022 JoomlaWorks Ltd. All rights reserved.
 * @license      GNU/GPL license: https://www.gnu.org/licenses/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<ul id="sigFreeId<?php echo $gal_id; ?>" class="sigFreeContainer sigFreeClassic<?php echo $extraWrapperClass; ?>">
    <?php foreach($gallery as $count=>$photo): ?>
    <li class="sigFreeThumb">
        <a href="<?php echo $photo->sourceImageFilePath; ?>" class="sigFreeLink<?php echo $extraClass; ?>" style="width:<?php echo $photo->width; ?>px;height:<?php echo $photo->height; ?>px;" title="<?php echo JText::_('JW_PLG_SIG_YOU_ARE_VIEWING').' '.$photo->filename; ?>" data-thumb="<?php echo $photo->thumbImageFilePath; ?>" target="_blank"<?php echo $customLinkAttributes; ?>>
            <img class="sigFreeImg" src="<?php echo $transparent; ?>" alt="<?php echo JText::_('JW_PLG_SIG_CLICK_TO_ENLARGE_IMAGE').' '.$photo->filename; ?>" title="<?php echo JText::_('JW_PLG_SIG_CLICK_TO_ENLARGE_IMAGE').' '.$photo->filename; ?>" style="width:<?php echo $photo->width; ?>px;height:<?php echo $photo->height; ?>px;background-image:url('<?php echo $photo->thumbImageFilePath; ?>');" />
        </a>
    </li>
    <?php endforeach; ?>
    <li class="sigFreeClear">&nbsp;</li>
</ul>

<?php if($isPrintPage): ?>
<!-- Print output -->
<div class="sigFreePrintOutput">
    <?php foreach($gallery as $count => $photo): ?>
    <img src="<?php echo $photo->thumbImageFilePath; ?>" alt="<?php echo $photo->filename; ?>" />
    <?php if(($count+1)%3 == 0): ?><br /><br /><?php endif; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
