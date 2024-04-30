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



defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

abstract class PhocaMapsBatch
{

	public static function item($published, $category = 0)
	{
		// Create the copy/move options.
		$options = array(
			HTMLHelper::_('select.option', 'c', Text::_('JLIB_HTML_BATCH_COPY')),
			HTMLHelper::_('select.option', 'm', Text::_('JLIB_HTML_BATCH_MOVE'))
		);

		$db = Factory::getDBO();

		if ($category == 1) {
			$data = array();
			array_unshift($data, HTMLHelper::_('select.option', 0, Text::_('JLIB_HTML_ADD_TO_ROOT'), 'value', 'text'));
		} else {

		   //build the list of categories
			$query = 'SELECT a.title AS text, a.id AS value, 0 as catid'
			. ' FROM #__phocamaps_map AS a'
			// TO DO. ' WHERE a.published = '.(int)$published
			. ' ORDER BY a.ordering';
			$db->setQuery( $query );
			$data = $db->loadObjectList();

			//$tree = array();
			//$text = '';
			//$catId= -1;
			//$tree = PhocaGalleryRenderAdmin::CategoryTreeOption($data, $tree, 0, $text, $catId);
		}



		// Create the batch selector to change select the category by which to move or copy.
		$lines = array(
			'<label id="batch-choose-action-lbl" for="batch-choose-action">',
			Text::_('JLIB_HTML_BATCH_MENU_LABEL'),
			'</label>',
			'<fieldset id="batch-choose-action" class="combo">',
				'<select name="batch[category_id]" class="form-select" id="batch-category-id">',
					'<option value="">'.Text::_('JSELECT').'</option>',
					/*JHtml::_('select.options',	JHtml::_('category.options', $extension, array('published' => (int) $published))),*/
					HTMLHelper::_('select.options',  $data ),
				'</select>',
				HTMLHelper::_( 'select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'),
			'</fieldset>'
		);

		return implode("\n", $lines);
	}
}
