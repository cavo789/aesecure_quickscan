<?php defined('_JEXEC') or die;
/*
 * @package     mod_article_slideshow
 * @copyright   © 2020 Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Helper\ModuleHelper;
JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');

// Include the news functions only once
JLoader::register('ModArticleSlideshowHelper', __DIR__ . '/helper.php');

$vars = [
    'slider_class', 'item_class', 'item_style','dotnav', 'slidenav','img_popup','velocity', 'autoplay', 'autoplay_interval', 'finite', 'pause_on_hover', 'link_target','index', 'center', 'sets','grid', 'grid_divider', 'cols','tooltip','circle','img_popup', 'cols_all', 'cols_s', 'cols_m', 'cols_l', 'cols_xl','items','dotnav', 'slidenav', 'animation', 'velocity', 'autoplay', 'finite', 'pause_on_hover', 'index','content_margin', 'ratio', 'min_height', 'max_height','max_height','imgorvideo','items','content_bg','content_color','content_pos','content_text_align','content_width'
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
$sw_params[] = 'finite:' . $finite;
$sw_params[] = 'pause-on-hover:' . $pause_on_hover;


$sw_params = $sw_params ? '="' . implode(';', $sw_params) . '"' : '';



$list            = ModArticleSlideshowHelper::getList($params);
$tagsList        = ModArticleSlideshowHelper::getTagsList($list); 

require JModuleHelper::getLayoutPath('mod_article_slideshow', $params->get('layout', 'default'));
