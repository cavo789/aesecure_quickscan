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
 * This file may contain code from the Joomla! Platform, Copyright (c) 2005 -
 * 2012 Open Source Matters, Inc. This file is NOT part of the Joomla! Platform.
 * It is derivative work and clearly marked as such as per the provisions of the
 * GNU General Public License.
 */

abstract class AUtilsPath
{
	/**
	 * Function to strip additional / or \ in a path name.
	 *
	 * @param   string  $path  The path to clean.
	 * @param   string  $ds    Directory separator (optional).
	 *
	 * @return  string  The cleaned path.
	 */
	public static function clean($path, $ds = DIRECTORY_SEPARATOR)
	{
		$path = trim($path);

		if (empty($path))
		{
			$path = APATH_ROOT;
		}
		else
		{
			// Remove double slashes and backslashes and convert all slashes and backslashes to DIRECTORY_SEPARATOR
			$path = preg_replace('#[/\\\\]+#', $ds, $path);
		}

		return $path;
	}

	/**
	 * Checks for snooping outside of the file system root.
	 *
	 * @param   string  $path  A file system path to check.
	 * @param   string  $ds    Directory separator (optional).
	 *
	 * @return  string  A cleaned version of the path or exit on error.
	 */
	public static function check($path, $ds = DIRECTORY_SEPARATOR)
	{
		if (strpos($path, '..') !== false)
		{
			// Don't translate
			throw new Exception( __CLASS__ . '::check Use of relative paths not permitted', 20);
		}

		$path = self::clean($path);
		if ((APATH_ROOT != '') && strpos($path, self::clean(APATH_ROOT)) !== 0)
		{
			// Don't translate
			throw new Exception( __CLASS__ . '::check Snooping out of bounds @ ' . $path, 20);
		}

		return $path;
	}

	/**
	 * Searches the directory paths for a given file.
	 *
	 * @param   mixed   $paths  An path string or array of path strings to search in
	 * @param   string  $file   The file name to look for.
	 *
	 * @return  mixed   The full path and file name for the target file, or boolean false if the file is not found in any of the paths.
	 */
	public static function find($paths, $file)
	{
		settype($paths, 'array'); //force to array

		// Start looping through the path set
		foreach ($paths as $path)
		{
			// Get the path to the file
			$fullname = $path . '/' . $file;

			// Is the path based on a stream?
			if (strpos($path, '://') === false)
			{
				// Not a stream, so do a realpath() to avoid directory
				// traversal attempts on the local file system.
				$path = realpath($path); // needed for substr() later
				$fullname = realpath($fullname);
			}

			// The substr() check added to make sure that the realpath()
			// results in a directory registered so that
			// non-registered directories are not accessible via directory
			// traversal attempts.
			if (file_exists($fullname) && substr($fullname, 0, strlen($path)) == $path)
			{
				return $fullname;
			}
		}

		// Could not find the file in the set of paths
		return false;
	}
}
