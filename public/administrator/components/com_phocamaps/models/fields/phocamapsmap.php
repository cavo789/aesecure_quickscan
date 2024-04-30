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
defined('_JEXEC') or die();
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;


class JFormFieldPhocaMapsMap extends FormField
{
	protected $type 		= 'PhocaMapsMap';

	protected function getInput() {

		$db = Factory::getDBO();

		$query = 'SELECT a.title AS text, a.id AS value'
		. ' FROM #__phocamaps_map AS a'
		. ' WHERE a.published = 1'
		. ' ORDER BY a.ordering';

		$db->setQuery( $query );
		$items = $db->loadObjectList();

		$attr = '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : 'class="form-select"';



		array_unshift($items, HTMLHelper::_('select.option', '', '- '.Text::_('COM_PHOCAMAPS_SELECT_MAP').' -', 'value', 'text'));

		return HTMLHelper::_('select.genericlist',  $items, $this->name, trim($attr), 'value', 'text', $this->value, $this->id );

	}
}
?>
