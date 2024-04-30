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

tzportfolioplusimport('database.database');

use Joomla\CMS\Filesystem\File;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

jimport('joomla.filesytem.file');

class TZ_Portfolio_PlusFrontHelperExtraFields{

    protected static $cache     = array();

    public static function getExtraField($field, $article = null, $resetArticleCache = false, $options = array())
    {
        if (!$field)
        {
            return null;
        }

        if (is_object($field))
        {
            $fieldId = $field->id;
        }
        else
        {
            $fieldId = $field;
        }

        if (is_object($article))
        {
            $articleId = $article->id;
        }
        else
        {
            $articleId = $article;
        }

        // Create storeId key
        $storeId    = "TZ_Portfolio_PlusCTField::" . $fieldId.($articleId?'_'.$articleId:'');

        $context    = null;
        if(count($options)){
            if(isset($options['context']) && $options['context']) {
                $storeId   .= '::' . $options['context'];
            }
        }

        $storeId = md5($storeId);

        if (!isset(self::$cache['fields'][$storeId]))
        {

            if (!is_object($field))
            {
                $field = self::getExtraFieldById($fieldId);
            }

            if (!$field)
            {
                return false;
            }
            tzportfolioplusimport('fields.extrafield');

            if (!self::checkExtraField($field-> type))
            {
                $fieldClassName = 'TZ_Portfolio_PlusExtraField';
            }
            else
            {
                $fieldClassName = 'TZ_Portfolio_PlusExtraField' . $field->type;
            }

            self::loadExtraFieldFile($field -> type);

            $_fieldObj = clone $field;

            $fieldClass = null;
            if (class_exists($fieldClassName))
            {
                $fieldClass = new $fieldClassName($_fieldObj, null, $options);
            }

            self::$cache['fields'][$storeId] = $fieldClass;
        }


        $fieldClass = self::$cache['fields'][$storeId];
        if ($fieldClass)
        {
            $fieldClassWithDoc = clone $fieldClass;
            $fieldClassWithDoc->loadArticle($article, $resetArticleCache);

            return $fieldClassWithDoc;
        }
        else
        {
            return $fieldClass;
        }
    }

    public static function getExtraFields($article, $params = null, $group = false, $options = array()){
        $fields     = null;
        if($group){
            $groupobj   = self::getFieldGroupsByArticleId($article -> id);
            $groupid    = ArrayHelper::getColumn($groupobj, 'id');
            $fields     = self::getExtraFieldsByFieldGroupId($groupid);
        }else{
            $fields = self::getExtraFieldsByArticle($article, $params, $options);
        }

        if($fields){
            if(count($fields)){
                $app    = JFactory::getApplication();
                $fieldsObject   = array();
                foreach($fields as $field){
                    if($field -> published == 1) {

                        $fieldObj   = self::getExtraField($field, $article, false, $options);

                        // Since v2.3.5
                        $app -> triggerEvent('onTPExtraFieldPreapare', array(&$fieldObj, $article, $params));

                        if($fieldObj) {
                            $fieldsObject[] = $fieldObj;
                        }
                    }
                }
                return $fieldsObject;
            }
        }
        return false;
    }

    public static function getFieldGroupsById($fieldGroupId)
    {
        $storeId = md5(__METHOD__ . "::" . (int) $fieldGroupId);
        if (!isset(self::$cache[$storeId]))
        {
            $db    = TZ_Portfolio_PlusDatabase::getDbo();
            $query = $db->getQuery(true);

            $query -> select('*');
            $query -> from('#__tz_portfolio_plus_fieldgroups');
            if(is_array($fieldGroupId)){
                $query -> where('id IN('.implode(',', $fieldGroupId).')');
            }else {
                $query->where('id = ' . $fieldGroupId);
            }
            $query -> where('published = 1');

            $user       = JFactory::getUser();
            $viewlevels = ArrayHelper::toInteger($user->getAuthorisedViewLevels());

            $query -> where('access IN (' . implode(',', $viewlevels) . ')');

            $db->setQuery($query);
            self::$cache[$storeId] = $db->loadObjectList();
        }

        return self::$cache[$storeId];
    }

    public static function getFieldGroupsByArticleId($articleId){
        $storeId = md5(__METHOD__ . "::" . (int) $articleId);
        if (!isset(self::$cache[$storeId]))
        {
            if($articleId) {
                $db = TZ_Portfolio_PlusDatabase::getDbo();
                $query = $db->getQuery(true);
                $subquery = $db->getQuery(true);

                $subquery->select('CASE WHEN c.groupid = 0 OR c.groupid IS NULL THEN cc.groupid ELSE c.groupid END AS groupid');
                $subquery->from('#__tz_portfolio_plus_content AS c');
                $subquery->join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = c.id');
                $subquery->join('LEFT', '#__tz_portfolio_plus_categories AS cc ON cc.id = m.catid');

                if (is_array($articleId)) {
                    $subquery->where('c.id IN(' . implode(',', $articleId) . ')');
                } else {
                    $subquery->where('c.id = ' . $articleId);
                }
                $subquery->group('groupid');

                $query->select('*');
                $query->from('#__tz_portfolio_plus_fieldgroups');
                $query->where('id IN(' . $subquery . ')');

                // Implement View Level Access
                $user       = JFactory::getUser();
                $viewlevels = ArrayHelper::toInteger($user->getAuthorisedViewLevels());

                $query -> where('access IN (' . implode(',', $viewlevels) . ')');

                $db -> setQuery($query);
                self::$cache[$storeId] = $db->loadObjectList();
            }else{
                self::$cache[$storeId]  = false;
            }
        }

        return self::$cache[$storeId];
    }

    public static function getFieldGroupsByCatId($catId)
    {
        $storeId = md5(__METHOD__ . "::" . (int) $catId);
        if (!isset(self::$cache[$storeId]))
        {
            if($catId) {
                $db     = TZ_Portfolio_PlusDatabase::getDbo();
                $query  = $db->getQuery(true);

                $query -> select('g.*, c.id AS catid, c.title AS category_title');
                $query -> from('#__tz_portfolio_plus_fieldgroups AS g');
                $query -> join('LEFT', '#__tz_portfolio_plus_categories AS c ON c.groupid = g.id');
                if (is_array($catId)) {
                    $query -> where('c.id IN(' . implode(',', $catId) . ')');
                } else {
                    $query -> where('c.id = ' . $catId);
                }
                $query -> where('g.published = 1');
                $query -> where('c.published = 1');

                // Implement View Level Access
                $user       = JFactory::getUser();
                $viewlevels = ArrayHelper::toInteger($user->getAuthorisedViewLevels());

                $query -> where('access IN (' . implode(',', $viewlevels) . ')');

                $db->setQuery($query);
                self::$cache[$storeId] = $db->loadObjectList();
                return self::$cache[$storeId];
            }else{
                self::$cache[$storeId]  = false;
            }
        }

        return self::$cache[$storeId];
    }

    public static function loadExtraFieldFile($name){
        $storeId = md5(__METHOD__ . "::$name");
        if(!isset(self::$cache[$storeId])){
            if(self::checkExtraField($name)){
                require_once(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.'extrafields'
                    .DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$name.'.php');
            }else {
                tzportfolioplusimport('fields.extrafield');
            }
            self::$cache[$storeId]  = true;
        }
        return self::$cache[$storeId];
    }

    protected static function checkExtraField($name){

        $storeId = md5(__METHOD__ . "::$name");
        if(!isset(self::$cache[$storeId])){
            $core_path  = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.'extrafields';
            if($core_folders = \JFolder::folders($core_path)){
                $core_f_xml_path    = $core_path.DIRECTORY_SEPARATOR.$name
                    .DIRECTORY_SEPARATOR.$name.'.xml';
                if(File::exists($core_f_xml_path)){
                    self::$cache[$storeId]  = true;
                    return self::$cache[$storeId];
                }
            }

            self::$cache[$storeId]  = false;
        }
        return self::$cache[$storeId];
    }

    public static function getExtraFieldById($fieldId, $fieldObj = null)
    {
        if (!$fieldId)
        {
            return null;
        }

        $storeId = md5(__METHOD__ . "::$fieldId");

        if (!isset(self::$cache[$storeId]))
        {
            if (!is_object($fieldObj))
            {
                $db    = TZ_Portfolio_PlusDatabase::getDbo();
                $query = $db->getQuery(true);
                $query->select('field.*, fg.id AS groupid')
                    ->from('#__tz_portfolio_plus_fields AS field');
                $query -> join('INNER', '#__tz_portfolio_plus_field_fieldgroup_map AS fm ON fm.fieldsid = field.id');
                $query -> join('INNER', '#__tz_portfolio_plus_fieldgroups AS fg ON fg.id = fm.groupid');

                $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = field.type')
                    -> where('e.type = '.$db -> quote('tz_portfolio_plus-plugin'))
                    -> where('e.folder = '.$db -> quote('extrafields'))
                    -> where('e.published = 1');

                if($fieldId){
                    $query->where('field.id = ' . (int) $fieldId);
                }

                // Implement View Level Access
                $user       = JFactory::getUser();
                $viewlevels = ArrayHelper::toInteger($user->getAuthorisedViewLevels());
                $viewlevels = implode(',', $viewlevels);
                $subquery   = $db -> getQuery(true);

                $subquery -> select('subg.id');
                $subquery -> from('#__tz_portfolio_plus_fieldgroups AS subg');
                $subquery -> where('subg.access IN('.$viewlevels.')');

                $query -> where('field.access IN('.$viewlevels.')');
                $query -> where('fg.id IN('.((string) $subquery).')');
                $query -> where('e.access IN('.$viewlevels.')');

                $db->setQuery($query);

                if($fieldObj = $db->loadObject()){
                    self::$cache[$storeId] = $fieldObj;
                    return $fieldObj;
                }
                self::$cache[$storeId] = false;
            }

            self::$cache[$storeId] = $fieldObj;
        }

        return self::$cache[$storeId];
    }

    public static function getExtraFieldsByFieldGroupId($groupid){
        if($groupid) {
            if (is_array($groupid)) {
                $storeId = md5(__METHOD__ . '::' . implode(',', $groupid));
            } else {
                $storeId = md5(__METHOD__ . '::' . $groupid);
            }
            if (!isset(self::$cache[$storeId])) {
                if($groupid){
                    $db     = TZ_Portfolio_PlusDatabase::getDbo();;
                    $query  = $db->getQuery(true);

                    $query -> select('f.*');
                    $query -> from('#__tz_portfolio_plus_fields AS f');
                    $query -> join('INNER', '#__tz_portfolio_plus_field_fieldgroup_map AS m ON m.fieldsid = f.id');
                    $query -> join('INNER', '#__tz_portfolio_plus_fieldgroups AS g ON g.id = m.groupid');

                    $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = f.type')
                        -> where('e.type = ' . $db->quote('tz_portfolio_plus-plugin'))
                        -> where('e.folder = ' . $db->quote('extrafields'))
                        -> where('e.published = 1');

                    if (is_array($groupid)) {
                        $query -> where('g.id IN(' . implode(',', $groupid) . ')');
                    } else {
                        $query -> where('g.id = ' . $groupid);
                    }

                    // Implement View Level Access
                    $user       = JFactory::getUser();
                    $viewlevels = ArrayHelper::toInteger($user->getAuthorisedViewLevels());
                    $viewlevels = implode(',', $viewlevels);
                    $subquery   = $db -> getQuery(true);

                    $subquery -> select('subg.id');
                    $subquery -> from('#__tz_portfolio_plus_fieldgroups AS subg');
                    $subquery -> where('subg.access IN('.$viewlevels.')');

                    $query -> where('f.access IN('.$viewlevels.')');
                    $query -> where('g.id IN('.((string) $subquery).')');
                    $query -> where('e.access IN('.$viewlevels.')');

                    $db->setQuery($query);
                    if ($fields = $db->loadObjectList()) {
                        self::$cache[$storeId] = $fields;
                        return $fields;
                    }
                }
                self::$cache[$storeId] = false;
            }
            return self::$cache[$storeId];
        }
        return false;
    }

    public static function getExtraFieldsByArticle($article, $params = null, $options = array()){
        if (is_numeric($article))
        {
            $article = TZ_Portfolio_PlusContentHelper::getArticleById($article);
        }
        $storeId    = __METHOD__;

        if($groupid    = self::getFieldGroupsByArticleId($article -> id)) {
            $groupid = ArrayHelper::getColumn($groupid, 'id');
            $storeId    .= '::'.implode(',',$groupid);
        }

        $storeId    .= '::'.$article -> id;
        $storeId    .= json_encode($options);

        $user       = JFactory::getUser();
        $viewlevels = ArrayHelper::toInteger($user->getAuthorisedViewLevels());
        $viewlevels = implode(',', $viewlevels);

        if($viewlevels) {
            $storeId .= '::'.$viewlevels;
        }

        $storeId    = md5($storeId);

        if(!isset(self::$cache[$storeId])){
            $db         = TZ_Portfolio_PlusDatabase::getDbo();;
            $query      = $db -> getQuery(true);

            $query -> select('f.*, fm.groupid');
            $query -> from('#__tz_portfolio_plus_fields AS f');
            $query -> join('INNER', '#__tz_portfolio_plus_field_content_map AS m ON m.fieldsid = f.id');
            $query -> join('INNER', '#__tz_portfolio_plus_content AS c ON c.id = m.contentid');
            $query -> join('INNER', '#__tz_portfolio_plus_field_fieldgroup_map AS fm ON fm.fieldsid = f.id');
            $query -> join('INNER', '#__tz_portfolio_plus_fieldgroups AS fg ON fg.id = fm.groupid');

            $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = f.type')
                -> where('e.type = '.$db -> quote('tz_portfolio_plus-plugin'))
                -> where('e.folder = '.$db -> quote('extrafields'))
                -> where('e.published = 1');

            if(count($groupid)) {
                $query->where('fm.groupid IN('.implode(',', $groupid).')');
            }
            $query -> where('c.id = '.$article -> id);
            $query -> where('f.published = 1');

            // Filter detail, list, search view
            if(isset($options['filter.detail_view'])){
                if($options['filter.detail_view']) {
                    $query -> where('f.detail_view = 1');
                }else{
                    $query -> where('f.detail_view = 0');
                }
            }
            if(isset($options['filter.list_view'])){
                if($options['filter.list_view']) {
                    $query -> where('f.list_view = 1');
                }else{
                    $query -> where('f.list_view = 0');
                }
            }
            if(isset($options['filter.advanced_search'])){
                if($options['filter.advanced_search']) {
                    $query -> where('f.advanced_search = 1');
                }else{
                    $query -> where('f.advanced_search = 0');
                }
            }

            // Implement View Level Access
            $subquery   = $db -> getQuery(true);

            $subquery -> select('subg.id');
            $subquery -> from('#__tz_portfolio_plus_fieldgroups AS subg');
            $subquery -> where('subg.access IN('.$viewlevels.')');

            $query -> where('f.access IN('.$viewlevels.')');
            $query -> where('fg.id IN('.((string) $subquery).')');
            $query -> where('e.access IN('.$viewlevels.')');


            if(isset($options['filter.group']) && $orderGroup = $options['filter.group']){
                switch ($orderGroup){
                    default:
                        $query -> order('fg.id DESC');
                        break;
                    case 'date':
                        $query -> order('fg.id ASC');
                        break;
                    case 'alpha':
                        $query -> order('fg.name ASC');
                        break;
                    case 'ralpha':
                        $query -> order('fg.name DESC');
                        break;
                    case 'order':
                        $query -> order('fg.ordering ASC');
                        break;
                }
            }

            // Ordering by default : core fields, then extra fields
            $query -> order('IF(fg.field_ordering_type = 2, '.$db -> quoteName('fm.ordering')
                .',IF(fg.field_ordering_type = 1,'.$db -> quoteName('f.ordering').',NULL))');

            $query -> group('f.id');

            $db    -> setQuery($query);

            if($fields = $db -> loadObjectList()){
                self::$cache[$storeId]  = $fields;
                return $fields;
            }
            self::$cache[$storeId]  = false;
        }
        return self::$cache[$storeId];
    }

    public static function getExtraFieldsByIds($fieldId, $fieldObj = null)
    {
        if (!$fieldId)
        {
            return null;
        }

        if (is_array($fieldId)) {
            $storeId = md5(__METHOD__ . '::' . implode(',', $fieldId));
        } else {
            $storeId = md5(__METHOD__ . '::' . $fieldId);
        }

        if (!isset(self::$cache[$storeId]))
        {
            if (!is_object($fieldObj))
            {
                $db    = TZ_Portfolio_PlusDatabase::getDbo();;
                $query = $db->getQuery(true);
                $query->select('field.*, fg.id AS groupid')
                    ->from('#__tz_portfolio_plus_fields AS field');
                $query -> join('INNER', '#__tz_portfolio_plus_field_fieldgroup_map AS fm ON fm.fieldsid = field.id');
                $query -> join('INNER', '#__tz_portfolio_plus_fieldgroups AS fg ON fg.id = fm.groupid');

                $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = field.type')
                    -> where('e.type = '.$db -> quote('tz_portfolio_plus-plugin'))
                    -> where('e.folder = '.$db -> quote('extrafields'))
                    -> where('e.published = 1');

                if (is_array($fieldId)) {
                    $query->where('field.id IN('.implode(',', $fieldId).')');
                } else {
                    $query->where('field.id = ' . (int) $fieldId);
                }

                // Implement View Level Access
                $user       = JFactory::getUser();
                $viewlevels = ArrayHelper::toInteger($user->getAuthorisedViewLevels());
                $viewlevels = implode(',', $viewlevels);
                $subquery   = $db -> getQuery(true);

                $subquery -> select('subg.id');
                $subquery -> from('#__tz_portfolio_plus_fieldgroups AS subg');
                $subquery -> where('subg.access IN('.$viewlevels.')');

                $query -> where('field.access IN('.$viewlevels.')');
                $query -> where('fg.id IN('.((string) $subquery).')');
                $query -> where('e.access IN('.$viewlevels.')');

                $db->setQuery($query);

                if($fieldObjs = $db->loadObjectList()){
                    self::$cache[$storeId]  = $fieldObjs;
                    return $fieldObjs;
                }
            }

            self::$cache[$storeId] = false;
        }

        return self::$cache[$storeId];
    }

    public static function getExtraFieldObjectById($fieldId, $fieldObj = null)
    {
        if (!$fieldId) {
            return null;
        }

        if (is_array($fieldId)) {
            $storeId = md5(__METHOD__ . '::' . implode(',', $fieldId));
        } else {
            $storeId = md5(__METHOD__ . '::' . $fieldId);
        }

        if (!isset(self::$cache[$storeId]))
        {
            if($fields = self::getExtraFieldsByIds($fieldId)){
                foreach($fields as $field){
                    if($fieldObj = self::getExtraField($field)){
                        self::$cache[$storeId][] = $fieldObj;
                    }
                }
                return self::$cache[$storeId];
            }
            self::$cache[$storeId]  = false;
        }

        return self::$cache[$storeId];

    }

    public static function getAdvFilterFields($fieldids = null, $options = array('group' => true))
    {
        $opt    = json_encode($options);

        if (is_array($fieldids)) {
            $storeId = md5(__METHOD__ . '::' . implode(',', $fieldids).'::'.$opt);
        } else {
            $storeId = md5(__METHOD__ . '::' . $fieldids.'::'.$opt);
        }

        if (!isset(self::$cache[$storeId]))
        {
            $app      = JFactory::getApplication();
//            $db       = TZ_Portfolio_PlusDatabase::getDbo();;
            $db     = TZ_Portfolio_PlusDatabase::getDbo();

//            $db->setQuery("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
//            $db->execute();

            $query    = $db->getQuery(true);
            $query->select('e.folder, f.*, fg.name AS field_group_name, fg.id AS groupid');
            $query->from('#__tz_portfolio_plus_fields AS f');
            $query -> where('f.published = 1');

            $query->join('', '#__tz_portfolio_plus_extensions AS e ON e.element = f.type')
                -> where('e.type = '.$db -> quote('tz_portfolio_plus-plugin'))
                -> where('e.folder = '.$db -> quote('extrafields'))
                -> where('e.published = 1');

            $query -> join('INNER', '#__tz_portfolio_plus_field_fieldgroup_map AS fm ON fm.fieldsid = f.id');
            $query->join('', '#__tz_portfolio_plus_fieldgroups AS fg ON fg.id = fm.groupid');

            if(isset($options['filter.groupid']) && $options['filter.groupid']){
                $groupIds   = $options['filter.groupid'];
                if(is_array($groupIds) && count($groupIds)) {
                    $query->where('fg.id IN(' .implode(',', $groupIds).')');
                }elseif(is_numeric($groupIds)){
                    $query -> where('fg.id = '. $groupIds);
                }
            }

            $query -> group('f.id');
            if ($app-> isClient('site'))
            {
                $query->where('f.advanced_search = 1');
            }

            if(is_array($fieldids)){
                $fieldids   = array_filter($fieldids);
                if(count($fieldids)) {
                    $query->where('f.id IN(' . implode(',', $fieldids) . ')');
                }
            }elseif(is_numeric($fieldids)){
                $query -> where('f.id = '. (int) $fieldids);
            }

            // Implement View Level Access
            $user       = JFactory::getUser();
            $viewlevels = ArrayHelper::toInteger($user->getAuthorisedViewLevels());
            $viewlevels = implode(',', $viewlevels);
            $subquery   = $db -> getQuery(true);

            $subquery -> select('subg.id');
            $subquery -> from('#__tz_portfolio_plus_fieldgroups AS subg');
            $subquery -> where('subg.access IN('.$viewlevels.')');

            $query -> where('f.access IN('.$viewlevels.')');
            $query -> where('fg.id IN('.((string) $subquery).')');
            $query -> where('e.access IN('.$viewlevels.')');

            if(isset($options['filter.group']) && $orderGroup = $options['filter.group']){
                switch ($orderGroup){
                    default:
                        $query -> order('fg.id DESC');
                        break;
                    case 'date':
                        $query -> order('fg.id ASC');
                        break;
                    case 'alpha':
                        $query -> order('fg.name ASC');
                        break;
                    case 'ralpha':
                        $query -> order('fg.name DESC');
                        break;
                    case 'order':
                        $query -> order('fg.ordering ASC');
                        break;
                }
            }

            // Ordering by default : core fields, then extra fields
            if($fieldids) {
                $query->order("FIELD(f.id, " . implode(",", $fieldids) . ")");
            }else{
                $query -> order('IF(fg.field_ordering_type = 2, '.$db -> quoteName('fm.ordering')
                    .',IF(fg.field_ordering_type = 1,'.$db -> quoteName('f.ordering').',NULL))');
            }

//            var_dump($query -> dump()); die();

            $db -> setQuery($query);
            if($fields = $db -> loadObjectList()){
                $fieldGroups = array();
                foreach($fields as $field){
                    $fieldClass = self::getExtraField($field, null, true);
                    if(count($options)) {
                        if(!isset($options['group']) || (isset($options['group']) && $options['group'])) {
                            if (!isset($fieldGroups[$field->groupid])) {
                                $fieldGroups[$field->groupid] = new stdClass();
                                $fieldGroups[$field->groupid]->name = $field->field_group_name;
                                $fieldGroups[$field->groupid]->id = $field->groupid;
                                $fieldGroups[$field->groupid]->fields = array();
                            }

                            $fieldGroups[$field->groupid]->fields[] = $fieldClass;
                        }else{
                            $fieldGroups[]  = $fieldClass;
                        }
                    }
                }
                self::$cache[$storeId]  = $fieldGroups;
                return $fieldGroups;
            }

            self::$cache[$storeId]  = false;
        }
        return self::$cache[$storeId];
    }
}