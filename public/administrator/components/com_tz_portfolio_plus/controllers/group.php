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

class TZ_Portfolio_PlusControllerGroup extends JControllerForm
{
    protected $view_list = 'groups';

    public function delete(&$pks){
        // Initialise variables.
        $dispatcher = JDispatcher::getInstance();
        $pks = (array) $pks;
        $table = $this->getTable();

        // Iterate the items to delete each one.
        foreach ($pks as $i => $pk)
        {

            if ($table->load($pk))
            {

                if ($this->canDelete($table))
                {

                    if (!$table->delete($pk))
                    {
                        $this->setMessage($table->getError(), 'error');
                        return false;
                    }

                }
                else
                {

                    // Prune items that you can't change.
                    unset($pks[$i]);
                    $error = $this->getError();
                    if ($error)
                    {
                        Factory::getApplication() -> enqueueMessage($error, 'error');
                        return false;
                    }
                    else
                    {
                        Factory::getApplication() -> enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), 'error');
                        return false;
                    }
                }

            }
            else
            {
                $this->setMessage($table->getError(), 'error');
                return false;
            }
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }

    protected function allowAdd($data = array())
    {
        $user = TZ_Portfolio_PlusUser::getUser();
        return ($user->authorise('core.create','com_tz_portfolio_plus.group'));
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

        // Check edit on the record asset (explicit or inherited)
        if ($user->authorise('core.edit', $this -> option.'.group.' . $recordId))
        {
            return true;
        }

        // Check edit own on the record asset (explicit or inherited)
        if ($user->authorise('core.edit.own', $this -> option.'.group.' . $recordId))
        {
            // Existing record already has an owner, get it
            $record = $this->getModel()->getItem($recordId);

            if (empty($record))
            {
                return false;
            }

            // Grant if current user is owner of the record
            return $user->id == $record->created_by;
        }

        return false;
    }
}