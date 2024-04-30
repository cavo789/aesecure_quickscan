<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

define('ANGIE_FORCED_SESSION_KEY', '');

define('APATH_BASE',          __DIR__);
define('APATH_INSTALLATION',  __DIR__);

$parts = explode(DIRECTORY_SEPARATOR, APATH_BASE);
array_pop($parts);

define('APATH_ROOT',          implode(DIRECTORY_SEPARATOR, $parts));

define('APATH_SITE',          APATH_ROOT);
define('APATH_CONFIGURATION', APATH_ROOT);
define('APATH_ADMINISTRATOR', APATH_ROOT . '/administrator');
define('APATH_LIBRARIES',     APATH_ROOT . '/libraries');
define('APATH_THEMES',        APATH_INSTALLATION . '/template');
define('APATH_TEMPINSTALL',   APATH_INSTALLATION . '/tmp');
