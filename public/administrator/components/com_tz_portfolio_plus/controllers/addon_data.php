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
defined('JPATH_PLATFORM') or die;

tzportfolioplusimport('controller.form');

class TZ_Portfolio_PlusControllerAddon_Data extends TZ_Portfolio_Plus_AddOnControllerForm
{
    protected function allowEdit($data = array(), $key = 'id')
    {
        $user       = TZ_Portfolio_PlusUser::getUser();
        $asset      = JTable::getInstance('Asset','JTable');
        $recordId   = (int) isset($data[$key]) ? $data[$key] : 0;

        if($recordId && $asset -> loadByName($this->option . '.addon_data.' . $recordId)) {
            return $user->authorise('tzportfolioplus.edit', $this->option . '.addon_data.' . $recordId);
        }

        return parent::allowEdit($data);
    }
}