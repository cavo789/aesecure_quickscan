<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

$data = $this->input->getData();
/** @var AngieModelSteps $stepsModel */
$stepsModel = AModel::getAnInstance('Steps', 'AngieModel', array(), $this->container);
$this->input->setData($data);
$crumbs = $stepsModel->getBreadCrumbs();
$i = 0;
?>

<?php if ((isset($helpurl) && !empty($helpurl)) || (isset($videourl) && !empty($videourl))): ?>
<div class="akeeba-block--info">
	<?php if (isset($helpurl) && !empty($helpurl)): ?>
	<?php echo AText::_('GENERIC_LBL_WHATTODONEXT'); ?>
	<a href="<?php echo $helpurl ?>" class="akeeba-btn--teal--small" target="_blank">
		<span class="akion-ios-book"></span>
		<?php echo AText::_('GENERIC_BTN_RTFM'); ?>
	</a>
	<?php endif; ?>
	<?php if (isset($videourl) && !empty($videourl)): ?>
	<a href="<?php echo $videourl ?>" class="akeeba-btn--dark--small" target="_blank">
		<span class="akion-videocamera"></span>
		<?php echo AText::_('GENERIC_BTN_VIDEO'); ?>
	</a>
	<?php endif; ?>
</div>
<?php endif; ?>

<ul class="breadcrumb">
<?php $found_active = false; foreach ($crumbs as $crumb): $i++; if ($crumb['active']) { $found_active = true; } ?>
  <li <?php echo $crumb['active'] ? 'class="active"' : '' ?>>
	  <?php echo AText::_('GENERIC_CRUMB_' . $crumb['name']) ?>
	  <?php if((($crumb['substeps'] - $crumb['active_substep']) > 0) && $found_active): ?>
	  <span class="akeeba-label--red">
		  <?php if ($crumb['active']): ?>
		  <?php echo $crumb['substeps'] - $crumb['active_substep'] ?>
		  <?php else: ?>
		  <?php echo $crumb['substeps'] ?>
		  <?php endif; ?>
	  </span>
	  <?php endif; ?>
	  <?php if($i < count($crumbs)): ?>
	  <span class="divider akion-chevron-right"></span>
	  <?php endif; ?>
  </li>
<?php endforeach; ?>
</ul>
