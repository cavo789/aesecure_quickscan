<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

/**
 * Cannot connect to database: cannot access database
 */
class ADatabaseRestoreExceptionDbname extends ADatabaseRestoreExceptionDberror
{
	public function __construct($message = "", $code = 500, Exception $previous = null)
	{
		$message = $message ?: 'Cannot connect to the database: the database name is incorrect or the database user does not have access to this database.';

		parent::__construct($message, $code, $previous);
	}

}
