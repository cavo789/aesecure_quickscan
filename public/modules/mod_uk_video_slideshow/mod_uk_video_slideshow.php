<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_video_slider
 * @copyright   © 2020 Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */
use Joomla\CMS\Helper\ModuleHelper;

$vars = [
    'dotnav', 'slidenav', 'link','link_text','animation', 'content_pos', 'content_overlay','velocity', 'autoplay', 'autoplay_interval', 'finite', 'pause_on_hover', 'index', 'autoplay','ratio', 'video_loop','video_control','video_mute','min_height', 'max_height',
    'items'
];

foreach ($vars as $var) {
    $$var = $params->get($var);
}


$sw_params = [];
if (!$min_height && !$max_height && $ratio != '') $sw_params[] = 'ratio:' . $ratio;
if ($min_height) $sw_params[] = 'min-height:' . (int)$min_height;
if ($max_height) $sw_params[] = 'max-height:' . (int)$max_height;
$sw_params[] = 'animation:' . $animation;
if ((int)$velocity > 1) $sw_params[] = 'velocity:' . (int)$velocity;
if ($autoplay) {
    $sw_params[] = 'autoplay:true';
    if ((int)$autoplay_interval != 7000 && (int)$autoplay_interval > 0) $sw_params[] = 'autoplay-interval:' . (int)$autoplay_interval;
}
if ($finite) $sw_params[] = 'finite:true';
if ($pause_on_hover) $sw_params[] = 'pause-on-hover:true';
$sw_params = $sw_params ? '="' . implode(';', $sw_params) . '"' : '';

if ($items) {
    require(ModuleHelper::getLayoutPath('mod_uk_video_slideshow', $params->get('layout', 'default')));
}
