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
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class TZ_Portfolio_PlusModelAddon_Datas extends JModelList{

    protected $addon_element   = null;

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

    protected function populateState($ordering = 'id', $direction = 'desc'){

        $addon_id   = Factory::getApplication()->input->getInt('addon_id');
        $this -> setState($this -> getName().'.addon_id',$addon_id);

        if($addon_id) {
            $addon  = TZ_Portfolio_PlusPluginHelper::getPluginById($addon_id);
            $this->setState($this->getName() . '.addon', $addon);
        }

        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    protected function getStoreId($id = '')
    {
        // Compile the store id.
        if($access = $this -> getState('filter.access')) {
            $id .= ':' . $this->getState('filter.access');
        }
        $id .= ':' . $this->getState('filter.published');

        return parent::getStoreId($id);
    }

    public function getListQuery(){
        if($addonId = $this -> getState($this -> getName().'.addon_id')){
            $db     = $this -> getDbo();
            $query  = $db -> getQuery(true)
                -> select('d.*')
                -> from($db -> quoteName('#__tz_portfolio_plus_addon_data').' AS d')
                -> where('d.extension_id ='.$addonId);
            if($element = $this -> addon_element){
                $query -> where('d.element ='.$db -> quote($element));
            }

            // Join over the users for the checked out user.
            $query->select('uc.name AS editor')
                ->join('LEFT', '#__users AS uc ON uc.id=d.checked_out');

            // Filter by published state
            $published = $this->getState('filter.published');
            if (is_numeric($published)) {
                $query->where('d.published = ' . (int) $published);
            }
            elseif ($published === '') {
                $query->where('(d.published = 0 OR d.published = 1 OR d.published = -1)');
            }

            // Add the list ordering clause.
            $orderCol = $this->getState('list.ordering','id');
            $orderDirn = $this->getState('list.direction','desc');

            if(!empty($orderCol) && !empty($orderDirn)){
                if(strpos($orderCol,'value.') !== false) {
                    $fields     = explode('.',$orderCol);
                    $orderCol   = array_pop($fields);
                    $query->order('substring_index(d.value,' . $db->quote('"'.$orderCol.'":') . ',-1) '. $orderDirn);
                }else{
                    $query->order($db->escape($orderCol . ' ' . $orderDirn));
                }
            }
            return $query;
        }
        return false;
    }

    public function getItems(){
        if($items = parent::getItems()){
            foreach($items as &$item){
                $item -> value  = json_decode($item -> value);
            }
            return $items;
        }
        return false;
    }

    public function getAddOnItem($pk = null){
        $pk         = (!empty($pk)) ? $pk : (int) $this->getState($this -> getName().'.addon_id');
        $storeId    = __METHOD__.'::' .$pk;

        if (!isset($this->cache[$storeId]))
        {
            $false	= false;

            // Get a row instance.
            $table = $this->getTable('Extensions','TZ_Portfolio_PlusTable');

            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());

                return $false;
            }

            // Convert to the JObject before adding other data.
            $properties = $table->getProperties(1);
            $this->cache[$storeId] = ArrayHelper::toObject($properties, 'JObject');

            $dispatcher     = TZ_Portfolio_PlusPluginHelper::getDispatcher();
            if($plugin         = TZ_Portfolio_PlusPluginHelper::getInstance($table -> folder,
                $table -> element, false, $dispatcher)){
                if(method_exists($plugin, 'onAddOnDisplayManager')) {
                    $this->cache[$storeId]->manager = $plugin->onAddOnDisplayManager();
                }
            }
        }

        return $this->cache[$storeId];
    }

    protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
    {

        if($addon = $this -> getState($this->getName() . '.addon')) {
            // Handle the optional arguments.
            $options['control'] = ArrayHelper::getValue((array) $options, 'control', false);

            // Create a signature hash.
            $hash = md5($source . serialize($options));

            // Check if we can use a previously loaded form.
            if (!$clear && isset($this->_forms[$hash]))
            {
                return $this->_forms[$hash];
            }

            // Get the form.
            \JForm::addFormPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH .'/'.$addon -> type.'/'
                .$addon -> name. '/admin/models/forms');
            \JForm::addFieldPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH .'/'.$addon -> type.'/'
                .$addon -> name. '/admin/models/fields');

            try
            {
                $form = \JForm::getInstance($name, $source, $options, false, $xpath);

                if (isset($options['load_data']) && $options['load_data'])
                {
                    // Get the data for the form.
                    $data = $this->loadFormData();
                }
                else
                {
                    $data = array();
                }

                // Allow for additional modification of the form, and events to be triggered.
                // We pass the data because plugins may require it.
                $this->preprocessForm($form, $data);

                // Load the data into the form after the plugins have operated.
                $form->bind($data);
            }
            catch (\Exception $e)
            {
                $this->setError($e->getMessage());

                return false;
            }

            // Store the form for later.
            $this->_forms[$hash] = $form;

            return $form;
        }
        return false;
    }
}