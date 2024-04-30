<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

$buttons = $this->getButtons();

if(!empty($buttons)) foreach($buttons as $button):

	if (!empty($button['url']))
	{
		$link = 'href = "' . htmlentities($button['url'], ENT_COMPAT, 'UTF-8') . '"';
	}
	else
	{
		$link = 'href="#" onclick="' . htmlentities($button['onclick'], ENT_COMPAT, 'UTF-8') . '"';
	}

	$class = "akeeba-btn";

	if (!empty($button['types'])) foreach($button['types'] as $type)
	{
		$class .= "--$type";
	}

	$iconClass = "";

	if (!empty($button['icons'])) foreach($button['icons'] as $icon)
	{
		$iconClass .= " akion-$icon";
	}

	$id = '';
	if (!empty($button['id']))
	{
		$id = ' id="' . $button['id'] . '" ';
	}
?>
<a <?php echo $link ?> class="<?php echo $class ?>"<?php echo $id ?>>
<?php if(!empty($iconClass)):?>
    <span class="<?php echo $iconClass ?>"></span>
<?php endif; ?>
    <?php echo $button['message']; ?>
</a>
<?php
endforeach;
