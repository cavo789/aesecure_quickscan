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

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

jimport('joomla.application.component.controllerform');

class TZ_Portfolio_PlusControllerTag extends JControllerForm
{
    public function cancel($key = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = Factory::getApplication();
        $model = $this->getModel();
        $table = $model->getTable();
        $context = "$this->option.edit.$this->context";

        if (empty($key))
        {
            $key = $table->getKeyName();
        }

        $recordId = $this -> input -> getInt($key);

        // Clean the session data and redirect.
        $this->releaseEditId($context, $recordId);
        $app->setUserState($context . '.data', null);

        $this->setRedirect(
            JRoute::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
            )
        );

        return true;
    }

    public function save($key = null, $urlVar = null){

        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app   = Factory::getApplication();
        $lang  = $app -> getLanguage();
        $model  = $this -> getModel();
        $table = $model->getTable();
        $context = "$this->option.edit.$this->context";
        $task = $this->getTask();

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

        $recordId = $this -> input -> getInt($urlVar);
        $data  = $this -> input -> post -> get('jform', array(), 'array');

        $context = "$this->option.edit.$this->context";
        $task = $this->getTask();

        // The save2copy task needs to be handled slightly differently.
        if ($task == 'save2copy')
        {
            // Reset the ID and then treat the request as for Apply.
            $data[$key] = 0;
            $task = 'apply';
        }

        // Attempt to save the data.
        if(!$model -> save($data)){
            // Redirect back to the edit screen.
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'error');
            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                        . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
            );

            return false;
        }

        // Redirect the user and adjust session state based on the chosen task.
        switch ($task)
        {
            case 'apply':
                // Set the record data in the session.
                $recordId = $model->getState($this->context . '.id');
                $app->setUserState($context . '.data', null);

                // Redirect back to the edit screen.
                $this->setRedirect(
                    JRoute::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_item
                            . $this->getRedirectToItemAppend($recordId, $urlVar), false
                    ),
                    JText::_('COM_TZ_PORTFOLIO_PLUS_TAGS_SUCCESS')
                );
                break;

            case 'save2new':
                // Clear the record id and data from the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState($context . '.data', null);

                // Redirect back to the edit screen.
                $this->setRedirect(
                    JRoute::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_item
                            . $this->getRedirectToItemAppend(null, $urlVar), false
                    ),
                    JText::_('COM_TZ_PORTFOLIO_PLUS_TAGS_SUCCESS')
                );
                break;

            default:
                // Clear the record id and data from the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState($context . '.data', null);

                // Redirect to the list screen.
                $this->setRedirect(
                    JRoute::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_list
                            . $this->getRedirectToListAppend(), false
                    ),
                    JText::_('COM_TZ_PORTFOLIO_PLUS_TAGS_SUCCESS')
                );
                break;
        }
        return true;
    }

    protected function allowAdd($data = array())
    {
        $user = TZ_Portfolio_PlusUser::getUser();
        return ($user->authorise('core.create','com_tz_portfolio_plus.tag'));
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
}