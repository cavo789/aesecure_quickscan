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

class TZ_Portfolio_PlusTableGroups extends JTable
{
    function __construct(&$db) {
        parent::__construct('#__tz_portfolio_plus_fieldgroups','id',$db);
    }
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;

        return 'com_tz_portfolio_plus.group.' . (int) $this->$k;
    }

    protected function _getAssetTitle()
    {
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
                ->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('com_tz_portfolio_plus.group'));

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

    public function store($updateNulls = false){

        $date = Factory::getDate();
        $user = Factory::getUser();

        if (!(int) $this -> created)
        {
            $this -> created = $date -> toSql();
        }

        if ($this -> id)
        {
            $this -> modified       = $date->toSql();
            $this -> modified_by  = $user -> get('id');
        }
        else
        {
            if (empty($this -> created_by))
            {
                $this -> created_by = $user -> get('id');
            }
        }
        return parent::store($updateNulls);
    }
}
