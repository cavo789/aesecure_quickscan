<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

if(!defined('ANGIE_LOG_ERROR'))
{
	define('ANGIE_LOG_ERROR',		90);
	define('ANGIE_LOG_WARNING',		70);
	define('ANGIE_LOG_NOTICE',		50);
	define('ANGIE_LOG_INFO',		30);
	define('ANGIE_LOG_DEBUG',		10);
}

abstract class ALog
{
	public static function _($level, $message)
	{
		if (!defined('AKEEBA_DEBUG'))
		{
			return;
		}
		
		switch ($level)
		{
			case ANGIE_LOG_ERROR:
				$type = 'ERROR';
				break;

			case ANGIE_LOG_WARNING:
				$type = 'WARNING';
				break;

			case ANGIE_LOG_NOTICE:
				$type = 'NOTICE';
				break;

			case ANGIE_LOG_INFO:
				$type = 'INFO';
				break;

			case ANGIE_LOG_DEBUG:
			default:
				$type = 'DEBUG';
				break;
		}
		
		$timestring = gmdate('Y/m/d H:i:s');
		$line = str_pad($type, 8, ' ') . '| ' . $timestring . ' | '
				. str_replace("\n", ' ', $message) . "\n";
		
		$fp = @fopen(APATH_INSTALLATION . '/log.txt', 'a');
		if ($fp !== false)
		{
			@fputs($fp, $line);
			@fclose($fp);
		}
	}
}
