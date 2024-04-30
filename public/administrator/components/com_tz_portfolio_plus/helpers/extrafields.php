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

use Joomla\Registry\Registry;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class TZ_Portfolio_PlusHelperExtraFields{

    public static function getExtraFields($groupid=null,$catid=null){
        if($groupid || $catid) {
            $db = TZ_Portfolio_PlusDatabase::getDbo();
            $query  = $db->getQuery(true);

            $query -> select('f.*');
            $query -> from($db -> quoteName('#__tz_portfolio_plus_fields').' AS f');
            $query -> join('LEFT', $db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map')
                            .' AS m ON m.fieldsid = f.id');
            $query -> join('LEFT', $db -> quoteName('#__tz_portfolio_plus_categories')
                            .' AS c ON c.groupid = m.groupid');

            $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = f.type')
                -> where('e.type = '.$db -> quote('tz_portfolio_plus-plugin'))
                -> where('e.folder = '.$db -> quote('extrafields'))
                -> where('e.published = 1');

            $query -> where('f.published = 1');

            // if inherit category
            if ($groupid == 0) {
                if($catid) {
                    $query->where('c.id = ' . $catid);
                }
            } else {
                $query -> where('m.groupid = '. $groupid);
            }
            $query -> order('f.ordering ASC');

            $db = TZ_Portfolio_PlusDatabase::getDbo();
            $db -> setQuery($query);

            if($rows   = $db -> loadObjectList()){
                return $rows;
            }
        }
        return null;
    }

    public static function getAllExtraFields(){
        $db = TZ_Portfolio_PlusDatabase::getDbo();
        $query  = $db->getQuery(true);

        $query -> select('f.*');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_fields').' AS f');
        $query -> join('LEFT', $db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map')
            .' AS m ON m.fieldsid = f.id');

        $query -> select('g.id AS groupid, g.name AS group_title');
        $query -> join('INNER', $db -> quoteName('#__tz_portfolio_plus_fieldgroups').' AS g ON g.id = m.groupid');

        $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = f.type')
            -> where('e.type = '.$db -> quote('tz_portfolio_plus-plugin'))
            -> where('e.folder = '.$db -> quote('extrafields'))
            -> where('e.published = 1');

        $query -> where('f.published = 1');
        $query -> group('f.id');

        $query -> order('g.id ASC');

//        $query -> select('f.*');
//
//        $subQuery   = $db -> getQuery(true);
//        $subQuery -> select('groupid');
//        $subQuery -> from('#__tz_portfolio_plus_field_fieldgroup_map');
//        $subQuery -> where('fieldsid = f.id');
//        $subQuery -> order('groupid ASC');
//
//        $query -> select('('.(string) $subQuery. ' LIMIT 1) AS groupid');
//
//        $subQuery -> clear();
//        $subQuery -> select('name');
//        $subQuery -> from('#__tz_portfolio_plus_fieldgroups');
//        $subQuery -> where('id = groupid');
//        $subQuery -> order('id ASC');
//
//        $query -> select('('.(string) $subQuery. ') AS group_title');
//
//        $query -> select('('.(string) $subQuery. ' LIMIT 1)');
//
//        $query -> from($db -> quoteName('#__tz_portfolio_plus_fields').' AS f');
//
//        $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = f.type')
//            -> where('e.type = '.$db -> quote('tz_portfolio_plus-plugin'))
//            -> where('e.folder = '.$db -> quote('extrafields'))
//            -> where('e.published = 1');
//
//        $query -> where('f.published = 1');
//
//        $query -> order('groupid ASC');

        $db -> setQuery($query);

        if($rows   = $db -> loadObjectList()){
            return $rows;
        }
        return null;
    }

    public static function getFieldGroups($fieldid){
        if($fieldid){
            $db = TZ_Portfolio_PlusDatabase::getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('groupid');
            $query -> from($db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map'));
            $query -> where('fieldsid='.$fieldid);
            $db -> setQuery($query);
            if($items = $db -> loadColumn()){
                return $items;
            }
        }
        return false;
    }

    public static function removeFieldGroups($fieldid, $groupid = null){
        if($fieldid){
            $db = TZ_Portfolio_PlusDatabase::getDbo();
            $query  = $db -> getQuery(true);
            $query -> delete($db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map'));
            if(is_array($fieldid)) {
                $query->where('fieldsid IN(' . implode(',', $fieldid) . ')');
            }else{
                $query->where('fieldsid =' .$fieldid);
            }
            if($groupid){
                if(is_numeric($groupid)){
                    $query -> where('groupid <> '.$groupid);
                }elseif(is_array($groupid)){
                    $query -> where('groupid NOT IN('. implode(',', $groupid) .')');
                }
            }
            $db -> setQuery($query);
            if($bool = $db -> execute()) {
                return true;
            }
        }
        return false;
    }

    public static function insertFieldGroups($fieldid, $groupid){
        if($fieldid && $groupid){
            $db = TZ_Portfolio_PlusDatabase::getDbo();
            $_groupid   = $groupid;
            $_dbGroupid = null;
            if($dbGroupid = self::getFieldGroups($fieldid)){
                $_groupid   = array_diff($groupid, $dbGroupid);
            }

            // Remove old field's group
            self::removeFieldGroups($fieldid, $groupid);

            if(count($_groupid)) {
                $query = $db->getQuery(true);
                $query->insert($db->quoteName('#__tz_portfolio_plus_field_fieldgroup_map'));
                $query->columns('fieldsid,groupid');
                if (is_array($_groupid)) {
                    foreach ($_groupid as $gid) {
                        $query->values($fieldid . ',' . $gid);
                    }
                } else {
                    $query->values($fieldid . ',' . $_groupid);
                }
                $db->setQuery($query);
                if ($db->execute()) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function prepareForm(&$form, $data){
        JLoader::import('addons', COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH);
        if($addons = TZ_Portfolio_PlusHelperAddons::getAddons(array('published' => 1, 'folder' => 'extrafields'))){
            require_once COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH.DIRECTORY_SEPARATOR.'extrafields.php';
            foreach($addons as $addon){
                $field          = new stdClass();
                $field -> id    = $addon -> id;
                $field -> params    = new Registry();
                if(isset($addon -> params) && !is_object($addon -> params)){
                    $field -> params    = new Registry($addon -> params);
                }else{
                    $field -> params    = $addon -> params;
                }
                $field -> type      = $addon -> element;
                $fieldObj   = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraField($field);
                $fieldObj -> prepareForm($form, $data);
            }
        }
    }
}