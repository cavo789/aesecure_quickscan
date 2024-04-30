<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2015 templaza.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

class PlgTZ_Portfolio_PlusUserProfileController extends TZ_Portfolio_Plus_AddOnControllerLegacy{
    protected $authorId;

    public function __construct($config = array())
    {
        parent::__construct($config);

        if(isset($config['authorId'])){
            $this -> authorId          = $config['authorId'];
        }
    }

    public function getModel($name = '', $prefix = '', $config = array())
    {
        JLoader::import('user.profile.helpers.profile', COM_TZ_PORTFOLIO_PLUS_ADDON_PATH);
        $model  = parent::getModel($name, $prefix, $config);

        if($model && $this -> authorId){
            $model -> set('authorId', $this -> authorId);
        }

        return $model;
    }
}