<?php
/**
* @package		Registration Authorization User Plugin
* @copyright	(C) 2016-2023 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

class JFormRuleAuthcode extends JFormRule
{
	public function test(SimpleXMLElement $element, $value, $group = null, Registry $input = null, JForm $form = null)
	{
		$authcode = trim($value);
		if (!$authcode) return false;

		$result = Factory::getApplication()->triggerEvent('onPlgRegAuthValidate', [$authcode]);
		if (is_array($result)) return $result[0];

		return false;
	}

}
