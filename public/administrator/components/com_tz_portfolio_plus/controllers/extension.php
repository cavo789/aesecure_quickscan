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
JLoader::import('com_tz_portfolio_plus.controllers.addon', JPATH_ADMINISTRATOR.'/components');

class TZ_Portfolio_PlusControllerExtension extends TZ_Portfolio_PlusControllerAddon
{
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
                'index.php?option=' . $this->option . '&view=' . $this->view_item . '&layout=upload', false
            )
        );

        return true;
    }

    public function install(){
//        // Check for request forgeries.
//        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
//
//        // Access check.
//        if (!$this->allowAdd())
//        {
//            // Set the internal error and also the redirect error.
//            $this->setMessage(\JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 'error');
//
//            $this->setRedirect(
//                \JRoute::_(
//                    'index.php?option=' . $this->option . '&view=' . $this->view_list
//                    . $this->getRedirectToListAppend(), false
//                )
//            );
//
//            return false;
//        }
//
//        $model  = $this -> getModel();
//        if(!$model -> install()){
//            $this -> setMessage($model -> getError(), 'error');
//        }else{
//            $this -> setMessage(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INSTALL_SUCCESS',
//                JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE')));
//        }
//
//        $this -> setRedirect('index.php?option=com_tz_portfolio_plus&view='.$this -> view_item.'&layout=upload');
        $result = parent::install();
        if($result){
            $this -> setMessage(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INSTALL_SUCCESS',
                    JText::_('COM_TZ_PORTFOLIO_PLUS_EXTENSION')));
        }
        return true;
    }

    public function uninstall(){

        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $eid    = $this->input->get('cid', array(), 'array');
        $model  = $this->getModel('Template');
        $eid    = ArrayHelper::toInteger($eid);

        $model->uninstall($eid);
        $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=templates', false));
    }

    protected function allowAdd($data = array())
    {
        $user = TZ_Portfolio_PlusUser::getUser();
        return ($user->authorise('core.create','com_tz_portfolio_plus.template'));
    }

    protected function allowEdit($data = array(), $key = 'id')
    {
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
        $user = Factory::getUser();

        // Zero record (id:0), return component edit permission by calling parent controller method
        if (!$recordId)
        {
            return parent::allowEdit($data, $key);
        }

        // Existing record already has an owner, get it
        $record = $this->getModel()->getItem($recordId);

        // Check edit on the record asset (explicit or inherited)
        if(isset($record -> asset_id) && $record -> asset_id){
            return $user->authorise('core.edit', $this -> option.'.tag.' . $recordId);
        }else{
            return $user->authorise('core.edit', $this -> option.'.tag');
        }

        return false;
    }

//    public function ajax_install()
//    {
//
//        die('TEST');
////        // Check for request forgeries.
////        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
////
////        $result = null;
////        $app    = Factory::getApplication();
////
////
////        // Access check.
////        if (!$this->allowAdd())
////        {
////            // Set the internal error and also the redirect error.
////            $app->enqueueMessage(\JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 'error');
////        }else{
////            $model  = $this -> getModel();
////            if($result = $model -> install()){
////                $app -> enqueueMessage(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INSTALL_SUCCESS'
////                    , JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE')));
////            }else{
////                $this -> setMessage($model -> getError());
////                $app -> enqueueMessage($model -> getError(), 'error');
////            }
////        }
////
////        $message = $this->message;
////
////        $this->setRedirect(
////            \JRoute::_(
////                'index.php?option=' . $this->option . '&view=' . $this->view_item.'&layout=upload', false
////            )
////        );
////
////        $redirect   = $this -> redirect;
////
////        // Push message queue to session because we will redirect page by Javascript, not $app->redirect().
////        // The "application.queue" is only set in redirect() method, so we must manually store it.
////        $app->getSession()->set('application.queue', $app->getMessageQueue());
////
////        header('Content-Type: application/json');
////
////        echo new JResponseJson(array('redirect' => $redirect), $message, !$result);
////
////        exit();
//    }
}