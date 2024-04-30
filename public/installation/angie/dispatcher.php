<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieDispatcher extends ADispatcher
{
	public function onBeforeDispatch()
	{
		if (!$this->checkSessionBlock())
		{
			return true;
		}

		if (!$this->checkSession())
        {
			return false;
		}

		if (!$this->passwordProtection())
        {
			return false;
		}

		$view = $this->input->getCmd('view', '');
		$this->input->set('step', $view);

		return true;
	}

	/**
	 * Check if the session storage is working. If not, tell the user how to make it work.
	 *
	 * @return  bool
	 */
	private function checkSession()
	{
		if(!$this->container->session->isStorageWorking())
		{
			$view = $this->input->getCmd('view', $this->defaultView);

			if (!in_array($view, array('session', 'ftpbrowser')))
			{
				$this->container->application->redirect('index.php?view=session');
			}
		}

		return true;
	}

	/**
	 * Check if the installer is password protected. If it is and the user has not yet entered a password forward him to
	 * the password entry page.
	 *
	 * @return  boolean
	 */
	private function passwordProtection()
	{
		$filePath = APATH_INSTALLATION . '/password.php';

		if (file_exists($filePath))
		{
			include_once $filePath;
		}

		$view = $this->input->get('view', $this->defaultView);

		if (defined('AKEEBA_PASSHASH'))
		{
			$savedHash = $this->container->session->get('angie.passhash', null);
			$parts = explode(':', AKEEBA_PASSHASH);
			$correctHash = $parts[0];
			$allowedViews = array('password', 'session', 'ftpbrowser');

			if (defined('AKEEBA_PASSHASH') && !in_array($view, $allowedViews) && ($savedHash != $correctHash))
			{
				$this->container->session->disableSave();
				$this->container->application->redirect('index.php?view=password');

				return true;
			}
		}

		if (!defined('AKEEBA_PASSHASH') && ($this->input->get('view', $this->defaultView) == 'password'))
		{
			return false;
		}

		return true;
	}

	/**
	 * If the session save file is empty we have to show a warning to the user and refuse to do anything else. This
	 * case means that ANGIE has detected another active session in the tmp directory, i.e. someone else has already
	 * started restoring the site. Therefore we shouldn't allow the current user to continue.
	 *
	 * @return  bool
	 */
	private function checkSessionBlock()
	{
		if ($this->container->session->hasStorageFile())
		{
			return true;
		}

		$this->input->set('view', 'session');
		$this->input->set('task', 'default');
		$this->input->set('layout', 'blocked');

		return false;
	}
}
