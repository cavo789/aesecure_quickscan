<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2016 tzportfolio.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Family Website: http://www.templaza.com

# Technical Support:  Forum - http://tzportfolio.com/Forum

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

JLoader::import('com_tz_portfolio_plus.libraries.helper.modulehelper', JPATH_ADMINISTRATOR.'/components');

$lang               = JFactory::getApplication() -> getLanguage();
$upper_limit        = $lang->getUpperLimitSearchWord();
$button             = $params->get('button', 0);
$imagebutton        = $params->get('imagebutton', 0);
$button_pos         = $params->get('button_pos', 'left');
$button_text        = htmlspecialchars($params->get('button_text', JText::_('MOD_TZ_PORTFOLIO_PLUS_FILTER_SEARCHBUTTON_TEXT')), ENT_COMPAT, 'UTF-8');
$width              = (int) $params->get('width');
$maxlength          = $upper_limit;
$text               = htmlspecialchars($params->get('text', JText::_('MOD_TZ_PORTFOLIO_PLUS_FILTER_SEARCHBOX_TEXT')), ENT_COMPAT, 'UTF-8');
$label              = htmlspecialchars($params->get('label', JText::_('MOD_TZ_PORTFOLIO_PLUS_FILTER_LABEL_TEXT')), ENT_COMPAT, 'UTF-8');
$set_Itemid         = (int) $params->get('set_itemid', 0);
$moduleclass_sfx    = htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_COMPAT, 'UTF-8');

$mitemid            = $set_Itemid > 0 ? $set_Itemid : $app->input->get('Itemid');
$advfilter          = modTZ_Portfolio_PlusFilterHelper::getAdvFilterFields($params);
$categoryOptions    = modTZ_Portfolio_PlusFilterHelper::getCategoriesOptions($params);

$doc    = JFactory::getDocument();

if($params -> get('enable_bootstrap', 0) && $params -> get('enable_bootstrap_js', 1)) {
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
if($params -> get('enable_bootstrap', 0) && $params -> get('bootstrapversion', 3) == 3) {
    $doc -> addStyleSheet(TZ_Portfolio_PlusUri::base(true).'/bootstrap/css/bootstrap.min.css');
}

require TZ_Portfolio_PlusModuleHelper::getTZLayoutPath($module, $params->get('layout', 'default'));