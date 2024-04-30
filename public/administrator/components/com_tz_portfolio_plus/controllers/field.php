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
jimport('joomla.application.component.controllerform');

class TZ_Portfolio_PlusControllerField extends JControllerForm
{
    protected function allowAdd($data = array())
    {
        $user = TZ_Portfolio_PlusUser::getUser();
        return ($user->authorise('core.create','com_tz_portfolio_plus.group')
            || count($user->getAuthorisedFieldGroups('core.create')) > 0);
    }

    protected function allowEdit($data = array(), $key = 'id')
    {
        $recordId   = (int) isset($data[$key]) ? $data[$key] : 0;
        $user       = TZ_Portfolio_PlusUser::getUser();

        // Existing record already has an owner, get it
        $record = $this->getModel()->getItem($recordId);

        if (empty($record))
        {
            return false;
        }

        $canEdit	    = $user->authorise('core.edit',		  $this -> option.'.field.'.$recordId)
            && (count($user -> getAuthorisedFieldGroups('core.edit', $record -> groupid)) > 0);
        $canEditOwn	    = $user->authorise('core.edit.own', $this -> option.'.field.'.$recordId)
            && $record->created_by == $user->id
            && (count($user -> getAuthorisedFieldGroups('core.edit.own', $record -> groupid)) > 0);

        // Check edit on the record asset (explicit or inherited)
        if ($canEdit)
        {
            return true;
        }

        // Check edit own on the record asset (explicit or inherited)
        if ($canEditOwn)
        {
            return true;
        }

        // Zero record (id:0), return component edit permission by calling parent controller method
        if (!$recordId)
        {
            return parent::allowEdit($data, $key);
        }

        return false;
    }
}