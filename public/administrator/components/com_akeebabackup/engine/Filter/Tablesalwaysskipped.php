<?php
/**
 * Akeeba Engine
 *
 * @package   akeebaengine
 * @copyright Copyright (c)2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * @package     Akeeba\Engine\Filter
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Akeeba\Engine\Filter;

defined('AKEEBAENGINE') || die();

class Tablesalwaysskipped extends Base
{
	public function __construct()
	{
		$this->object  = 'dbobject';
		$this->subtype = 'content';
		$this->method  = 'api';

		parent::__construct();
	}

	/**
	 * This method must be overridden by API-type exclusion filters.
	 *
	 * @param   string  $test  The object to test for exclusion
	 * @param   string  $root  The object's root
	 *
	 * @return  bool  Return true if it matches your filters
	 */
	protected function is_excluded_by_api($test, $root)
	{
		static $alwaysExcludeTables = [
			// Tables from the service connector that shall not be named
			'bf_core_hashes',
			'bf_files',
			'bf_files_last',
			'bf_folders',
			'bf_folders_to_scan',
		];

		// Is it one of the always excluded tables?
		return in_array($test, $alwaysExcludeTables);
	}

}