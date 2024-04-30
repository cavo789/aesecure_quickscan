<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieModelFtpbrowser extends AModel
{
	/**
	 * The FTP connection object
	 * 
	 * @var AFtp
	 */
	private $ftp = null;
	
	/**
	 * Get the state variables pertaining to the FTP connection
	 * 
	 * @staticvar  \stdClass  $statevars
	 * 
	 * @return  \stdClass  A stdClass object with the variables
	 */
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
	
	public function getFtp()
	{
		static $ftp = null;
		
		if(!is_object($ftp))
		{
			$vars = $this->getStateVariables();
			$this->ftp = AFtp::getInstance($vars->hostname, $vars->port, array('type' => FTP_AUTOASCII), $vars->username, $vars->password);
		}
		return $this->ftp;
	}
	
	public function getListingAndCrumbs()
	{
		$vars = $this->getStateVariables();
		$ftp = $this->getFtp();
		
		try
		{
			$syst = $ftp->syst();
		}
		catch (Exception $exc)
		{
			$syst = 'UNIX';
		}

		$ds = ($syst == 'WIN') ? '\\' : '/';

		try
		{
			// Try to get the real FTP path
			$ftp->chdir($vars->directory);
			$path = $ftp->pwd();
		}
		catch (Exception $exc)
		{
			// The chdir failed. We will fall back to chopping the user-defined
			// directory, trying to go one level up and take it from there
			$path = '';
			if (empty($vars->directory))
			{
				// If the directory is empty, rethrow the exception
				throw $exc;
			}
			$pathParts = explode($ds, $vars->directory);
			if (count($pathParts) <= 1)
			{
				// If there are no path parts we assume the path is $ds (root)
				$path = $ds;
			}
			else
			{
				// Try to go one level up. If this throws an exception we cannot
				// figure out what we need to do and we will fail gracefully.
				array_pop($pathParts);
				$path = implode($ds, $pathParts);
				$ftp->chdir($path);
				$path = $ftp->pwd();
			}
		}

		// Now that we are connected, let's first get the crumbs
		$crumbs = array();
		$pathParts = explode($ds, $path);
		$pathToPart = '';
		foreach ($pathParts as $part)
		{
			$pathToPart .= $ds . $part;
			$crumbs[] = array(
				'name'	=> $part,
				'path'	=> $pathToPart
			);
		}
		
		// Get the directory listing
		$directories = array();
		try
		{
			$rawList = $ftp->listDetails($path, 'folders');
			foreach ($rawList as $item)
			{
				switch($item['name'])
				{
					case '.':
						continue;
						break;
					
					case '..';
						$myPathParts = explode($ds, $path);
						if (count($myPathParts) <= 1)
						{
							continue;
						}
						array_pop($myPathParts);
						$myPath = $ds . implode($ds, $myPathParts);
						break;
					
					default:
						$myPath = $path . $ds . $item['name'];
				}
				
				$directories[] = array(
					'name'	=> $item['name'],
					'path'	=> $myPath,
				);
			}
		}
		catch (Exception $exc)
		{
			// No directories for you, we can't list 'em
		}
		
		// Return the directories and crumbs
		return array(
			'directories'	=> $directories,
			'crumbs'		=> $crumbs,
			'path'			=> $path,
		);
	}

}
