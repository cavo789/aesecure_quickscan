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

jimport('joomla.application.component.controllerform');

class TZ_Portfolio_PlusControllerAddon extends JControllerForm
{
    public function __construct($config = array()){
        parent::__construct($config);
    }
    public function display($cachable = false, $urlparams = false)
    {
        parent::display($cachable,$urlparams);
    }

    public function manager(){
        $app   = Factory::getApplication();
        $model = $this->getModel();
        $table = $model->getTable();
        $cid    = array();
        $context = "$this->option.edit.$this->context";
        $this -> input -> set('layout','manager');

        $addon_view     = $this -> input -> getCmd('addon_view');
        $addon_task     = $this -> input -> getCmd('addon_task');
        $addon_layout   = $this -> input -> getCmd('addon_layout');

        $link           = '';
        if($addon_view){
            $link   .= '&addon_view='.$addon_view;
        }
        if($addon_task){
            $link   .= '&addon_task='.$addon_task;
        }
        if($addon_layout){
            $link   .= '&addon_layout='.$addon_layout;
        }

        // Determine the name of the primary key for the data.
        if (empty($key))
        {
            $key = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar))
        {
            $urlVar = $key;
        }

        // Get the previous record id (if any) and the current record id.
        $recordId = (int) (count($cid) ? $cid[0] : $this->input->getInt($urlVar));
        $checkin = property_exists($table, 'checked_out');

        // Access check.
        if (!$this->allowEdit(array($key => $recordId), $key))
        {
            $this->setMessage(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), 'error');

            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend().$link, false
                )
            );

            return false;
        }

        // Attempt to check-out the new record for editing and redirect.
        if ($checkin && !$model->checkout($recordId))
        {
            // Check-out failed, display a notice but allow the user to see the record.
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()), 'error');

            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar).$link, false
                )
            );

            return false;
        }
        else
        {
            // Check-out succeeded, push the new record id into the session.
            $this->holdEditId($context, $recordId);
            $app->setUserState($context . '.data', null);


            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar).$link, false
                )
            );

            return true;
        }
    }

    public function upload()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Access check.
        if (!$this->allowAdd())
        {
            // Set the internal error and also the redirect error.
            $this->setMessage(\JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 'error');

            $this->setRedirect(
                \JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }

        // Redirect to the edit screen.
        $this->setRedirect(
            JRoute::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item.'&layout=upload', false
            )
        );

        return true;
    }

    public function install(){
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Access check.
        if (!$this->allowAdd())
        {
            // Set the internal error and also the redirect error.
            $this->setMessage(\JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 'error');

            $this->setRedirect(
                \JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }

        $model  = $this -> getModel();
        if(!$model -> install()){
            $this -> setMessage($model -> getError(), 'error');
        }else{
            $this -> setMessage(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INSTALL_SUCCESS',
                JText::_('COM_TZ_PORTFOLIO_PLUS_'.strtoupper($this -> view_item))));
        }

        $this -> setRedirect('index.php?option=com_tz_portfolio_plus&view='.$this -> view_item.'&layout=upload');
    }

    public function uninstall(){

        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $eid   = $this->input->get('cid', array(), 'array');
        $model = $this->getModel();

        $eid    = ArrayHelper::toInteger($eid);
        $model->uninstall($eid);
        $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=addons', false));
    }

    public function cancel($key = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $cancel = parent::cancel($key);

        $app    = Factory::getApplication();
        $app -> setUserState($this->option . '.'.$this -> context.'.limitstart', 0);

        if($return = $this -> input -> get('return', null, 'base64')){
            $this -> setRedirect(base64_decode($return));
            return true;
        }

        return $cancel;
    }

    public function save($key = null, $urlVar = null)
    {
        $user   = Factory::getUser();

        $data   = $this->input->get('jform', array(), 'array');

        // Remove the permissions rules data if user isn't allowed to edit them.
        if (!$user->authorise('core.admin', 'com_tz_portfolio_plus.addon')
            && isset($data['params']) && isset($data['params']['rules']))
        {
            unset($data['params']['rules']);
        }

        if (parent::save($key, $urlVar)) {
            if($return = $this->input->get('return', null, 'base64')){
                $task   = $this->getTask();
                $model  = $this->getModel();
                $table  = $model->getTable();

                // Determine the name of the primary key for the data.
                if (empty($key))
                {
                    $key = $table->getKeyName();
                }

                // To avoid data collisions the urlVar may be different from the primary key.
                if (empty($urlVar))
                {
                    $urlVar = $key;
                }

                $recordId = $this->input->getInt($urlVar);

                switch ($task)
                {
                    case 'apply':
                        // Redirect back to the edit screen.
                        $this->setRedirect(
                            JRoute::_(
                                'index.php?option=' . $this->option . '&view=' . $this->view_item
                                . $this->getRedirectToItemAppend($recordId, $urlVar).'&return='.$return, false
                            )
                        );
                        break;
                    case 'save':
                        $this->setRedirect(base64_decode($return));
                        break;
                    default:
                        break;
                }
            }
            return true;
        }
        return false;
    }

    protected function allowAdd($data = array())
    {
        $user = TZ_Portfolio_PlusUser::getUser();
        return ($user->authorise('core.create','com_tz_portfolio_plus.'.$this -> getName()));
    }

    protected function allowEdit($data = array(), $key = 'id')
    {
        $user       = TZ_Portfolio_PlusUser::getUser();
        $recordId   = (int) isset($data[$key]) ? $data[$key] : 0;
        $tblAsset   = JTable::getInstance('Asset','JTable');

        // Return the addon edit options permission
        if($recordId){
            return $user->authorise('core.edit', 'com_tz_portfolio_plus.addon.'.$recordId)
            || $user->authorise('core.admin', 'com_tz_portfolio_plus.addon.'.$recordId)
            || $user->authorise('core.options', 'com_tz_portfolio_plus.addon.'.$recordId);
        }

        // Zero record (id:0), return component edit permission by calling parent controller method
        if (!$recordId)
        {
            return parent::allowEdit($data, $key);
        }

        if($tblAsset -> loadByName('com_tz_portfolio_plus.addon.'.$recordId)) {
            return $user->authorise('core.edit', $this->option . '.addon.'.$recordId);
        }
        return $user->authorise('core.edit', $this->option . '.addon');
    }

    public function edit($key = null, $urlVar = null)
    {
        // Do not cache the response to this, its a redirect, and mod_expires and google chrome browser bugs cache it forever!
        Factory::getApplication()->allowCache(false);

        $model = $this->getModel();
        $table = $model->getTable();
        $cid = $this->input->post->get('cid', array(), 'array');

        // Determine the name of the primary key for the data.
        if (empty($key)) {
            $key = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar)) {
            $urlVar = $key;
        }

        // Get the previous record id (if any) and the current record id.
        $recordId = (int)(count($cid) ? $cid[0] : $this->input->getInt($urlVar));

        // Access check.
        if (!$this->allowEdit(array($key => $recordId), $key)) {
            $this->setMessage(\JText::_('JERROR_ALERTNOAUTHOR'), 'error');

            $this->setRedirect(
                \JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }

        return parent::edit($key, $urlVar);
    }

    public function ajax_install()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $result = null;
        $app    = Factory::getApplication();


        // Access check.
        if (!$this->allowAdd())
        {
            // Set the internal error and also the redirect error.
            $app->enqueueMessage(\JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 'error');
        }else{
            $model  = $this -> getModel();
            if($result = $model -> install()){
                $app -> enqueueMessage(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INSTALL_SUCCESS'
                    , JText::_('COM_TZ_PORTFOLIO_PLUS_'.strtoupper($this -> view_item))));
            }else{
                $this -> setMessage($model -> getError());
                $app -> enqueueMessage($model -> getError(), 'error');
            }
        }

        $message = $this->message;

        $this->setRedirect(
            \JRoute::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item.'&layout=upload', false
            )
        );

        $redirect   = $this -> redirect;

        // Push message queue to session because we will redirect page by Javascript, not $app->redirect().
        // The "application.queue" is only set in redirect() method, so we must manually store it.
        $app->getSession()->set('application.queue', $app->getMessageQueue());


        header('Content-Type: application/json');

        echo new JResponseJson(array('redirect' => $redirect), $message, !$result);

        exit();
    }
}