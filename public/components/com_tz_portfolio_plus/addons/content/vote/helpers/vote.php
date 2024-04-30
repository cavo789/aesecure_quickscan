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

class TZ_Portfolio_PlusAddOnContentVoteHelper{

    protected static $cache = array();

    public static function getVoteByArticleId($artId){
        if(!$artId){
            return false;
        }

        $storeId    = md5(__METHOD__.':'.(int) $artId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);

        $query -> select('*');
        $query -> from('#__tz_portfolio_plus_content_rating');
        $query -> where('content_id = '. (int) $artId);
        $db -> setQuery($query);

        if($item = $db -> loadObject()) {
            self::$cache[$storeId]  = $item;
            return $item;
        }
//        return self::$cache[$storeId];
    }
}