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

/**
 * Joomla Platform Database Factory class
 */
class ADatabaseFactory
{
	/**
	 * Contains the current ADatabaseFactory instance
	 *
	 * @var    ADatabaseFactory
	 * @since  12.1
	 */
	private static $_instance = null;

	/**
	 * Method to return a ADatabaseDriver instance based on the given options. There are three global options and then
	 * the rest are specific to the database driver. The 'database' option determines which database is to
	 * be used for the connection. The 'select' option determines whether the connector should automatically select
	 * the chosen database.
	 *
	 * Instances are unique to the given options and new objects are only created when a unique options array is
	 * passed into the method.  This ensures that we don't end up with unnecessary database connection resources.
	 *
	 * @param   string  $name     Name of the database driver you'd like to instantiate
	 * @param   array   $options  Parameters to be passed to the database driver.
	 *
	 * @return  ADatabaseDriver  A database driver object.
	 */
	public function getDriver($name = 'mysqli', $options = array())
	{
		// Sanitize the database connector options.
		$options['driver']   = preg_replace('/[^A-Z0-9_\.-]/i', '', $name);
		$options['database'] = (isset($options['database'])) ? $options['database'] : null;
		$options['select']   = (isset($options['select'])) ? $options['select'] : true;

		// Derive the class name from the driver.
		$class = 'ADatabaseDriver' . ucfirst(strtolower($options['driver']));

		// If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
		if (!class_exists($class))
		{
			throw new RuntimeException(sprintf('Unable to load Database Driver: %s', $options['driver']));
		}

		// Create our new ADatabaseDriver connector based on the options given.
		try
		{
			$instance = new $class($options);
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException(sprintf('Unable to connect to the Database: %s', $e->getMessage()));
		}

		return $instance;
	}

	/**
	 * Gets an instance of the factory object.
	 *
	 * @return  ADatabaseFactory
	 */
	public static function getInstance()
	{
		return self::$_instance ? self::$_instance : new ADatabaseFactory;
	}

	/**
	 * Get the current query object or a new ADatabaseQuery object.
	 *
	 * @param   string           $name  Name of the driver you want an importer for.
	 * @param   ADatabaseDriver  $db    Optional ADatabaseDriver instance
	 *
	 * @return  ADatabaseQuery  The current query object or a new object extending the ADatabaseQuery class.
	 *
	 * @throws  RuntimeException
	 */
	public function getQuery($name, ADatabaseDriver $db = null)
	{
		// Derive the class name from the driver.
		$class = 'ADatabaseQuery' . ucfirst(strtolower($name));

		// Make sure we have a query class for this driver.
		if (!class_exists($class))
		{
			// If it doesn't exist we are at an impasse so throw an exception.
			throw new RuntimeException('Database Query class not found');
		}

		return new $class($db);
	}

	/**
	 * Gets an instance of a factory object to return on subsequent calls of getInstance.
	 *
	 * @param   ADatabaseFactory  $instance  A ADatabaseFactory object.
	 *
	 * @return  void
	 */
	public static function setInstance(ADatabaseFactory $instance = null)
	{
		self::$_instance = $instance;
	}
}
