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

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

JLoader::import('com_tz_portfolio_plus.libraries.helper.modulehelper', JPATH_ADMINISTRATOR.'/components');

$doc    = JFactory::getDocument();

if($params -> get('enable_bootstrap', 0)  && $params -> get('enable_bootstrap_js', 1)) {
    if( $params -> get('bootstrapversion', 3) == 4) {
        $doc->addScript(TZ_Portfolio_PlusUri::base(true) . '/vendor/bootstrap/js/bootstrap.min.js',
            array('version' => 'auto'));
        $doc->addScript(TZ_Portfolio_PlusUri::base(true) . '/vendor/bootstrap/js/bootstrap.bundle.min.js',
            array('version' => 'auto'));
    }else{
        $doc -> addScript(TZ_Portfolio_PlusUri::base(true).'/bootstrap/js/bootstrap.min.js',
            array('version' => 'auto'));
    }
}

$list               = modTzPortfolioTagsHelper::getList($params);
$articleCount       = modTzPortfolioTagsHelper::getArticleTotal();
$menuActive         = $params -> get('menu_active', 'auto');
$tagAllLink         = JRoute::_('index.php?option=com_tz_portfolio_plus&view=portfolio'
    .(($menuActive != 'auto')?'&Itemid='.$menuActive:''));
$moduleclass_sfx    = htmlspecialchars($params->get('moduleclass_sfx', ''));

require TZ_Portfolio_PlusModuleHelper::getTZLayoutPath($module, $params->get('layout', 'default'));

?>
