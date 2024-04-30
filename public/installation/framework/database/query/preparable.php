<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * This file may contain code from the Joomla! Platform, Copyright (c) 2005 -
 * 2012 Open Source Matters, Inc. This file is NOT part of the Joomla! Platform.
 * It is derivative work and clearly marked as such as per the provisions of the
 * GNU General Public License.
 */

/**
 * Database Query Preparable Interface.
 *
 * Adds bind/unbind methods as well as a getBounded() method
 * to retrieve the stored bounded variables on demand prior to
 * query execution.
 *
 * @since  1.0
 *
 * @codeCoverageIgnore
 */
interface ADatabaseQueryPreparable
{
	/**
	 * Method to add a variable to an internal array that will be bound to a prepared SQL statement before query execution. Also
	 * removes a variable that has been bounded from the internal bounded array when the passed in value is null.
	 *
	 * @param   string|integer  $key            The key that will be used in your SQL query to reference the value. Usually of
	 *                                          the form ':key', but can also be an integer.
	 * @param   mixed           &$value         The value that will be bound. The value is passed by reference to support output
	 *                                          parameters such as those possible with stored procedures.
	 * @param   integer         $dataType       Constant corresponding to a SQL datatype.
	 * @param   integer         $length         The length of the variable. Usually required for OUTPUT parameters.
	 * @param   array           $driverOptions  Optional driver options to be used.
	 *
	 * @return  ADatabaseQueryPreparable
	 *
	 * @since   1.0
	 */
	public function bind($key = null, &$value = null, $dataType = \PDO::PARAM_STR, $length = 0, $driverOptions = array());

	/**
	 * Retrieves the bound parameters array when key is null and returns it by reference. If a key is provided then that item is
	 * returned.
	 *
	 * @param   mixed  $key  The bounded variable key to retrieve.
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 */
	public function &getBounded($key = null);
}
