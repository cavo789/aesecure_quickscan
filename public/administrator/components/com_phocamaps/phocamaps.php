<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
if (!Factory::getUser()->authorise('core.manage', 'com_phocamaps')) {
	throw new Exception(Text::_('COM_PHOCAMAPS_ERROR_ALERTNOAUTHOR'), 404);
	return false;
}
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
require_once JPATH_ADMINISTRATOR . '/components/com_phocamaps/libraries/autoloadPhoca.php';
require_once( JPATH_COMPONENT.'/controller.php' );
require_once( JPATH_COMPONENT.'/helpers/phocamaps.php' );
require_once( JPATH_COMPONENT.'/helpers/phocamapsmap.php' );
require_once( JPATH_COMPONENT.'/helpers/phocamapsmaposm.php' );
require_once( JPATH_COMPONENT.'/helpers/phocamapsutils.php' );
require_once( JPATH_COMPONENT.'/helpers/phocamapsrenderadmin.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadminview.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadminviews.php' );
require_once( JPATH_COMPONENT.'/helpers/html/map.php' );
require_once( JPATH_COMPONENT.'/helpers/html/batch.php' );

jimport('joomla.application.component.controller');
$controller	= BaseController::getInstance('PhocaMapsCp');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
?>
