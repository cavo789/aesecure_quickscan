<?php
/*------------------------------------------------------------------------

# JVisualContent Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

// Require defines.php file
JLoader::import('com_tz_portfolio_plus.includes.defines',JPATH_ADMINISTRATOR.'/components');

// Require tzportfolioplus file with some functions php
JLoader::import('com_tz_portfolio_plus.libraries.tzportfolioplus',JPATH_ADMINISTRATOR.'/components');

// Require uri files
if(!class_exists('TZ_Portfolio_PlusUri')){
    JLoader::import('com_tz_portfolio_plus.libraries.uri',JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components');
}

JHtml::addIncludePath(COM_TZ_PORTFOLIO_PLUS_LIBRARIES.'/html');

tzportfolioplusimport('helper.toolbar');
tzportfolioplusimport('helper.acl');

tzportfolioplusimport('user.user');

// Register class TZ_Portfolio_PlusPluginHelper from folder libraries of TZ Portfolio Plus
tzportfolioplusimport('plugin.helper');

// Register class TZ_Portfolio_PlusExtraField from folder libraries of TZ Portfolio Plus
tzportfolioplusimport('fields.extrafield');

// Register class TZ_Portfolio_PlusDatabase from folder libraries of TZ Portfolio Plus
tzportfolioplusimport('database.database');

// Register class TZ_Portfolio_PlusModelDialogBase from libraries folder.
tzportfolioplusimport('model.dialog');

// Register class TZ_Portfolio_PlusModelRejectBase from libraries folder.
tzportfolioplusimport('model.reject');

/* Register class TZ_Portfolio_PlusModelAdmin from libraries folder. */
tzportfolioplusimport('model.adminlegacy');

// Register class TZ_Portfolio_PlusControllerRejectBase from libraries folder.
tzportfolioplusimport('controller.reject');


// Register class TZ_Portfolio_PlusControllerArticleBase from libraries folder.
tzportfolioplusimport('controller.article');

