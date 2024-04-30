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

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;

class TZ_Portfolio_PlusTableExtensions extends JTable
{
    function __construct(&$db)
    {
        parent::__construct('#__tz_portfolio_plus_extensions', 'id', $db);
    }

    public function find($options = array())
    {
        // Get the JDatabaseQuery object
        $query = $this->_db->getQuery(true);

        foreach ($options as $col => $val)
        {
            $query->where($col . ' = ' . $this->_db->quote($val));
        }

        $query->select($this->_db->quoteName('id'))
            ->from($this->_db->quoteName('#__tz_portfolio_plus_extensions'));
        $this->_db->setQuery($query);

        return $this->_db->loadResult();
    }

    protected function _getAssetName()
    {
        $k = $this->_tbl_key;

        return 'com_tz_portfolio_plus.addon.' . (int)$this->$k;
    }

    protected function _getAssetTitle()
    {
        $text   = null;
        $lang   = Factory::getApplication() -> getLanguage();

        if(isset($this -> folder) && isset($this -> element) && $this -> folder && $this -> element) {
            $text = 'PLG_' . strtoupper($this->folder . '_' . $this->element);

            if(method_exists('TZ_Portfolio_PlusPluginHelper', 'loadLanguage')) {
                TZ_Portfolio_PlusPluginHelper::loadLanguage($this->element, $this->folder);
            }else{
                $tag            = $lang -> getTag();
                $basePath       = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $this->folder . '/' . $this->element;
                $_filename      = $this->folder . '_' . $this->element;

                $prefix      = 'tp_addon_';
                if(!File::exists($basePath.'/language/'.$tag.'/'.$tag.'.'.$prefix.$_filename.'.ini')){
                    $prefix = 'plg_';
                }
                $extension = $prefix . $_filename;

                $lang->load(strtolower($extension), $basePath, null, false, true);
            }

        }else{
            $text   = strtoupper($this -> name);
        }

        if ($text && $lang->hasKey($text)) {
            return JText::_($text);
        }

        return $this->name;
    }

    protected function _getAssetParentId(JTable $table = null, $id = null)
    {
        $assetId = null;

        // This is a category under a category.
        if ($assetId === null)
        {
            // Build the query to get the asset id for the parent category.
            $query = $this->_db->getQuery(true)
                ->select($this->_db->quoteName('id'))
                ->from($this->_db->quoteName('#__assets'))
                ->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('com_tz_portfolio_plus.addon'));

            // Get the asset id from the database.
            $this->_db->setQuery($query);

            if ($result = $this->_db->loadResult())
            {
                $assetId = (int) $result;
            }
        }

        // Return the asset id.
        if ($assetId)
        {
            return $assetId;
        }
        else
        {
            return parent::_getAssetParentId($table, $id);
        }
    }

    public function store($updateNulls = false)
    {
        if($this -> type && $this -> type != 'tz_portfolio_plus-plugin'){
            $this -> _trackAssets   = false;
        }

        if(!$this -> checked_out){
            $this -> checked_out = 0;
        }

        if(!$this -> checked_out_time){
            $this -> checked_out_time = '0000-00-00 00:00:00';
        }

        if(!$this -> ordering){
            $this -> ordering = 0;
        }

        if(!$this ->protected){
            $this ->protected = 0;
        }

        if(!$this ->params){
            $this ->params = '';
        }

        return parent::store($updateNulls);
    }

    public function bind($array, $ignore = ''){
        // Bind the rules.
        if (isset($array['rules']) && is_array($array['rules']))
        {
            $rules = new JAccessRules($array['rules']);
            $this->setRules($rules);
        }
        return parent::bind($array, $ignore);
    }
}
?>