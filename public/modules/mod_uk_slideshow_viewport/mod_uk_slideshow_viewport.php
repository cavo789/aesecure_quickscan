<?php defined('_JEXEC') or die;
/*
 * @package     uk_slideshow_viewport
 * @copyright   © 2020 Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */
use Joomla\CMS\Helper\ModuleHelper;

$vars = [
    'dotnav', 'slidenav', 'animation', 'velocity', 'autoplay', 'video_loop','video_control','video_mute','autoplay_interval', 'finite', 'pause_on_hover', 'index', 'ratio', 'viewport', 'max_height','max_height','imgorvideo','items'
];

foreach ($vars as $var) {
    $$var = $params->get($var);
}

$sw_params = [];
$sw_params[] = 'animation:' . $animation;
$sw_params[] = 'velocity:' .  $velocity;
if ($autoplay) {
    $sw_params[] = 'autoplay:true';
    if ((int)$autoplay_interval != 7000 && (int)$autoplay_interval > 0) $sw_params[] = 'autoplay-interval:' . (int)$autoplay_interval;
}
if ($finite) $sw_params[] = 'finite:true';
if ($viewport) $sw_params[] = 'ratio: false';

$sw_params[] = 'pause-on-hover:' . $pause_on_hover;

$sw_params = $sw_params ? '="' . implode(';', $sw_params) . '"' : '';

if ($items) {
    require(ModuleHelper::getLayoutPath('mod_uk_slideshow_viewport', $params->get('layout', 'default')));
}