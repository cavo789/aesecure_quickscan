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

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.controlleradmin');

/**
 * Articles list controller class.
 */
class TZ_Portfolio_PlusControllerArticles extends JControllerAdmin
{
    protected $input    = null;

    protected $text_prefix  = 'COM_TZ_PORTFOLIO_PLUS_ARTICLE';

	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	ContentControllerArticles
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		
        Factory::getApplication() -> getLanguage() -> load('com_content');
		// Articles default form can come from the articles or featured view.
		// Adjust the redirect view on the value of 'view' in the request.
		if ($this -> input -> getCmd('view') == 'featured') {
			$this->view_list = 'featured';
		}

		$this->registerTask('unfeatured',	'featured');

        $this->registerTask('priorityup', 'repriority');
        $this->registerTask('prioritydown', 'repriority');

        $this->registerTask('approve', 'publish');
	}

    protected function allowApprove($data = array())
    {
        $user = Factory::getUser();

        return $user->authorise('core.approve', $this->option);
    }

    /**
	 * Method to publish a list of items
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function publish()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app	= Factory::getApplication();
		// Get items to publish from the request.
		$cid 	= $this -> input -> get('cid', array(), 'array');
		$data 	= array('publish' => 1, 'approve' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
		$task 	= $this->getTask();
		$value 	= ArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			$app -> enqueueMessage(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), 'error');
		}
		else
		{
//
//            if($task == 'approve' && !$this -> allowApprove()){
//
//                $url = 'index.php?option=' . $this->option . '&view='.$this -> view_list;
//
//                // Check if there is a return value
//                $return = $this->input->get('return', null, 'base64');
//
//                if (!is_null($return) && \JUri::isInternal(base64_decode($return)))
//                {
//                    $url = base64_decode($return);
//                }
//
//                $this->setError(\JText::_('COM_TZ_PORTFOLIO_PLUS_ERROR_NOT_APPROVE_ARTICLE'));
//                $this->setMessage($this->getError(), 'error');
//
//                $this->setRedirect(
//                    \JRoute::_($url, false)
//                );
//                $this->redirect();
//            }

			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			$cid    = ArrayHelper::toInteger($cid);

			// Publish the items.
			if (!$model->publish($cid, $value))
			{
				$app -> enqueueMessage($model->getError(), 'error');
			}
			else
			{
				if ($value == 1)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
					if($task == 'approve'){
                        $ntext = $this->text_prefix . '_N_ITEMS_APPROVED';
                    }
				}
				elseif ($value == 0)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
				}
				elseif ($value == 2)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
				}
				else
				{
					$ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
				}
				$this->setMessage(JText::plural($ntext, count($cid)));
			}
		}
		$extension = $this -> input -> getCmd('extension');
		$extensionURL = ($extension) ? '&extension=' . $this -> input -> getCmd('extension') : '';
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
	}

	/**
	 * Method to toggle the featured setting of a list of articles.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function featured()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= Factory::getUser();
		$ids	= $this -> input -> get('cid', array(), 'array');
		$values	= array('featured' => 1, 'unfeatured' => 0);
		$task	= $this->getTask();
		$value	= ArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_tz_portfolio_plus.article.'.(int) $id)) {
				// Prune items that you can't change.
				unset($ids[$i]);
				Factory::getApplication() -> enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 'warning');
			}
		}

		if (empty($ids)) {
			Factory::getApplication() -> enqueueMessage(JText::_('JERROR_NO_ITEMS_SELECTED'), 'error');
		}
		else {
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->featured($ids, $value)) {
				Factory::getApplication() -> enqueueMessage($model->getError(), 'error');
			}
		}

		$this->setRedirect('index.php?option=com_tz_portfolio_plus&view=articles');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.6
	 */
	public function getModel($name = 'Article', $prefix = 'TZ_Portfolio_PlusModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

    public function saveOrderAjax()
    {
        $pks = $this->input->post->get('cid', array(), 'array');
        $order = $this->input->post->get('order', array(), 'array');

        // Sanitize the input
        $pks    = ArrayHelper::toInteger($pks);
        $order  = ArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return)
        {
            echo "1";
        }

        // Close the application
        Factory::getApplication()->close();
    }

    public function savepriority()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get the input
        $pks = $this->input->post->get('cid', array(), 'array');
        $order = $this->input->post->get('priority', array(), 'array');

        // Sanitize the input
        ArrayHelper::toInteger($pks);
        ArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->savepriority($pks, $order);

        if ($return === false)
        {
            // Reorder failed
            $message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');
            return false;
        }
        else
        {
            // Reorder succeeded.
            $this->setMessage(JText::_('COM_TZ_PORTFOLIO_PLUS_SUCCESS_PRIORITY_SAVED'));
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
            return true;
        }
    }

    public function repriority()
    {
        // Check for request forgeries.
        \JSession::checkToken() or jexit(\JText::_('JINVALID_TOKEN'));

        $ids = $this->input->post->get('cid', array(), 'array');
        $inc = $this->getTask() === 'priorityup' ? -1 : 1;

        $model = $this->getModel();
        $return = $model->repriority($ids, $inc);

        if ($return === false)
        {
            // Reorder failed.
            $message = \JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
            $this->setRedirect(\JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');

            return false;
        }
        else
        {
            // Reorder succeeded.
            $message = \JText::_('COM_TZ_PORTFOLIO_PLUS_SUCCESS_PRIORITY_SAVED');
            $this->setRedirect(\JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message);

            return true;
        }
    }
}
