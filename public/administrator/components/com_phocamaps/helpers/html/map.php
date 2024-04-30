<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
 defined('_JEXEC') or die;
use Joomla\CMS\Factory;
class PhocaMapsSelectMap
{
	public static function options()
	{
		$db = Factory::getDBO();

       //build the list of categories
		$query = 'SELECT a.title AS text, a.id AS value'
		. ' FROM #__phocamaps_map AS a'
		. ' WHERE a.published = 1'
		. ' ORDER BY a.ordering';
		$db->setQuery( $query );
		$maps = $db->loadObjectList();
	
		$catId	= -1;
		
		$javascript 	= 'class="form-control" size="1" onchange="submitform( );"';
		
		/*$tree = array();
		$text = '';
		$tree = PhocaGalleryRenderAdmin::CategoryTreeOption($maps, $tree, 0, $text, $catId);
		*/
		return $maps;

	}
}
