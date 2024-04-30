<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieApplication extends AApplication
{
	public function initialise()
	{
		$this->setTemplate('flat');

		// Load the version file
		require_once APATH_INSTALLATION . '/version.php';

        // Load text callbacks
		if (file_exists(APATH_INSTALLATION.'/angie/platform/iniprocess.php'))
		{
			require_once APATH_INSTALLATION.'/angie/platform/iniprocess.php';

			AText::addIniProcessCallback(array('IniProcess', 'processLanguageIniFile'));
		}
        elseif (file_exists(APATH_INSTALLATION.'/platform/iniprocess.php'))
        {
            require_once APATH_INSTALLATION.'/platform/iniprocess.php';

            AText::addIniProcessCallback(array('IniProcess', 'processLanguageIniFile'));
        }
	}
}
