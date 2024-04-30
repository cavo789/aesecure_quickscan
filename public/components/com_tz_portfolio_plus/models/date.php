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

use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.modellist');
JLoader::import('category',COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'helpers');

class TZ_Portfolio_PlusModelDate extends JModelList{
    protected $_item = null;

    protected $parameter_fields = array();

    public function __construct($config = array()){
        parent::__construct($config);

        $config['parameter_fields'] = array(
            'tz_use_image_hover' => array('tz_image_timeout'),
            'show_image_gallery' => array('image_gallery_animSpeed',
                'image_gallery_animation_duration',
                'image_gallery_startAt', 'image_gallery_itemWidth',
                'image_gallery_itemMargin', 'image_gallery_minItems',
                'image_gallery_maxItems'),
            'show_video' => array('video_width','video_height'),
            'tz_show_gmap' => array('tz_gmap_width', 'tz_gmap_height',
                'tz_gmap_latitude', 'tz_gmap_longitude',
                'tz_gmap_address','tz_gmap_custom_tooltip'),
            'useCloudZoom' => array('zoomWidth','zoomHeight',
                'adjustX','adjustY','tint','tintOpacity',
                'lensOpacity','smoothMove'),
            'show_comment' => array('disqusSubDomain','disqusApiSecretKey'),
            'show_audio' => array('audio_soundcloud_color','audio_soundcloud_theme_color',
                'audio_soundcloud_width','audio_soundcloud_height')
        );
    }

    public function populateState($ordering = null, $direction = null){
        // Initiliase variables.
        $app	= JFactory::getApplication('site');

        // Load the parameters. Merge Global and Menu Item params into new object
        $params = $app->getParams('com_tz_portfolio_plus');

        $pk = $params -> get('tz_catid',array());
        $this->setState('category.id', $pk);

        $this->setState('params', $params);
        $user		= JFactory::getUser();
                // Create a new query object.
        $db		= $this->getDbo();
        $query	= $db->getQuery(true);
        $groups	= implode(',', $user->getAuthorisedViewLevels());

        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio_plus')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio_plus'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
            // Filter by start and end dates.
            $nullDate = $db->Quote($db->getNullDate());
            $nowDate = $db->Quote(JFactory::getDate()->toSQL());

            $query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
            $query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        // process show_noauth parameter

        if (!$params->get('show_noauth')) {
            $this->setState('filter.access', true);
        }
        else {
            $this->setState('filter.access', false);
        }


        // Optional filter text
        $this->setState('list.filter', $app -> input -> getString('filter-search'));

        // filter.order

        $orderby    = '';
        $secondary  = TZ_Portfolio_PlusHelperQuery::orderbySecondary($params -> get('orderby_sec', 'rdate'), 'created', 'a');
        $primary    = TZ_Portfolio_PlusHelperQuery::orderbyPrimary($params -> get('orderby_pri'), 'c');

        $orderby .= $primary . ' ' . $secondary;

        $this -> setState('list.ordering', $orderby);
        $this -> setState('list.direction', null);

        $this->setState('list.start', $app -> input -> getInt('limitstart', 0));


//        $this->setState('list.limit', $limit);
        $this->setState('list.limit', $params -> get('tz_article_limit', 10));

        //Filter by first letter of article's title
        $this -> setState('filter.char',$app -> input -> getString('char',null));
        $this -> setState('filter.use_filter_first_letter',$params -> get('use_filter_first_letter',1));

        $this->setState('filter.language', $app->getLanguageFilter());


        $itemid = $app -> input -> getInt('id', 0) . ':' . $app -> input -> getInt('Itemid', 0);

        $_month = null;
        $_year  = null;
        if($params -> get('date')){
            $_year  = date('Y',strtotime($params -> get('date')));
            $_month = date('m',strtotime($params -> get('date')));
        }
        $year   = $app -> getUserStateFromRequest('com_tz_portfolio_plus.date.list'.$itemid.'.year','year',
            $_year,'int');
        $month  = $app -> getUserStateFromRequest('com_tz_portfolio_plus.date.list'.$itemid.'.month','month',
            $_month,'int');
        $this -> setState('filter.year',$year);
        $this -> setState('filter.month',$month);
    }

    function getListQuery(){
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.alias, a.introtext, ' .
                'a.checked_out, a.checked_out_time, ' .
                'a.type, a.media, m.catid, ' .
                'a.created, a.created_by, a.created_by_alias, ' .
                // use created if modified is 0
                'CASE WHEN a.modified = ' . $db->q($db->getNullDate()) . ' THEN a.created ELSE a.modified END as modified, ' .
                    'a.modified_by, uam.name as modified_by_name,' .
                // use created if publish_up is 0
                'CASE WHEN a.publish_up = ' . $db->q($db->getNullDate()) . ' THEN a.created ELSE a.publish_up END as publish_up,' .
                    'a.publish_down, a.images, a.urls, a.attribs, a.metadata, a.metakey, a.metadesc, a.access, ' .
                    'a.hits, a.xreference, a.featured,'.' '.$query->length('a.fulltext').' AS readmore'
            )
        );

        $query -> select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
        $query -> select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug');
        $query -> select('DATE_FORMAT(a.created, '.$db -> quote('%Y-%m').') AS date_group');

        // Process an Archived Article layout
        if ($this->getState('filter.published') == 2) {
            // If badcats is not null, this means that the article is inside an archived category
            // In this case, the state is set to 2 to indicate Archived (even if the article state is Published)
            $query->select($this->getState('list.select', 'CASE WHEN badcats.id is null THEN a.state ELSE 2 END AS state'));
        }
        else {
            // Process non-archived layout
            // If badcats is not null, this means that the article is inside an unpublished category
            // In this case, the state is set to 0 to indicate Unpublished (even if the article state is Published)
            $query->select($this->getState('list.select', 'CASE WHEN badcats.id is not null THEN 0 ELSE a.state END AS state'));
        }

        $query->from('#__tz_portfolio_plus_content AS a');

        // Join over the frontpage articles.
        if ($this->context != 'com_tz_portfolio_plus.featured') {
            $query->join('LEFT', '#__tz_portfolio_plus_content_featured_map AS fp ON fp.content_id = a.id');
        }

        $query -> join('LEFT', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = a.id');

        // Join over the categories.
        $query->select('c.title AS category_title, c.path AS category_route, c.access AS category_access, c.alias AS category_alias');
        $query->join('LEFT', '#__tz_portfolio_plus_categories AS c ON c.id = m.catid');

        // Join over the users for the author and modified_by names.
        $query->select("CASE WHEN a.created_by_alias > ' ' THEN a.created_by_alias ELSE ua.name END AS author");
        $query->select("ua.email AS author_email");

        $query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
        $query->join('LEFT', '#__users AS uam ON uam.id = a.modified_by');

        // Join on contact table
        $subQuery = $db->getQuery(true);
        $subQuery->select('contact.user_id, MAX(contact.id) AS id, contact.language');
        $subQuery->from('#__contact_details AS contact');
        $subQuery->where('contact.published = 1');
        $subQuery->group('contact.user_id, contact.language');
        $query->select('contact.id as contactid');
        $query->join('LEFT', '(' . $subQuery . ') AS contact ON contact.user_id = a.created_by');

        // Join over the categories to get parent category titles
        $query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
        $query->join('LEFT', '#__tz_portfolio_plus_categories as parent ON parent.id = c.parent_id');

        // Join to check for category published state in parent categories up the tree
        $query->select('c.published, CASE WHEN badcats.id is null THEN c.published ELSE 0 END AS parents_published');
        $subquery = 'SELECT cat.id as id FROM #__tz_portfolio_plus_categories AS cat JOIN #__tz_portfolio_plus_categories AS parent ';
        $subquery .= 'ON cat.lft BETWEEN parent.lft AND parent.rgt ';
        $subquery .= 'WHERE parent.extension = ' . $db->quote('com_tz_portfolio_plus');

        if ($this->getState('filter.published') == 2) {
            // Find any up-path categories that are archived
            // If any up-path categories are archived, include all children in archived layout
            $subquery .= ' AND parent.published = 2 GROUP BY cat.id ';
            // Set effective state to archived if up-path category is archived
            $publishedWhere = 'CASE WHEN badcats.id is null THEN a.state ELSE 2 END';
        }
        else {
            // Find any up-path categories that are not published
            // If all categories are published, badcats.id will be null, and we just use the article state
            $subquery .= ' AND parent.published != 1 GROUP BY cat.id ';
            // Select state to unpublished if up-path category is unpublished
            $publishedWhere = 'CASE WHEN badcats.id is null THEN a.state ELSE 0 END';
        }
        $query->join('LEFT OUTER', '(' . $subquery . ') AS badcats ON badcats.id = c.id');

        // Filter by access level.
        if ($access = $this->getState('filter.access')) {
            $user	= JFactory::getUser();
            $groups	= implode(',', $user->getAuthorisedViewLevels());
            $query->where('a.access IN ('.$groups.')');
            $query->where('c.access IN ('.$groups.')');
        }

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published)) {
            // Use article state if badcats.id is null, otherwise, force 0 for unpublished
            $query->where($publishedWhere . ' = ' . (int) $published);
        }
        elseif (is_array($published)) {
            $published  = ArrayHelper::toInteger($published);
            $published  = implode(',', $published);
            // Use article state if badcats.id is null, otherwise, force 0 for unpublished
            $query->where($publishedWhere . ' IN ('.$published.')');
        }

        // Filter by featured state
        $featured = $this->getState('filter.featured');
        switch ($featured)
        {
            case 'hide':
                $query->where('a.featured = 0');
                break;

            case 'only':
                $query->where('a.featured = 1');
                break;

            case 'show':
            default:
                // Normally we do not discriminate
                // between featured/unfeatured items.
                break;
        }

        // Filter by a category
        $catIds = $this -> getState('category.id');
        if(empty($catIds[0])){
            array_shift($catIds);
        }

        if(count($catIds)){
            $query -> where('m.catid IN('.implode(',',$catIds).')');
        }

        // Filter by author
        $authorId = $this->getState('filter.author_id');
        $authorWhere = '';

        if (is_numeric($authorId)) {
            $type = $this->getState('filter.author_id.include', true) ? '= ' : '<> ';
            $authorWhere = 'a.created_by '.$type.(int) $authorId;
        }
        elseif (is_array($authorId)) {
            $authorId   = ArrayHelper::toInteger($authorId);
            $authorId   = implode(',', $authorId);

            if ($authorId) {
                $type = $this->getState('filter.author_id.include', true) ? 'IN' : 'NOT IN';
                $authorWhere = 'a.created_by '.$type.' ('.$authorId.')';
            }
        }

        // Filter by author alias
        $authorAlias = $this->getState('filter.author_alias');
        $authorAliasWhere = '';

        if (is_string($authorAlias)) {
            $type = $this->getState('filter.author_alias.include', true) ? '= ' : '<> ';
            $authorAliasWhere = 'a.created_by_alias '.$type.$db->Quote($authorAlias);
        }
        elseif (is_array($authorAlias)) {
            $first = current($authorAlias);

            if (!empty($first)) {
                $authorAlias    = ArrayHelper::toString($authorAlias);

                foreach ($authorAlias as $key => $alias)
                {
                    $authorAlias[$key] = $db->Quote($alias);
                }

                $authorAlias = implode(',', $authorAlias);

                if ($authorAlias) {
                    $type = $this->getState('filter.author_alias.include', true) ? 'IN' : 'NOT IN';
                    $authorAliasWhere = 'a.created_by_alias '.$type.' ('.$authorAlias .
                        ')';
                }
            }
        }

        if (!empty($authorWhere) && !empty($authorAliasWhere)) {
            $query->where('('.$authorWhere.' OR '.$authorAliasWhere.')');
        }
        elseif (empty($authorWhere) && empty($authorAliasWhere)) {
            // If both are empty we don't want to add to the query
        }
        else {
            // One of these is empty, the other is not so we just add both
            $query->where($authorWhere.$authorAliasWhere);
        }

        // Filter by start and end dates.
        $nullDate	= $db->Quote($db->getNullDate());
        $nowDate	= $db->Quote(JFactory::getDate()->toSql());

        $query->where('(a.publish_up = '.$nullDate.' OR a.publish_up <= '.$nowDate.')');
        $query->where('(a.publish_down = '.$nullDate.' OR a.publish_down >= '.$nowDate.')');

        // Filter by Date Range or Relative Date
        $dateFiltering = $this->getState('filter.date_filtering', 'off');
        $dateField = $this->getState('filter.date_field', 'a.created');

        switch ($dateFiltering)
        {
            case 'range':
                $startDateRange = $db->Quote($this->getState('filter.start_date_range', $nullDate));
                $endDateRange = $db->Quote($this->getState('filter.end_date_range', $nullDate));
                $query->where('('.$dateField.' >= '.$startDateRange.' AND '.$dateField .
                    ' <= '.$endDateRange.')');
                break;

            case 'relative':
                $relativeDate = (int) $this->getState('filter.relative_date', 0);
                $query->where(
                    $dateField.' >= DATE_SUB(' . $nowDate.', INTERVAL ' .
                    $relativeDate.' DAY)'
                );
                break;

            case 'off':
            default:
                break;
        }

        // process the filter for list views with user-entered filters
        $params = $this->getState('params');

        if ((is_object($params)) && ($params->get('filter_field') != 'hide') && ($filter = $this->getState('list.filter'))) {
            // clean filter variable
            $filter = StringHelper::strtolower($filter);
            $hitsFilter = (int) $filter;
            $filter = $db->Quote('%'.$db->escape($filter, true).'%', false);

            switch ($params->get('filter_field'))
            {
                case 'author':
                    $query->where(
                        'LOWER( CASE WHEN a.created_by_alias > '.$db->quote(' ').
                        ' THEN a.created_by_alias ELSE ua.name END ) LIKE '.$filter.' '
                    );
                    break;

                case 'hits':
                    $query->where('a.hits >= '.$hitsFilter.' ');
                    break;

                case 'title':
                default: // default to 'title' if parameter is not valid
                    $query->where('LOWER( a.title ) LIKE '.$filter);
                    break;
            }
        }

        // Filter by first letter of title
        if($this -> getState('filter.use_filter_first_letter')){
            if($char = $this -> getState('filter.char')){
                $query -> where('ASCII(SUBSTR(LOWER(a.title),1,1)) = ASCII("'.mb_strtolower($char).'")');
            }
        }

        // Filter by language
        if ($this->getState('filter.language')) {
            $query->where('a.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
            $query->where('(contact.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').') OR contact.language IS NULL)');
        }

        // Filter by create date
        if($year = $this -> getState('filter.year')){
            $query -> where('YEAR(a.created) = '.$this -> _db -> quote($year));
        }
        if($month = $this -> getState('filter.month')){
            $query -> where('MONTH(a.created) = '.$this -> _db -> quote($month));
        }

        // Add the list ordering clause.
        $query->order('a.created DESC,'.$this->getState('list.ordering', 'a.ordering').' '.$this->getState('list.direction', ''));

        $query -> group('a.id');

        return $query;

    }

    public function getItems(){
        if($items	= parent::getItems()){
            $user	= JFactory::getUser();
            $userId	= $user->get('id');
            $guest	= $user->get('guest');
            $groups	= $user->getAuthorisedViewLevels();

            // Get the global params
            $globalParams = JComponentHelper::getParams('com_tz_portfolio_plus', true);

            // Variable date
            $date   = null;

            // Convert the parameter fields into objects.
            foreach ($items as &$item)
            {
                if($item -> date_group != $date){
                    $date   = $item -> date_group;
                }else{
                    $item -> date_group = null;
                }

                $params         = clone($this -> getState('params'));
                $item->params   = clone($params);

                // get display date
                switch ($item->params->get('list_show_date'))
                {
                    case 'modified':
                        $item->displayDate = $item->modified;
                        break;

                    case 'published':
                        $item->displayDate = ($item->publish_up == 0) ? $item->created : $item->publish_up;
                        break;

                    default:
                    case 'created':
                        $item->displayDate = $item->created;
                        break;
                }

                // Compute the asset access permissions.
                // Technically guest could edit an article, but lets not check that to improve performance a little.
                if (!$guest) {
                    $asset	= 'com_tz_portfolio_plus.article.'.$item->id;

                    // Check general edit permission first.
                    if ($user->authorise('core.edit', $asset)) {
                        $item->params->set('access-edit', true);
                    }
                    // Now check if edit.own is available.
                    elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
                        // Check for a valid user and that they are the owner.
                        if ($userId == $item->created_by) {
                            $item->params->set('access-edit', true);
                        }
                    }
                }

                $access = $this->getState('filter.access');

                if ($access) {
                    // If the access filter has been set, we already have only the articles this user can view.
                    $item->params->set('access-view', true);
                }
                else {
                    // If no access filter is set, the layout takes some responsibility for display of limited information.
                    if ($item->catid == 0 || $item->category_access === null) {
                        $item->params->set('access-view', in_array($item->access, $groups));
                    }
                    else {
                        $item->params->set('access-view', in_array($item->access, $groups) && in_array($item->category_access, $groups));
                    }
                }

                $media      = $item -> media;
                $registry   = new JRegistry;
                $registry -> loadString($media);

                $media              = $registry -> toObject();
                $item -> media      = $media;

                $item -> mediatypes = array();
            }

            return $items;
        }
        return false;
    }

    public function getCategory()
    {
        if (!is_object($this->_item)) {
            if( isset( $this->state->params ) ) {
                $params = $this->state->params;
                $options = array();
                $options['countItems'] = $params->get('show_cat_num_articles', 1) || !$params->get('show_empty_categories_cat', 0);
            }
            else {
                $options['countItems'] = 0;
            }

            $categories = JCategories::getInstance('Content', $options);
            $this->_item = $categories->get($this->getState('category.id', 'root'));

            // Compute selected asset permissions.
            if (is_object($this->_item)) {
                $user	= JFactory::getUser();
                $userId	= $user->get('id');
                $asset	= 'com_tz_portfolio_plus.category.'.$this->_item->id;

                // Check general create permission.
                if ($user->authorise('core.create', $asset)) {
                    $this->_item->getParams()->set('access-create', true);
                }

                // TODO: Why aren't we lazy loading the children and siblings?
                $this->_children = $this->_item->getChildren();
                $this->_parent = false;

                if ($this->_item->getParent()) {
                    $this->_parent = $this->_item->getParent();
                }

                $this->_rightsibling = $this->_item->getSibling();
                $this->_leftsibling = $this->_item->getSibling(false);
            }
            else {
                $this->_children = false;
                $this->_parent = false;
            }
        }

        return $this->_item;
    }

    protected function _buildContentOrderBy()
    {
        $app		= JFactory::getApplication('site');
        $db			= $this->getDbo();
        $params		= $this->state->params;
        $itemid		= $app -> input -> getInt('id', 0) . ':' . $app -> input -> getInt('Itemid', 0);
        $orderCol	= $app->getUserStateFromRequest('com_tz_portfolio_plus.category.list.' . $itemid . '.filter_order', 'filter_order', '', 'string');
        $orderDirn	= $app->getUserStateFromRequest('com_tz_portfolio_plus.category.list.' . $itemid . '.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
        $orderby	= ' ';

        if (!in_array($orderCol, $this->filter_fields)) {
            $orderCol = null;
        }

        if (!in_array(strtoupper($orderDirn), array('ASC', 'DESC', ''))) {
            $orderDirn = 'ASC';
        }

        if ($orderCol && $orderDirn) {
            $orderby .= $db->escape($orderCol) . ' ' . $db->escape($orderDirn) . ', ';
        }

        $articleOrderby		= $params->get('orderby_sec', 'rdate');
        $articleOrderDate	= $params->get('order_date');
        $categoryOrderby	= $params->def('orderby_pri', '');
        $secondary			= TZ_Portfolio_PlusHelperQuery::orderbySecondary($articleOrderby, $articleOrderDate) . ', ';
        $primary			= TZ_Portfolio_PlusHelperQuery::orderbyPrimary($categoryOrderby);

        $orderby .= $db->escape($primary) . ' ' . $db->escape($secondary) . ' a.created ';

        return $orderby;
    }

    protected function getStoreId($id = '')
    {
        // Compile the store id.
        if (is_array($this->getState('filter.published'))) {
        	$id .= ':'.implode(',', $this->getState('filter.published'));
        } else {
        	$id .= ':'.$this->getState('filter.published');
        }
        $id .= ':'.$this->getState('filter.access');
        $id .= ':'.$this->getState('filter.featured');
//        $id .= ':'.$this->getState('filter.article_id');
//        $id .= ':'.$this->getState('filter.article_id.include');
//        $id .= ':'.$this->getState('filter.category_id');
//        $id .= ':'.$this->getState('filter.category_id.include');
        $id .= ':'.$this->getState('filter.author_id');
        $id .= ':'.$this->getState('filter.author_id.include');
        $id .= ':'.$this->getState('filter.author_alias');
        $id .= ':'.$this->getState('filter.author_alias.include');
        $id .= ':'.$this->getState('filter.date_filtering');
        $id .= ':'.$this->getState('filter.date_field');
        $id .= ':'.$this->getState('filter.start_date_range');
        $id .= ':'.$this->getState('filter.end_date_range');
        $id .= ':'.$this->getState('filter.relative_date');

        return parent::getStoreId($id);
    }

    public function getPagination(){
        return parent::getPagination();
    }
}