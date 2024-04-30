<?php
/*------------------------------------------------------------------------
# plg_extravote - ExtraVote Plugin
# ------------------------------------------------------------------------
# author    Joomla!Vargas
# copyright Copyright (C) 2010 joomla.vargas.co.cr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomla.vargas.co.cr
# Technical Support:  Forum - http://joomla.vargas.co.cr/forum
-------------------------------------------------------------------------*/

// Set flag that this is a parent file
define('_JEXEC', 1);

// No direct access.
defined('_JEXEC') or die;

if (!isset($_SERVER['HTTP_REFERER'])) die();
$refer = $_SERVER['HTTP_REFERER'];
$url_arr = parse_url($refer);

if (isset($url_arr['port']) && $url_arr['port'] != '80') {
	$check = $url_arr['host'] . ":" . $url_arr['port'];
} else {
	$check = $url_arr['host'];
}
if ($_SERVER['HTTP_HOST'] != $check) die();

define( 'DS', DIRECTORY_SEPARATOR );

define('JPATH_BASE', __DIR__.DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'..' );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

jimport('joomla.database.database');
jimport('joomla.database.table');

$app = JFactory::getApplication('site');
$app->initialise();

$user = JFactory::getUser();

JLoader::import('com_tz_portfolio_plus.includes.framework',JPATH_ADMINISTRATOR.'/components');

// Register TZ_Portfolio_PlusPluginHelper class
tzportfolioplusimport('plugin.helper');

$plugin	= TZ_Portfolio_PlusPluginHelper::getPlugin('content', 'vote');

$params = new JRegistry;
$params->loadString($plugin->params);

if ( $params->get('access') == 1 && !$user->get('id') ) {
	echo 'login';
} else {
	$user_rating = $app -> input -> getInt('user_rating');
	$cid = $app -> input -> getInt('cid');
	$db  = JFactory::getDbo();
	if ($user_rating >= 1 && $user_rating <= 5) {
		$currip = $_SERVER['REMOTE_ADDR'];
		$query	= $db -> getQuery(true);
		$query -> select('*');
		$query -> from('#__tz_portfolio_plus_content_rating');
		$query -> where('content_id = '.$cid);
		$db -> setQuery( $query );
		$votesdb = $db->loadObject();
		try{
            if ( !$votesdb ) {
                $query -> clear();
                $query -> insert('#__tz_portfolio_plus_content_rating');
                $query -> columns('content_id, lastip, rating_sum, rating_count');
                $query -> values($cid.','. $db -> quote($currip).','.$user_rating.',1');
                $db->setQuery( $query );
                $db -> execute();
            } else {
                if ($currip != ($votesdb->lastip)) {
                    $query -> clear();
                    $query -> update('#__tz_portfolio_plus_content_rating');
                    $query -> set('rating_count = rating_count + 1')
                        -> set('rating_sum = rating_sum + ' .   $user_rating);
                    $query -> where('content_id = '. $cid);
                    $db -> execute();
                } else {
                    echo 'voted';
                    exit();
                }
            }
		}catch (\InvalidArgumentException $e)
        {
            die($e->getMessage());
        }

		echo 'thanks';
	}
}
