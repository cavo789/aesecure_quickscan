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

jimport('joomla.application.categories');

use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

/**
 * Content Component Category Tree.
 */
class TZ_Portfolio_PlusCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__tz_portfolio_plus_content';
		$options['extension'] = 'com_tz_portfolio_plus';
		parent::__construct($options);
	}

	protected function _load($id)
	{
		$db = TZ_Portfolio_PlusDatabase::getDbo();
		$user = JFactory::getUser();
		$extension = $this->_extension;

		// Record that has this $id has been checked
		$this->_checkedCategories[$id] = true;

		$query = $db->getQuery(true);

		// Right join with c for category
		$query->select('c.id, c.asset_id, c.access, c.images, c.alias, c.checked_out, c.checked_out_time,
			c.created_time, c.created_user_id, c.description, c.extension, c.hits, c.language, c.level,
			c.lft, c.metadata, c.metadesc, c.metakey, c.modified_time, c.note, c.params, c.parent_id,
			c.path, c.published, c.rgt, c.title, c.modified_user_id, c.version');
		$case_when = ' CASE WHEN ';
		$case_when .= $query->charLength('c.alias', '!=', '0');
		$case_when .= ' THEN ';
		$c_id = $query->castAsChar('c.id');
		$case_when .= $query->concatenate(array($c_id, 'c.alias'), ':');
		$case_when .= ' ELSE ';
		$case_when .= $c_id . ' END as slug';
		$query->select($case_when)
			->from('#__tz_portfolio_plus_categories as c')
			->where('(c.extension=' . $db->quote($extension) . ' OR c.extension=' . $db->quote('system') . ')');

		if ($this->_options['access'])
		{
			$query->where('c.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
		}

		if ($this->_options['published'] == 1)
		{
			$query->where('c.published = 1');
		}

//        // Filter by ids
//        if (isset($this -> _options['filter.id']) && ($filterIds = $this -> _options['filter.id'])
//            && is_array($filterIds) && count($filterIds)){
////            $query -> where('c.id IN('.implode(',', $filterIds).')');
//        }

		$query->order('c.lft ');

		// Note: s for selected id
		if ($id != 'root')
		{
			// Get the selected category
			$query->join('LEFT', '#__tz_portfolio_plus_categories AS s ON (s.lft <= c.lft AND s.rgt >= c.rgt) OR (s.lft > c.lft AND s.rgt < c.rgt)')
				->where('s.id=' . (int) $id);
		}

		$subQuery = ' (SELECT cat.id as id FROM #__tz_portfolio_plus_categories AS cat JOIN #__tz_portfolio_plus_categories AS parent ' .
			'ON cat.lft BETWEEN parent.lft AND parent.rgt WHERE parent.extension = ' . $db->quote($extension) .
			' AND parent.published != 1 GROUP BY cat.id) ';
		$query->join('LEFT', $subQuery . 'AS badcats ON badcats.id = c.id')
			->where('badcats.id is null');

		// Note: i for item
		if ($this->_options['countItems'] == 1)
		{
			$queryjoin 	= $db -> quoteName('#__tz_portfolio_plus_content_category_map').' AS m ON m.'
					.$db->quoteName($this->_field).'=c.id';
			$queryjoin 	.= ' LEFT JOIN '.$db->quoteName($this->_table) . ' AS i ON m.contentid = i.id';

			if ($this->_options['published'] == 1)
			{
				$queryjoin .= ' AND i.' . $this->_statefield . ' = 1';
			}

			if ($this->_options['currentlang'] !== 0)
			{
				$queryjoin .= ' AND (i.language = ' . $db->quote('*') . ' OR i.language = ' . $db->quote($this->_options['currentlang']) . ')';
			}

			$query->join('LEFT', $queryjoin);
			$query->select('COUNT(i.' . $db->quoteName($this->_key) . ') AS numitems');
		}

		// Group by
		$query->group(
			'c.id, c.asset_id, c.access, c.alias, c.checked_out, c.checked_out_time,
			 c.created_time, c.created_user_id, c.description, c.extension, c.hits, c.language, c.level,
			 c.lft, c.metadata, c.metadesc, c.metakey, c.modified_time, c.note, c.params, c.parent_id,
			 c.path, c.published, c.rgt, c.title, c.modified_user_id, c.version'
		);

		// Get the results
		$db->setQuery($query);
		$results = $db->loadObjectList('id');
		$childrenLoaded = false;

		if (count($results))
		{
		    $filterIds  = array();
		    if(isset($this -> _options['filter.id'])){
                $filterIds = $this -> _options['filter.id'];
                $filterIds  = array_filter($filterIds);
            }

			// Foreach categories
			foreach ($results as $i => $result)
			{
				// Deal with root category
				if ($result->id == 1)
				{
					$result->id = 'root';
				}

                // Filter by ids
                if ($result->id != 'root' && count($filterIds)){
                    if(!in_array($result -> id, $filterIds)) {
                        continue;
                    }else{
                        // Set parent_id is 1(root) if parent_id not in filter ids
                        if(!in_array($result -> parent_id, $filterIds)){
                            $result -> parent_id    = 1;
                        }

                    }
                }

				// Deal with parent_id
				if ($result->parent_id == 1)
				{
					$result->parent_id = 'root';
				}

				// Create the node
				if (!isset($this->_nodes[$result->id]))
				{
					// Create the JCategoryNode and add to _nodes
					$this->_nodes[$result->id] = new JCategoryNode($result, $this);

					// If this is not root and if the current node's parent is in the list or the current node parent is 0
					if ($result->id != 'root' && (isset($this->_nodes[$result->parent_id]) || $result->parent_id == 1))
					{
//					    var_dump($result -> id);
						// Compute relationship between node and its parent - set the parent in the _nodes field
						$this->_nodes[$result->id]->setParent($this->_nodes[$result->parent_id]);
					}

					// If the node's parent id is not in the _nodes list and the node is not root (doesn't have parent_id == 0),
					// then remove the node from the list
					if (!(isset($this->_nodes[$result->parent_id]) || $result->parent_id == 0))
					{
						unset($this->_nodes[$result->id]);
						continue;
					}

					if ($result->id == $id || $childrenLoaded)
					{
						$this->_nodes[$result->id]->setAllLoaded();
						$childrenLoaded = true;
					}
				}
				elseif ($result->id == $id || $childrenLoaded)
				{
					// Create the JCategoryNode
					$this->_nodes[$result->id] = new JCategoryNode($result, $this);

					if ($result->id != 'root' && (isset($this->_nodes[$result->parent_id]) || $result->parent_id))
					{
//                        if (count($filterIds) && !in_array($result->parent_id, $filterIds))
//                        {
//                            $this->_nodes[$result->id]->setParent($this->_nodes['root']);
//                        }else {

                            // Compute relationship between node and its parent
                            $this->_nodes[$result->id]->setParent($this->_nodes[$result->parent_id]);
//                        }
					}

					// If the node's parent id is not in the _nodes list and the node is not root (doesn't have parent_id == 0),
					// then remove the node from the list
					if (!(isset($this->_nodes[$result->parent_id]) || $result->parent_id == 0))
					{
						unset($this->_nodes[$result->id]);
						continue;
					}

					if ($result->id == $id || $childrenLoaded)
					{
						$this->_nodes[$result->id]->setAllLoaded();
						$childrenLoaded = true;
					}
				}
			}
		}
		else
		{
			$this->_nodes[$id] = null;
		}

//		if($result -> id == 6){
//		    var_dump($result->parent_id);
//		    var_dump($this->_nodes[$result->parent_id]);
////		    var_dump($this->_nodes[$id]);
//        }
	}

	public function getFilter(){
//	    var_dump($this->_nodes); die('getFilter');
    }
}
