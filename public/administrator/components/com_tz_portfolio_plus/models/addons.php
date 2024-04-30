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

jimport('joomla.application.component.modellist');
jimport('joomla.filesystem.folder');

class TZ_Portfolio_PlusModelAddons extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'e.id',
                'name', 'e.name',
                'folder', 'e.folder',
                'element', 'e.element',
                'checked_out', 'e.checked_out',
                'checked_out_time', 'e.checked_out_time',
                'published', 'e.published',
                'ordering', 'e.ordering',
            );
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = 'folder', $direction = 'asc'){

        $app    = Factory::getApplication();
        $input  = $app -> input;

        $search  = $this -> getUserStateFromRequest($this -> context.'.filter.search','filter_search',null,'string');
        $this -> setState('filter.search',$search);

        $status = $this->getUserStateFromRequest($this->context.'.filter.status', 'filter_status', '');
        $this->setState('filter.status', $status);

        $folder = $this->getUserStateFromRequest($this->context . '.filter.folder', 'filter_folder', null, 'cmd');
        $this->setState('filter.folder', $folder);

        $ids = $input ->get->get('filter_exclude_ids', array(), 'array');
        $this->setState('filter.exclude_ids', $ids);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.status');
        $id .= ':' . $this->getState('filter.folder');

        return parent::getStoreId($id);
    }

    function getListQuery(){
        $db     = $this -> getDbo();
        $user   = Factory::getUser();
        $query  = $db -> getQuery(true);
        $query -> select('e.*');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_extensions').' AS e');

        $query -> where('type = '.$db -> quote('tz_portfolio_plus-plugin'));

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor')
            ->join('LEFT', '#__users AS uc ON uc.id=e.checked_out');

        // Join over the asset addons.
        $query -> select('v.title AS access_level')
            ->join('LEFT', '#__viewlevels AS v ON v.id = e.access');

        // Implement View Level Access
        if (!$user->authorise('core.admin'))
        {
            $level = implode(',', $user->getAuthorisedViewLevels());
            $query -> where('e.access IN (' . $level . ')');
        }

        // Filter by search in name.
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('e.id = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where(
                    '(' . $db->quoteName('e.name') . ' LIKE ' . $search . ')'
                );
            }
        }

        // Filter by published state
        $status = $this->getState('filter.status');
        if ($status != '')
        {
            if ($status == '2')
            {
                $query->where('protected = 1');
            }
            elseif ($status == '3')
            {
                $query->where('protected = 0');
            }
            else
            {
                $query->where('published=' . (int) $status);
            }
        }

        // Filter by folder.
        if ($folder = $this->getState('filter.folder'))
        {
            $query->where('e.folder = ' . $db->quote($folder));
        }

        // Filter by ids if exists.
        if ($excludeIds = $this->getState('filter.exclude_ids'))
        {
            if(count($excludeIds)) {
                $query->where('e.id NOT IN('.implode(',', $excludeIds).')');
            }
        }

        // Add the list ordering clause.
        $orderCol   = $this->getState('list.ordering','e.folder');
        $orderDirn  = $this->getState('list.direction','asc');
        if ($orderCol == 'e.ordering')
        {
            $orderCol = 'e.name ' . $orderDirn . ', e.ordering';
        }

        if(!empty($orderCol) && !empty($orderDirn)){
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            $language   = Factory::getApplication() -> getLanguage();
            foreach($items as &$item){
                if (strlen($item -> manifest_cache))
                {
                    $data = json_decode($item -> manifest_cache);

                    if ($data)
                    {
                        foreach ($data as $key => $value)
                        {
                            if ($key == 'type')
                            {
                                // Ignore the type field
                                continue;
                            }

                            $item -> $key = $value;
                        }
                    }
                }

                $plugin = TZ_Portfolio_PlusPluginHelper::getInstance($item -> folder, $item -> element);

                $item -> data_manager        = false;
                if(is_object($plugin) && method_exists($plugin, 'getDataManager')){
                    $item -> data_manager    = $plugin -> getDataManager();
                }

                $langKey    = 'plg_'.$item -> folder.'_'.$item -> element;
                if($loaded = TZ_Portfolio_PlusPluginHelper::loadLanguage($item -> element, $item -> folder)) {
                    $langKey = strtoupper($langKey);
                    if ($language->hasKey($langKey)) {
                        $item->name = JText::_($langKey);
                    }
                }

                $item -> author_info = @$item -> authorEmail . '<br />' . @$item -> authorUrl;


            }

            return $items;
        }
        return false;
    }

    public function getItemsUpdate(){

        $storeId    = __METHOD__;
        $storeId    = md5($storeId);

        if(isset($this -> cache[$storeId])){
            return $this -> cache[$storeId];
        }

        JLoader::import('com_tz_portfolio_plus.helpers.addons', JPATH_ADMINISTRATOR.'/components');
        $addons = TZ_Portfolio_PlusHelperAddons::getAddons();

        if(!$addons){
            return false;
        }

        $data   = false;

        foreach($addons as $item){

            $adoFinded = $this -> findAddOnFromServer($item);

            $item -> new_version    = null;
            $manifest   = json_decode($item -> manifest_cache);
            if($adoFinded && $adoFinded -> pProduces){
                if($pProduces = $adoFinded -> pProduces) {
                    if(isset($pProduces -> pProduce) && $pProduces -> pProduce
                        && version_compare($manifest -> version, $pProduces -> pProduce -> pVersion, '<')) {
                        $item -> new_version    = $pProduces -> pProduce -> pVersion;
                        $data[] = $item;
                    }
                }
            }
        }

        if($data){
            $this -> cache[$storeId]    = $data;
        }

        return $data;
    }

    protected function findAddOnFromServer($addon){

        $finded     = false;
        $adoFinded  = false;

        $options = array(
            'defaultgroup'	=> $this -> option,
            'storage' 		=> 'file',
            'caching'		=> true,
            'lifetime'      => 30 * 60,
            'cachebase'		=> JPATH_ADMINISTRATOR.'/cache'
        );
        $cache = JCache::getInstance('', $options);

        $model  = JModelLegacy::getInstance('AddOn', 'TZ_Portfolio_PlusModel');

        $page   = 1;
        while(!$finded){
            $addons = $cache -> get('addons_server:'.$page);
            if(!$addons){
                $url    = $model -> getUrlFromServer();
                if($page > 1) {
                    $prevAddon  = $cache -> get('addons_server:'.($page - 1));
                    $url .= '&start=' . (($page - 1) * $prevAddon -> limit );
                }

                $response = TZ_Portfolio_PlusHelper::getDataFromServer($url);

                if($response){
                    $addons   = json_decode($response -> body);
                    $cache -> store($addons, 'addons_server:'.$page);
                }
            }

            if($addons){
                if($page > ceil($addons -> total / $addons -> limit) - 1){
                    $finded = true;
                }
                foreach($addons -> items as $item){
                    if($item -> pElement == $addon -> element){
                        $finded     = true;
                        $adoFinded  = $item;

                        break;
                    }
                }
            }

            $page++;
        }
        return $adoFinded;
    }
}