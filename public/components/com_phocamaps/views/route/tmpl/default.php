<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
  defined('_JEXEC') or die('Restricted access');
echo '<div id="phocamapsprintroute">';
$id		= '';
$map	= new PhocaMapsMap();

echo $map->getIconPrintScreen();

//$map->loadAPI();
//$map->loadAPI();

echo $map->startJScData();
echo $map->addAjaxAPI('maps', '3.x', $this->t['params']);
echo $map->createDirection(1);
echo $map->setDirectionFunction();
echo $map->directionInitializeFunctionSpecificMap($this->t['from'], $this->t['to']);
echo $map->directionInitializeFunction();
echo $map->endJScData();
echo $map->loadAPI('', $this->t['lang']);// must be loaded as last

echo '<div id="directionsPanel'.$id.'" ></div>';
echo PhocaMapsHelper::getExtInfo();
echo '</div>';


