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

class TZ_Portfolio_PlusModelTemplate_Styles extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 't.id',
                'name', 't.name',
                'home', 't.home',
                'published', 't.published',
                'template', 't.template',
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

    function populateState($ordering = 't.template', $direction = 'asc'){

        parent::populateState($ordering, $direction);

        $search  = $this -> getUserStateFromRequest($this -> context.'.filter_search','filter_search',null,'string');
        $this -> setState('filter.search',$search);

        $template  = $this -> getUserStateFromRequest($this -> context.'.filter.template','filter_template',null,'string');
        $this -> setState('filter.template',$template);
    }

    function getListQuery(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select($this->getState(
            'list.select',
            't.*'
            )
        );

        $query -> select('(SELECT COUNT(xc2.template_id) FROM #__tz_portfolio_plus_templates AS t2'
            .' INNER JOIN #__tz_portfolio_plus_content AS xc2 ON t2.id = xc2.template_id WHERE t.id = t2.id)'
            .' AS content_assigned');
        $query -> select('(SELECT COUNT(c2.template_id) FROM #__tz_portfolio_plus_templates AS t3'
            .' INNER JOIN #__tz_portfolio_plus_categories AS c2 ON t3.id = c2.template_id WHERE t.id = t3.id)'
            .' AS category_assigned');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_templates').' AS t');

        $query -> join('INNER',$db -> quoteName('#__tz_portfolio_plus_extensions').' AS e ON t.template = e.element')
            -> where('e.published = 1')
            ->where('e.type=' . $db->quote('tz_portfolio_plus-template'));

        // Filter by search in name.
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('t.id = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where(
                    '(' . $db->quoteName('t.title') . ' LIKE ' . $search . ')'
                );
            }
        }

        if($template = $this -> getState('filter.template')){
            $query -> where('t.template = '.$db -> quote($template));
        }

        // Add the list ordering clause.
        $orderCol   = $this->getState('list.ordering','t.template');
        $orderDirn  = $this->getState('list.direction','asc');

        if(!empty($orderCol) && !empty($orderDirn)){
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

//        $query -> group('t.id');

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            $component  = JComponentHelper::getComponent('com_tz_portfolio_plus');
            $menus  = JMenu::getInstance('site');
            $menu_assigned  = array();
            if($menu_items  = $menus -> getItems(array('component_id'),$component -> id)){
                if(count($menu_items)){
                    foreach($menu_items as $m){
                        if(isset($m -> params)){
                            $params = $m -> params;
                            if($tpl_style_id = $params -> get('tz_template_style_id')){
                                if(!isset($menu_assigned[$tpl_style_id])){
                                    $menu_assigned[$tpl_style_id]   = 0;
                                }
                                $menu_assigned[$tpl_style_id] ++;
                            }
                        }
                    }
                }
            }

            foreach($items as $i => &$item){
                $item -> menu_assigned      = 0;
                if(isset($menu_assigned[$item -> id])){
                    $item -> menu_assigned  = $menu_assigned[$item -> id];
                }
            }

            return $items;
        }
        return false;
    }


}