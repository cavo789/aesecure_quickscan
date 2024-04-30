<?php
/**
 * @package   Phoca Download
 * @author    Jan Pavelka - https://www.phoca.cz
 * @copyright Copyright (C) Jan Pavelka https://www.phoca.cz
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 and later
 * @cms       Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Version;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Uri\Uri;

// Frontend editor - button plugin
require_once JPATH_ADMINISTRATOR . '/components/com_phocamaps/libraries/autoloadPhoca.php';

use Phoca\Render\Adminviews;


class PhocaMapsRenderAdminViews extends AdminViews


{

	public $view = '';
	public $viewtype = 1;
	public $option = '';
	public $optionLang = '';
	public $tmpl = '';
	public $compatible = false;
	public $sidebar = true;
	protected $document = false;


	public function __construct() {

		parent::__construct();
	}
}
?>
