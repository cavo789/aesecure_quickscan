<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
 defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Controller\BaseController;
jimport('joomla.application.component.controller');
$app		= Factory::getApplication();
$option 	= $app->input->get('option');

$l['cp']	= array('COM_PHOCAMAPS_CONTROL_PANEL', '');
$l['mp']	= array('COM_PHOCAMAPS_MAPS', 'phocamapsmaps');
$l['mr']	= array('COM_PHOCAMAPS_MARKERS', 'phocamapsmarkers');
$l['mi']	= array('COM_PHOCAMAPS_ICONS', 'phocamapsicons');
$l['in']	= array('COM_PHOCAMAPS_INFO', 'phocamapsinfo');

// Submenu view


$view	= Factory::getApplication()->input->get('view');
$layout	= Factory::getApplication()->input->get('layout');

if ($layout == 'edit') {
} else {
	foreach ($l as $k => $v) {
		
		if ($v[1] == '') {
			$link = 'index.php?option='.$option;
		} else {
			$link = 'index.php?option='.$option.'&view=';
		}

		if ($view == $v[1]) {
			JHtmlSidebar::addEntry(Text::_($v[0]), $link.$v[1], true );
		} else {
			JHtmlSidebar::addEntry(Text::_($v[0]), $link.$v[1]);
		}
	}
}
class phocaMapsCpController extends BaseController
{
	function display($cachable = false, $urlparams = array()) {
		parent::display($cachable, $urlparams);
	}
}
?>
