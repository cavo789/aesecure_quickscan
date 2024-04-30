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
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Language\Text;
jimport('joomla.application.component.modeladmin');


class PhocaMapsCpModelPhocaMapsMarker extends AdminModel
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

	public function getTable($type = 'PhocaMapsMarker', $prefix = 'Table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {

		$app	= Factory::getApplication();
		$form 	= $this->loadForm('com_phocamaps.phocamapsmarker', 'phocamapsmarker', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_phocamaps.edit.phocamapsmarker.data', array());

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
				$db->setQuery('SELECT MAX(ordering) FROM #__phocamaps_marker WHERE catid='.(int)$table->catid);
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
		$condition[] = 'catid = '.(int) $table->catid;
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
					//throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk), 500);
					$app->enqueueMessage(Text::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk), 'error');
					continue;
				}
			}

			// Alter the title & alias
			$data = $this->generateNewTitle($categoryId, $table->alias, $table->title);
			$table->title   = $data['0'];
			$table->alias   = $data['1'];

			// Reset the ID because we are making a copy
			$table->id		= 0;

			// Set the new category ID
			$table->catid = $categoryId;

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
					//throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					$app->enqueueMessage(Text::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk), 'error');
					continue;
				}
			}

			// Set the new category ID
			$table->catid = $categoryId;

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
		$this->_db->setQuery('SELECT MAX(ordering) FROM #__phocamaps_marker WHERE catid='.(int)$categoryId);
		$max = $this->_db->loadResult();
		$ordering = $max + 1;
		return $ordering;
	}
}
?>
