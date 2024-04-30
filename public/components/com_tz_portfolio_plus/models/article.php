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

jimport('joomla.application.component.modelitem');

/**
 * Content Component Article Model.
 */
class TZ_Portfolio_PlusModelArticle extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_tz_portfolio_plus.article';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app -> input -> getInt('id');
		$this->setState('article.id', $pk);

        $this -> setState('article.catid',null);

		$offset = $app -> input -> getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();

		$this->setState('params', $params);

		// TODO: Tune these values based on other permissions.
		$user		= TZ_Portfolio_PlusUser::getUser();
		if ((!$user->authorise('core.edit.state', 'com_tz_portfolio_plus'))
            &&  (!$user->authorise('core.edit', 'com_tz_portfolio_plus'))){
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}

        $this->setState('filter.language', JLanguageMultilang::isEnabled());
	}

    function getItemRelated($pk=null){
        try{
            // Initialise variables.
            $pk = (!empty($pk)) ? $pk : (int) $this->getState('article.id');

            if($pk){
                $_params     = $this -> getState('params');
                $article    = $this -> getItem($pk);
                $params     = $article -> params;

                $limit      = $article -> params -> get('related_limit',5);

                $orderBy    = null;

                switch($params -> get('related_orderby','rdate')){
                    default:
                    case 'rdate':
                        $orderBy    = 'c.created DESC';
                        break;
                    case 'date':
                        $orderBy    = 'c.created ASC';
                        break;
                    case 'hits':
                        $orderBy    = 'c.hits DESC';
                        break;
                    case 'rhits':
                        $orderBy    = 'c.hits ASC';
                        break;
                }

                $db     = JFactory::getDbo();
                $query  = $db -> getQuery(true);
                $query -> select('DISTINCT c.*, cc.id AS catid,CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
                $query -> select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
                $query -> from($db -> quoteName('#__tz_portfolio_plus_content').' AS c');
                $query -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = c.id AND m.main = 1');
                $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_plus_categories').' AS cc ON cc.id = m.catid');

                $query -> where('c.state = 1');
                $query -> where('NOT c.id='.$pk);

                if(!$article -> params -> get('show_related_featured', 1)){
                    $query -> where('c.featured <> 1');
                }elseif($article -> params -> get('show_related_featured', 1) == 2){
                    $query -> where('c.featured = 1');
                }

                if($article -> params -> get('related_article_by', 'tag') == 'tag'){
                    $query -> join('INNER', '#__tz_portfolio_plus_tag_content_map AS tm ON tm.contentid = c.id');
                    $query -> join('INNER', '#__tz_portfolio_plus_tags AS t ON t.id = tm.tagsid');

                    $subquery   = $db -> getQuery(true);

                    $subquery -> select('t2.id');
                    $subquery -> from('#__tz_portfolio_plus_tags AS t2');
                    $subquery -> join('INNER', '#__tz_portfolio_plus_tag_content_map AS tm2 ON tm2.tagsid = t2.id');
                    $subquery -> join('INNER', '#__tz_portfolio_plus_content AS c2 ON c2.id = tm2.contentid');
                    $subquery -> where('c2.id = '. $pk);

                    $query -> where('t.id IN('.(string) $subquery.')');
                }
                else{
                    $query -> where('cc.id='.$article -> catid);
                }


                // Filter by language
                if ($this->getState('filter.language'))
                {
                    $query->where('c.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
                }

                if($orderBy) {
                    $query->order($orderBy);
                }
//                var_dump($query -> dump()); die();

                $db -> setQuery($query,0,$limit);

                return $db -> loadObjectList();
            }
        }catch (Exception $e){
            $this -> setError($e -> getMessage());
            return false;
        }

        return false;
    }

	/**
	 * Method to get article data.
	 *
	 * @param	integer	The id of the article.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function &getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('article.id');


		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {

			try {
				$db = $this->getDbo();
				$query = $db->getQuery(true);

				$query->select($this->getState(
					'item.select', 'a.id, a.asset_id, a.title, a.alias, a.type, a.media, a.introtext, a.fulltext, ' .
					// If badcats is not null, this means that the article is inside an unpublished category
					// In this case, the state is set to 0 to indicate Unpublished (even if the article state is Published)
					'CASE WHEN badcats.id is null THEN a.state ELSE 0 END AS state, ' .
					'a.created, a.created_by, a.created_by_alias, ' .
				// use created if modified is 0
				'CASE WHEN a.modified = 0 THEN a.created ELSE a.modified END as modified, ' .
					'a.modified_by, a.checked_out, a.checked_out_time, a.publish_up, a.publish_down, ' .
					'a.images, a.urls, a.attribs, a.version, a.ordering, ' .
					'a.metakey, a.metadesc, a.access, a.hits, a.metadata, a.featured, a.language, a.xreference'
					)
				);
				$query->from('#__tz_portfolio_plus_content AS a');

                $query -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = a.id AND m.main = 1');

				// Join on category table.
				$query->select('c.id AS catid, c.title AS category_title, c.alias AS category_alias, c.access AS category_access');
				$query->join('LEFT', '#__tz_portfolio_plus_categories AS c on c.id = m.catid');

				// Join on user table.
				$query->select('u.id AS author_id, u.name AS author, u.params AS author_params, u.email AS author_email');
				$query->join('LEFT', '#__users AS u on u.id = a.created_by');

                // Filter by language
                if ($this->getState('filter.language'))
                {
                    $query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
                }

				// Join on contact table
				$subQuery = $db->getQuery(true);
				$subQuery->select('contact.user_id, MAX(contact.id) AS id, contact.language');
				$subQuery->from('#__contact_details AS contact');
				$subQuery->where('contact.published = 1');
				$subQuery->group('contact.user_id, contact.language');
				$query->select('contact.id as contactid' );
				$query->join('LEFT', '(' . $subQuery . ') AS contact ON contact.user_id = a.created_by');

				// Join over the categories to get parent category titles
				$query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
				$query->join('LEFT', '#__tz_portfolio_plus_categories as parent ON parent.id = c.parent_id');

				// Join on voting table
//				$query->select('ROUND(v.rating_sum / v.rating_count, 1) AS rating, v.rating_count as rating_count');
//				$query->join('LEFT', '#__content_rating AS v ON a.id = v.content_id');

				$query->where('a.id = ' . (int) $pk);

				// Filter by start and end dates.
				$nullDate = $db->Quote($db->getNullDate());
				$date = JFactory::getDate();

				$nowDate = $db->Quote($date->toSql());

				$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
				$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

				// Join to check for category published state in parent categories up the tree
				// If all categories are published, badcats.id will be null, and we just use the article state
				$subquery = ' (SELECT cat.id as id FROM #__tz_portfolio_plus_categories AS cat JOIN #__tz_portfolio_plus_categories AS parent ';
				$subquery .= 'ON cat.lft BETWEEN parent.lft AND parent.rgt ';
				$subquery .= 'WHERE parent.extension = ' . $db->quote('com_tz_portfolio_plus');
				$subquery .= ' AND parent.published <= 0 GROUP BY cat.id)';
				$query->join('LEFT OUTER', $subquery . ' AS badcats ON badcats.id = c.id');

				// Filter by published state.
				$published = $this->getState('filter.published');
				$archived = $this->getState('filter.archived');

				if (is_numeric($published)) {
					$query->where('(a.state = ' . (int) $published . ' OR a.state =' . (int) $archived . ')');
				}

				$db->setQuery($query);

				$data = $db->loadObject();

                if (empty($data)) {
                    throw new \Exception(JText::_('COM_TZ_PORTFOLIO_PLUS_ERROR_ARTICLE_NOT_FOUND'), 404);
                }

                // Check for published state if filter set.
                if (((is_numeric($published)) || (is_numeric($archived))) && (($data->state != $published) && ($data->state != $archived))) {
                    throw new \Exception(JText::_('COM_TZ_PORTFOLIO_PLUS_ERROR_ARTICLE_NOT_FOUND'), 404);
                }

                $params = $this->getState('params');

				// Convert parameter fields to objects.
				$registry = new JRegistry;
				$registry->loadString($data->attribs);

				$data->params = clone $params;

                /*** Merge category params to menu params ***/
                $categories = JCategories::getInstance('TZ_Portfolio_Plus');
                if($category   = $categories->get($data -> catid)) {
                    $catParams = new JRegistry($category->params);
                    if($inheritCatid = $catParams -> get('inheritFrom')){
                        if($inheritCategory = $categories -> get($inheritCatid)){
                            $inheritParams  = new JRegistry;
                            $inheritParams -> loadString($inheritCategory -> params);
                            $data -> params -> merge($inheritParams);
                        }
                    }else{
                        $data -> params -> merge($catParams);
                    }
                }

				$data->params->merge($registry);

				$registry = new JRegistry;
				$registry->loadString($data->metadata);
				$data->metadata = $registry;

                $media      = $data -> media;
                $registry   = new JRegistry;
                $registry -> loadString($media);

                $media              = $registry -> toObject();
                $data -> media      = $media;

				// Compute selected asset permissions.
				$user	= JFactory::getUser();

				// Technically guest could edit an article, but lets not check that to improve performance a little.
				if (!$user->get('guest')) {
					$userId	= $user->get('id');
					$asset	= 'com_tz_portfolio_plus.article.'.$data->id;

					// Check general edit permission first.
					if ($user->authorise('core.edit', $asset)) {
						$data->params->set('access-edit', true);
					}
					// Now check if edit.own is available.
					elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
						// Check for a valid user and that they are the owner.
						if ($userId == $data->created_by) {
							$data->params->set('access-edit', true);
						}
					}
				}

				// Compute view access permissions.
				if ($access = $this->getState('filter.access')) {
					// If the access filter has been set, we already know this user can view.
					$data->params->set('access-view', true);
				}
				else {
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$user = JFactory::getUser();
					$groups = $user->getAuthorisedViewLevels();

					if ($data->catid == 0 || $data->category_access === null) {
						$data->params->set('access-view', in_array($data->access, $groups));
					}
					else {
						$data->params->set('access-view', in_array($data->access, $groups) && in_array($data->category_access, $groups));
					}
				}

				$this->_item[$pk] = $data;
			}
			catch (JException $e)
			{
				if ($e->getCode() == 404) {
					// Need to go thru the error handler to allow Redirect to work.
                    throw new \Exception($e->getMessage(), 404);
				}
				else {
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

        $item   = $this -> _item[$pk];
        $author_registry    = new Registry();
        if(isset($item -> author_params) && !empty($item -> author_params)) {
            $author_registry->loadString($item->author_params);
        }
        $author_info    = new stdClass();

        $author_info -> url                                 = $author_registry -> get('tz_portfolio_plus_user_url');
        $author_info -> email                               = $item -> author_email;
        $author_info -> gender                              = $author_registry -> get('tz_portfolio_plus_user_gender');
        $author_info -> avatar                              = $author_registry -> get('tz_portfolio_plus_user_avatar');
        $author_info -> twitter                             = $author_registry -> get('tz_portfolio_plus_user_twitter');
        $author_info -> facebook                            = $author_registry -> get('tz_portfolio_plus_user_facebook');
        $author_info -> instagram                           = $author_registry -> get('tz_portfolio_plus_user_instagram');
        $author_info -> googleplus                          = $author_registry -> get('tz_portfolio_plus_user_googleplus');
        $author_info -> description                         = $author_registry -> get('tz_portfolio_plus_user_description');
        $item -> author_info                                = $author_info;

        $this -> _item[$pk] = $item;

		return $this->_item[$pk];
	}

	/**
	 * Increment the hit counter for the article.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function hit($pk = 0)
	{
            $hitcount = JFactory::getApplication() -> input -> getInt('hitcount', 1);

            if ($hitcount)
            {
                // Initialise variables.
                $pk = (!empty($pk)) ? $pk : (int) $this->getState('article.id');
                $db = $this->getDbo();

                $db->setQuery(
                        'UPDATE #__tz_portfolio_plus_content' .
                        ' SET hits = hits + 1' .
                        ' WHERE id = '.(int) $pk
                );

                try{
                    return $db -> execute();
                }catch (Exception $exception){
                    $this -> setError($exception -> getMessage());
                    return false;
                }
            }

            return true;
	}
}
