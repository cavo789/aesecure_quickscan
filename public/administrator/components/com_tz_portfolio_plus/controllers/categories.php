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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

jimport('joomla.application.component.controlleradmin');

/**
 * The Categories List Controller.
 */
class TZ_Portfolio_PlusControllerCategories extends JControllerAdmin
{
    protected $input    = null;

    public function __construct($config = array()){
        $this -> input  = Factory::getApplication() -> input;
        parent::__construct($config);
    }
	/**
	 * Proxy for getModel
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 *
	 * @return	object	The model.
	 * @since	1.6
	 */
	function getModel($name = 'Category', $prefix = 'TZ_Portfolio_PlusModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Rebuild the nested set tree.
	 *
	 * @return	bool	False on failure or error, true on success.
	 * @since	1.6
	 */
	public function rebuild()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$extension = $this -> input -> getCmd('extension','com_tz_portfolio_plus');
		$this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=categories&extension='.$extension, false));

		// Initialise variables.
		$model = $this->getModel();

		if ($model->rebuild()) {
			// Rebuild succeeded.
			$this->setMessage(JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_REBUILD_SUCCESS'));
			return true;
		} else {
			// Rebuild failed.
			$this->setMessage(JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_REBUILD_FAILURE'));
			return false;
		}
	}

	/**
	 * Save the manual order inputs from the categories list page.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function saveorder()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the arrays from the Request
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this -> input -> getString('original_order_values'));

		// Make sure something has changed
		if (!($order === $originalOrder)) {
			parent::saveorder();
		} else {
			// Nothing to reorder
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
			return true;
		}
	}

    /**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return	void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the arrays from the Request
		$pks   = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		// Make sure something has changed
		if (!($order === $originalOrder)) {
			// Get the model
			$model = $this->getModel();
			// Save the ordering
			$return = $model->saveorder($pks, $order);
			if ($return)
			{
				echo "1";
			}
		}
		// Close the application
		Factory::getApplication()->close();

	}

}
