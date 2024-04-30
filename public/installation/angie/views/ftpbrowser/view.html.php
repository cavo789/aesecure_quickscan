<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieViewFtpbrowser extends AView
{
	public function onBeforeMain()
	{
		try
		{
			$stuff = $this->getModel()->getListingAndCrumbs();
			
			$uri = clone AUri::getInstance();
			$queryParts = $uri->getQuery(true);
			unset($queryParts['directory']);
			$uri->setQuery($queryParts);
			
			$this->ftppath = $stuff['path'];
			$this->crumbs = $stuff['crumbs'];
			$this->directories = $stuff['directories'];
			$this->badFTP = false;
			$this->ftpError = '';
			$this->baseURL = $uri->toString();
		}
		catch (Exception $exc)
		{
			$this->badFTP = true;
			$this->ftpError = $exc->getMessage();
		}
		
		return true;
	}
}
