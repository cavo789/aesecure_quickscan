<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

interface AUtilsParserInterface
{
	/**
	 * Get the parser's priority. Lowest numbers run first.
	 *
	 * @return  int
	 * @since   9.1.0
	 */
	public function getPriority();

	/**
	 * Is the parser supported on this server
	 *
	 * @return  bool
	 * @since   9.1.0
	 */
	public function isSupported();

	/**
	 * Parse a configuration file, returning an array of configuration values
	 *
	 * @param   string  $file       Absolute filesystem path to the file to parse
	 * @param   string  $className  Expected class name
	 *
	 * @return  array
	 */
	public function parseFile($file, $className);
}