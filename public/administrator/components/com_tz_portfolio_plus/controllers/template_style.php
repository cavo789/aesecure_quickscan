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
use Joomla\CMS\Filesystem\File;

jimport('joomla.application.component.controllerform');

class TZ_Portfolio_PlusControllerTemplate_Style extends JControllerForm
{
    protected $view_list = 'template_styles';

    public function loadPreset($key = null, $urlVar = null){

        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app        = Factory::getApplication();
        $model      = $this->getModel();
        $table      = $model->getTable();
        $data       = $this->input->post->get('jform', array(), 'array');
        $context    = "$this->option.loadpreset.$this->context";

        // Determine the name of the primary key for the data.
        if (empty($key))
        {
            $key    = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar))
        {
            $urlVar = $key;
        }

        $recordId = $this->input->getInt($urlVar);

        // Validate the posted data.
        // Sometimes the form needs some posted data, such as for plugins and modules.
        $form = $model->getForm($data, false);

        if (!$form)
        {
            $app->enqueueMessage($model->getError(), 'error');

            return false;
        }

        // Test whether the data is valid.
        $validData = $model->validate($form, $data);

        // Check for validation errors.
        if ($validData === false)
        {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
            {
                if ($errors[$i] instanceof Exception)
                {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                }
                else
                {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            // Save the data in the session.
            $app->setUserState($context . '.data', $data);

            // Redirect back to the edit screen.
            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
            );

            return false;
        }

        if (!isset($validData['tags']))
        {
            $validData['tags'] = null;
        }

        // Attempt to save the data.
        if (!$model->loadPreset($validData))
        {
            // Save the data in the session.
            $app->setUserState($context . '.data', $validData);

            // Redirect back to the edit screen.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
            );

            return false;
        }

        $this->setRedirect(
            JRoute::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item
                . $this->getRedirectToItemAppend($recordId, $urlVar), false
            ), 'Configuration successfully loaded and saved', 'message'
        );
    }

    public function removePreset($key = null, $urlVar = null){

        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app        = Factory::getApplication();
        $model      = $this->getModel();
        $table      = $model->getTable();
        $data       = $this->input->post->get('jform', array(), 'array');
        $context    = "$this->option.loadpreset.$this->context";

        // Determine the name of the primary key for the data.
        if (empty($key))
        {
            $key    = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar))
        {
            $urlVar = $key;
        }

        $recordId = $this->input->getInt($urlVar);

        // Validate the posted data.
        // Sometimes the form needs some posted data, such as for plugins and modules.
        $form = $model->getForm($data, false);

        if (!$form)
        {
            $app->enqueueMessage($model->getError(), 'error');

            return false;
        }

        // Test whether the data is valid.
        $validData = $model->validate($form, $data);

        // Check for validation errors.
        if ($validData === false)
        {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
            {
                if ($errors[$i] instanceof Exception)
                {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                }
                else
                {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            // Save the data in the session.
            $app->setUserState($context . '.data', $data);

            // Redirect back to the edit screen.
            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
            );

            return false;
        }

        if (!isset($validData['tags']))
        {
            $validData['tags'] = null;
        }

        // Attempt to save the data.
        if (!$model->removePreset($validData))
        {
            // Save the data in the session.
            $app->setUserState($context . '.data', $validData);

            // Redirect back to the edit screen.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
            );

            return false;
        }

        $this->setRedirect(
            JRoute::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item
                . $this->getRedirectToItemAppend($recordId, $urlVar), false
            ), $validData['preset'].'.json configuration file deleted successfully.', 'message'
        );
    }

    public function uploadpreset(){

        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app        = Factory::getApplication();
        $model      = $this->getModel();
        $table      = $model->getTable();
        $data       = $this->input->post->get('jform', array(), 'array');
        $filePreset = $this -> input -> files -> get('Filepreset', array(), 'array');

        // Determine the name of the primary key for the data.
        if (empty($key))
        {
            $key    = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar))
        {
            $urlVar = $key;
        }

        $recordId = $this->input->getInt($urlVar);

        if(!count($filePreset)){
            $this -> setMessage(JText::_('COM_TZ_PORTFOLIO_PLUS_FILE_NOT_FOUND'), 'error');
            $this -> setRedirect(JRoute::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item
                . $this->getRedirectToItemAppend($recordId, $urlVar), false
            ));
        }

        $folderPath         = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.'/'.$data['template'].'/config';
        $mediaHelper        = new JHelperMedia;
        $uploadMaxFileSize  = $mediaHelper->toBytes(ini_get('upload_max_filesize'));
        $fileErrors         = array();

        foreach ($filePreset as $file){

            $fileError  = false;
            $fileType   = File::getExt($file["name"]);

            //-- Check image information --//
            // Check MIME Type
            if ($file['type'] != 'application/json') {
                $fileError  = true;
                $app->enqueueMessage(JText::sprintf('JLIB_MEDIA_ERROR_WARNINVALID_MIMETYPE', $file['type']), 'notice');
            }

            // Check file type
            if ($fileType != 'json') {
                $fileError  = true;
                $app->enqueueMessage(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_ERROR_WARNFILETYPE', $fileType, $file['name']), 'notice');
            }

            if (($file['error'] == 1)
                || ($uploadMaxFileSize > 0 && $file['size'] > $uploadMaxFileSize))
            {
                $fileError  = true;
                $app->enqueueMessage(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_ERROR_WARNFILETOOLARGE', $file['name']), 'notice');
            }
            //-- End check image information --//

            if($fileError){
                $fileErrors[]   = $fileError;
                continue;
            }

            if(!File::upload($file['tmp_name'], $folderPath.'/'.$file['name'])){
                $fileError  = true;
                $app->enqueueMessage(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_ERROR_UNABLE_TO_UPLOAD_FILE', $file['name']), 'notice');
            }
            if($fileError){
                $fileErrors[]   = $fileError;
                continue;
            }
        }

        if(!count($fileErrors)){
            $this -> setMessage(JText::_('COM_TZ_PORTFOLIO_PLUS_UPLOAD_PRESET_SUCCESS'));
        }

        // Redirect back to the edit screen.
        $this->setRedirect(
            JRoute::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item
                . $this->getRedirectToItemAppend($recordId, $urlVar), false
            )
        );
    }

    public function downloadpreset(){

        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app        = Factory::getApplication();
        $model      = $this->getModel();
        $table      = $model->getTable();
        $data       = $this->input->post->get('jform', array(), 'array');
        $context    = "$this->option.loadpreset.$this->context";

        // Determine the name of the primary key for the data.
        if (empty($key))
        {
            $key    = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar))
        {
            $urlVar = $key;
        }

        $recordId = $this->input->getInt($urlVar);

        // Validate the posted data.
        // Sometimes the form needs some posted data, such as for plugins and modules.
        $form = $model->getForm($data, false);

        if (!$form)
        {
            $app->enqueueMessage($model->getError(), 'error');

            return false;
        }

        // Test whether the data is valid.
        $validData = $model->validate($form, $data);

        // Check for validation errors.
        if ($validData === false)
        {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
            {
                if ($errors[$i] instanceof Exception)
                {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                }
                else
                {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            // Save the data in the session.
            $app->setUserState($context . '.data', $data);

            // Redirect back to the edit screen.
            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
            );

            return false;
        }

        $file   = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.'/'.$validData['template'].'/config/'.$validData['preset'].'.json';

        if(!File::exists($file)){
            $this -> setMessage(JText::_('COM_TZ_PORTFOLIO_PLUS_FILE_NOT_FOUND'), 'error');
            // Redirect back to the edit screen.
            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
            );

            return false;
        }


        $app -> clearHeaders();
        $app -> setHeader('Pragma', 'public', true);
        $app -> setHeader('Expires', '0', true);
        $app -> setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $app -> setHeader('Content-Type', 'application/json', true);
        $app -> setHeader('Content-Disposition', 'attachment; filename=' .basename($file) . ';', true);
        $app -> setHeader('Content-Transfer-Encoding', 'binary', true);
        $app -> setHeader('Content-Length', filesize($file), true);


        $app -> sendHeaders();

        echo @file_get_contents($file);

        $app -> close();
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
            return $user->authorise('core.edit', $this -> option.'.style.' . $recordId);
        }else{
            return $user->authorise('core.edit', $this -> option.'.style');
        }

        return false;
    }

}