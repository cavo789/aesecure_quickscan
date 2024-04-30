<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_accordion
 * @copyright   © 2020 Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Helper\ModuleHelper;

$vars = [
    'accordeon_class', 'title_class', 'content_class',
    'active', 'multiple', 'collapsible', 'animation','item_style', 'item_headline','img_popup','title_style','item_space','img_width','duration', 'transition','titlesize','titlecolor','itemspace','titlebg','itembg','titlespace','items',
];

foreach ($vars as $var) {
    $$var = $params->get($var);
}


$accordion_params = [];
if ((int)$collapsible < 1) $accordion_params[] = 'collapsible:false';
if ((int)$collapsible > 0) $accordion_params[] = 'collapsible:true';
if ($multiple) $accordion_params[] = 'multiple:true';
if (!$animation) {
    $accordion_params[] = 'animation:false';
} else {
    if ($duration > 0 && (int)$duration != 200) $accordion_params[] = 'duration:' . $duration;
    if ($transition != 'ease') $accordion_params[] = 'transition:' . $transition;
}
$accordion_params = $accordion_params ? '="' . implode(';', $accordion_params) . '"' : '';

if ($items) {
    require(ModuleHelper::getLayoutPath('mod_uk_accordion', $params->get('layout', 'default')));
}
