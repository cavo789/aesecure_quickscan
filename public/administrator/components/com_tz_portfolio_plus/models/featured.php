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

use Joomla\Utilities\ArrayHelper;

require_once dirname(__FILE__) . '/articles.php';

/**
 * About Page Model
 */
class TZ_Portfolio_PlusModelFeatured extends TZ_Portfolio_PlusModelArticles
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
				'catid', 'a.catid', 'category_title',
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
				'fp.ordering',
                'groupname','g.name'
			);
		}

		parent::__construct($config);
	}

	/**
	 * @param	boolean	True to join selected foreign information
	 *
	 * @return	string
	 */
	function getListQuery($resolveFKs = true)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.alias, a.checked_out, a.checked_out_time, a.state, a.access, a.created, a.hits,' .
				'a.language, a.publish_up, a.publish_down,a.created_by, a.featured'
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

		// Join over the content table.
		$query->select('fp.ordering');
		$query->join('INNER', '#__tz_portfolio_plus_content_featured_map AS fp ON fp.content_id = a.id');

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

        // Filter by fields group
        if($this -> state -> get('filter.group')!=0)
            $query -> where('g.id ='.$this -> getState('filter.group'));

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

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.state = ' . (int) $published);
		} elseif ($published === '') {
			$query->where('(a.state = 0 OR a.state = 1)');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('a.title LIKE '.$search.' OR a.alias LIKE '.$search);
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = '.$db->quote($language));
		}

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.title')).' '.$db->escape($this->getState('list.direction', 'ASC')));

		//echo nl2br(str_replace('#__','jos_',(string)$query));
		return $query;
	}
}
