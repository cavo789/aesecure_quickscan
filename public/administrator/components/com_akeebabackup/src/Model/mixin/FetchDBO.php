<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * @package     Akeeba\Component\AkeebaBackup\Administrator\Model\Mixin
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Akeeba\Component\AkeebaBackup\Administrator\Model\Mixin;

use Joomla\CMS\Factory as JoomlaFactory;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;

trait FetchDBO
{
	private $akeebaInternalDbo = null;

	/**
	 * Get the database object for a database aware model.
	 *
	 * This is a shim which delegates to getDatabase (Joomla 4.2+) or getDbo (Joomla 4.0+), thus avoiding deprecated
	 * notices.
	 *
	 * @return  DatabaseDriver|DatabaseInterface
	 *
	 * @throws  \Exception
	 * @since   9.3.0
	 */
	public function getDB()
	{
		if (is_object($this->akeebaInternalDbo))
		{
			return $this->akeebaInternalDbo;
		}

		if (method_exists($this, 'getDatabase'))
		{
			$this->akeebaInternalDbo = $this->getDatabase();
		}
		elseif (method_exists($this, 'getDbo'))
		{
			$this->akeebaInternalDbo = $this->getDbo();
		}
		else
		{
			throw new \LogicException(
				sprintf('Class %s is not a database model.', get_class($this))
			);
		}

		return $this->akeebaInternalDbo;
	}
}