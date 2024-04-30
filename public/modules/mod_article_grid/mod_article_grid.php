<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_grid
 * @copyright   © 2020 Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Helper\ModuleHelper;
JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');

$vars = [
    'grid_class', 'item_class','img_popup','item_style','margin_class', 'first_column_class', 'height_match', 'height_match_class',
    'grid', 'grid_divider', 'grid_center','link_popup','show_tags','link_target', 'cols', 'cols_all', 'cols_s', 'cols_m', 'cols_l', 'cols_xl', 'masonry',
    'items'
];

foreach ($vars as $var) {
    $$var = $params->get($var);
}


$grid_params = [];
if ($margin_class) $grid_params[] = 'margin_class:.' . $margin_class;
if ($first_column_class) $first_column_class[] = 'margin_class:.' . $first_column_class;
if ($masonry) $grid_params[] = 'masonry:true';
$grid_params = $grid_params ? '="' . implode(';', $grid_params) . '"' : '';

$hm_param = '';
if ((int)$height_match && $height_match_class) $hm_param = ' uk-height-match="target:' . $height_match_class . '"';

$classes = [];
if ($grid != '') {
    $classes[] = $grid;
    if ((int)$grid_divider) $classes[] = 'uk-grid-divider';
    if ((int)$grid_center) $classes[] = 'uk-flex-center';
}
if ((int)$cols) {
    $classes[] = $cols_all;
    $classes[] = $cols_s;
    $classes[] = $cols_m;
    $classes[] = $cols_l;
    $classes[] = $cols_xl;
}
if ((int)$height_match && !$height_match_class) $classes[] = 'uk-grid-match';
$classes = $classes ? ' ' . implode(' ', $classes) : '';


// Include the news functions only once
JLoader::register('ModArticleGridHelper', __DIR__ . '/helper.php');


$list            = ModArticleGridHelper::getList($params);
$tagsList        = ModArticleGridHelper::getTagsList($list); 

require JModuleHelper::getLayoutPath('mod_article_grid', $params->get('layout', 'default'));
