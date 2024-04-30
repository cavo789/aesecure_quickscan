<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
jimport( 'joomla.application.component.controller' );

class PhocaMapsController extends BaseController
{
	public function display($cachable = false, $urlparams = false)
	{
		
		if ( ! Factory::getApplication()->input->get('view') ) {
			Factory::getApplication()->input->set('view', 'map' );
		}
		
		$paramsC 	= ComponentHelper::getParams('com_phocamaps');
		$cache 		= $paramsC->get( 'enable_cache', 0 );
		$cachable 	= false;
		if ($cache == 1) {
			$cachable 	= true;
		}
		
		$document 	= Factory::getDocument();

		$safeurlparams = array('catid'=>'INT','id'=>'INT','cid'=>'ARRAY','year'=>'INT','month'=>'INT','limit'=>'INT','limitstart'=>'INT',
			'showall'=>'INT','return'=>'BASE64','filter'=>'STRING','filter_order'=>'CMD','filter_order_Dir'=>'CMD','filter-search'=>'STRING','print'=>'BOOLEAN','lang'=>'CMD');

		parent::display($cachable,$safeurlparams);

		return $this;
	}
}
?>