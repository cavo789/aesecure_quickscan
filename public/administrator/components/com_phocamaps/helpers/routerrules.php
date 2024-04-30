<?php
/**
 * @package   Phoca Maps
 * @author    Jan Pavelka - https://www.phoca.cz
 * @copyright Copyright (C) Jan Pavelka https://www.phoca.cz
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 and later
 * @cms       Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die();
use Joomla\CMS\Component\Router\Rules\MenuRules;


use Joomla\Registry\Registry;

class PhocaMapsRouterrules extends MenuRules
{
	public function preprocess(&$query)
	{

		parent::preprocess($query);

	}

	protected function buildLookup($language = '*')
	{
		parent::buildLookup($language);

	}

}
