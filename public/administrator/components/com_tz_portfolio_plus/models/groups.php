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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

jimport('joomla.application.component.modellist');

class TZ_Portfolio_PlusModelGroups extends JModelList{

    public function __construct($config = array()){
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'g.id',
                'name', 'g.name',
                'published', 'g.published',
                'ordering', 'g.ordering',
                'access', 'g.access',
            );
        }
        parent::__construct($config);

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

    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.access');
        $id .= ':' . $this->getState('filter.published');

        return parent::getStoreId($id);
    }

    protected function populateState($ordering = 'g.id', $direction = 'DESC'){

        $app        = Factory::getApplication();

        $search  = $app -> getUserStateFromRequest($this->context.'.filter.search','filter_search',null,'string');
        $this -> setState('filter.search',$search);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', '');
        $this->setState('filter.access', $access);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    protected function getListQuery(){
        $db         = $this -> getDbo();
        $query      = $db -> getQuery(true);
        $user       = Factory::getUser();

        $subQuery   = $db -> getQuery(true);
        $subQuery -> select('COUNT(DISTINCT f.id)');
        $subQuery -> from('#__tz_portfolio_plus_fields AS f');
        $subQuery -> join('INNER', '#__tz_portfolio_plus_field_fieldgroup_map AS m ON m.fieldsid = f.id');
        $subQuery -> where('g.id = m.groupid');
        $query->select(
            $this->getState(
                'list.select',
        'g.*'
            )
        );

        $query -> select('('.(string) $subQuery.') AS total');

        $query -> from('#__tz_portfolio_plus_fieldgroups AS g');

        // Join over the users for the checked out user.
        $query-> select('uc.name AS editor')
            ->join('LEFT', '#__users AS uc ON uc.id=g.checked_out');

        // Join over the asset groups.
        $query -> select('v.title AS access_level')
            ->join('LEFT', '#__viewlevels AS v ON v.id = g.access');

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published))
        {
            $query->where('g.published = ' . (int) $published);
        }
        elseif ($published === '')
        {
            $query->where('(g.published IN (0, 1))');
        }

        // Filter by access level.
        $access = $this->getState('filter.access');
        if (is_numeric($access))
        {
            $query->where('g.access = ' . (int) $access);
        }
        elseif (is_array($access))
        {
            $access = ArrayHelper::toInteger($access);
            $access = implode(',', $access);
            $query->where('g.access IN (' . $access . ')');
        }

        // Implement View Level Access
        if (!$user->authorise('core.admin'))
        {
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $query->where('g.access IN (' . $groups . ')');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('g.id = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('(g.name LIKE ' . $search . ')');
            }
        }

        // Add the list ordering clause.
        $orderCol	= $this -> getState('list.ordering', 'g.id');
        $orderDirn	= $this -> getState('list.direction', 'desc');

        $query->order($db->escape($orderCol).' '.$db->escape($orderDirn));

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            foreach($items as &$item){
                $item -> categories = null;
                if($categories = TZ_Portfolio_PlusHelperCategories::getCategoriesByGroupId($item -> id)){
                    $item -> categories = $categories;
                }
            }
            return $items;
        }
        return false;
    }

    // Get fields group with type array[key=groupid] = groupname
    public function getItemsArray(){
        $db     = $this -> getDbo();
        $db -> setQuery($this -> getListQuery());

        if($items = $db -> loadObjectList()){
            foreach($items as $item){
                $list[$item -> id]  = $item -> name;
            }
            return $list;
        }
        return array();
    }

    // Get fields group name have had fields
    public function getGroupNamesContainFields(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('g.*,x.fieldsid');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_fieldgroups').' AS g');
        $query -> join('INNER',$db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map').' AS x ON x.groupid=g.id');
        $query -> order('x.fieldsid ASC');
        $db -> setQuery($query);

        if($items = $db -> loadObjectList()){
            $list   = array();
            foreach($items as $i => $item){
                if(isset($items[$i-1]) && ($items[$i - 1] -> fieldsid == $items[$i] -> fieldsid)){
                    $list[$item -> fieldsid]    .= ', '.$item -> name;
                }
                else{
                    $list[$item -> fieldsid]    = $item -> name;
                }
            }
            return $list;
        }
        return;

    }

    // Get fields group have had fields
    public function getGroupsContainFields(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('g.*,x.fieldsid');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_fieldgroups').' AS g');
        $query -> join('INNER',$db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map').' AS x ON x.groupid=g.id');
        $query -> order('x.fieldsid ASC');
        $db -> setQuery($query);

        if($items = $db -> loadObjectList()){
            $list   = array();
            foreach($items as $i => $item){
                if(!isset($list[$item -> fieldsid])) {
                    $list[$item->fieldsid] = array();
                }
                if(!isset($list[$item -> fieldsid][$item -> id])){
                    $list[$item -> fieldsid][$item -> id]   = $item;
                }
            }
            return $list;
        }
        return;

    }
}