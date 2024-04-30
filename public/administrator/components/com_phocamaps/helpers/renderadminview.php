<?php
/**
 * @package   Phoca Gallery
 * @author    Jan Pavelka - https://www.phoca.cz
 * @copyright Copyright (C) Jan Pavelka https://www.phoca.cz
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 and later
 * @cms       Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Session\Session;
use Phoca\Render\Adminview;
use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;


class PhocaMapsRenderAdminView extends AdminView
{
	public $view 			= '';
	public $viewtype		= 2;
	public $option			= '';
	public $optionLang  	= '';
	public $compatible		= false;
	public $sidebar 		= true;
	protected $document		= false;

	public function __construct(){
		parent::__construct();
	}

}
?>
