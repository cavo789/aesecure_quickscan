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
defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

class TZ_Portfolio_PlusTableAddon_Data extends JTable
{
    public function __construct(JDatabaseDriver $db)
    {
        parent::__construct('#__tz_portfolio_plus_addon_data', 'id', $db);
    }

    public function bind($array, $ignore = '')
    {
        // Search for the {readmore} tag and split the text up accordingly.
        if (isset($array['value']) && is_array($array['value']))
        {
            $registry = new Registry;
            $registry->loadArray($array['value']);
            $array['value'] = (string) $registry;

        }

        return parent::bind($array, $ignore);
    }

    protected function _getAssetName()
    {
        $k = $this->_tbl_key;

        return 'com_tz_portfolio_plus.addon_data.' . (int) $this->$k;
    }

    protected function _getAssetTitle()
    {
        return $this -> element;
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
                ->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('com_tz_portfolio_plus.addon.'
                        .$this -> extension_id));

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
        $user = Factory::getUser();
        $date = Factory::getDate();

        if (empty($this->modified_by))
        {
            $this->modified_by = 0;
        }
        if (empty($this->created_by))
        {
            $this->created_by = $user->get('id');
        }

        if ($this->id)
        {
            $this->modified = $date->toSql();
        }
        else
        {
            // New article. An article created and created_by field can be set by the user,
            // so we don't touch either of these if they are set.
            if (!(int) $this->created)
            {
                $this->created = $date->toSql();
            }
        }

        return parent::store($updateNulls);
    }
}
