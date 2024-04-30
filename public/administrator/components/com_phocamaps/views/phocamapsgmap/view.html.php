<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
jimport( 'joomla.application.component.view');

class PhocaMapsCpViewPhocaMapsGMap extends HtmlView
{
	protected $latitude;
	protected $longitude;
	protected $zoom;
	protected $type;
	protected $t;
	protected $r;
	protected $p;

	public function display($tpl = null) {

		$paramsC			= ComponentHelper::getParams('com_phocamaps') ;
		$app 				= Factory::getApplication();

		$this->t	    = PhocaMapsUtils::setVars();
		$this->r		= new PhocaMapsRenderAdminview();

		$this->latitude			= $app->input->get( 'lat', '50', 'get', 'string' );
		$this->longitude		= $app->input->get( 'lng', '-30', 'get', 'string' );
		$this->zoom				= $app->input->get( 'zoom', '2', 'get', 'string' );
		$this->type				= $app->input->get( 'type', 'map', 'get', 'string' );

		$this->p['enable_ssl'] 	= $paramsC->get('load_api_ssl', 0);
		$this->p['map_type']	= $paramsC->get( 'map_type', 2 );

		$document	= Factory::getDocument();
		$document->addCustomTag( "<style type=\"text/css\"> \n"
			." html,body, .contentpane{overflow:hidden;background:#ffffff;} \n"
			." </style> \n");




		if ($this->p['map_type'] == 2) {
			parent::display('osm');
		} else {
			parent::display($tpl);
		}
	}
}
?>
