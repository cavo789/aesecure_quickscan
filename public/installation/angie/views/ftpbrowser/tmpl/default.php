<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

/** @var $this AView */

$document = $this->container->application->getDocument();

$document->addScript('angie/js/ftpbrowser.min.js');
?>

<?php if ($this->badFTP): ?>
<p></p>
<div class="alert alert-error">
	<?php echo AText::_('FTPBROWSER_ERR_CANNOTCONNECT') ?></br>
	<code><?php echo $this->ftpError; ?></code>
</div>
<?php else: ?>

<?php if(!empty($this->crumbs)):?>
<ul class="breadcrumb">
<?php foreach($this->crumbs as $crumb): ?>
	<li <?php echo ($crumb['path'] == $this->ftppath) ? 'class="active" ' : '' ?>>
		<a href="#" onclick="ftpBrowser.navTo('<?php echo $crumb['path'] ?>')">
			<?php echo $this->escape($crumb['name']); ?>
		</a>
		<?php if ($crumb['path'] != $this->ftppath): ?>
		<span class="divider">/</span>
		<?php endif; ?>
	</li>
<?php endforeach; ?>
	<li class="pull-right">
		<button type="button" class="btn btn-primary" onclick="ftpBrowser.useThis('<?php echo $crumb['path'] ?>')">
			<span class="icon-white icon-check"></span>
			<?php echo AText::_('FTPBROWSER_BTN_USE'); ?>
		</button>
	</li>
</ul>
<?php endif; ?>

<?php if(!empty($this->directories)):?>
<table class="table-striped" width="100%">
	<tbody>
<?php foreach($this->directories as $directory): ?>
		<tr>
			<td>
				<a href="#" onclick="ftpBrowser.navTo('<?php echo $directory['path'] ?>')">
					<?php echo $this->escape($directory['name']); ?>
				</a>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<div class="alert">
	<?php echo AText::_('FTPBROWSER_ERR_NODIRECTORIES'); ?>
</div>
<?php endif; ?>

<script type="text/javascript">
var ftpBrowser = null;
$(document).ready(function(){
	ftpBrowser = new ftpBrowserClass('<?php echo $this->baseURL ?>')
});
</script>

<?php endif; // badFTP ?>
