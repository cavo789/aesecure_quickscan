<?php

/**
 * @package   JD Simple Contact Form
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2021 Joomdev, Inc. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// no direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/helper.php';

$document = JFactory::getDocument();
$document->addStylesheet(JURI::root() . 'media/mod_jdsimplecontactform/assets/css/style.css?v=' . $document->getMediaVersion());
$document->addStylesheet('//cdn.jsdelivr.net/npm/pikaday/css/pikaday.css');

$layout = $params->get('layout', 'default');
// Adding Module Class Suffix.
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');
require JModuleHelper::getLayoutPath('mod_jdsimplecontactform', $layout);
