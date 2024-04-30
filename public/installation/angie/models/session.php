<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieModelSession extends AModel
{
	public function getStateVariables()
	{
		static $statevars = null;
		
		if (is_null($statevars))
		{
			$statevars = new stdClass();
			$vars = array('hostname', 'port', 'username', 'password', 'directory');
			
			foreach ($vars as $v)
			{
				$value = $this->getState($v, null, 'raw');
				
				$statevars->$v = $value;
				
				switch ($v)
				{
					case 'hostname':
						if (empty($statevars->$v))
						{
							$uri = AUri::getInstance();
							$statevars->$v = $uri->getHost();
						}
						break;
					
					case 'port':
						$statevars->$v = (int)$statevars->$v;
						if (($statevars->$v <= 0) || ($statevars->$v >= 65536))
						{
							$statevars->$v = 21;
						}
						break;
				}
			}
		}
		
		return $statevars;
	}
	
	public function fix()
	{
		// Connect to FTP
		$vars = $this->getStateVariables();
		$ftp = AFtp::getInstance($vars->hostname, $vars->port, array('type' => FTP_AUTOASCII), $vars->username, $vars->password);
		
		$root = rtrim($vars->directory,'/');
		
		// Can we find ourself?
		try
		{
			$ftp->chdir($root . '/installation');
			$ftp->read('defines.php', $buffer);
			if (!strlen($buffer))
			{
				throw new Exception('Cannot read defines.php');
			}
		}
		catch (Exception $exc)
		{
			throw new Exception(AText::_('SESSION_ERR_INVALIDDIRECTORY'));
		}
		
		// Let's try to chmod the directory
		$success = true;
		try
		{
			$trustMeIKnowWhatImDoing = 500 + 10 + 1; // working around overzealous scanners written by bozos
			$ftp->chmod($root . '/installation/tmp', $trustMeIKnowWhatImDoing);
		}
		catch (Exception $exc)
		{
			$success = false;
		}
		if ($success) return true;
		
		try
		{
			// That didn't work. Let's try creating an empty file in there.
			$ftp->write($root . '/installation/tmp/storagedata.dat', '');

			// ...and let's try giving it some Number Of The Server Beast permissions
			$trustMeIKnowWhatImDoing = 500 + 10 + 1; // working around overzealous scanners written by bozos
			$ftp->chmod($root . '/installation/tmp/storagedata.dat', $trustMeIKnowWhatImDoing);
		}
		catch (Exception $exc)
		{
			throw new Exception(AText::_('SESSION_ERR_CANNOTFIX'));
		}

		return true;
	}
}
