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

// no direct access
defined('_JEXEC') or die();

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.modellist');
jimport('joomla.html.pagination');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class TZ_Portfolio_PlusModelSearch extends JModelList
{
    protected $pagNav                   = null;
    protected $rowsTag                  = null;
    protected $categories               = null;

    function populateState($ordering = null, $direction = null){
        parent::populateState($ordering,$direction);

        $app    = JFactory::getApplication('site');
        $params = $app -> getParams('com_tz_portfolio_plus');

        $global_params    = JComponentHelper::getParams('com_tz_portfolio_plus');

        if($layout_type = $params -> get('layout_type',array())){

            if(!count($layout_type)){
                $params -> set('layout_type',$global_params -> get('layout_type',array()));
            }
        }else{
            $params -> set('layout_type',$global_params -> get('layout_type',array()));
        }

        $user		= JFactory::getUser();

        $offset = $app -> input -> getUInt('limitstart',0);

        if($params -> get('show_limit_box',0) ){
            $limit  = $app->getUserStateFromRequest('com_tz_portfolio_plus.portfolio.limit','limit',$params -> get('tz_article_limit',10));
        }
        else{
            $limit  = (int) $params -> get('tz_article_limit',10);
        }

        $db		= $this->getDbo();
        $query	= $db->getQuery(true);

        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio_plus')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio_plus'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
            // Filter by start and end dates.
            $nullDate = $db->quote($db->getNullDate());
            $nowDate = $db->quote(JFactory::getDate()->toSql());

            $query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
            $query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $this->setState('filter.language', JLanguageMultilang::isEnabled());

        $this -> setState('params',$params);
        $this -> setState('list.start', $offset);
        $this -> setState('Itemid',$params -> get('id'));
        $this -> setState('list.limit',$limit);
        $this -> setState('catid',$params -> get('catid'));
        $this -> setState('filter.char',$app -> input -> getString('char',null));
        $this -> setState('filter.tagId',null);
        $this -> setState('filter.userId',null);
        $this -> setState('filter.featured',null);
        $this -> setState('filter.year',null);
        $this -> setState('filter.month',null);

        $data   = $app -> input -> getArray();
        $this -> setState('filter.category_id',isset($data['id'])?$app -> input -> getInt('id'):null);

        $this -> setState('filter.searchword', $app->input->getString('searchword'));
        $this -> setState('filter.fields', $app -> input -> get('fields', array(), 'array'));
    }

    protected function getListQuery(){
        $params = $this -> getState('params');

        $user		= JFactory::getUser();

        $app        = JFactory::getApplication();
        $input      = $app -> input;

        $db         = JFactory::getDbo();
        $query      = $db -> getQuery(true);

        $catid      = $this -> getState('filter.category_id');
        $searchWord = $this -> getState('filter.searchword');
        $fields     = $input -> get('fields', array(), 'array');

        if($searchWord || count($fields) || (!$searchWord && !count($fields) && $catid)) {
            $query->select('c.*,t.title AS tagName, m.catid AS catid ,cc.title AS category_title,u.name AS author');
            $query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
            $query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
            $query->select('CASE WHEN CHAR_LENGTH(c.fulltext) THEN c.fulltext ELSE null END as readmore');

            $query->from($db->quoteName('#__tz_portfolio_plus_content') . ' AS c');

            $query->join('INNER', $db->quoteName('#__tz_portfolio_plus_content_category_map') . ' AS m ON m.contentid=c.id');
            $query->join('LEFT', $db->quoteName('#__tz_portfolio_plus_categories') . ' AS cc ON cc.id=m.catid');
            $query->join('LEFT', $db->quoteName('#__tz_portfolio_plus_tag_content_map') . ' AS x ON x.contentid=c.id');
            $query->join('LEFT', $db->quoteName('#__tz_portfolio_plus_tags') . ' AS t ON t.id=x.tagsid');
            $query->join('LEFT', $db->quoteName('#__users') . ' AS u ON u.id=c.created_by');

            // Join over the categories to get parent category titles
            $query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
            $query->join('LEFT', '#__tz_portfolio_plus_categories as parent ON parent.id = cc.parent_id');

            // Filter by published state
            $published = $this->getState('filter.published');

            if (is_numeric($published)) {
                // Use article state if badcats.id is null, otherwise, force 0 for unpublished
                $query->where('c.state = ' . (int)$published);
            } elseif (is_array($published)) {
                $published  = ArrayHelper::toInteger($published);
                $published  = implode(',', $published);
                // Use article state if badcats.id is null, otherwise, force 0 for unpublished
                $query->where('c.state IN (' . $published . ')');
            }

            if ((!$user->authorise('core.edit.state', 'com_tz_portfolio_plus')) && (!$user->authorise('core.edit', 'com_tz_portfolio_plus'))) {
                // Filter by start and end dates.
                $nullDate = $db->Quote($db->getNullDate());
                $nowDate = $db->Quote(JFactory::getDate()->toSQL());

                $query->where('(c.publish_up = ' . $nullDate . ' OR c.publish_up <= ' . $nowDate . ')');
                $query->where('(c.publish_down = ' . $nullDate . ' OR c.publish_down >= ' . $nowDate . ')');
            }

            // Filter by access level.
            if (!$params->get('show_noauth')) {
                $groups = implode(',', $user->getAuthorisedViewLevels());
                $query->where('c.access IN (' . $groups . ')');
                $query->where('cc.access IN (' . $groups . ')');
            }

            // Filter by category
            if ($this->getState('filter.category_id')) {
                $catid = $this->getState('filter.category_id');
                if (is_array($catid)) {
                    $catid = array_filter($catid);
                    if (count($catid)) {
                        $categoryEquals = 'm.catid IN(' . implode(',', $catid) . ')';
                    }
                } elseif (!empty($catid)) {
                    $categoryEquals = 'm.catid = '. (int) $catid;
                }

                // Filter by sub categories
                // Create a subquery for the subcategory list
                if($params -> get('search_subcategory', 0)) {
                    $subQuery = $db->getQuery(true)
                        ->select('sub.id')
                        ->from('#__tz_portfolio_plus_categories as sub')
                        ->join('INNER', '#__tz_portfolio_plus_categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt')
                        ->where('this.id = ' . (int)$catid);

                    // Add the subquery to the main query
                    $query->where('(' . $categoryEquals . ' OR m.catid IN (' . (string)$subQuery . '))');
                }else{
                    $query -> where($categoryEquals);
                }
            }

            // Filter by word
            if ($searchWord = $this->getState('filter.searchword')) {
                $searchWord = $db->quote('%' . $db->escape($searchWord, true) . '%', true);
                $query->where('(c.title LIKE ' . $searchWord . ' OR c.introtext LIKE ' . $searchWord.')');
            }

            // Filter by extrafields
            if ($fields = $input->get('fields', array(), 'array')) {
                if (count($fields)) {
                    $fields = array_filter($fields);
                    $fieldIds = array_keys($fields);
                    $fieldIds = array_unique($fieldIds);

                    JLoader::import('extrafields', JPATH_SITE . '/components/com_tz_portfolio_plus/helpers');
                    if ($extraFields = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraFieldObjectById($fieldIds)) {
                        $where = array();
                        if (count($extraFields)) {
                            foreach ($extraFields as $field) {
                                $field->onSearch($query, $where, $fields[$field->id]);
                            }
                        }
                        if (count($where)) {
                            $query->where('(' . implode(' AND ', $where) . ')');
                        }
                    }
                }
            }

            $articleOrderby		= $params->get('orderby_sec', 'rdate');
            $articleOrderDate	= $params->get('order_date');
            $categoryOrderby	= $params->def('orderby_pri', '');
            $secondary          = TZ_Portfolio_PlusHelperQuery::orderbySecondary($articleOrderby, $articleOrderDate);
            $primary            = TZ_Portfolio_PlusHelperQuery::orderbyPrimary($categoryOrderby);

            $query->order($primary . ' ' . $secondary);

            // Filter by language
            if ($this->getState('filter.language')) {
                $query->where('c.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
            }

            $query->group('c.id');
        }

        return $query;
    }

    function getAvailableLetter(){
        $params = $this -> getState('params');
        if($params -> get('use_filter_first_letter',1)){
            if($letters = $params -> get('tz_letters','a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z')){
                $db = JFactory::getDbo();
                $letters = explode(',',$letters);
                $arr    = null;
                if($catids = $params -> get('catid')){
                    if(count($catids) > 1){
                        if(empty($catids[0])){
                            array_shift($catids);
                        }
                        $catids = implode(',',$catids);
                    }
                    else{
                        if(!empty($catids[0])){
                            $catids = $catids[0];
                        }
                        else
                            $catids = null;
                    }
                }

                $where  = null;
                if($catids){
                    $where  = ' AND cc.id IN('.$catids.')';
                }

                if($featured = $this -> getState('filter.featured')){
                    if(is_array($featured)){
                        $featured   = implode(',',$featured);
                    }
                    $where  .= ' AND c.featured IN('.$featured.')';
                }

                if($tagId = $this -> getState('filter.tagId')){
                    $where  .= ' AND t.id='.$tagId;
                }

                if($userId = $this -> getState('filter.userId')){
                    $where  .= ' AND c.created_by='.$userId;
                }

                if($year = $this -> getState('filter.year')){
                    $where  .= ' AND YEAR(c.created) = '.$year;
                }

                if($month = $this -> getState('filter.month')){
                    $where  .= ' AND MONTH(c.created) = '.$month;
                }

                foreach($letters as $i => &$letter){
                    $letter = trim($letter);
                    $query  = 'SELECT c.*'
                          .' FROM #__tz_portfolio_plus_content AS c'
                          .' LEFT JOIN #__tz_portfolio_plus_content_category_map AS m ON m.contentid = c.id'
                          .' LEFT JOIN #__tz_portfolio_plus_categories AS cc ON cc.id=m.catid'
                          .' LEFT JOIN #__tz_portfolio_plus_tag_content_map AS x ON x.contentid=c.id'
                          .' LEFT JOIN #__tz_portfolio_plus_tags AS t ON t.id=x.tagsid'
                          .' LEFT JOIN #__users AS u ON c.created_by=u.id'
                          .' WHERE c.state=1'
                              .$where
                              .' AND ASCII(SUBSTR(LOWER(c.title),1,1)) = ASCII("'.mb_strtolower($letter).'")'
                          .' GROUP BY c.id';
                    $db -> setQuery($query);
                    $count  = $db -> loadResult();
                    $arr[$i]    = false;
                    if($count){
                        $arr[$i]  = true;
                    }
                }

                return $arr;

            }
        }
        return false;
    }


    public function getCategoriesOptions(){
        $leveltmp   = 1;
        $options    = array();
        $option     = new stdClass();

        $option -> text     = JText::_('JOPTION_SELECT_CATEGORY');
        $option -> value    = '';
        $options[]          = $option;
        $params             = $this -> getState('params');

        if($parentid = $params -> get('search_parent_cat', 0)){
            if($categories = TZ_Portfolio_PlusFrontHelperCategories::getSubCategoriesByParentId((int) $parentid)){

                $leveltmp   = $categories[0] -> level - 1;

                foreach($categories as $i => $item){
                    if(!$params -> get('show_s_parent_root', 1) && $parentid == $item -> id){
                        if(isset($categories[$i + 1]) && $categories[$i + 1]) {
                            $leveltmp = $categories[$i + 1] -> level - 1;
                        }
                        unset($categories[$i]);
                        continue;
                    }
                    $option = new stdClass();

                    $repeat = ($item->level - $leveltmp - 1 >= 0) ? $item->level - $leveltmp - 1 : 0;
                    $title  = str_repeat('- ', $repeat) . $item->title;
                    $option -> text     = $title;
                    $option -> value    = $item -> id;

                    $options[]  = $option;
                }
            }
        }else{
            JHtml::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tz_portfolio_plus/helpers/html');
            $_options   = JHtml::_('tzcategory.options', 'com_tz_portfolio_plus',array('filter.published' => 1));
            $options    = array_merge($options, $_options);
        }
        return $options;
    }

    public function getAdvFilterFields(){
        JLoader::import('extrafields', JPATH_SITE.'/components/com_tz_portfolio_plus/helpers');
        $params     = $this -> getState('params');
        $groupIds   = $params -> get('search_groupid', array());
        $groupIds   = array_filter($groupIds);
        if(count($groupIds)){
            if($advfilter = TZ_Portfolio_PlusFrontHelperExtraFields::getAdvFilterFields(null, array(
                'filter.groupid' => $groupIds, 'filter.group' => $params -> get('order_fieldgroup', 'rdate')))) {
                return $advfilter;
            }
        }
        return false;
    }
}
?>