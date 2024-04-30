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

use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

jimport('joomla.application.component.modellist');

class TZ_Portfolio_PlusModelTags extends JModelList
{
    public function __construct($config = array()){
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'f.id',
                'title', 'f.title',
                'published', 'f.published'
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

    function populateState($ordering = 'id', $direction = 'desc'){

        parent::populateState($ordering, $direction);

        $state  = $this -> getUserStateFromRequest($this -> context.'.filter_published','filter_published',null,'string');
        $this -> setState('filter.published',$state);

        $search  = $this -> getUserStateFromRequest($this -> context.'.filter.search','filter_search',null,'string');
        $this -> setState('filter.search',$search);
    }

    protected function getListQuery(){
        $db = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select($this->getState(
            'list.select',
            '*'
            )
        );
        $query -> from('#__tz_portfolio_plus_tags');

        // Filter by search in name.
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('id = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where(
                    '(' . $db->quoteName('title') . ' LIKE ' . $search . ')'
                );
            }
        }

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published))
        {
            $query->where('published = ' . (int) $published);
        }
        elseif ($published === '')
        {
            $query->where('(published IN (0, 1))');
        }

        // Add the list ordering clause
        $listOrdering   = $this->getState('list.ordering', 'f.id');
        $listDirn       = $this->getState('list.direction', 'DESC');

        $query -> order($db->escape($listOrdering) . ' ' . $db->escape($listDirn));

        return $query;

    }

    public function getItems(){
        return parent::getItems();
    }


}