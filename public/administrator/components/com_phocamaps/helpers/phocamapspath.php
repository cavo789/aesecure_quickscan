<?php
/*
 * @package Joomla 3.8
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Uri\Uri;

class PhocaMapsPath extends CMSObject
{
	function __construct() {}
	
	public static function getInstance() {
		static $instance;
		if (!$instance) {
			$instance = new PhocaMapsPath();
			$instance->kml_abs 			= JPATH_ROOT . '/phocamapskml/';
			$instance->kml_rel			= Uri::base(true) . '/phocamapskml/';
			$instance->kml_rel_full		= Uri::base() . 'phocamapskml/';
		}
		return $instance;
	}

	public static function getPath() {
		$instance 	= PhocaMapsPath::getInstance();
		return $instance;
	}

}
?>