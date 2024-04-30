<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

$scripts = $this->getScripts();
$scriptDeclarations = $this->getScriptDeclarations();
$styles = $this->getStyles();
$styleDeclarations = $this->getStyleDeclarations();

// Scripts before the template ones
if(!empty($scripts)) foreach($scripts as $url => $params)
{
	if($params['before'])
	{
		echo "\t<script type=\"{$params['mime']}\" src=\"$url\"></script>\n";
	}
}

// Template scripts
?>
<script type="text/javascript" src="template/flat/js/jquery.js"></script>
<script type="text/javascript" src="template/flat/js/chosen.jquery.min.js"></script>
<script type="text/javascript" src="template/flat/js/menu.min.js"></script>
<script type="text/javascript" src="template/flat/js/tabs.min.js"></script>
<script type="text/javascript" src="template/flat/js/dropdown.min.js"></script>
<script type="text/javascript" src="template/flat/js/system.min.js"></script>
<script type="text/javascript" src="template/flat/js/tooltip.min.js"></script>
<script type="text/javascript" src="template/flat/js/modal.min.js"></script>
<?php
// Scripts after the template ones
if(!empty($scripts)) foreach($scripts as $url => $params)
{
	if(!$params['before'])
	{
		echo "\t<script type=\"{$params['mime']}\" src=\"$url\"></script>\n";
	}
}

// Script declarations
if(!empty($scriptDeclarations)) foreach($scriptDeclarations as $type => $content)
{
	echo "\t<script type=\"$type\">\n$content\n</script>";
}

// CSS files before the template CSS
if(!empty($styles)) foreach($styles as $url => $params)
{
	if($params['before'])
	{
		$media = ($params['media']) ? "media=\"{$params['media']}\"" : '';
		echo "\t<link rel=\"stylesheet\" type=\"{$params['mime']}\" href=\"$url\" $media></script>\n";
	}
}
?>
<link rel="stylesheet" type="text/css" href="template/flat/css/chosen.min.css" />
<link rel="stylesheet" type="text/css" href="template/flat/css/fef.min.css" />
<link rel="stylesheet" type="text/css" href="template/flat/css/dark.min.css" />
<link rel="stylesheet" type="text/css" href="template/flat/css/theme.min.css" />
<?php
// CSS files before the template CSS
if(!empty($styles)) foreach($styles as $url => $params)
{
	if(!$params['before'])
	{
		$media = ($params['media']) ? "media=\"{$params['media']}\"" : '';
		echo "\t<link rel=\"stylesheet\" type=\"{$params['mime']}\" href=\"$url\" $media></script>\n";
	}
}

// Script declarations
if(!empty($styleDeclarations)) foreach($styleDeclarations as $type => $content)
{
	echo "\t<style type=\"$type\">\n$content\n</style>";
}
