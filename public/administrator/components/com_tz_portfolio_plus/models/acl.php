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
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

jimport('joomla.application.component.modeladmin');

class TZ_Portfolio_PlusModelAcl extends JModelAdmin
{
    public function __construct($config = array(), MVCFactoryInterface $factory = null)
    {
        parent::__construct($config, $factory);

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

    protected function populateState()
    {
        $app    = Factory::getApplication();
        $input  = $app -> input;

        $this -> setState('acl.section', $input -> get('section'));

        parent::populateState();
    }

    public function getTable($type = 'Asset', $prefix = 'JTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true){
        $form = $this->loadForm('com_tz_portfolio_plus.acl', 'acl', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    public function save($data)
    {
        $_data          = array();
        $parentAsset    = $this -> getTable();
        $asset          = $this -> getTable();
        if($data && isset($data['section'])){
            if($section      = $data['section']){
                $_data['tags']   = null;
                if($parentAsset -> loadByName('com_tz_portfolio_plus')){
                    $_data['parent_id']  = $parentAsset -> id;
                }
                $_data['id'] = 0;
                if($asset -> loadByName('com_tz_portfolio_plus.'.$section)){
                    $_data['id'] = $asset -> id;
                }else{
                    $_data['name']   = 'com_tz_portfolio_plus.'.$section;
                }

                $lang   = Factory::getApplication() -> getLanguage();
                switch ($section){
                    default:
                        $text   = 'COM_TZ_PORTFOLIO_PLUS_'.strtoupper($section).'S';
                        if($lang -> hasKey($text)){
                            $_data['title'] = JText::_($text);
                        }else{
                            $_data['title'] = 'com_tz_portfolio_plus.'.$section;
                        }
                        break;
                    case 'category':
                        $_data['title'] = JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES');
                        break;
                    case 'group':
                        $_data['title'] = JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_GROUPS');
                        break;
                    case 'style':
                        $_data['title'] = JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE_STYLES');
                        break;
                }

                if(parent::save($_data)){
                    $db     = $this -> getDbo();
                    $query  = $db -> getQuery(true);

                    switch ($section){
                        case 'category':
                            $query -> select('*');
                            $query -> from('#__tz_portfolio_plus_categories');
                            $query -> where('extension='.$db -> quote('com_tz_portfolio_plus'));
                            $query -> order('id DESC');
                            $this -> _storePermission($query, $section);
                            break;
                        case 'group':
                            // Create or update permission of field's groups
                            $query -> select('*');
                            $query -> from('#__tz_portfolio_plus_fieldgroups');
                            $query -> order('id DESC');
                            $this -> _storePermission($query, $section,'', 'name');

                            // Create or update permission of fields
                            $query -> clear();
                            $query -> select('*');
                            $query -> from('#__tz_portfolio_plus_fields');
                            $query -> order('id DESC');
                            $this -> _storePermission($query, $section, 'field');
                            break;
                        case 'addon':
                            // Create or update permission of addon
                            $query -> select('*');
                            $query -> from('#__tz_portfolio_plus_extensions');
                            $query -> where('type='.$db -> quote('tz_portfolio_plus-plugin'));
                            $query -> order('id DESC');
                            $this -> _storePermission($query, $section);
                            break;
                    }

                    return true;
                }
            }

        }
        return false;
    }

    protected function prepareTable($table)
    {
        $parentAsset    = $this -> getTable();
        if($parentAsset -> loadByName('com_tz_portfolio_plus')){
            $table -> setLocation($parentAsset -> id, 'last-child');
        }else{
            $table -> setLocation($table -> getRootId(), 'last-child');
        }
    }

    // Store each permissions
    protected function _storePermission($query, $groupSection, $section = null, $titleFieldName = 'title', &$start = 0){

        $limit  = 10;
        $db     = $this -> getDbo();
        $asset  = $this -> getTable();

        // Get parent asset id
        $parentTable    = $this -> getTable();
        if($parentTable -> loadByName('com_tz_portfolio_plus')){
            $parentAssetId = (int) $parentTable->id;
        }
        if($parentTable -> loadByName('com_tz_portfolio_plus.'.$groupSection)){
            $parentAssetId = (int) $parentTable->id;
        }

        $db -> setQuery($query, $start, $limit);

        // Set section if it is null
        if(!$section){
            $section    = $groupSection;
        }

        if ($items = $db->loadObjectList()) {
            if (count($items)) {
                foreach ($items as $item) {
                    $asset->reset();

                    // Check the item's asset and set data if it is null
                    if (!$asset->loadByName('com_tz_portfolio_plus.' . $section . '.' . $item->id)) {
                        $asset->id = 0;
                        $asset->name = 'com_tz_portfolio_plus.' . $section . '.' . $item->id;
                        if ($section == 'addon') {
                            $asset->title = $item->folder . '-' . $item->element;
                        } else {
                            $asset->title = $item->{$titleFieldName};
                        }
                    }

                    // Update asset_id for table from $query
                    if ($asset->id && isset($item->asset_id) && $item->asset_id != $asset->id) {
                        $this->_updateAssetId($query, $item->id, $asset->id);
                    }

                    // Check & set parent_id for item's asset
                    if (isset($parentAssetId) && $parentAssetId) {
                        if (((int)$asset->parent_id) == $parentAssetId) {
                            continue;
                        }
                        $asset->parent_id = 0;
                        $asset->setLocation($parentAssetId, 'last-child');
                    }

                    // Check and store asset
                    if ($asset->check()) {
                        if ($asset->store()) {
                            // Update asset_id for table from $query
                            $this->_updateAssetId($query, $item->id, $asset->id);
                        }
                    }
                }
            }
        }

        // Check next items with limit
        $start += $limit;
        $db->setQuery($query, $start, $limit);
        $result = $db -> loadResult();
        $count = !empty($result)?count($db->loadResult()):0;

        if($count > 0){
            $this -> _storePermission($query, $groupSection, $section, $titleFieldName, $start);
        }
    }

    // Update asset_id for each item
    protected function _updateAssetId($query, $id, $asset_id){
        if($query && $id && $asset_id) {
            $db = $this->getDbo();
            $from = $query->__get('from');
            $tbl = $from->getElements();
            $tbl = array_shift($tbl);
            $query = $db->getQuery(true);

            $query -> update($tbl);
            $query -> set('asset_id = '.$asset_id);
            $query -> where('id=' . $id);
            $db->setQuery($query);
            $db->execute();
            return true;
        }
        return false;
    }

    protected function preprocessForm(JForm $form, $data, $group = 'content')
    {
        $app        = Factory::getApplication();
        $input      = $app -> input;
        $section    = $input -> get('section');
        $title      = $section;

        switch ($section){
            default:
                $title  = JText::_('COM_TZ_PORTFOLIO_PLUS_'.strtoupper($section).'S');
                break;
            case 'category':
                $title  = JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES');
                break;
            case 'group':
                $title  = JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_GROUPS');
                break;
            case 'style':
                $title  = JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE_STYLES');
                break;

        }

        $form -> setFieldAttribute('rules', 'section', $section);
        $form -> setFieldAttribute('section', 'value', $section);
        $form -> setFieldAttribute('title', 'value', $title);

        parent::preprocessForm($form, $data, $group);
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app = Factory::getApplication();
        $data = $app->getUserState($this -> option. '.edit.'.$this -> getName().'.data', array());

        if($section = $this -> state -> get('acl.section')){
            $table  = $this -> getTable();
            if($table -> loadByName('com_tz_portfolio_plus.'.$section)){
                $data['asset_id']   = $table -> id;
            }
            $data['section']    = $section;

            $title      = $section;
            switch ($section){
                default:
                    $title  = JText::_('COM_TZ_PORTFOLIO_PLUS_'.strtoupper($section).'S');
                    break;
                case 'category':
                    $title  = JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES');
                    break;
                case 'group':
                    $title  = JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_GROUPS');
                    break;
                case 'style':
                    $title  = JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE_STYLES');
                    break;
            }
            $data['title']    = $title;
        }
        return $data;
    }
}