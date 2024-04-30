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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

jimport('joomla.application.component.model');
JLoader::import('category',COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'helpers');

/**
 * This models supports retrieving lists of article categories.
 */
class TZ_Portfolio_PlusModelCategories extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_tz_portfolio_plus.categories';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_tz_portfolio_plus';

	private $_parent = null;

	private $_items = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$this->setState('filter.extension', $this->_extension);

		// Get the parent id if defined.
		$parentId = $app -> input -> getInt('id');
		$this->setState('filter.parentId', $parentId);

        $params     = $app -> getParams();
		$this->setState('params', $params);

		$this->setState('filter.published',	1);
		$this->setState('filter.access',	true);
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
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.parentId');

		return parent::getStoreId($id);
	}

	/**
	 * Redefine the function an add some properties to make the styling more easy
	 *
	 * @param	bool	$recursive	True if you want to return children recursively.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 * @since	1.6
	 */
	public function getItems($recursive = false)
	{
        $store = $this->getStoreId();

        if (!isset($this->cache[$store]))
        {
            $app = JFactory::getApplication();
            $menu = $app->getMenu();
            $active = $menu->getActive();
            $params = new Registry;

            if ($active)
            {
                $params->loadString($active->getParams());
            }

            $options = array();
            $options['countItems'] = $params->get('show_cat_num_articles_cat', 1) || !$params->get('show_empty_categories_cat', 0);
            $categories = JCategories::getInstance('TZ_Portfolio_Plus', $options);
            $this->_parent = $categories->get($this->getState('filter.parentId', 'root'));

            if (is_object($this->_parent))
            {
                $this->cache[$store] = $this->_parent->getChildren($recursive);
            }
            else
            {
                $this->cache[$store] = false;
            }
        }

        return $this->cache[$store];
	}

	public function getParent()
	{
		if (!is_object($this->_parent)) {
			$this->getItems();
		}

		return $this->_parent;
	}
}
