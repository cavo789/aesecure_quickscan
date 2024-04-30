<?php
/*
 * @package Joomla 3.8
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Factory;
require_once( JPATH_COMPONENT.'/controller.php' );
require_once( JPATH_COMPONENT.'/helpers/route.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocamaps/helpers/phocamapspath.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocamaps/helpers/phocamaps.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocamaps/helpers/phocamapsmap.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocamaps/helpers/phocamapsmaposm.php' );

// Require specific controller if requested
if($controller = Factory::getApplication()->input->get( 'controller')) {
    $path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}
// Create the controller
$classname    = 'PhocaMapsController'.ucfirst($controller);
$controller   = new $classname( );

// Perform the Request task
$controller->execute( Factory::getApplication()->input->get('task') );

// Redirect if set by the controller
$controller->redirect();
?>