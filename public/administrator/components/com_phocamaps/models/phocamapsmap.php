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
defined( '_JEXEC' ) or die();

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;
jimport('joomla.application.component.modeladmin');

use Joomla\String\StringHelper;

class PhocaMapsCpModelPhocaMapsMap extends AdminModel
{

	protected	$option 		= 'com_phocamaps';
	protected 	$text_prefix	= 'com_phocamaps';

	protected function canDelete($record)
	{
		//$user = JFactory::getUser();
		return parent::canDelete($record);
	}

	protected function canEditState($record)
	{
		//$user = JFactory::getUser();
		return parent::canEditState($record);
	}

	public function getTable($type = 'PhocamapsMap', $prefix = 'Table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {

		$app	= Factory::getApplication();
		$form 	= $this->loadForm('com_phocamaps.phocamapsmap', 'phocamapsmap', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_phocamaps.edit.phocamapsmap.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');
		$date = Factory::getDate();
		$user = Factory::getUser();

		$table->title		= htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias		= ApplicationHelper::stringURLSafe($table->alias);

		if (empty($table->alias)) {
			$table->alias = ApplicationHelper::stringURLSafe($table->title);
		}

		if (empty($table->id)) {
			// Set the values
			//$table->created	= $date->toSql();

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__phocamaps_map');
				$max = $db->loadResult();

				$table->ordering = $max+1;
			}
		}
		else {
			// Set the values
			//$table->modified	= $date->toSql();
			//$table->modified_by	= $user->get('id');
		}
	}


	protected function getReorderConditions($table = null)
	{
		$condition = array();
		//$condition[] = 'parent_id = '. (int) $table->parent_id;
		//$condition[] = 'state >= 0';
		return $condition;
	}

	protected function batchCopy($value, $pks, $contexts)
	{
		$categoryId	= (int) $value;
		$app = Factory::getApplication();

		$table	= $this->getTable();
		$db		= $this->getDbo();
		$i		= 0;

		// Check that the category exists
		if ($categoryId) {
			$categoryTable = Table::getInstance('PhocaMapsMap', 'Table');

			if (!$categoryTable->load($categoryId)) {
				if ($error = $categoryTable->getError()) {
					// Fatal error
					throw new Exception($error, 500);
					return false;
				}
				else {
					throw new Exception(Text::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'), 500);
					return false;
				}
			}
		}

		//if (empty($categoryId)) {
		if (!isset($categoryId)) {
			throw new Exception(Text::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'), 500);
			return false;
		}

		// Check that the user has create permission for the component
		$extension	= Factory::getApplication()->input->getCmd('option');
		$user		= Factory::getUser();
		if (!$user->authorise('core.create', $extension)) {
			throw new Exception(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'), 500);
			return false;
		}

		// Parent exists so we let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$table->reset();

			// Check that the row actually exists
			if (!$table->load($pk)) {
				if ($error = $table->getError()) {
					// Fatal error
					throw new Exception($error, 500);
					return false;
				}
				else {
					// Not fatal error

					$app->enqueueMessage(Text::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Alter the title & alias
			$data = $this->generateNewTitle($categoryId, $table->alias, $table->title);
			$table->title   = $data['0'];
			$table->alias   = $data['1'];

			// Reset the ID because we are making a copy
			$table->id		= 0;

			// New category ID
			//$table->parent_id	= $categoryId;

			// Ordering
			$table->ordering = $this->increaseOrdering($categoryId);

			$table->hits = 0;

			// Check the row.
			if (!$table->check()) {
				throw new Exception($table->getError(), 500);
				return false;
			}

			// Store the row.
			if (!$table->store()) {
				throw new Exception($table->getError(), 500);
				return false;
			}

			// Get the new item ID
			$newId = $table->get('id');

			// Add the new ID to the array
			$newIds[$pk]	= $newId;
			$i++;
		}

		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	protected function batchMove($value, $pks, $contexts)
	{
		$categoryId	= (int) $value;
		$app = Factory::getApplication();
		$table	= $this->getTable();
		//$db		= $this->getDbo();

		// Check that the category exists
		if ($categoryId) {
			$categoryTable = Table::getInstance('PhocaMapsMap', 'Table');
			if (!$categoryTable->load($categoryId)) {
				if ($error = $categoryTable->getError()) {
					// Fatal error
					throw new Exception($error, 500);
					return false;
				}
				else {
					throw new Exception(Text::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'), 500);
					return false;
				}
			}
		}

		//if (empty($categoryId)) {
		if (!isset($categoryId)) {
			throw new Exception(Text::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'), 500);
			return false;
		}

		// Check that user has create and edit permission for the component
		$extension	= Factory::getApplication()->input->getCmd('option');
		$user		= Factory::getUser();
		if (!$user->authorise('core.create', $extension)) {
			throw new Exception(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'), 500);
			return false;
		}

		if (!$user->authorise('core.edit', $extension)) {
			throw new Exception(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'), 500);
			return false;
		}

		// Parent exists so we let's proceed
		foreach ($pks as $pk)
		{
			// Check that the row actually exists
			if (!$table->load($pk)) {
				if ($error = $table->getError()) {
					// Fatal error
					throw new Exception($error, 500);
					return false;
				}
				else {
					// Not fatal error
					$app->enqueueMessage(Text::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk), 'error');
					continue;
				}
			}

			// Set the new category ID
			//$table->parent_id = $categoryId;

			// Check the row.
			if (!$table->check()) {
				throw new Exception($table->getError(), 500);
				return false;
			}

			// Store the row.
			if (!$table->store()) {
				throw new Exception($table->getError(), 500);
				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}


	public function increaseOrdering($categoryId) {

		$ordering = 1;
		$this->_db->setQuery('SELECT MAX(ordering) FROM #__phocamaps_map');
		$max = $this->_db->loadResult();
		$ordering = $max + 1;
		return $ordering;
	}


	public function batch($commands, $pks, $contexts)
	{

		// Sanitize user ids.
		$pks = array_unique($pks);
		ArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true)) {
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks)) {
			throw new Exception(Text::_('JGLOBAL_NO_ITEM_SELECTED'), 500);
			return false;
		}

		$done = false;

		if (!empty($commands['assetgroup_id'])) {
			if (!$this->batchAccess($commands['assetgroup_id'], $pks)) {
				return false;
			}

			$done = true;
		}


		//PHOCAEDIT - because parent it is 0
		//if (!empty($commands['category_id'])) {
		if (isset($commands['category_id']))
		{
			$cmd = ArrayHelper::getValue($commands, 'move_copy', 'c');

			if ($cmd == 'c')
			{
				$result = $this->batchCopy($commands['category_id'], $pks, $contexts);
				if (is_array($result))
				{
					$pks = $result;
				}
				else
				{
					return false;
				}
			}
			elseif ($cmd == 'm' && !$this->batchMove($commands['category_id'], $pks, $contexts))
			{
				return false;
			}
			$done = true;
		}

		if (!empty($commands['language_id']))
		{
			if (!$this->batchLanguage($commands['language_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!$done) {
			throw new Exception(Text::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'), 500);
			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}



	protected function generateNewTitle($category_id, $alias, $title)
	{
		// Alter the title & alias
		$table = $this->getTable();
		while ($table->load(array('alias' => $alias)))
		{
			$title = StringHelper::increment($title);
			$alias = StringHelper::increment($alias, 'dash');
			// Joomla! 3.5
			//$title = StringHelper::increment($title);
			//$alias = StringHelper::increment($alias, 'dash');
		}

		return array($title, $alias);
	}
}
?>
