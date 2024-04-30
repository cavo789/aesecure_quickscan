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

jimport('joomla.application.component.controllerform');
jimport('joomla.filesystem.file');

class TZ_Portfolio_PlusControllerRejectBase extends JControllerForm
{
    protected function allowReject($data = array())
    {
        $user = Factory::getUser();

        return $user->authorise('core.approve', $this->option);
    }

    public function getModel($name = 'Reject', $prefix = 'TZ_Portfolio_PlusModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    protected function getUrlRedirect(){

        $url = 'index.php?option=' . $this->option . '&view='.$this -> view_list
            . $this->getRedirectToListAppend();

        // Check if there is a return value
        $return = $this->input->get('return', null, 'base64');

        if (!is_null($return) && \JUri::isInternal(base64_decode($return)))
        {
            $url = base64_decode($return);
        }

        return $url;
    }

    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app        = Factory::getApplication();

        // Require Reject model from front-end
        $model      = $this->getModel();
        $table      = $model->getTable();
        $cid        = $this->input->get('cid', array(), 'array');
        $data       = $this->input->post->get('jform', array(), 'array');
        $context    = "$this->option.edit.$this->context";

        $url = $this -> getUrlRedirect();

        if (empty($cid))
        {
            \JLog::add(\JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), \JLog::WARNING, 'jerror');
        }else {
            // Make sure the item ids are integers
            $cid = ArrayHelper::toInteger($cid);

            // Access check.
            if (!$this->allowReject($data)) {

                $this->setError(\JText::_('COM_TZ_PORTFOLIO_PLUS_ERROR_NOT_REJECT_ARTICLE'));
                $this->setMessage($this->getError(), 'error');

                $this->setRedirect(
                    \JRoute::_($url, false)
                );
                $this->redirect();
            }

            // Validate the posted data.
            // Sometimes the form needs some posted data, such as for plugins and modules.
            $form = $model->getForm($data, false);

            if (!$form) {
                $app->enqueueMessage($model->getError(), 'error');

                return false;
            }

            // Send an object which can be modified through the plugin event
            $objData = (object)$data;
            $app->triggerEvent(
                'onContentNormaliseRequestData',
                array($this->option . '.' . $this->context, $objData, $form)
            );
            $data = (array)$objData;

            // Test whether the data is valid.
            $validData = $model->validate($form, $data);

            // Attempt to save the data.
            if (!$model->save($validData, $cid)) {
                // Save the data in the session.
                $app->setUserState($context . '.data', $validData);

                // Redirect back to the edit screen.
                $this->setError(\JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
                $this->setMessage($this->getError(), 'error');

                $this->setRedirect(
                    \JRoute::_(
                        $url, false
                    )
                );

                return false;
            }
        }
//            $url = 'index.php?option=' . $this->option . '&view=' . $this->view_list
//                . $this->getRedirectToListAppend();
//
//            // Check if there is a return value
//            $return = $this->input->get('return', null, 'base64');
//
//            if (!is_null($return) && \JUri::isInternal(base64_decode($return)))
//            {
//                $url = base64_decode($return);
//            }

        $this->setMessage(JText::plural('COM_TZ_PORTFOLIO_PLUS_N_ITEMS_REJECTED', count($cid)));

            // Redirect to the list screen.
            $this->setRedirect(\JRoute::_($url, false));

            // Invoke the postSave method to allow for the child class to access the model.
            $this->postSaveHook($model, $validData);

        return true;
    }
}
