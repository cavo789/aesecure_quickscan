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
use Joomla\CMS\User\User;
use Joomla\Registry\Registry;
use Joomla\CMS\User\UserWrapper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

//jimport('joomla.user.user');

class TZ_Portfolio_PlusUser extends \JUser{

    protected static $instances = array();

    public static function getUser($id = null)
    {
        $instance = Factory::getUser();

        if (is_null($id))
        {
            if (!($instance instanceof TZ_Portfolio_PlusUser))
            {
                $instance = TZ_Portfolio_PlusUser::getInstance($instance -> id);
            }
        }
        // Check if we have a string as the id or if the numeric id is the current instance
        elseif (!($instance instanceof TZ_Portfolio_PlusUser) || is_string($id) || $instance->id !== $id)
        {
            $instance = TZ_Portfolio_PlusUser::getInstance($id);
        }

        return $instance;
    }

    public static function getInstance($identifier = 0, UserWrapper $userHelper = null)
    {
        if (null === $userHelper)
        {
            if(class_exists('UserWrapper')){
                $userHelper = new UserWrapper;
            }elseif(class_exists('JUserWrapperHelper')){
                $userHelper	= new JUserWrapperHelper;
            }
        }

        // Find the user id
        if (!is_numeric($identifier))
        {
            if (!$id = $userHelper->getUserId($identifier))
            {
                // If the $identifier doesn't match with any id, just return an empty User.
                return new TZ_Portfolio_PlusUser;
            }
        }
        else
        {
            $id = $identifier;
        }

        // If the $id is zero, just return an empty User.
        // Note: don't cache this user because it'll have a new ID on save!
        if ($id === 0)
        {
            return new TZ_Portfolio_PlusUser;
        }

        // Check if the user ID is already cached.
        if (empty(self::$instances[$id]))
        {
            $user = new TZ_Portfolio_PlusUser($id, $userHelper);
            self::$instances[$id] = $user;
        }

        return self::$instances[$id];
    }

    public function getAuthorisedCategories($component, $action)
    {
        $component  = !empty($component)?$component:'com_tz_portfolio_plus';
        // Brute force method: get all published category rows for the component and check each one
        // TODO: Modify the way permissions are stored in the db to allow for faster implementation and better scaling
        $db     = TZ_Portfolio_PlusDatabase::getDbo();

        $subQuery = $db->getQuery(true)
            ->select('id,asset_id')
            ->from('#__tz_portfolio_plus_categories')
            ->where('extension = ' . $db->quote($component))
            ->where('published = 1');

        $query = $db->getQuery(true)
            ->select('c.id AS id, a.name AS asset_name')
            ->from('(' . $subQuery->__toString() . ') AS c')
            ->join('INNER', '#__assets AS a ON c.asset_id = a.id');
        $db->setQuery($query);

        $allCategories      = $db->loadObjectList('id');
        $allowedCategories  = array();

        foreach ($allCategories as $category)
        {
            if ($this->authorise($action, $category->asset_name))
            {
                $allowedCategories[] = (int) $category->id;
            }
        }

        return $allowedCategories;
    }

    public function getAuthorisedFieldGroups($action, $ids = null)
    {
        // Brute force method: get all published category rows for the component and check each one
        // TODO: Modify the way permissions are stored in the db to allow for faster implementation and better scaling
        $db     = TZ_Portfolio_PlusDatabase::getDbo();

        $subQuery = $db->getQuery(true)
            ->select('id,asset_id')
            ->from('#__tz_portfolio_plus_fieldgroups')
            ->where('published = 1');

        if($ids){
            if(is_numeric($ids)){
                $subQuery -> where('id ='.$ids);
            }else{
                $subQuery -> where('id IN('.implode(',', $ids).')');
            }
        }

        $query = $db->getQuery(true)
            ->select('c.id AS id, a.name AS asset_name')
            ->from('(' . $subQuery->__toString() . ') AS c')
            ->join('INNER', '#__assets AS a ON c.asset_id = a.id');
        $db->setQuery($query);

        $allGroups      = $db->loadObjectList('id');
        $allowedGroups  = array();

        foreach ($allGroups as $group)
        {
            if ($this->authorise($action, $group->asset_name))
            {
                $allowedGroups[] = (int) $group->id;
            }
        }

        return $allowedGroups;
    }
}