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

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

jimport('joomla.application.component.modeladmin');

class TZ_Portfolio_PlusModelGroup extends JModelAdmin
{

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

    public function getTable($type = 'Groups', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    function getForm($data = array(), $loadData = true){
        $form = $this->loadForm('com_tz_portfolio_plus.group', 'group', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    protected function loadFormData()
    {
        if (empty($data)) {
            $data = $this->getItem();
            $data -> categories_assignment = $this -> getCategoriesAssignment();
        }

        if(!empty($data)){
            $data -> title  = $data -> name;
        }

        return $data;
    }

    public function getCategoriesAssignment($pk = null){
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        if($pk > 0){
            $db     = $this -> getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('id');
            $query -> from('#__tz_portfolio_plus_categories');
            $query -> where('groupid = '.$pk);
            $db -> setQuery($query);
            if($rows = $db -> loadColumn()){
                return implode(',',$rows);
            }
        }
        return null;
    }

    public function save($data){
        if(isset($data['categories_assignment']) && count($data['categories_assignment'])){
            $categoriesAssignment  = $data['categories_assignment'];
            unset($data['categories_assignment']);
        }

        if(isset($data['title'])){
            $data['name']   = $data['title'];
        }

        if(parent::save($data)){
            $db = $this -> getDbo();
            $id = (int) $this->getState($this->getName() . '.id');

            // Assign categories with this group;
            if(!empty($categoriesAssignment) && count($categoriesAssignment)){

                // Update the mapping for category items that this field's group IS assigned to.
                $query = $db->getQuery(true)
                    ->update('#__tz_portfolio_plus_categories')
                    ->set('groupid = ' . $id)
                    ->where('id IN (' . implode(',', $categoriesAssignment) . ')')
                    ->where('groupid != ' . $id);
                $db->setQuery($query);
                $db->execute();

                $query  = $db -> getQuery(true);
                $query -> select('id');
                $query -> from($db -> quoteName('#__tz_portfolio_plus_categories'));
                $query -> where($db -> quoteName('id').' IN('
                    .implode(',',$categoriesAssignment).')');
                $db -> setQuery($query);

                if(!$updateIds = $db -> loadColumn()){
                    $updateIds  = array();
                }

                // Insert category items with this template if they were created in com_content
                if($insertIds  = array_diff($categoriesAssignment,$updateIds)){
                    $query  = $db -> getQuery(true);
                    $query -> insert($db -> quoteName('#__tz_portfolio_plus_categories'));
                    $query ->columns('id,groupid');
                    foreach($insertIds as $cid){
                        $query -> values($cid.',0,'.$id);
                    }
                    $db -> setQuery($query);
                    $db -> execute();
                }
            }

            // Remove field's groups mappings for category items this style is NOT assigned to.
            // If unassigned then all existing maps will be removed.
            $query = $db->getQuery(true)
                ->update('#__tz_portfolio_plus_categories')
                ->set('groupid = 0');

            if (!empty($categoriesAssignment) && count($categoriesAssignment))
            {
                $query->where('id NOT IN (' . implode(',', $categoriesAssignment) . ')');
            }

            $query->where('groupid = ' . $id);
            $db->setQuery($query);
            $db->execute();
            return true;
        }
        return true;
    }

    public function getItem($pk=null){
        // Initialise variables.
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        $table  = $this -> getTable();

        if ($pk > 0)
        {
            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());
                return false;
            }
        }

        // Convert to the JObject before adding other data.
        $properties = $table->getProperties(1);
        $item = ArrayHelper::toObject($properties, 'JObject');

        if (property_exists($item, 'params'))
        {
            $registry = new JRegistry;
            $registry->loadString($item->params);
            $item->params = $registry->toArray();
        }

        return parent::getItem($pk);
    }

    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            $user = Factory::getUser();

            $state  = $user->authorise('core.delete', $this->option.'.group.' . (int) $record->id)
                || ($user->authorise('core.delete.own', $this->option.'.group.' . (int) $record->id)
                    && $record -> created_by == $user -> id);
            return $state;
        }

        return false;
    }

    protected function canEditState($record)
    {
        $user = Factory::getUser();

        // Check for existing group.
        if (!empty($record->id))
        {
            if(isset($record -> asset_id) && !empty($record -> asset_id)) {
                $state = $user->authorise('core.edit.state', $this->option . '.group.' . (int)$record->id)
                    || ($user->authorise('core.edit.state.own', $this->option . '.group.' . (int)$record->id)
                        && $record->created_by == $user->id);

            }else
            {
                $state  = $user->authorise('core.edit.state', $this->option.'.group')
                    || ($user->authorise('core.edit.state.own',$this -> option.'.group')
                        && $record -> created_by == $user -> id);
            }
            return $state;
        }

        return parent::canEditState($record);
    }

}