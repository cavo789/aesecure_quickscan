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

require_once dirname(__FILE__).'/articles.php';

class TZ_Portfolio_PlusControllerFeatured extends TZ_Portfolio_PlusControllerArticles
{
	/**
	 * Removes an item
	 */
	function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app	= Factory::getApplication();
		
		// Initialise variables.
		$user	= Factory::getUser();
		$ids	= $this -> input -> get('cid', array(), 'array');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.delete', 'com_tz_portfolio_plus.article.'.(int) $id))
			{
				// Prune items that you can't delete.
				unset($ids[$i]);
				$app -> enqueueMessage(JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'), 'warning');
			}
		}

		if (empty($ids)) {
			$app -> enqueueMessage(JText::_('JERROR_NO_ITEMS_SELECTED'), 'error');
		}
		else {
			// Get the model.
			$model = $this->getModel();

			// Remove the items.
			if (!$model->featured($ids, 0)) {
				$app -> enqueueMessage($model->getError(), 'error');
			}
		}

		$this->setRedirect('index.php?option=com_tz_portfolio_plus&view=featured');
	}

	/**
	 * Method to publish a list of articles.
	 *
	 * @return	void
	 * @since	1.0
	 */
	function publish()
	{
		parent::publish();

		$this->setRedirect('index.php?option=com_tz_portfolio_plus&view=featured');
	}

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Feature', $prefix = 'TZ_Portfolio_PlusModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}
