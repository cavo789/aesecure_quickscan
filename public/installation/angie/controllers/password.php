<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieControllerPassword extends AController
{
	public function unlock()
	{
		$parts    = explode(':', AKEEBA_PASSHASH);
		$password = $this->input->get('password', '', 'raw');
		$passHash = md5($password . $parts[1]);

		$this->container->session->set('angie.passhash', $passHash);

		if ($passHash == $parts[0])
		{
			$this->container->session->saveData();
			$this->setRedirect('index.php?view=main');

			return;
		}

		$msg = AText::_('PASSWORD_ERR_INVALIDPASSWORD');
		$this->container->session->disableSave();
		$this->setRedirect('index.php?view=password', $msg, 'error');
	}
}
