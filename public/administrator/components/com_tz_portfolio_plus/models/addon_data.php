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

jimport('joomla.application.component.modeladmin');

class TZ_Portfolio_PlusModelAddon_Data extends JModelAdmin{

    protected $addon_element   = null;

    public function __construct($config = array())
    {
        // Guess the option from the class name (Option)Model(View).
        if (empty($this->option))
        {
            $r = null;

            if (!preg_match('/(.*)Model/i', get_class($this), $r))
            {
                throw new Exception(JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'), 500);
            }

            $this->option = 'com_tz_portfolio_plus';
        }

        parent::__construct($config);
    }

    protected function populateState(){

        $addon_id   = Factory::getApplication()->input->getInt('addon_id');
        $this -> setState($this -> getName().'.addon_id',$addon_id);

        // List state information.
        parent::populateState();
    }

    function getForm($data = array(), $loadData = true){

        // Load addon's form
        if($addonId = Factory::getApplication()->input->getInt('addon_id')){
            // Get a row instance.
            $table = $this->getTable('Extensions','TZ_Portfolio_PlusTable');

            // Attempt to load the row.
            $return = $table->load($addonId);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());

                return $return;
            }

            $path   = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.$table -> folder
                .DIRECTORY_SEPARATOR.$table -> element;

            // Add plugin form's path
            JForm::addFormPath($path.DIRECTORY_SEPARATOR.'admin/models/form');
            JForm::addFormPath($path.DIRECTORY_SEPARATOR.'admin/models/forms');
        }

        $form = $this->loadForm('com_tz_portfolio_plus.'.$this -> getName()
            , $this -> getName(), array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    public function getTable($type = 'Addon_Data', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app    = Factory::getApplication();
        $data   = $app->getUserState('com_tz_portfolio_plus.edit.'.$this -> getName().'.data', array());

        if (empty($data)) {
            $data           = $this->getItem();
            if($data && isset($data -> value) && is_string($data -> value)){
                $data -> value  = json_decode($data -> value);
            }
        }

        return $data;
    }

    protected function prepareTable($table){
        $table -> set('_trackAssets', false);

        if(!isset($table -> extension_id)
            || (isset($table -> extension_id) && !$table -> extension_id)){
            $input  = Factory::getApplication() -> input;
            $table -> extension_id   = $input -> getInt('addon_id');

            if(!isset($table -> element)){
                $table -> element   = $this -> addon_element;
            }
            if(!isset($table -> published)){
                $table -> published    = -1;
            }
        }
    }

    protected function canDelete($record)
    {
        $user   = TZ_Portfolio_PlusUser::getUser();
        $asset  = $this -> getTable('Asset', 'JTable');

        if (!empty($record->id))
        {

            if(isset($record -> asset_id) && !empty($record -> asset_id)) {
                return $user->authorise('tzportfolioplus.delete', $this->option . '.addon_data.' . (int)$record->id);
            }
        }
        if(isset($record -> extension_id) && $record -> extension_id
            && $asset -> loadByName($this -> option.'.addon.'.$record -> extension_id)) {
            return $user->authorise('tzportfolioplus.delete', $this -> option
                .'.addon.'.$record -> extension_id);
        }elseif($asset -> loadByName($this -> option.'.addon')){
            return $user->authorise('tzportfolioplus.delete', $this -> option.'.addon');
        }

        return parent::canDelete($record);
    }

    protected function canEditState($record)
    {
        $user   = TZ_Portfolio_PlusUser::getUser();
        $asset  = $this -> getTable('Asset', 'JTable');

        if (!empty($record->id))
        {
            if(isset($record -> asset_id) && !empty($record -> asset_id)) {
                return $user->authorise('tzportfolioplus.edit.state', $this->option . '.addon_data.' . (int)$record->id);
            }
        }

        if(isset($record -> extension_id) && $record -> extension_id
            && $asset -> loadByName($this -> option.'.addon.'.$record -> extension_id)) {
            return $user->authorise('tzportfolioplus.edit.state', $this -> option
                .'.addon.'.$record -> extension_id);
        }elseif($asset -> loadByName($this -> option.'.addon')){
            return $user->authorise('tzportfolioplus.edit.state', $this -> option.'.addon');
        }

        return parent::canEditState($record);
    }

    public function save($data)
    {
        $data['tags']   = null;
        return parent::save($data);
    }
}