<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieControllerSession extends AController
{
	public function main()
	{
		$session = $this->container->session;

		if ($session->isStorageWorking())
		{
			$this->setRedirect('index.php?view=main');
			return;
		}

		parent::main();
	}

	public function fix()
	{
		try
		{
			$this->getThisModel()->fix();
			$this->setRedirect('index.php?view=main');
		}
		catch (Exception $exc)
		{
			$this->container->application->enqueueMessage($exc->getMessage(), 'error');
			$this->task = 'main';
			$this->doTask = 'main';
			$this->main();
		}
	}
}
