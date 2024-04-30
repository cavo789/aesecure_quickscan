<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;

class TablePhocaMapsMarker extends Table
{
	function __construct( &$db ) {
		parent::__construct( '#__phocamaps_marker', 'id', $db );
	}
	
	function check(){
		
		if (trim( $this->catid ) == '') {
			throw new Exception( Text::_( 'COM_PHOCAMAPS_ERROR_MAP_NOT_SELECTED'), 500 );
			return false;
		}
		
		if (trim( $this->title ) == '') {
			throw new Exception( Text::_( 'COM_PHOCAMAPS_ERROR_TITLE_NOT_SET'), 500 );
			return false;
		}

		if (empty($this->alias)) {
			$this->alias = $this->title;
		}
		$this->alias = ApplicationHelper::stringURLSafe($this->alias);
		if (trim(str_replace('-', '', $this->alias)) == '') {
			$this->alias = Factory::getDate()->format("Y-m-d-H-i-s");
		}

		return true;
	}
}
?>