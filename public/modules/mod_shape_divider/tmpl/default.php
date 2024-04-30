<?php defined('_JEXEC') or die;
/*
 * @package     mod_shape_divider
 * @copyright   Copyright (C) Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

$shape_height    = $params->get('shape_height');
$shape_bg    = $params->get('shape_bg');
$shape_angle    = $params->get('shape_angle');
$shape_pos    = $params->get('shape_pos');


?>

<style type="text/css">
.skewed_<?php echo $module->id; ?>{
  position: absolute;
  top: 0;
  bottom: 0;
  right: 0;
  left: 0;
  width: 100%;
  height: 250px;
  background: <?php echo $shape_bg; ?>;
  z-index: 0;
  transform: skewY(<?php if ($params->get('shape_pos') !== 'right'  ) : ?>-<?php endif; ?><?php echo $shape_angle; ?>);
  transform-origin: top <?php echo $shape_pos; ?>;
}
</style>

<div class="skewed_<?php echo $module->id; ?>"></div>

