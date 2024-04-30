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

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class TZ_Portfolio_PlusHelperACL
{
	protected static $cache         = array();

	public static function allowApprove($article = null, $option = 'com_tz_portfolio_plus'){

        $user       = Factory::getUser();

        if($user->authorise('core.approve', $option)){
            if($article && $article -> id && $article -> created_by != $user -> id){
                return true;
            }elseif($article && $article -> id && $article -> created_by == $user -> id
                && $article -> state != 4){
                return true;
            }elseif (!$article || ($article && !$article -> id)){
                return true;
            }
        }

        return false;
    }

}
