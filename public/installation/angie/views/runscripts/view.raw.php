<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieViewRunscripts extends AView
{
    public function onBeforeMain()
    {
        // Load system defines
        if (file_exists(APATH_ROOT . '/defines.php'))
        {
            include_once APATH_ROOT . '/defines.php';
        }

        if (!defined('_JDEFINES'))
        {
	        if(!defined('JPATH_BASE'))
	        {
		        define('JPATH_BASE', APATH_SITE);
	        }

	        require_once JPATH_BASE . '/includes/defines.php';
        }

        // Load the rest of the framework include files
        if (file_exists(JPATH_LIBRARIES . '/import.legacy.php'))
        {
            require_once JPATH_LIBRARIES . '/import.legacy.php';
        }
        else
        {
            require_once JPATH_LIBRARIES . '/import.php';
        }
        require_once JPATH_LIBRARIES . '/cms.php';

        // You can't fix stupid… but you can try working around it
        if( (!function_exists('json_encode')) || (!function_exists('json_decode')) )
        {
            require_once JPATH_ADMINISTRATOR . '/components/com_akeeba/helpers/jsonlib.php';
        }

	    // Manually require the configuration file
	    $this->container->platform->getConfig(JPATH_CONFIGURATION.'/configuration.php');

        // Load the JApplicationCli class
        JLoader::import('joomla.application.web');

        require_once APATH_INSTALLATION.'/angie/assets/runscripts.php';

        $run = JApplicationWeb::getInstance('RunScripts');

        $run->execute();

        return false;
    }
}
