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
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of article records.
 */
class TZ_Portfolio_PlusModelArticles extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'm.catid', 'category_title',
				'type', 'a.type',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
                'priority', 'a.priority',
                'groupname','g.name'
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

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = 'a.id', $direction = 'desc')
	{

        // List state information.
        parent::populateState($ordering, $direction);

		// Initialise variables.
		$app    = Factory::getApplication();
		$input  = $app -> input;

//        $this-> context = 'com_tz_portfolio_plus.articles';
		// Adjust the context to support modal layouts.
		if ($layout = $input -> get('layout')) {
			$this->context .= '.'.$layout;
		}

        $group  = $this -> getUserStateFromRequest($this -> context.'.group','filter_group',0,'int');
        $this -> setState('filter.group',$group);

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$level = $this->getUserStateFromRequest($this->context.'.filter.level', 'filter_level', 0, 'int');
		$this->setState('filter.level', $level);

		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

        $formSubmited = $app->input->post->get('form_submited');

        $access         = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
        $authorId       = $this->getUserStateFromRequest($this->context . '.filter.author_id', 'filter_author_id');
        $categoryId     = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
        $categoryIdSec  = $this->getUserStateFromRequest($this->context . '.filter.category_id_sec', 'filter_category_id_sec');
        $mediatype	    = $this -> getUserStateFromRequest($this -> context.'.type','filter_type');

        if ($formSubmited)
        {
            $mediatype = $app->input->post->get('type');
            $this -> setState('filter.type',$mediatype);

            $access = $app->input->post->get('access');
            $this->setState('filter.access', $access);

            $authorId = $app->input->post->get('author_id');
            $this->setState('filter.author_id', $authorId);

            $categoryId = $app->input->post->get('category_id');
            $this->setState('filter.category_id', $categoryId);

            $categoryIdSec = $app->input->post->get('category_id_sec');
            $this->setState('filter.category_id_sec', $categoryIdSec);
        }

        // Force a language
        $forcedLanguage = $app->input->get('forcedLanguage');

        if (!empty($forcedLanguage))
        {
            $this->setState('filter.language', $forcedLanguage);
            $this->setState('filter.forcedLanguage', $forcedLanguage);
        }
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
        $id .= ':' . serialize($this->getState('filter.access'));
		$id	.= ':'.serialize($this->getState('filter.published'));
        $id .= ':' . serialize($this->getState('filter.category_id'));
        $id .= ':' . serialize($this->getState('filter.author_id'));
        $id .= ':' . serialize($this->getState('filter.type'));
		$id	.= ':'.$this->getState('filter.group');
		$id	.= ':'.$this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user	= Factory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.alias, a.checked_out, a.checked_out_time' .
				', a.state, a.status, a.access, a.created, a.created_by, a.ordering, a.featured, a.language, a.hits' .
				', a.publish_up, a.publish_down, a.priority'
			)
		);

		$query -> select('a.type');


		$query->from('#__tz_portfolio_plus_content AS a');

        // Join over fieldgroups
        $query -> select('g.name AS groupname,g.id AS groupid');
        $query -> join('LEFT','#__tz_portfolio_plus_fieldgroups AS g ON a.groupid=g.id');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		$query -> select('m.catid');
		$query -> join('LEFT', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = a.id');

		// Join over the categories.
		$query->select('m.catid, c.title AS category_title');
		$query->join('LEFT', '#__tz_portfolio_plus_categories AS c ON c.id = m.catid');
		$query->where('m.main = 1');

		// Join over the users for the author.
		$query->select('ua.name AS author_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');

		// Join over the content rejected.
        $query -> select('cj.id AS rejected_id, cj.message AS rejected_message');
        $query -> join('LEFT', '#__tz_portfolio_plus_content_rejected AS cj ON a.id = cj.content_id');

		// Join over the associations.
		if (JLanguageAssociations::isEnabled())
		{
			$query->select('COUNT(asso2.id)>1 as association')
				->join('LEFT', '#__associations AS asso ON asso.id = a.id AND asso.context=' . $db->quote('com_tz_portfolio_plus.article.item'))
				->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key')
				->group('a.id');
		}

        // Filter by access level.
        $access = $this->getState('filter.access');
        if (is_numeric($access))
        {
            $query->where('a.access = ' . (int) $access);
        }
        elseif (is_array($access))
        {
            $access = ArrayHelper::toInteger($access);
            $access = implode(',', $access);
            $query->where('a.access IN (' . $access . ')');
        }

        // Filter by media type
        if($type  = $this->getState('filter.type')){
            if (is_string($type))
            {
                $query -> where('a.type = ' . $db -> quote($type));
            }
            elseif (is_array($type))
            {
                foreach($type as $i => $t) {
                    $type[$i]  = 'a.type = '.$db -> quote($t);
                }
                $query -> andWhere($type);
            }
        }

        // Filter by fields group
        if($group_id = $this -> state -> get('filter.group')) {
            $query->where('g.id =' .$group_id);
        }

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
		    $groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if(is_array($published)){
            $query->where('a.state IN('.implode(',', $published).')');
        }elseif (is_numeric($published)) {
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '') {
			$query->where('(a.state = 0 OR a.state = 1)');
		}

		// Filter by a single or group of main categories.
		$baselevel  = 1;
		$categoryId = $this->getState('filter.category_id');
		$catWhere   = array();
		if (is_numeric($categoryId)) {
			$cat_tbl = JTable::getInstance('Category', 'TZ_Portfolio_PlusTable');
			$cat_tbl->load($categoryId);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int) $cat_tbl->level;
			$query->where('c.lft >= '.(int) $lft);
			$query->where('c.rgt <= '.(int) $rgt);
		}
		elseif (is_array($categoryId)) {
			ArrayHelper::toInteger($categoryId);

			$categoryId = implode(',', $categoryId);
            $catWhere[] = 'm.catid IN('.$categoryId.')';
		}

        // Filter by a single or group of secondary categories.
        $categoryIdSec = $this->getState('filter.category_id_sec');
		if (is_array($categoryIdSec)) {
			ArrayHelper::toInteger($categoryIdSec);
            $categoryIdSec = implode(',', $categoryIdSec);
            $catWhere[]   = 'sm.catid IN('.$categoryIdSec.')';

            $query -> join('LEFT', '#__tz_portfolio_plus_content_category_map AS sm ON sm.contentid = a.id AND sm.main = 0');
            $query->join('LEFT', '#__tz_portfolio_plus_categories AS sc ON sc.id = sm.catid');
		}

		if(count($catWhere)){
		    $query -> where('('.implode(' OR ', $catWhere).')');
        }

		// Filter on the level.
		if ($level = $this->getState('filter.level')) {
			$query->where('c.level <= '.((int) $level + (int) $baselevel - 1));
		}

        // Filter by author
        $authorId = $this->getState('filter.author_id');

        if (is_numeric($authorId))
        {
            $type = $this->getState('filter.author_id.include', true) ? '= ' : '<>';
            $query->where('a.created_by ' . $type . (int) $authorId);
        }
        elseif (is_array($authorId))
        {
            $authorId = ArrayHelper::toInteger($authorId);
            $authorId = implode(',', $authorId);
            $query->where('a.created_by IN (' . $authorId . ')');
        }

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			elseif (stripos($search, 'author:') === 0) {
				$search = $db->Quote('%'.$db->escape(substr($search, 7), true).'%');
				$query->where('(ua.name LIKE '.$search.' OR ua.username LIKE '.$search.')');
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = '.$db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.id');
		$orderDirn	= $this->state->get('list.direction', 'desc');

		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}

	/**
	 * Build a list of authors
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	public function getAuthors() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Construct the query
		$query->select('u.id AS value, u.name AS text');
		$query->from('#__users AS u');
		$query->join('INNER', '#__tz_portfolio_plus_content AS c ON c.created_by = u.id');
		$query->group('u.id, u.name');
		$query->order('u.name');

		// Setup the query
		$db->setQuery($query->__toString());

		// Return the result
		return $db->loadObjectList();
	}

    public function getGroupQuery($catid){
        // Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

        $query -> select('g.*');
        $query -> from('#__tz_portfolio_plus_fieldgroups AS g');
        $query -> join('LEFT','#__tz_portfolio_plus_categories AS c ON c.groupid = g.id');

        $query -> where('c.catid ='.$catid);
        $query -> order($this -> state -> get('list.groupname','g.name'));
        $query -> group('g.id');

        return $this -> _getList($query);
    }

    function checkGroups($contentid){
        // Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

        $query -> select('xc.*');
        $query -> from('#__tz_portfolio_plus_content AS c');
        $query -> join('LEFT','#__tz_portfolio_plus_field_content_map AS xc ON c.id = xc.contentid');

        $query -> where('c.id ='.$contentid);

        return $this -> _getList($query);
    }

    public function getFilterForm($data = array(), $loadData = true)
    {
//        $user   = Factory::getUser();
        $form   = parent::getFilterForm($data, $loadData);

        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){

            $field  = $form -> getFieldXml('fullordering', 'list');
            $field -> addChild('option', 'JFEATURED_ASC') -> addAttribute('value', 'a.featured ASC');
            $field -> addChild('option', 'JFEATURED_DESC') -> addAttribute('value', 'a.featured DESC');
        }

//        if(!$user -> authorise('core.approve', 'com_tz_portfolio_plus')){
//            $filterDefault  = $form -> getFieldAttribute('published', 'filter','','filter');
//            $filterDefault  = explode(',', $filterDefault);
//
//            if($key = array_search(3, $filterDefault)){
//                unset($filterDefault[$key]);
//            }
//            $form -> setFieldAttribute('published', 'filter',
//                implode(',', $filterDefault), 'filter');
//        }
        return $form;
    }

    /**
	 * Method to get a list of articles.
	 * Overridden to add a check for access levels.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 * @since	1.6.1
	 */
	public function getItems()
	{
		$items	= parent::getItems();
		$app	= Factory::getApplication();
        // Get fields group
        $data   = array();

		if ($app->isClient('site')) {
			$user	= Factory::getUser();
			$groups	= $user->getAuthorisedViewLevels();

			for ($x = 0, $count = count($items); $x < $count; $x++) {
				//Check the access level. Remove articles the user shouldn't see
				if (!in_array($items[$x]->access, $groups)) {
					unset($items[$x]);
				}
			}
		}

        if($items){
			$texts		= array();
			$values		= array();
			TZ_Portfolio_PlusPluginHelper::importPlugin('mediatype');
			$results	= Factory::getApplication() -> triggerEvent('onAddMediaType');
			if(count($results)) {
				$texts = ArrayHelper::getColumn($results, 'text');
				$values = ArrayHelper::getColumn($results, 'value');
			}
            foreach($items as &$item){
				$categories			= TZ_Portfolio_PlusHelperCategories::getCategoriesByArticleId($item -> id, 0);
				$item -> categories	= $categories;

                if(isset($item -> type) && in_array($item -> type, $values)){
					$index			= array_search($item -> type, $values);
					$item -> type	= $texts[$index];
                }else{
					$item -> type  = JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_NONE_MEDIA');
				}
            }
        }
		return $items;
	}
}
