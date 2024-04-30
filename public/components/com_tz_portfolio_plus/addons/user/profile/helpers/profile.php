<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class PlgTZ_Portfolio_PlusUserProfileHelper{
    
    protected static $cache = array();
    
    public static function getAuthorAbout($authorId){
        if(!$authorId){
            return false;
        }
        
        $storeId    = __METHOD__;
        $storeId    .= serialize($authorId);
        $storeId    = md5($storeId);
        
        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $_author = JFactory::getUser($authorId);

        if(!$_author){
            return false;
        }

        $author                 = new stdClass();
        $author -> id           = $_author -> id;
        $author -> name         = $_author -> name;
        $author -> email        = $_author -> email;
        $author -> url          = $_author -> getParam('tz_portfolio_plus_user_url');
        $author -> gender       = $_author -> getParam('tz_portfolio_plus_user_gender');
        $author -> avatar       = $_author -> getParam('tz_portfolio_plus_user_avatar');
        $author -> twitter      = $_author -> getParam('tz_portfolio_plus_user_twitter');
        $author -> facebook     = $_author -> getParam('tz_portfolio_plus_user_facebook');
        $author -> instagram    = $_author -> getParam('tz_portfolio_plus_user_instagram');
        $author -> googleplus   = $_author -> getParam('tz_portfolio_plus_user_googleplus');
        $author -> description  = $_author -> getParam('tz_portfolio_plus_user_description');

        self::$cache[$storeId]  = $author;
        return $author;
    }
}