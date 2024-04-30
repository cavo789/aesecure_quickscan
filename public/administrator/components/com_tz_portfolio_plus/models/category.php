<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Filesystem\File;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

jimport('joomla.filesytem.file');
jimport('joomla.application.component.modeladmin');

/**
 * Categories Component Category Model
 */
class TZ_Portfolio_PlusModelCategory extends JModelAdmin
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_TZ_PORTFOLIO_PLUS_CATEGORIES';

    public function __construct($config = array(), MVCFactoryInterface $factory = null)
    {
        parent::__construct($config, $factory);

        // Set the model dbo
        if (array_key_exists('dbo', $config))
        {
            $this->_db = $config['dbo'];
        }
        else
        {
            $this->_db = TZ_Portfolio_PlusDatabase::getDbo();
        }
    }

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since	1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->published != -2)
			{
				return;
			}
			$user = Factory::getUser();

			$state  = $user->authorise('core.delete', $record->extension . '.category.' . (int) $record->id)
                || ($user->authorise('core.delete.own', $record->extension . '.category.' . (int) $record->id)
                    &&  $record -> created_user_id == $user -> id);
			return $state;
		}
		return parent::canDelete($record);
	}

	/**
	 * Method to test whether a record can have its state changed.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = Factory::getUser();

		// Check for existing category.
		if (!empty($record->id))
		{
		    $state  = $user->authorise('core.edit.state', $record->extension . '.category.' . (int) $record->id)
                || ($user->authorise('core.edit.state.own', $record->extension . '.category.' . (int) $record->id)
                    && $record -> created_user_id == $user -> id);
			return $state;
		}
		// New category, so check against the parent.
		elseif (!empty($record->parent_id))
		{
			$state  =  $user->authorise('core.edit.state', $record->extension . '.category.' . (int) $record->parent_id)
                || ($user->authorise('core.edit.state.own', $record->extension . '.category.' . (int) $record->parent_id)
                    && $record -> created_user_id == $user -> id);
			return $state;
		}
		// Default to component settings if neither category nor parent known.
		else
		{
			$state  = $user->authorise('core.edit.state', $record->extension.'.category')
                || ($user->authorise('core.edit.state.own', $record->extension.'.category')
                    && $record -> created_user_id == $user -> id);
			return $state;
		}
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	*/
	public function getTable($type = 'Category', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
		$app = Factory::getApplication('administrator');

		$parentId = $app -> input -> getInt('parent_id');
		$this->setState('category.parent_id', $parentId);

		// Load the User state.
		$pk = (int) $app -> input -> getInt('id');
		$this->setState($this->getName() . '.id', $pk);

		$extension = $app -> input -> getCmd('extension', 'com_tz_portfolio_plus');
		$this->setState('category.extension', $extension);
		$parts = explode('.', $extension);

		// Extract the component name
		$this->setState('category.component', $parts[0]);

		// Extract the optional section name
		$this->setState('category.section', (count($parts) > 1) ? $parts[1] : null);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_tz_portfolio_plus');
		$this->setState('params', $params);
	}

	/**
	 * Method to get a category.
	 *
	 * @param   integer  $pk  An optional id of the object to get, otherwise the id from the model state is used.
	 *
	 * @return  mixed  Category data object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		if ($result = parent::getItem($pk))
		{

			// Prime required properties.
			if (empty($result->id))
			{
				$result->parent_id = $this->getState('category.parent_id');
				$result->extension = $this->getState('category.extension');
			}

			// Convert the metadata field to an array.
			$registry = new Registry($result->metadata);
//			$registry->loadString();
			$result->metadata = $registry->toArray();

			// Convert the created and modified dates to local user time for display in the form.
			jimport('joomla.utilities.date');
			$tz = new DateTimeZone(Factory::getApplication()->getCfg('offset'));

			if (intval($result->created_time))
			{
				$date = new JDate($result->created_time);
				$date->setTimezone($tz);
				$result->created_time = $date->toSql(true);
			}
			else
			{
				$result->created_time = null;
			}

			if (intval($result->modified_time))
			{
				$date = new JDate($result->modified_time);
				$date->setTimezone($tz);
				$result->modified_time = $date->toSql(true);
			}
			else
			{
				$result->modified_time = null;
			}
		}

		$assoc = $this->getAssoc();
		if ($assoc)
		{
			if ($result->id != null)
			{
				$result->associations = TZ_Portfolio_PlusHelperCategories::getAssociations($result->id, $result->extension);

                $result->associations   = ArrayHelper::toInteger($result->associations);
			}
			else
			{
				$result->associations = array();
			}
		}

		return $result;
	}

	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$extension = $this->getState('category.extension');

		$jinput = Factory::getApplication()->input;

		// A workaround to get the extension into the model for save requests.
		if (empty($extension) && isset($data['extension']))
		{
			$extension = $data['extension'];
			$parts = explode('.', $extension);

			$this->setState('category.extension', $extension);
			$this->setState('category.component', $parts[0]);
			$this->setState('category.section', @$parts[1]);
		}

		// Get the form.
		$form = $this->loadForm('com_tz_portfolio_plus.category', 'category', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		// Modify the form based on Edit State access controls.
		if (empty($data['extension']))
		{
			$data['extension'] = $extension;
		}
		$user = TZ_Portfolio_PlusUser::getUser();
		if (!$user->authorise('core.edit.state', 'com_tz_portfolio_plus.category.' . $jinput->get('id'))
            && !$user->authorise('core.edit.state.own', 'com_tz_portfolio_plus.category.' . $jinput->get('id')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
		    $form -> removeField('show_email_icon', 'params');
		    $form -> removeField('show_cat_email_icon', 'params');
        }

		return $form;
	}

	/**
	 * A protected method to get the where clause for the reorder
	 * This ensures that the row will be moved relative to a row with the same extension
	 *
	 * @param   JCategoryTable  $table  Current table instance
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($table)
	{
		return 'extension = ' . $this->_db->Quote($table->extension);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_tz_portfolio_plus.edit.' . $this->getName() . '.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

        $template   = JModelLegacy::getInstance('Template_Style','TZ_Portfolio_PlusModel');
		if($data){
            if(is_array($data)){
                $data['template_id']    = $template->getItemTemplate(null, $this->getState($this->getName() . '.id'));
            }else {
                $data->set('template_id', $template->getItemTemplate(null, $this->getState($this->getName() . '.id')));
            }
        }

		return $data;
	}

	/**
	 * Method to preprocess the form.
	 *
	 * @param   JForm   $form    A JForm object.
	 * @param   mixed   $data    The data expected for the form.
	 * @param   string  $groups  The name of the plugin group to import.
	 *
	 * @return  void
	 *
	 * @see     JFormField
	 * @since   1.6
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		jimport('joomla.filesystem.path');

		// Initialise variables.
		$lang = Factory::getApplication() -> getLanguage();
		$extension = $this->getState('category.extension');
		$component = $this->getState('category.component');
		$section = $this->getState('category.section');

		// Get the component form if it exists
		jimport('joomla.filesystem.path');
		$name = 'category' . ($section ? ('.' . $section) : '');

		// Try to find the component helper.
		$eName = str_replace('com_', '', $component);
		$path = JPath::clean(JPATH_ADMINISTRATOR . "/components/$component/helpers/category.php");

		if (file_exists($path))
		{
			require_once $path;
			$cName = ucfirst($eName) . ucfirst($section) . 'HelperCategory';

			if (class_exists($cName) && is_callable(array($cName, 'onPrepareForm')))
			{
				$lang->load($component, JPATH_BASE, null, false, false)
				|| $lang->load($component, JPATH_BASE . '/components/' . $component, null, false, false)
				|| $lang->load($component, JPATH_BASE, $lang->getDefault(), false, false)
				|| $lang->load($component, JPATH_BASE . '/components/' . $component, $lang->getDefault(), false, false);
				call_user_func_array(array($cName, 'onPrepareForm'), array(&$form));

				// Check for an error.
				if ($form instanceof Exception)
				{
					$this->setError($form->getMessage());
					return false;
				}
			}
		}

		// Set the access control rules field component value.
		$form->setFieldAttribute('rules', 'component', $component);
		$form->setFieldAttribute('rules', 'section', $name);

        // Association category items
		if ($assoc = $this->getAssoc())
		{
            $languages = JLanguageHelper::getContentLanguages(false, true, null, 'ordering', 'asc');
            if (count($languages) > 1)
            {
                $addform = new SimpleXMLElement('<form />');
                $fields = $addform->addChild('fields');
                $fields->addAttribute('name', 'associations');
                $fieldset = $fields->addChild('fieldset');
                $fieldset->addAttribute('name', 'item_associations');
                $fieldset->addAttribute('description', 'COM_TZ_PORTFOLIO_PLUS_CATEGORIES_ITEM_ASSOCIATIONS_FIELDSET_DESC');
                $add = false;
                foreach ($languages as $language)
                {
                    $field = $fieldset->addChild('field');
                    $field->addAttribute('name', $language->lang_code);
                    $field->addAttribute('type', 'modal_category');
                    $field->addAttribute('language', $language->lang_code);
                    $field->addAttribute('label', $language->title);
                    $field->addAttribute('translate_label', 'false');
                    $field->addAttribute('extension', $extension);
                    $field->addAttribute('edit', 'true');
                    $field->addAttribute('clear', 'true');
                }
                $form->load($addform, false);
			}
		}

        // Insert parameter from extrafield
        JLoader::import('extrafields', COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH);
        TZ_Portfolio_PlusHelperExtraFields::prepareForm($form, $data);

		// Trigger the default form events.
		parent::preprocessForm($form, $data, $group);
	}

    public function getAssoc()
    {
        static $assoc = null;

        if (!is_null($assoc))
        {
            return $assoc;
        }

        $app = Factory::getApplication();
        $extension = $this->getState('category.extension');

        $assoc = JLanguageAssociations::isEnabled();
        $extension = explode('.', $extension);
        $component = array_shift($extension);
        $cname = str_replace('com_', '', $component);

        if (!$assoc || !$component || !$cname)
        {
            $assoc = false;
        }
        else
        {
            $hname = $cname . 'HelperAssociation';
            JLoader::register($hname, JPATH_SITE . '/components/' . $component . '/helpers/association.php');

            $assoc = class_exists($hname) && !empty($hname::$category_association);
        }

        return $assoc;
    }

    function deleteImages($fileName){
        if($fileName){
            $file   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$fileName);
            if(!File::exists($file)){
                $this -> setError(JText::_('COM_TZ_PORTFOLIO_PLUS_INVALID_FILE'));
                return false;
            }
            File::delete($file);
        }
        return true;
    }

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{

	    $app    = Factory::getApplication();
		$input	= $app -> input;

		// Initialise variables;
		$table = $this->getTable();
		$pk = (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		// Load the row if saving an existing category.
		if ($pk > 0)
		{
			$table->load($pk);
			$isNew = false;
		}

		// Set the new parent id if parent id not matched OR while New/Save as Copy .
		if ($table->parent_id != $data['parent_id'] || $data['id'] == 0)
		{
			$table->setLocation($data['parent_id'], 'last-child');
		}

		// Alter the title for save as copy
		if ($input -> get('task') == 'save2copy')
		{
			list($title, $alias) = $this->generateNewTitle($data['parent_id'], $data['alias'], $data['title']);
			$data['title'] = $title;
			$data['alias'] = $alias;
		}

		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());
			return false;
		}


		// Bind the rules.
		if (isset($data['rules']))
		{
			$rules = new JAccessRules($data['rules']);
			$table->setRules($rules);
		}

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());
			return false;
		}

        // Check alias with prefix tag, prefix users and prefix date url
        $params = JComponentHelper::getParams('com_tz_portfolio_plus');
        if(isset($table -> alias) && $table -> alias){
            if($table -> alias == $params -> get('sef_users_prefix', 'users')
                || $table -> alias == $params -> get('sef_tags_prefix', 'tags')
                || $table -> alias == $params -> get('sef_date_prefix', 'date')){
                $this -> setError(JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORY_ALIAS_EXISTS'));
                return false;
            }
        }

		// Trigger the onContentBeforeSave event.
        $result = $app -> triggerEvent($this->event_before_save, array($this->option . '.' . $this->name, &$table, $isNew, $data));
		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());
			return false;
		}

		$assoc = $this->getAssoc();
		if ($assoc)
		{

			// Adding self to the association
			$associations = $data['associations'];

			foreach ($associations as $tag => $id)
			{
				if (empty($id))
				{
					unset($associations[$tag]);
				}
			}

			// Detecting all item menus
			$all_language = $table->language == '*';

			if ($all_language && !empty($associations))
			{
				JError::raiseNotice(403, JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_ERROR_ALL_LANGUAGE_ASSOCIATED'));
			}

			$associations[$table->language] = $table->id;

			// Deleting old association for these items
            try{
                $db = $this -> getDbo();
                $query = $db->getQuery(true)
                    ->delete('#__associations')
                    ->where($db->quoteName('context') . ' = ' . $db->quote('com_tz_portfolio_plus.categories.item'))
                    ->where($db->quoteName('id') . ' IN (' . implode(',', $associations) . ')');
                $db->setQuery($query);
                $db->execute();
			}catch (\InvalidArgumentException $e)
            {
                $this->setError($e->getMessage());
                return false;
            }

			if (!$all_language && count($associations))
			{
			    try{
                    // Adding new association for these items
                    $key = md5(json_encode($associations));
                    $query->clear()
                        ->insert('#__associations');

                    foreach ($associations as $id)
                    {
                        $query->values($id . ',' . $db->quote('com_tz_portfolio_plus.categories.item') . ',' . $db->quote($key));
                    }

                    $db->setQuery($query);
                    $db->execute();

				}catch (\InvalidArgumentException $e)
                {
                    $this->setError($e->getMessage());
                    return false;
                }
			}
		}

		// Trigger the onContentAfterSave event.
		$app -> triggerEvent($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));

		// Rebuild the path for the category:
		if (!$table->rebuildPath($table->id))
		{
			$this->setError($table->getError());
			return false;
		}

		// Rebuild the paths of the category's children:
		if (!$table->rebuild($table->id, $table->lft, $table->level, $table->path))
		{
			$this->setError($table->getError());
			return false;
		}

		$this->setState($this->getName() . '.id', $table->id);

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    $pks    A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 */
	function publish(&$pks, $value = 1)
	{
		if (parent::publish($pks, $value)) {
			// Initialise variables.
            $app        = Factory::getApplication();
			$extension	= $app -> input -> getCmd('extension');

			// Include the content plugins for the change of category state event.
			JPluginHelper::importPlugin('content');

			// Trigger the onCategoryChangeState event.
            $app -> triggerEvent('onCategoryChangeState', array($extension, $pks, $value));

			return true;
		}
	}

	/**
	 * Method rebuild the entire nested set tree.
	 *
	 * @return  boolean  False on failure or error, true otherwise.
	 *
	 * @since   1.6
	 */
	public function rebuild()
	{
		// Get an instance of the table object.
		$table = $this->getTable();

		if (!$table->rebuild())
		{
			$this->setError($table->getError());
			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to save the reordered nested set tree.
	 * First we save the new order values in the lft values of the changed ids.
	 * Then we invoke the table rebuild to implement the new ordering.
	 *
	 * @param   array    $idArray    An array of primary key ids.
	 * @param   integer  $lft_array  The lft value
	 *
	 * @return  boolean  False on failure or error, True otherwise
	 *
	 * @since   1.6
	*/
	public function saveorder($idArray = null, $lft_array = null)
	{
		// Get an instance of the table object.
		$table = $this->getTable();

		if (!$table->saveorder($idArray, $lft_array))
		{
			$this->setError($table->getError());
			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Batch copy categories to a new category.
	 *
	 * @param   integer  $value     The new category.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 * @since   1.6
	 */
	protected function batchCopy($value, $pks, $contexts)
	{
		$type = new JUcmType;
		$this->type = $type->getTypeByAlias($this->typeAlias);

		// $value comes as {parent_id}.{extension}
		$parts = explode('.', $value);
		$parentId = (int) ArrayHelper::getValue($parts, 0, 1);

		$db = $this->getDbo();
		$extension = Factory::getApplication()->input->get('extension', '', 'word');
		$newIds = array();

		// Check that the parent exists
		if ($parentId)
		{
			if (!$this->table->load($parentId))
			{
				if ($error = $this->table->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
				else
				{
					// Non-fatal error
					$this->setError(JText::_('JGLOBAL_BATCH_MOVE_PARENT_NOT_FOUND'));
					$parentId = 0;
				}
			}

			// Check that user has create permission for parent category
			if ($parentId == $this->table->getRootId())
			{
				$canCreate = $this->user->authorise('core.create', 'com_tz_portfolio_plus');
			}
			else
			{
				$canCreate = $this->user->authorise('core.create', 'com_tz_portfolio_plus.category.' . $parentId);
			}

			if (!$canCreate)
			{
				// Error since user cannot create in parent category
				$this->setError(JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_BATCH_CANNOT_CREATE'));

				return false;
			}
		}

		// If the parent is 0, set it to the ID of the root item in the tree
		if (empty($parentId))
		{
			if (!$parentId = $this->table->getRootId())
			{
				$this->setError($db->getErrorMsg());

				return false;
			}
			// Make sure we can create in root
			elseif (!$this->user->authorise('core.create', $extension))
			{
				$this->setError(JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_BATCH_CANNOT_CREATE'));

				return false;
			}
		}

		// We need to log the parent ID
		$parents = array();

		// Calculate the emergency stop count as a precaution against a runaway loop bug
		$query = $db->getQuery(true)
			->select('COUNT(id)')
			->from($db->quoteName('#__tz_portfolio_plus_categories'));
		$db->setQuery($query);

		try
		{
			$count = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Parent exists so let's proceed
		while (!empty($pks) && $count > 0)
		{
			// Pop the first id off the stack
			$pk = array_shift($pks);

			$this->table->reset();

			// Check that the row actually exists
			if (!$this->table->load($pk))
			{
				if ($error = $this->table->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JGLOBAL_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Copy is a bit tricky, because we also need to copy the children
			$query->clear()
				->select('id')
				->from($db->quoteName('#__tz_portfolio_plus_categories'))
				->where('lft > ' . (int) $this->table->lft)
				->where('rgt < ' . (int) $this->table->rgt);
			$db->setQuery($query);
			$childIds = $db->loadColumn();

			// Add child ID's to the array only if they aren't already there.
			foreach ($childIds as $childId)
			{
				if (!in_array($childId, $pks))
				{
					array_push($pks, $childId);
				}
			}

			// Make a copy of the old ID and Parent ID
			$oldId = $this->table->id;
			$oldParentId = $this->table->parent_id;

			// Reset the id because we are making a copy.
			$this->table->id = 0;

			// If we a copying children, the Old ID will turn up in the parents list
			// otherwise it's a new top level item
			$this->table->parent_id = isset($parents[$oldParentId]) ? $parents[$oldParentId] : $parentId;

			// Set the new location in the tree for the node.
			$this->table->setLocation($this->table->parent_id, 'last-child');

			// TODO: Deal with ordering?
			// $this->table->ordering	= 1;
			$this->table->level = null;
			$this->table->asset_id = null;
			$this->table->lft = null;
			$this->table->rgt = null;

			// Alter the title & alias
			list($title, $alias) = $this->generateNewTitle($this->table->parent_id, $this->table->alias, $this->table->title);
			$this->table->title = $title;
			$this->table->alias = $alias;

			// Unpublish because we are making a copy
			$this->table->published = 0;

			parent::createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);

			// Store the row.
			if (!$this->table->store())
			{
				$this->setError($this->table->getError());

				return false;
			}

			// Get the new item ID
			$newId = $this->table->get('id');

			// Add the new ID to the array
			$newIds[$pk] = $newId;

			// Now we log the old 'parent' to the new 'parent'
			$parents[$oldId] = $this->table->id;
			$count--;
		}

		// Rebuild the hierarchy.
		if (!$this->table->rebuild())
		{
			$this->setError($this->table->getError());

			return false;
		}

		// Rebuild the tree path.
		if (!$this->table->rebuildPath($this->table->id))
		{
			$this->setError($this->table->getError());

			return false;
		}

		return $newIds;
	}

	/**
	 * Batch move categories to a new category.
	 *
	 * @param   integer  $value     The new category ID.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	protected function batchMove($value, $pks, $contexts)
	{
		$parentId = (int) $value;

		$table = $this->getTable();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = Factory::getUser();
		$extension = Factory::getApplication()->input->get('extension', '', 'word');

		// Check that the parent exists.
		if ($parentId)
		{
			if (!$table->load($parentId))
			{
				if ($error = $table->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
				else
				{
					// Non-fatal error
					$this->setError(JText::_('JGLOBAL_BATCH_MOVE_PARENT_NOT_FOUND'));
					$parentId = 0;
				}
			}
			// Check that user has create permission for parent category
			$canCreate = ($parentId == $table->getRootId()) ? $user->authorise('core.create', $extension) : $user->authorise('core.create', $extension . '.category.' . $parentId);
			if (!$canCreate)
			{
				// Error since user cannot create in parent category
				$this->setError(JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_BATCH_CANNOT_CREATE'));
				return false;
			}

			// Check that user has edit permission for every category being moved
			// Note that the entire batch operation fails if any category lacks edit permission
			foreach ($pks as $pk)
			{
				if (!$user->authorise('core.edit', $extension . '.category.' . $pk))
				{
					// Error since user cannot edit this category
					$this->setError(JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_BATCH_CANNOT_EDIT'));
					return false;
				}
			}
		}

		// We are going to store all the children and just move the category
		$children = array();

		// Parent exists so we let's proceed
		foreach ($pks as $pk)
		{
			// Check that the row actually exists
			if (!$table->load($pk))
			{
				if ($error = $table->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JGLOBAL_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Set the new location in the tree for the node.
			$table->setLocation($parentId, 'last-child');

			// Check if we are moving to a different parent
			if ($parentId != $table->parent_id)
			{
				// Add the child node ids to the children array.
				$query->clear();
				$query->select('id');
				$query->from($db->quoteName('#__tz_portfolio_plus_categories'));
				$query->where($db->quoteName('lft' ) .' BETWEEN ' . (int) $table->lft . ' AND ' . (int) $table->rgt);
				$db->setQuery($query);
				$children = array_merge($children, (array) $db->loadColumn());
			}

			// Store the row.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}

			// Rebuild the tree path.
			if (!$table->rebuildPath())
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Process the child rows
		if (!empty($children))
		{
			// Remove any duplicates and sanitize ids.
			$children   = array_unique($children);
			$children   = ArrayHelper::toInteger($children);

			// Check for a database error.
			if ($db->getErrorNum())
			{
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	/**
	 * Custom clean the cache of com_content and content modules
	 *
	 * @since	1.6
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		$extension = Factory::getApplication() -> input -> getCmd('extension');
		switch ($extension)
		{
			case 'com_tz_portfolio_plus':
				parent::cleanCache('com_tz_portfolio_plus');
				parent::cleanCache('mod_tz_portfolio_plus_articles');
				parent::cleanCache('mod_tz_portfolio_plus_articles_archive');
				parent::cleanCache('mod_tz_portfolio_plus_categories');
				parent::cleanCache('mod_tz_portfolio_plus_tags');
				break;
			default:
				parent::cleanCache($extension);
				break;
		}
	}

	/**
	 * Method to change the title & alias.
	 *
	 * @param   integer  $parent_id  The id of the parent.
	 * @param   string   $alias      The alias.
	 * @param   string   $title      The title.
	 *
	 * @return  array  Contains the modified title and alias.
	 *
	 * @since	1.7
	 */
	protected function generateNewTitle($parent_id, $alias, $title)
	{
		// Alter the title & alias
		$table = $this->getTable();
		while ($table->load(array('alias' => $alias, 'parent_id' => $parent_id)))
		{
			$title = StringHelper::increment($title);
			$alias = StringHelper::increment($alias, 'dash');
		}

		return array($title, $alias);
	}
}
