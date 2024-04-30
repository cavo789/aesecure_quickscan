<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_counter
 * @copyright   © 2020 Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Helper\ModuleHelper;


$vars = [
    'slider_class', 'item_class', 'item_style','dotnav', 'slidenav',
    'velocity', 'autoplay', 'autoplay_interval', 'finite', 'pause_on_hover', 'index', 'center', 'sets',
    'grid', 'grid_divider', 'cols','date','tooltip','circle','img_popup', 'cols_all', 'cols_s', 'cols_m', 'cols_l', 'cols_xl',
    'items'
];

foreach ($vars as $var) {
    $$var = $params->get($var);
}


$sw_params = [];
if ((int)$velocity > 1) $sw_params[] = 'velocity:' . (int)$velocity;
if ($autoplay) {
    $sw_params[] = 'autoplay:true';
    if ((int)$autoplay_interval != 7000 && (int)$autoplay_interval > 0) $sw_params[] = 'autoplay_interval:' . (int)$autoplay_interval;
}
if ($finite) $sw_params[] = 'finite:true';
if ($pause_on_hover) $sw_params[] = 'pause-on-hover:true';
if ((int)$index > 0) $sw_params[] = 'index:' . (int)$index;
if ((int)$center > 0) $sw_params[] = 'center:true';
if ((int)$sets > 0) $sw_params[] = 'sets:true';
$sw_params = $sw_params ? '="' . implode(';', $sw_params) . '"' : '';

$classes = [];
if ($grid != '') {
    $classes[] = $grid;
    if ((int)$grid_divider) $classes[] = 'uk-grid-divider';
}
if ((int)$cols) {
    $classes[] = $cols_all;
    $classes[] = $cols_s;
    $classes[] = $cols_m;
    $classes[] = $cols_l;
    $classes[] = $cols_xl;
}
$classes = $classes ? ' ' . implode(' ', $classes) : '';

if ($items) {
    require(ModuleHelper::getLayoutPath('mod_uk_dotnav', $params->get('layout', 'default')));
}

