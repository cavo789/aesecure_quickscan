<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die;

abstract class AUtilsParserAbstract implements AUtilsParserInterface
{
	/**
	 * The priority for this parser. Lower number runs first.
	 *
	 * @var   int
	 * @since 9.1.0
	 */
	protected $priority = PHP_INT_MAX;

	/**
	 * Get the best match configuration parser.
	 *
	 * @return  AUtilsParserInterface
	 */
	public static function getParser()
	{
		$excluded     = ['abstract', 'interface'];
		$bestPriority = PHP_INT_MAX;
		$parser       = null;
		$di           = new DirectoryIterator(__DIR__);

		foreach ($di as $file)
		{
			// Ignore folders and non-PHP files
			if ($file->isDot() || !$file->isFile() || $file->getExtension() != 'php')
			{
				continue;
			}

			$baseName = $file->getBasename('.php');

			// Make sure the filename is not one of the forbidden ones
			if (in_array($baseName, $excluded))
			{
				continue;
			}

			// Make sure the filename does not contain any non-alpha characters
			$didMatch = preg_match('#[a-z]*#', $baseName, $matches);

			if (!$didMatch || $matches[0] !== $baseName)
			{
				continue;
			}

			// Get the classname
			$className = 'AUtilsParser' . ucfirst($baseName);

			include_once $file->getPathname();

			if (!class_exists($className))
			{
				continue;
			}

			/** @var AUtilsParserInterface $o */
			$o = new $className();

			if (!$o->isSupported() || $o->getPriority() > $bestPriority)
			{
				continue;
			}

			$bestPriority = $o->getPriority();
			$parser       = $o;
		}

		return $parser;
	}

	/**
	 * @inheritDoc
	 */
	public function getPriority()
	{
		return $this->priority;
	}
}