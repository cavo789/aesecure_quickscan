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

class TZ_Portfolio_PlusModelFields extends JModelList{

    public function __construct($config = array()){
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'f.id',
                'title', 'f.title',
                'groupname', 'f.groupname',
                'type', 'f.type',
                'list_view', 'f.list_view',
                'detail_view', 'f.detail_view',
                'advanced_search', 'f.advanced_search',
                'published', 'f.published',
                'ordering', 'f.ordering'
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

    public function populateState($ordering = 'f.id', $direction = 'desc'){

        parent::populateState($ordering, $direction);
        $app = Factory::getApplication();


        $group  = $this -> getUserStateFromRequest($this->context.'.filter.group','filter_group',0);
        $this -> setState('filter.group',$group);

        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $search  = $this -> getUserStateFromRequest($this->context.'.filter_search','filter_search',null,'string');
        $this -> setState('filter.search',$search);

        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', '');
        $this->setState('filter.access', $access);


        $formSubmited = $app->input->post->get('form_submited');

        $type  = $this -> getUserStateFromRequest($this->context.'filter.type','filter_type','');
        if ($formSubmited) {
            $type = $app->input->post->get('type');
            $this -> setState('filter.type', $type);
        }
    }

    protected function getListQuery(){

        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $user   = TZ_Portfolio_PlusUser::getUser();

        $query->select(
            $this->getState(
                'list.select',
                'DISTINCT f.id, f.*'
            )
        );

        $query -> from('#__tz_portfolio_plus_fields AS f');
        $query -> join('LEFT','#__tz_portfolio_plus_field_fieldgroup_map AS x ON f.id=x.fieldsid');

        $query -> join('LEFT','#__tz_portfolio_plus_fieldgroups AS fg ON fg.id=x.groupid');

        $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = f.type')
            -> where('e.type = '.$db -> quote('tz_portfolio_plus-plugin'))
            -> where('e.folder = '.$db -> quote('extrafields'))
            -> where('e.published = 1');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor')
            ->join('LEFT', '#__users AS uc ON uc.id=f.checked_out');

        // Join over the asset groups.
        $query -> select('v.title AS access_level')
            ->join('LEFT', '#__viewlevels AS v ON v.id = f.access');

        // Join over the users for the author.
        $query->select('ua.name AS author_name')
            ->join('LEFT', '#__users AS ua ON ua.id = f.created_by');

        if($search = $this -> getState('filter.search'))
            $query -> where('f.title LIKE '.$db -> quote('%'.$search.'%'));

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published))
        {
            $query->where('f.published = ' . (int) $published);
        }
        elseif ($published === '')
        {
            $query->where('(f.published IN (0, 1))');
        }

        // Filter by group
        if($group  = $this->getState('filter.group')){
            if (is_numeric($group))
            {
                $query -> where('x.groupid = ' . (int) $group);
            }
            elseif (is_array($group))
            {
                $group  = ArrayHelper::toInteger($group);
                $group  = implode(',', $group);
                $query -> where('x.groupid IN (' . $group . ')');
            }
        }

        // Filter by field's type
        if($type  = $this->getState('filter.type')){
            if (is_string($type))
            {
                $query -> where('f.type = ' . $db -> quote($type));
            }
            elseif (is_array($type))
            {
                foreach($type as $i => $t) {
                    $type[$i]  = 'f.type = '.$db -> quote($t);
                }
                $query -> andWhere($type);
            }
        }

        // Filter by access level.
        $access = $this->getState('filter.access');
        if (is_numeric($access))
        {
            $query->where('f.access = ' . (int) $access);
        }
        elseif (is_array($access))
        {
            $access = ArrayHelper::toInteger($access);
            $access = implode(',', $access);
            $query->where('f.access IN (' . $access . ')');
        }

        // Implement View Level Access
        if (!$user->authorise('core.admin'))
        {
            $groups     = implode(',', $user->getAuthorisedViewLevels());
            $subquery   = $db -> getQuery(true);
            $subquery -> select('subg.id');
            $subquery -> from('#__tz_portfolio_plus_fieldgroups AS subg');
            $subquery -> where('subg.access IN('.$groups.')');

            $query -> where('f.access IN('.$groups.')');
            $query -> where('fg.id IN('.((string) $subquery).')');
            $query -> where('e.access IN('.$groups.')');
        }

//        $query -> group('f.id');

        // Add the list ordering clause
        $listOrdering   = $this->getState('list.ordering', 'f.id');
        $listDirn       = $this->getState('list.direction', 'DESC');

        if(isset($group) && $group){
            $listOrdering   = 'x.ordering';
            $query -> select('x.ordering AS ordering');
        }

        $query->order($db->escape($listOrdering) . ' ' . $db->escape($listDirn));
//        $query -> group('f.id');
//        var_dump($query -> dump()); die();

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            $groupModel = JModelLegacy::getInstance('Groups','TZ_Portfolio_PlusModel');
            if($groupNames = $groupModel -> getGroupNamesContainFields()){
                $groups = $groupModel -> getGroupsContainFields();
                foreach($items as $item){
                    if(isset($groupNames[$item -> id])){
                        $item -> groupname  = $groupNames[$item -> id];
                        if($groups && isset($groups[$item -> id])) {
                            $groupIds   = $groups[$item -> id];
                            if(is_array($groups[$item -> id])) {
                                $groupIds = array_keys($groups[$item -> id]);
                            }

                            $item->groupid = (count($groupIds) == 1)?$groupIds[0]:$groupIds;
                        }
                    }
                }
            }
            return $items;
        }
    }

}