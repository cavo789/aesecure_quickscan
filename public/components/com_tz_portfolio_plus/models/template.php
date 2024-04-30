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

// no direct access
defined('_JEXEC') or die();

use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.model');

class TZ_Portfolio_PlusModelTemplate extends JModelLegacy
{
    function populateState(){
        parent::populateState();
        $this -> setState('template.id',JFactory::getApplication() -> input -> getInt('id'));
        $this -> setState('content.id',null);
        $this -> setState('category.id',null);
    }
    public function getTable($type = 'Templates', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    function getItem($pk = null){
        // Initialise variables.
        $pk = (!empty($pk)) ? $pk : (int) $this->getState('template.id');

        if(empty($pk)){
            $pk = $this -> getItemTemplate();
        }

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
        }else{
            if($table -> getHome() === false){
                $this->setError($table->getError());
                return false;
            }
        }
        // Convert to the JObject before adding other data.
        $properties = $table->getProperties(1);
        $item = ArrayHelper::toObject($properties, 'JObject');

        if (property_exists($item, 'params'))
        {
            $item->params = json_decode($item -> params);

        }

        return $item;
    }

    public function getItemTemplate($artId = null,$catId = null){
        $_artId = !empty($artId)?$artId:$this -> getState('content.id');
        $_catId = !empty($catId)?$catId:$this -> getState('category.id');

        $db         = JFactory::getDbo();
        $templateId = null;

        if($_catId){
            $query  = $db -> getQuery(true);
            $query -> select($db -> quoteName('template_id'));
            $query -> from($db -> quoteName('#__tz_portfolio_plus_categories'));
            $query -> where($db -> quoteName('id').'='.$_catId);
            $db -> setQuery($query);
            if($crow = $db -> loadObject()){
                if($crow -> template_id){
                    $templateId = $crow -> template_id;
                }
            }
        }
        if($_artId){
            $query  = $db -> getQuery(true);
            $query -> select($db -> quoteName('template_id'));
            $query -> from($db -> quoteName('#__tz_portfolio_plus_content'));
            $query -> where($db -> quoteName('id').'='.$_artId);
            $db -> setQuery($query);
            if($row = $db -> loadObject()){
                if($row -> template_id){
                    $templateId = $row -> template_id;
                }
            }
        }
        return (int) $templateId;
    }
}