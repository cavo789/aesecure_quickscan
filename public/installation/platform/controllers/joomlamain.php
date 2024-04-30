<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieControllerJoomlaMain extends AngieControllerBaseMain
{
	/**
	 * Try to read configuration.php
	 */
	public function getconfig()
	{
		// Load the default configuration and save it to the session
		$data = $this->input->getData();

        /** @var AngieModelJoomlaConfiguration $model */
        $model = AModel::getAnInstance('Configuration', 'AngieModel', array(), $this->container);
        $this->input->setData($data);
        $this->container->session->saveData();

		// Try to load the configuration from the site's configuration.php
		$filename = APATH_SITE . '/configuration.php';

		if (file_exists($filename))
		{
			$vars = $model->loadFromFile($filename);

			foreach ($vars as $k => $v)
			{
				$model->set($k, $v);
			}

            $this->container->session->saveData();

			@ob_clean();
			echo json_encode(true);
		}
		else
		{
			@ob_clean();
			echo json_encode(false);
		}
	}
}
