<?php
/*
 * @package Joomla 3.8
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;

jimport('joomla.application.component.model');

class PhocaMapsModelMap extends BaseDatabaseModel
{
	var $_id			= null;
	var $_data			= null;

	function __construct() {
		parent::__construct();
		$app				= Factory::getApplication();
		$this->setState('filter.language',$app->getLanguageFilter());
		$id 	= $app->input->get('id', 0, 'int');
		$this->setId((int)$id);
	}
	
	function setId($id){
		$this->_id			= $id;
		$this->_data		= null;
	}
	
	
	function &getData() {
		if (!$this->_loadData()) {
			$this->_initData();
		}
		return $this->_data;
	}
	
	function _loadData() {
		if (empty($this->_data)) {
		
			// Map
			$where 		= array();
			$where[]	= 'a.id = '.(int) $this->_id;
			//$where[]	= 'a.published = 1';
			// Filter by language
			if ($this->getState('filter.language')) {
				$where[] =  'a.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
			}
			$where		= (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
			$query = 'SELECT a.*'
					.' FROM #__phocamaps_map AS a'
					.' ' . $where;
			$this->_db->setQuery($query);
			$this->_data['map'] = $this->_db->loadObject();
			
			// Marker
			$where 		= array();
			$where[]	= 'c.id = '.(int) $this->_id;
			$where[]	= 'a.published = 1';
			
			if ($this->getState('filter.language')) {
				$where[] =  'a.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
			}
			$where		= (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
			
			$query = 'SELECT a.*, i.id as iconid, i.url as iurl, i.urls as iurls, i.object as iobject, i.objects as iobjects, i.objectshape as iobjectshape'
					.' FROM #__phocamaps_marker AS a'
					.' LEFT JOIN #__phocamaps_map AS c ON c.id = a.catid '
					.' LEFT JOIN #__phocamaps_icon AS i ON i.id = a.iconext '
					.' ' . $where
					.' ORDER BY a.ordering ASC';
			$this->_db->setQuery($query);
			$this->_data['marker'] = $this->_db->loadObjectList();
			
			return (boolean) $this->_data;
		}
		
		return true;
	}
	
	
	function _initData() {
		if (empty($this->_data)) {
			$this->_data	= '';
			return (boolean) $this->_data;
		}
		return true;
	}
}
?>
