<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class RunScripts extends JApplicationWeb
{
    public function doExecute()
    {
        // The script file requires an installer instance, however it's not used inside the code...
        $installer  = JInstaller::getInstance();
        $scriptFile = JPATH_ROOT.'/administrator/components/com_admin/script.php';

        if (!is_file($scriptFile))
        {
            return;
        }

        include_once $scriptFile;

        $classname = 'JoomlaInstallerScript';

        if (!class_exists($classname))
        {
            return;
        }

        $manifestClass = new $classname();

        if ($manifestClass && method_exists($manifestClass, 'update'))
        {
	        // We need to call this statement for JFactory to populate $application with JApplicationSite
	        /**
	         * Joomla! 3.5+'s update script calls cleanJoomlaCache() which in turns loads a JModelLegacy class called
	         * CacheModelCache. This class tries to figure out the client_id using getUserStateFromRequest which in
	         * turn calls JFactory::getApplication. However, since JFactory has no $application set and the call to
	         * getApplication does not have a site ID parameter ($id is implicitly null), JFactory::getApplication
	         * throws an exception.
	         *
	         * We need to call JFactory::getApplication('site'), that is WITH the 'site' parameter, so that JFactory
	         * can prime the $application variable. Otherwise we'd have to inject a mock object into JFactory to satisfy
	         * the Joomla! internals.
	         */
	        try
	        {
		        $app = JFactory::getApplication('site');
	        }
	        catch (Exception $e)
	        {
		        // No problemo if we fail
	        }

	        /**
	         * Following the logic above and trying to guard against future changes in Joomla! which might affect our
	         * code, let's put everything in a try-catch block and Hope For The Best™.
	         */
	        try
	        {
		        $manifestClass->update($installer);
	        }
	        catch (Exception $e)
	        {
		        // Don't cry if it fails
	        }
        }
    }
}
