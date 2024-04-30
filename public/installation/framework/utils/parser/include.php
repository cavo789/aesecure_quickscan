<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * File inclusion parser.
 *
 * This uses an in-memory buffer to include a modified version of the file under a different class name.
 *
 * Only works on PHP 7 and later which allows us to catch any PHP fatal errors and fall back to the legacy parser.
 *
 * @since 9.1.0
 */
class AUtilsParserInclude extends AUtilsParserAbstract
{
	/**
	 * The priority for this parser. Lower number runs first.
	 *
	 * @var   int
	 * @since 9.1.0
	 */
	protected $priority = 1000;

	/**
	 * @inheritDoc
	 */
	public function isSupported()
	{
		return version_compare(PHP_VERSION, '7.0.0', 'ge')
			&& AUtilsBuffer::canRegisterWrapper();
	}

	/**
	 * @inheritDoc
	 */
	public function parseFile($file, $className)
	{
		try
		{
			// Create a random class name
			do
			{
				$randomClass = 'ParseFile' . str_replace(['+', '/', '='], [
						'Z', 'X', '',
					], base64_encode(random_bytes(32)));
			} while (class_exists($randomClass));

			// Get the original file's contents and replace the class name
			$contents = file_get_contents($file);
			$contents = str_replace(' ' . $className . ' ', ' ' . $randomClass . ' ', $contents);
			$contents = str_replace(' ' . $className . "\n", $randomClass . "\n", $contents);
			$contents = str_replace(' ' . $className . "\r", $randomClass . "\r", $contents);

			// Use the memory buffer to include the modified file
			file_put_contents('buffer://temp.php', $contents);

			include('buffer://temp.php');

			// This should never happen. If it does, we fall back to the legacy parser
			if (!class_exists($randomClass))
			{
				throw new RuntimeException('Oopsie');
			}

			// Create a new object from the random class and return its public properties.
			$o = new $randomClass();

			return get_object_vars($o);
		}
		/** @noinspection PhpElementIsNotAvailableInCurrentPhpVersionInspection */
		catch (Throwable $e)
		{
			// Fallback to the legacy parser if the file throws an error.
			$legacyParser = new AUtilsParserLegacy();

			return $legacyParser->parseFile($file, $className);
		}
	}
}