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

class TZ_Portfolio_PlusPluginModelAdmin extends JModelAdmin
{
    protected $data         = null;
    protected $plugin_type  = null;


    public function getTable($type = 'Content', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $name   = $this -> getName();

        JForm::addFormPath(JPath::clean(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> plugin_type
            .'/'.$name.'/admin/models/form'));
        JForm::addFormPath(JPath::clean(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> plugin_type
            .'/'.$name.'/admin/models/forms'));

        $xml_file   = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> plugin_type
            .'/'.$name.'/admin/models/form/'.$name.'.xml';
        $xml_file   = file_exists($xml_file)?$xml_file:COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> plugin_type
            .'/'.$name.'/admin/models/forms/'.$name.'.xml';

        if(file_exists($xml_file)) {
            $form = $this->loadForm('plg_' . $this->plugin_type . $name . '.' . $name, $name,
                array('control' => 'jform', 'load_data' => $loadData));
        }

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app = Factory::getApplication();
        $data = $app->getUserState('com_tz_portfolio_plus.edit.article.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_tz_portfolio_plus.article', $data);

        return $data;
    }

    public function getItem($pk = null)
    {
        return $this->data;
    }

    protected function __save($data,$dataInsert){
        if($dataInsert && count($dataInsert)){
            $registry = new JRegistry;
            if($data && !empty($data) && isset($data -> media) && !is_object($data -> media)){
                // Process data
                $registry->loadString($data -> media);

                if($registry -> get($this -> getName())) {
                    $old_data   = ArrayHelper::fromObject($registry->get($this -> getName()));
                    $dataInsert = array_merge($old_data, $dataInsert);
                }
            }

            // Store data to database
            $registry -> set($this -> getName(),$dataInsert);
            $data -> media  = $registry -> toString();
            $data -> store();
        }
    }

    protected function prepareImageSize($image_size){
        if($image_size && !is_array($image_size) && preg_match_all('/(\{.*?\})/',$image_size,$match)) {
            $image_size = $match[1];
        }
        return $image_size;
    }

}