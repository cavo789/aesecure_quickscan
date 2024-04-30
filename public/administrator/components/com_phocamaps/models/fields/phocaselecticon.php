<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('JPATH_BASE') or die;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
jimport('joomla.form.formfield');

class JFormFieldPhocaSelectIcon extends FormField
{
	public $type = 'PhocaSelectIcon';

	protected function getInput() {

		$db = Factory::getDBO();

		$query = 'SELECT a.title AS text, a.id AS value'
		. ' FROM #__phocamaps_icon AS a'
		. ' WHERE a.published = 1'
		. ' ORDER BY a.ordering';

		$db->setQuery( $query );
		$items = $db->loadObjectList();

		array_unshift($items, HTMLHelper::_('select.option', '', '- '.Text::_('COM_PHOCAMAPS_SELECT_ICON').' -', 'value', 'text'));

		return HTMLHelper::_('select.genericlist',  $items, $this->name, 'class="form-select"', 'value', 'text', $this->value, $this->id );

	}
}
?>
