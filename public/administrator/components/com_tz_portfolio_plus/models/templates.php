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

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

jimport('joomla.filesytem.file');
jimport('joomla.filesytem.folder');
jimport('joomla.application.component.modellist');

class TZ_Portfolio_PlusModelTemplates extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 't.id',
                'name', 't.name',
                'published', 't.published',
                'type', 't.type'
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

    function populateState($ordering = null, $direction = null){

        parent::populateState($ordering,$direction);

        $search  = $this -> getUserStateFromRequest('com_tz_portfolio_plus.templates.filter_search','filter_search',null,'string');
        $this -> setState('filter_search',$search);

        $status = $this->getUserStateFromRequest($this->context.'.filter.status', 'filter_status', '');
        $this->setState('filter.status', $status);

        $order  = $this -> getUserStateFromRequest('com_tz_portfolio_plus.templates.filter_order','filter_order',null,'string');
        $this -> setState('filter_order',$order);

        $orderDir  = $this -> getUserStateFromRequest('com_tz_portfolio_plus.templates.filter_order_Dir','filter_order_Dir','asc','string');
        $this -> setState('filter_order_Dir',$orderDir);
    }

    function getForm($data = array(), $loadData = true){
        $form = $this->loadForm('com_tz_portfolio_plus.template', 'template', array('control' => ''));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    public function getTemplates(){
        $items  = array();
        $tpl_path   = COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'templates';
        if(!Folder::exists($tpl_path)){
            return false;
        }

        if($folders    = Folder::folders($tpl_path)){
            if(count($folders)){
                foreach($folders as $i => $folder){
                    $xmlFile    = $tpl_path.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.'template.xml';
                    if(File::exists($xmlFile)){
                        $installer  = JInstaller::getInstance($tpl_path.DIRECTORY_SEPARATOR.$folder);
                        if($manifest = $installer ->isManifest($xmlFile)){

                            TZ_Portfolio_PlusTemplate::loadLanguage((string) $manifest -> name);

                            $item                   = new stdClass();
                            $item -> id             = $i;
                            $item -> name           = (string) $manifest -> name;
                            $item -> type           = (string) $manifest -> type;
                            $item -> version        = (string) $manifest -> version;
                            $item -> creationDate   = (string) $manifest -> creationDate;
                            $item -> author         = (string) $manifest -> author;
                            $item -> authorEmail    = (string) $manifest -> authorEmail;
                            $item -> description    = JText::_((string) $manifest -> description);
                            $items[]    = $item;
                        }
                    }
                }
            }
        }
        return $items;
    }

    function getListQuery(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('t.*');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_extensions').' AS t');

        $query -> where('type = '.$db -> quote('tz_portfolio_plus-template'));

        // Add the list ordering clause.
        $orderCol = $this->getState('list.ordering','t.id');
        $orderDirn = $this->getState('list.direction','desc');
        if ($orderCol == 't.ordering')
        {
            $orderCol = 't.name ' . $orderDirn . ', a.ordering';
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
                $query->where('protected = 0')
                    ->where('published=' . (int) $status);
            }
        }

        if(!empty($orderCol) && !empty($orderDirn)){
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            foreach($items as $item){
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

//                $item -> author_info = @$item -> authorEmail . '<br />' . @$item -> authorUrl;
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

        JLoader::import('com_tz_portfolio_plus.helpers.templates', JPATH_ADMINISTRATOR.'/components');
        $styles = TZ_Portfolio_PlusHelperTemplates::getStyles();

        if(!$styles){
            return false;
        }

        $data   = false;

        foreach($styles as $item){

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

        $model  = JModelLegacy::getInstance('Template', 'TZ_Portfolio_PlusModel');

        $page   = 1;
        while(!$finded){
            $styles = $cache -> get('styles_server:'.$page);
            if(!$styles){
                $url    = $model -> getUrlFromServer();
                if($page > 1) {
                    $prevAddon  = $cache -> get('styles_server:'.($page - 1));
                    $url .= '&start=' . (($page - 1) * $prevAddon -> limit );
                }

                $response = TZ_Portfolio_PlusHelper::getDataFromServer($url);

                if($response){
                    $styles   = json_decode($response -> body);
                    $cache -> store($styles, 'styles_server:'.$page);
                }
            }

            if($styles){
                if($page > ceil($styles -> total / $styles -> limit) - 1){
                    $finded = true;
                }
                foreach($styles -> items as $item){
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