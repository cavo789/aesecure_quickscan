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

use Joomla\CMS\Filesystem\File;

jimport('joomla.filesystem.file');

class TZ_Portfolio_PlusFrontHelper{

    protected static $cache = array();

    public static function getImageURLBySize($url, $size = 'o'){
        if(!$url){
            return false;
        }
        $newUrl     = $url;
        if($size) {
            $newUrlExt  = File::getExt($url);
            $newUrl     = str_replace('.' . $newUrlExt, '_' . $size . '.' . $newUrlExt, $newUrl);
        }

        return $newUrl;

    }

    public static function scriptExists($funcRegex, $flags = 0){
        $storeId    = __METHOD__;
        $storeId   .= ':'.$funcRegex;
        $storeId   .= ':'.$flags;
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

//        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
//            return false;
////            var_dump(\Joomla\CMS\Factory::getApplication() -> getDocument() -> getWebAssetManager() -> getAssets('text/javascript')); die();
//        }else {
            $doc = JFactory::getDocument();
//        }

        if(isset($doc -> _script) && count($doc -> _script) && isset($doc -> _script['text/javascript'])){
            $script = $doc -> _script['text/javascript'];
            if(is_array($script)){
                foreach($script as $sstr){
                    if(preg_match($funcRegex, $sstr, $match, $flags)){
                        return true;
                    }
                }
            }else{
                if(preg_match($funcRegex, $script, $match, $flags)){
                    return true;
                }
            }
        }
        return false;
    }
}