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

class TZ_Portfolio_PlusControllerFields extends JControllerAdmin
{
    protected $input        = null;
    protected $text_prefix  = 'COM_TZ_PORTFOLIO_PLUS_FIELDS';

    public function __construct($config = array())
    {
        $this -> input  = Factory::getApplication()->input;

        parent::__construct($config);

        $this->registerTask('listview', 'updatestate');
        $this->registerTask('unlistview', 'updatestate');
        $this->registerTask('detailview', 'updatestate');
        $this->registerTask('undetailview', 'updatestate');
        $this->registerTask('advsearch', 'updatestate');
        $this->registerTask('unadvsearch', 'updatestate');
    }
    public function getModel($name = 'Field', $prefix = 'TZ_Portfolio_PlusModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function delete()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get items to remove from the request.
        $cid = $this -> input -> get('cid', array(), 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            Factory::getApplication() -> enqueueMessage(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), 'error');
			
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            $cid    = ArrayHelper::toInteger($cid);

            // Remove the items.
            if ($model->delete($cid))
            {
                $this->setMessage(JText::plural('COM_TZ_PORTFOLIO_PLUS_FIELDS_COUNT_DELETED', count($cid)));
            }
            else
            {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
    }

    public function updateState(){
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $cid    = Factory::getApplication()->input->get('cid', array(), 'array');
        $data = array('listview' => 1, 'unlistview' => 0,
            'detailview' => 1, 'undetailview' => 0,
            'advsearch' => 1, 'unadvsearch' => 0);
        $task   = $this->getTask();
        $value  = ArrayHelper::getValue($data, $task, 0, 'int');

        if (empty($cid))
        {
            JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            $cid    = ArrayHelper::toInteger($cid);

            // Publish the items.
            try
            {
                $model->updateState($cid, $value, $task);
                $errors = $model->getErrors();

                if ($value == 1)
                {
                    if ($errors)
                    {
                        $app = Factory::getApplication();
                        $app->enqueueMessage(JText::plural($this->text_prefix . '_N_ITEMS_FAILED_PUBLISHING', count($cid)), 'error');
                    }
                    else
                    {
                        $ntext = $this->text_prefix . '_N_ITEMS_UPDATESTATE';
                    }
                }
                elseif ($value == 0)
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_UPDATESTATE';
                }

                $this->setMessage(JText::plural($ntext, count($cid)));
            }
            catch (Exception $e)
            {
                $this->setMessage($e->getMessage(), 'error');
            }
        }

        $extension = $this->input->get('extension');
        $extensionURL = ($extension) ? '&extension=' . $extension : '';
        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));

    }

    public function saveOrderAjax()
    {
        // Get the input
        $pks    = $this->input->post->get('cid', array(), 'array');
        $order  = $this->input->post->get('order', array(), 'array');
        $group  = $this->input->post->get('filter_group');

        // Sanitize the input
        $pks    = ArrayHelper::toInteger($pks);
        $order  = ArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveOrderAjax($pks, $order, $group);

        if ($return)
        {
            echo "1";
        }

        // Close the application
        Factory::getApplication()->close();
    }
}