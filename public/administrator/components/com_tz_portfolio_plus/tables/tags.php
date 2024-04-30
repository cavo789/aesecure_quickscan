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

class TZ_Portfolio_PlusTableTags extends JTable
{

    function __construct(&$db) {
        parent::__construct('#__tz_portfolio_plus_tags','id',$db);

    }

    public function store($updateNulls = false)
    {
        if(!isset($this -> params) || is_null($this -> params)){
            $this -> params = '';
        }
        if(!isset($this -> description) || is_null($this -> description)){
            $this -> description = '';
        }

        return parent::store($updateNulls);
    }

    public function publish($pks = null,$state=1,$userId = 0){
        $k      = $this -> _tbl_key;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks))
        {
            if ($this->$k)
            {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else
            {
                $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                return false;
            }
        }

        // Build the WHERE clause for the primary keys.
        $where = $k . '=' . implode(' OR ' . $k . '=', $pks);

        $query  = $this -> _db -> getQuery(true);
        $query -> update($this -> _db -> quoteName($this -> _tbl));
        $query -> set($this->_db->quoteName('published') . ' = ' . (int) $state);
        $query -> where('(' . $where . ')');
        $this -> _db -> setQuery($query);

        $this -> _db -> execute();

        return true;
    }

    public function check()
    {
        if (trim($this->alias) == '')
        {
            $this->alias = $this->title;
        }

        $this->alias = JApplicationHelper::stringURLSafe($this->alias);

        if (trim(str_replace('-', '', $this->alias)) == '')
        {
            $this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
        }

        return true;
    }

    public function delete($pk = null)
    {
        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // If no primary key is given, return false.
        if ($pk === null)
        {
            $e = new JException(JText::_('JLIB_DATABASE_ERROR_NULL_PRIMARY_KEY'));
            $this->setError($e);
            return false;
        }

        // If tracking assets, remove the asset first.
        if ($this->_trackAssets)
        {
            // Get and the asset name.
            $this->$k = $pk;
            $name = $this->_getAssetName();
            $asset = JTable::getInstance('Asset');

            if ($asset->loadByName($name))
            {
                if (!$asset->delete())
                {
                    $this->setError($asset->getError());
                    return false;
                }
            }
            else
            {
                $this->setError($asset->getError());
                return false;
            }
        }

        try{
            // Delete the row by primary key from tags_xref table
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from($this -> _db -> quoteName('#__tz_portfolio_plus_tag_content_map')) ;
            $query->where($this->_tbl_key . ' = ' . $this->_db->quote($pk));
            $this ->_db->setQuery($query);

            $this -> _db -> execute();
        }catch (\InvalidArgumentException $e)
        {
            $this->setError(JText::sprintf('JLIB_DATABASE_ERROR_DELETE_FAILED', get_class($this), $e -> getMessage()));
            return false;
        }

        // Delete the row by primary key.
        $query = $this->_db->getQuery(true);
        $query->delete();
        $query->from($this->_tbl);
        $query->where($this->_tbl_key . ' = ' . $this->_db->quote($pk));
        $this->_db->setQuery($query);

        // Check for a database error.
        if (!$this->_db->execute())
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_DELETE_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        return true;
    }
}