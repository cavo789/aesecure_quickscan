<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

function _angie_autoloader($class_name)
{
    static $angiePath = null;

    // The best thing would be having namespaces, but we can try to emulate the same behavior with CamelCase classes
    $parts = preg_split('#(?<!^)(?=[A-Z])#', $class_name);

    // Make sure the class has an Angie prefix
    if($parts[0] != 'Angie')
    {
        return;
    }

    // Currently we are using the autoloader only for the base class of Controllers and Models
    // Quick check on number of parts
    if(count($parts) < 4)
    {
        return;
    }

    $parts = array_map('strtolower', $parts);

    if(!in_array($parts[1], array('controller', 'model')) || $parts[2] != 'base')
    {
        return;
    }

    // Set up the path to the application
    if(is_null($angiePath))
    {
        $angiePath = __DIR__;
    }

    // Very simple inflector
    $plural = array(
        'controller' => 'controllers',
        'model'      => 'models'
    );

    $file_path = $angiePath.'/'.$plural[$parts[1]].'/base/'.$parts[3].'.php';

    if(file_exists($file_path))
    {
        include_once $file_path;
    }
}

// Register the autoloader
if( function_exists('spl_autoload_register') )
{
    // Joomla! is using its own autoloader function which has to be registered first...
    if(function_exists('__autoload'))
    {
        spl_autoload_register('__autoload');
    }

    // ...and then register ourselves.
    spl_autoload_register('_angie_autoloader');
}
else
{
    throw new Exception('Akeeba Next Generation Installer Framework requires the SPL extension to be loaded and activated', 500);
}
