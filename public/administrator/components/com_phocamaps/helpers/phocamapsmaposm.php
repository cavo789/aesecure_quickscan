<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Form;

class PhocaMapsMapOsm
{

	protected $name					= 'phocaMap';
	protected $id					= '';
	private	$output					= array();

	public $router					= '';
	public $maprouterapikey 		= '';
	public $routerserviceurl 		= '';
	public $routerprofile 			= '';
	public $thunderforestmaptype	= '';
	public $osmmaptype				= '';
	public $osmmaproutertype			= '';
	public $currentposition			= '';
	public $fullscreen				= '';
	public $search					= '';
	public $zoomwheel				= '';
	public $zoomcontrol				= '';
	public $easyprint				= '';
	public $markerclustering		= 0;

	/*var $_map			= 'mapPhocaMap';
	var $_latlng		= 'phocaLatLng';
	var $_options		= 'phocaOptions';
	var $_tst			= 'tstPhocaMap';
	var $_tstint		= 'tstIntPhocaMap';
	var $_marker		= FALSE;
	var $_window		= FALSE;
	var $_dirdisplay	= FALSE;
	var $_dirservice	= FALSE;
	var $_geocoder		= FALSE;
	var $_iconArray		= array();*/

	function __construct($id = '') {

		$app 						= Factory::getApplication();
		$paramsC 					= ComponentHelper::getParams('com_phocamaps');
		$this->router 				= $paramsC->get( 'osm_router', 0 );
		$this->maprouterapikey 		= $paramsC->get( 'osm_map_router_api_key', '' );
		$this->routerserviceurl 	= $paramsC->get( 'osm_router_routerserviceurl', '' );
		$this->routerprofile 		= $paramsC->get( 'osm_router_profile', '' );
		$this->thunderforestmaptype	= $paramsC->get( 'thunderforest_map_type', '' );
		$this->osmmaptype			= $paramsC->get( 'osm_map_type', '' );
		$this->osmmaproutertype		= $paramsC->get( 'osm_map_router_type', '' );
		$this->currentposition		= $paramsC->get( 'osm_current_position', 1 );
		$this->fullscreen			= $paramsC->get( 'osm_full_screen',1 );
		$this->search				= $paramsC->get( 'osm_search', 0 );
		$this->zoomwheel			= $paramsC->get( 'osm_zoom_wheel', 1);
		$this->zoomcontrol			= $paramsC->get( 'osm_zoom_control', 1 );
		$this->easyprint			= $paramsC->get( 'osm_easyprint', 0 );

		$this->markerclustering		= $paramsC->get( 'marker_clustering', 0 );



		$this->id	= $id;




		if ($app->isClient('administrator')) {
			$this->fullscreen 		= 1;
			$this->search			= 1;
			$this->zoomwheel		= 1;
			$this->zoomcontrol		= 1;
			$this->currentposition 	= 1;
		}
	}



	function loadAPI() {
		$document	= Factory::getDocument();


		$document->addScript(Uri::root(true) . '/media/com_phocamaps/js/leaflet/leaflet.js');
		$document->addStyleSheet(Uri::root(true) . '/media/com_phocamaps/js/leaflet/leaflet.css');

		$document->addScript(Uri::root(true) . '/media/com_phocamaps/js/leaflet-awesome/leaflet.awesome-markers.js');
		$document->addStyleSheet(Uri::root(true) . '/media/com_phocamaps/js/leaflet-awesome/leaflet.awesome-markers.css');

		$document->addScript(Uri::root(true) . '/media/com_phocamaps/js/leaflet-fullscreen/Leaflet.fullscreen.js');
		$document->addStyleSheet(Uri::root(true) . '/media/com_phocamaps/js/leaflet-fullscreen/leaflet.fullscreen.css');


		$document->addScript(Uri::root(true) . '/media/com_phocamaps/js/leaflet-control-locate/L.Control.Locate.min.js');
		$document->addStyleSheet(Uri::root(true) . '/media/com_phocamaps/js/leaflet-control-locate/L.Control.Locate.css');
		$document->addStyleSheet(Uri::root(true) . '/media/com_phocamaps/js/leaflet-control-locate/font-awesome.min.css');

		$document->addScript(Uri::root(true) . '/media/com_phocamaps/js/leaflet-omnivore/leaflet-omnivore.js');

		$document->addScript(Uri::root(true) . '/media/com_phocamaps/js/leaflet-search/leaflet-search.min.js');
		$document->addStyleSheet(Uri::root(true) . '/media/com_phocamaps/js/leaflet-search/leaflet-search.css');

		if ($this->router == 1) {
			$document->addScript(Uri::root(true) . '/media/com_phocamaps/js/leaflet-routing-machine/leaflet-routing-machine.min.js');
			$document->addStyleSheet(Uri::root(true) . '/media/com_phocamaps/js/leaflet-routing-machine/leaflet-routing-machine.css');

			$document->addStyleSheet(Uri::root(true) . '/media/com_phocamaps/js/leaflet-geocoder/Control.Geocoder.css');
			$document->addScript(Uri::root(true) . '/media/com_phocamaps/js/leaflet-geocoder/Control.Geocoder.js');
		}

		if ($this->easyprint == 1) {
			$document->addScript(Uri::root(true) . '/media/com_phocamaps/js/leaflet-easyprint/bundle.js');

		}

		if ($this->markerclustering == 1) {
			$document->addStyleSheet(Uri::root(true) . '/media/com_phocamaps/js/leaflet-markercluster/MarkerCluster.css');
			$document->addStyleSheet(Uri::root(true) . '/media/com_phocamaps/js/leaflet-markercluster/MarkerCluster.Default.css');
			$document->addScript(Uri::root(true) . '/media/com_phocamaps/js/leaflet-markercluster/leaflet.markercluster.min.js');
		}

	}

	function loadCoordinatesJS() {
		$document	= Factory::getDocument();
		$document->addScript(Uri::root(true).'/media/com_phocamaps/js/administrator/coordinates.js');
	}

	function createMap($lat, $lng, $zoom) {

		$app = Factory::getApplication();

		$opt = array();
		if ($this->zoomwheel == 0) {
			$opt[] = 'scrollWheelZoom: false,';
		}
		if ($this->zoomcontrol == 0) {
			$opt[] = 'zoomControl: false,';
		}

		//if ($this->zoomcontrol == 0) {
			$opt[] = 'zoomControl: false,';
		//}

		$options = '{' . implode("\n", $opt) . '}';

		$o 	= array();

		$o[]= 'var map'.$this->name.$this->id.' = L.map("'.$this->name.$this->id.'", '.$options.').setView(['.PhocaMapsHelper::filterValue($lat, 'number2').', '.PhocaMapsHelper::filterValue($lng, 'number2').'], '.(int)$zoom.');';


		if ($this->zoomcontrol == 1) {
			$o[] = 'new L.Control.Zoom({ zoomInTitle: \''.Text::_('COM_PHOCACART_ZOOM_IN_TITLE').'\', zoomOutTitle: \''.Text::_('COM_PHOCACART_ZOOM_OUT_TITLE').'\' }).addTo(map'.$this->name.$this->id.');';
		}

		if ($this->markerclustering == 1) {
			$o[] = 'var markers' . $this->name . $this->id . ' = L.markerClusterGroup();';
		}

		$this->output[] = implode("\n", $o);
		return true;
	}

	function setMapType() {

		$app = Factory::getApplication();

		// Possible new parameters
		$thunderForestMapType = $this->thunderforestmaptype;
		$thunderForestKey	= $this->maprouterapikey;
		$mapBoxKey	= $this->maprouterapikey;
		$type = $this->osmmaptype;

		$o = array();
		if ($type === "osm_de") {

			$o[] = 'L.tileLayer(\'https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png\', {';
			$o[] = '	maxZoom: 18,';
			$o[] = '	attribution: \'&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';

		} else if ($type === "osm_bw") {

			//$o[] = 'L.tileLayer(\'http://{s}.tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png\', {';
			$o[] = 'L.tileLayer(\'https://tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png\', {';

			$o[] = '	maxZoom: 18,';
			$o[] = '	attribution: \'&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';

		} else if ($type === 'thunderforest') {

			if ($thunderForestKey == '') {
				$app->enqueueMessage(Text::_('COM_PHOCAMAPS_ERROR_API_KEY_NOT_SET'));
				return false;
			}
			if ($thunderForestMapType == '') {
				$app->enqueueMessage(Text::_('COM_PHOCAMAPS_ERROR_MAP_TYPE_NOT_SET'));
				return false;
			}
			$o[] = 'L.tileLayer(\'https://{s}.tile.thunderforest.com/'.PhocaMapsHelper::filterValue($thunderForestMapType).'/{z}/{x}/{y}.png?apikey={apikey}\', {';
			$o[] = '	maxZoom: 22,';
			$o[] = '	apikey: '.PhocaMapsHelper::filterValue($thunderForestKey).',';
			$o[] = '	attribution: \'&copy; <a href="https://www.thunderforest.com/" target="_blank">Thunderforest</a>, &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';

		} else if ($type === 'mapbox') {

			if ($mapBoxKey == '') {
				$app->enqueueMessage(Text::_('COM_PHOCAMAPS_ERROR_API_KEY_NOT_SET'));
				return false;
			}


			$o[] = 'L.tileLayer(\'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token='.PhocaMapsHelper::filterValue($mapBoxKey).'\', {';
			$o[] = '	maxZoom: 18,';
			$o[] = '	attribution: \'Map data &copy; <a href="https://openstreetmap.org" target="_blank">OpenStreetMap</a> contributors, \' + ';
			$o[] = '		\'<a href="https://creativecommons.org/licenses/by-sa/2.0/" target="_blank" target="_blank">CC-BY-SA</a>, \' + ';
			$o[] = '		\'Imagery © <a href="https://mapbox.com" target="_blank">Mapbox</a>\',';
			$o[] = '	id: \'mapbox.streets\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';

		} else if ($type === 'opentopomap') {

			$o[] = 'L.tileLayer(\'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png\', {';
			$o[] = '	maxZoom: 17,';
			$o[] = '	attribution: \'Map data: &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>, <a href="https://viewfinderpanoramas.org" target="_blank">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org" target="_blank">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/" target="_blank">CC-BY-SA</a>)\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';

		} else if ($type === 'google') {
			/*
			$o[] = 'L.gridLayer.googleMutant({';
			$o[] = '	type: googlemapstype,';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';
			*/
		} else if ($type === 'wikimedia') {
			$o[] = 'L.tileLayer(\'https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png\', {';
			$o[] = '	maxZoom: 18,';
			$o[] = '	attribution: \'&copy; <a href="https://wikimediafoundation.org/wiki/Maps_Terms_of_Use" target="_blank">Wikimedia maps</a> | Map data © <a href="https://openstreetmap.org/copyright" target="_blank">OpenStreetMap contributors</a>\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';

		} else if ($type == 'osm_fr') {

			$o[] = 'L.tileLayer(\'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png\', {';
			$o[] = '	maxZoom: 20,';
			$o[] = '	attribution: \'&copy; <a href="https://www.openstreetmap.fr" target="_blank">Openstreetmap France</a> & <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';

		} else if ($type == 'osm_hot') {

			$o[] = 'L.tileLayer(\'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png\', {';
			$o[] = '	maxZoom: 20,';
			$o[] = '	attribution: \'&copy; <a href="https://hotosm.org/" target="_blank">Humanitarian OpenStreetMap Team</a> & <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';

		} else {


			$o[] = 'L.tileLayer(\'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png\', {';
			$o[] = '	maxZoom: 18,';
			$o[] = '	attribution: \'&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';

		}

		$this->output[] = implode("\n", $o);
		return true;
	}

	public function setMarker($markerId, $title, $description, $lat, $lng, $text = '', $width = '', $height = '', $open = 0, $closeOpenedWindow = 0) {


		$o = array();


		if($open != 2){
			$o[]= 'var marker'.$markerId.' = L.marker(['.PhocaMapsHelper::filterValue($lat, 'number2').', '.PhocaMapsHelper::filterValue($lng, 'number2').'])';


			if ($this->markerclustering == 1) {
				// marker will be added to layer with cluster function
			} else {
				$o[] = '.addTo(map'.$this->name.$this->id.');';
			}

			$o[] = ';';
		}

		jimport('joomla.filter.output');

		$style = '';
		if ($width != '') {
			$style .= 'width: '.(int)$width.'px;';
		}
		if ($height != '') {
			$style .= 'height: '.(int)$height.'px;';
		}

		if ($text == '') {
			if ($title != ''){
				$hStyle = 'font-size:120%;margin: 5px 0px;font-weight:bold;';
				$text .= '<div style="'.$hStyle.'">' . addslashes($title) . '</div>';
			}
			if ($description != '') {
				$text .=  '<div>'.PhocaMapsHelper::strTrimAll(addslashes($description)).'</div>';
			}
		}



		if ($text != '') {
			if ($style != '') {
				$text = '<div style="'.$style.'">' . $text . '</div>';
			}

			$openO = '';
			if ($open == 1) {
				$openO = '.openPopup()';
			}
			$o[]= 'marker'.$markerId.'.bindPopup(\''.$text.'\')'.$openO.';';
		}

		if ($this->markerclustering == 1) {
			$o[] = 'markers' . $this->name . $this->id . '.addLayer(marker' . $markerId . ');';
		}

		$this->output[] = implode("\n", $o);
		return true;

	}

	public function setMarkerClusterer() {

		if ($this->markerclustering == 1) {
			$o              = array();
			$o[]            = 'map' . $this->name . $this->id . '.addLayer(markers' . $this->name . $this->id . ');';
			$this->output[] = implode("\n", $o);
		}
	}

	public function setMarkerIcon($markerId, $icon = 'circle', $markerColor = 'blue', $iconColor = '#ffffff', $prefix = 'fa', $spin = 'false', $extraClasses = '' ) {

		$o = $o2 = array();

		$o[]= 'var icon'.$markerId.' = new L.AwesomeMarkers.icon({';

		$o[]= $o2[] = '   icon: "'.PhocaMapsHelper::filterValue($icon).'",';
		$o[]= $o2[] = '   markerColor: "'.PhocaMapsHelper::filterValue($markerColor).'",';
		$o[]= $o2[] = '   iconColor: "'.PhocaMapsHelper::filterValue($iconColor).'",';
		$o[]= $o2[] = '   prefix: "'.PhocaMapsHelper::filterValue($prefix).'",';
		$o[]= $o2[] = '   spin: '.PhocaMapsHelper::filterValue($spin).',';
		$o[]= $o2[] = '   extraClasses: "'.PhocaMapsHelper::filterValue($extraClasses).'",';

		$o[]= '})';
		$o[]= ' marker'.$markerId.'.setIcon(icon'.$markerId.');';

		$this->output[] = implode("\n", $o);
		return $o2;//return only options;
	}


	public function inputMarker($latInput, $longInput, $zoomInput = '', $setGPS = 0) {

		$o = array();
		$o[]= 'function phmInputMarker(lat, lng) {';
		$o[]= 'var phLat = jQuery(\'#jform_latitude_id\', window.parent.document);';
		$o[]= 'var phLng = jQuery(\'#jform_longitude_id\', window.parent.document);';

		$o[]= 'phLat.val(lat);';
		$o[]= 'phLng.val(lng);';

		if ( $zoomInput != '') {
			$o[]= 'var phZoom = jQuery(\'#jform_zoom_id\', window.parent.document);';
			$o[]= 'phZoom.val(map'.$this->name.$this->id.'.getZoom());';
			$o[]= 'var phmMsg = \'<span class="ph-msg-success">'.Text::_('COM_PHOCAMAPS_LAT_LNG_ZOOM_SET').'</span>\';';
		} else {
			$o[]= 'var phmMsg = \'<span class="ph-msg-success">'.Text::_('COM_PHOCAMAPS_LAT_LNG_SET').'</span>\';';
		}

		$o[]= 'jQuery(\'#phmPopupInfo\', window.parent.document).html(phmMsg);';

		if ($setGPS == 1) {
			$o[]= '   if (window.parent) {setPMGPSLatitudeJForm(lat);}';
			$o[]= '   if (window.parent) {setPMGPSLongitudeJForm(lng);}';
		}
		$o[]= '}';
		$this->output[] = implode("\n", $o);
		return true;
	}


	public function moveMarker() {

		$o = array();
		$o[]= 'function phmMoveMarker(marker, lat, lng) {';
		$o[]= '   var newLatLng = new L.LatLng(lat, lng);';
		$o[]= '   marker.setLatLng(newLatLng);';
		$o[]= '}';
		$this->output[] = implode("\n", $o);
		return true;
	}

	public function exportMarker($markerId) {

		$o 	= array();
		$o[] = 'map'.$this->name.$this->id.'.on(\'click\', onMapClick);';

		$o[] = 'function onMapClick(e) {';
		$o[] = '	phmInputMarker(e.latlng.lat, e.latlng.lng);';
		$o[] = '	phmMoveMarker(marker'.$markerId.', e.latlng.lat, e.latlng.lng);';
		$o[] = '}';
		$this->output[] = implode("\n", $o);
		return true;
	}


	public function renderSearch($markerId = '', $position = '') {



		$position = $position != '' ? $position : 'topright';
		$o 	= array();
		$o[] = 'map'.$this->name.$this->id.'.addControl(new L.Control.Search({';

		$o[] = '	url: \'https://nominatim.openstreetmap.org/search?format=json&q={s}\',';
		$o[] = '	jsonpParam: \'json_callback\',';
		$o[] = '	propertyName: \'display_name\',';
		$o[] = '	propertyLoc: [\'lat\',\'lon\'],';
		$o[] = '	marker: L.circleMarker([0,0],{radius:30}),';
		$o[] = '	autoCollapse: true,';
		$o[] = '	autoType: false,';
		$o[] = '	minLength: 3,';
		$o[] = '	position: \''.$position.'\',';


		$o[] = '	textErr: \''.Text::_('COM_PHOCAMAPS_SEARCH_LOCATION_NOT_FOUND').'\',';
		$o[] = '	textCancel: \''.Text::_('COM_PHOCAMAPS_SEARCH_CANCEL').'\',';
		$o[] = '	textPlaceholder: \''.Text::_('COM_PHOCAMAPS_SEARCH_SEARCH').'\',';

		if ($markerId != '') {
			$o[] = '	moveToLocation: function(latlng, title, map) {';
			$o[] = '		map'.$this->name.$this->id.'.setView(latlng, 7);';// set the zoom first so it will be added to form input
			$o[] = '		phmInputMarker(latlng.lat, latlng.lng);';
			$o[] = '		phmMoveMarker(marker'.$markerId.', latlng.lat, latlng.lng);';
			$o[] = '	}';
		}
		$o[] = '}));';

		$this->output[] = implode("\n", $o);
		return true;
	}

	public function renderFullScreenControl() {


		if ($this->fullscreen == 0) {
			return false;
		}

		$o 	= array();
		$o[] = 'map'.$this->name.$this->id.'.addControl(';

		$o[] = '	new L.Control.Fullscreen({';
		$o[] = '		position: \'topright\',';
		$o[] = '		title: {';
		$o[] = '			\'false\': \''.Text::_('COM_PHOCAMAPS_VIEW_FULLSCREEN').'\',';
		$o[] = '			\'true\': \''.Text::_('COM_PHOCAMAPS_EXIT_FULLSCREEN').'\'';
		$o[] = '		}';
		$o[] = '	})';

		$o[] = ')';

		$this->output[] = implode("\n", $o);
		return true;

	}

	public function renderCurrentPosition($markerId = '') {


		if ($this->currentposition == 0) {
			return false;
		}

		$o 	= array();

		$o[] = 'L.control.locate({';
		$o[] = '	position: \'topright\',';
		$o[] = '	strings: {';
		$o[] = '		\'title\': \''.Text::_('COM_PHOCAMAPS_CURRENT_POSITION').'\'';
		$o[] = '	},';
		$o[] = '	locateOptions: {';
		$o[] = '		enableHighAccuracy: true,';
		$o[] = '		watch: true,';
		$o[] = '	},';

		/*if ($markerId != '') {
			$o[] = '	onlocationfound: function(latlng, title, map) {';
			$o[] = '		map'.$this->name.$this->id.'.setView(latlng, 7);';// set the zoom first so it will be added to form input
			$o[] = '		phmInputMarker(latlng.lat, latlng.lng);';
			$o[] = '		phmMoveMarker(marker'.$markerId.', latlng.lat, latlng.lng);';
			$o[] = '	}';
		}*/


		$o[] = '}).addTo(map'.$this->name.$this->id.');';


		if ($markerId != '') {
			$o[] = ' map' . $this->name . $this->id . '.on("locationfound", function(e) {';
			//$o[] = '		map'.$this->name.$this->id.'.setView(e.latlng, 7);';// set the zoom first so it will be added to form input
			$o[] = '		phmInputMarker(e.latitude, e.longitude);';
			$o[] = '		phmMoveMarker(marker' . $markerId . ', e.latitude, e.longitude);';
			$o[] = ' });';
		}


		$this->output[] = implode("\n", $o);
		return true;

	}

	public function renderEasyPrint() {


		if ($this->easyprint == 0) {
			return false;
		}

		$o 	= array();

		$o[] = 'map'.$this->name.$this->id.'.addControl(';
		$o[] = '	new L.easyPrint({';
		$o[] = '	   hideControlContainer: true,';
		$o[] = '	   sizeModes: [\'Current\', \'A4Portrait\', \'A4Landscape\'],';
		$o[] = '	   position: \'topleft\',';
		$o[] = '	   exportOnly: true';
		$o[] = '	})';
		$o[] = ');';


		$this->output[] = implode("\n", $o);
		return true;

	}


	public function renderRouting($latFrom = 0, $lngFrom = 0, $latTo = 0, $lngTo = 0, $markerId = '', $markerIconOptions = array(), $language = '') {

		if ($this->router == 0) {
			return false;
		}


		$o 	= array();
		if ($this->routerserviceurl == '' && $this->maprouterapikey == '') {
			$o[] = 'console.log(\'Routing Error: No router or service url set\')';
			$this->output[] = implode("\n", $o);
			return true;
		}

		$o[] = 'var routingControl = L.Routing.control({';
		$o[] = '   waypoints: [';


		if ($latFrom == 0 && $lngFrom == 0 && $latTo != 0 && $lngTo != 0) {
			$o[] = '      L.latLng(\'\'),';
		} else if ($latFrom == 0 && $lngFrom == 0) {
			$o[] = '      L.latLng(\'\'),';
		} else {
			$o[] = '      L.latLng('.PhocaMapsHelper::filterValue($latFrom, 'number2').', '.PhocaMapsHelper::filterValue($lngFrom, 'number2').'),';
		}
	    if ($latTo == 0 && $lngTo == 0) {
	    	$o[] = '      L.latLng(\'\'),';
	    } else {
	    	$o[] = '      L.latLng('.PhocaMapsHelper::filterValue($latTo, 'number2').', '.PhocaMapsHelper::filterValue($lngTo, 'number2').')';
	    }
	    $o[] = '   ],';
	    if ($language != '') {
	     	$o[] = '   language: \''.PhocaMapsHelper::filterValue($language, 'text').'\',';
	    }

	    if ($markerId != '') {

	    	//$o[] = '   marker: marker'.$markerId.',';

	    	// Don't create new marker for routing (so if we have "TO" address with marker created in map
	    	// don't display any marker
	    	//if (!empty($markerIconOptions)) {
	    	if ($latTo != 0 && $lngTo != 0) {
	    		$o[] = '   createMarker: function(i,wp, n) {';

	    		$o[] = '      var latToMarker = '.PhocaMapsHelper::filterValue($latTo, 'number2').';';
	    		$o[] = '      var lngToMarker = '.PhocaMapsHelper::filterValue($lngTo, 'number2').';';

	    		$o[] = '      if (wp.latLng.lat == latToMarker && wp.latLng.lng == lngToMarker) {';
	    		$o[] = '         return false;';
	    		$o[] = '      } else {';

	    		// Get the same icon as the "To" (End) has
	    		if (!empty($markerIconOptions)) {

	    			$o[] = '       var ma = L.marker(wp.latLng);';
		    		$o[] = '       var ic = new L.AwesomeMarkers.icon({';
		    		foreach($markerIconOptions as $k => $v) {

		    			// Change the icon to circle (e.g. the "To" (End) is set to home, so don't render the same icon for "From" (start) address
		    			if (strpos($v, 'icon:') !== false) {
		    				$v = 'icon: "circle",';
		    			}

		    			$o[] = '          '.$v. "\n";
		    		}
		    		$o[] = '       });';
		    		$o[] = '       ma.setIcon(ic);';
		    		$o[] = '       return ma;';

	    		} else {
	    			$o[] = '         return L.marker(wp.latLng);';
	    		}

	    		$o[] = '      }';
	    		$o[] = '   },';
	    	}

	    }


	    $o[] = '   routeWhileDragging: true,';
	    $o[] = '   geocoder: L.Control.Geocoder.nominatim(),';
	    $o[] = '   reverseWaypoints: true,';
	    $o[] = '   showAlternatives: true,';
	    $o[] = '   collapsible: true,';
	    $o[] = '   show: false,';


/*
	    if ($this->routerserviceurl == 'https://api.mapbox.com/directions/v5') {
	    	// DEBUG DEMO - default address of leaflet-routing-machine to debug
	    } else if ($this->routerserviceurl != '') {
	    	$o[] = '   routerserviceurl: \''.$this->routerserviceurl.'\',';
	    } else if ($this->osm_map_type == 'mapbox' && $this->maprouterapikey != '') {
	    	$o[] = '   router: L.Routing.mapbox(\''.PhocaMapsHelper::filterValue($this->maprouterapikey).'\'),';
	    } else {
			$o[] = array();
			$o[] = 'console.log(\'Routing Error: No router or service url set\')';
			$this->output[] = implode("\n", $o);
			return true;
		}*/


		if ($this->routerserviceurl == 'https://api.mapbox.com/directions/v5') {
	    	// DEBUG DEMO - default address of leaflet-routing-machine to debug
	    }  else if ($this->routerserviceurl != '' && $this->maprouterapikey != '' && $this->osmmaproutertype == 'mapbox'){
	    	$o[] = '   routerserviceurl: \''.$this->routerserviceurl.'\',';
	      	$o[] = '   router: L.Routing.mapbox(\''.PhocaMapsHelper::filterValue($this->maprouterapikey).'\',{language: \''.PhocaMapsHelper::filterValue($language, 'text').'\'}),';
		} else if ($this->routerserviceurl != '') {
	    	$o[] = '   routerserviceurl: \''.$this->routerserviceurl.'\',';
	    }


		/* else if ($this->osm_map_type == 'mapbox' && $this->maprouterapikey != '') {
	    	$o[] = '   router: L.Routing.mapbox(\''.PhocaMapsHelper::filterValue($this->maprouterapikey).'\'),';
	    }*/

	    else {
			$o[] = array();
			$o[] = 'console.log(\'Routing Error: No router or service url set\')';
			$this->output[] = implode("\n", $o);
			return true;
		}


/*
	    if ($this->routerserviceurl == 'https://api.mapbox.com/directions/v5') {
	    	// DEBUG DEMO - default address of leaflet-routing-machine to debug
	    } else if ($this->osm_map_type == 'mapbox' && $this->maprouterapikey != '' && $this->routerserviceurl != '') {
			$o[] = '   routerserviceurl: \''.$this->routerserviceurl.'\',';
	    	//$o[] = '   router: L.Routing.mapbox(\''.PhocaMapsHelper::filterValue($this->maprouterapikey).'\'),';
	    	$o[] = '   router: L.Routing.mapbox(\''.PhocaMapsHelper::filterValue($this->maprouterapikey).'\',{language: \''.PhocaMapsHelper::filterValue($language, 'text').'\'}),';
	    } else if ($this->osm_map_type == 'mapbox' && $this->maprouterapikey != '') {
	    	$o[] = '   router: L.Routing.mapbox(\''.PhocaMapsHelper::filterValue($this->maprouterapikey).'\'),';
	    } else if ($this->routerserviceurl != '') {
	    	$o[] = '   routerserviceurl: \''.$this->routerserviceurl.'\',';
	    } else {
			$o[] = array();
			$o[] = 'console.log(\'Routing Error: No router or service url set\')';
			$this->output[] = implode("\n", $o);
			return true;

		}
*/


	    if ($this->routerprofile != '') {
	    	$o[] = '   profile: \''.PhocaMapsHelper::filterValue($this->routerprofile).'\',';
	    }
	    $o[] = '})';

	   // $o[] = '.on(\'routingstart\', showSpinner)';
	    //$o[] = '.on(\'routesfound routingerror\', hideSpinner)';
	    $o[] = '.addTo(map'.$this->name.$this->id.');';

	    //$o[] = 'routingControl.hide();';

	    $this->output[] = implode("\n", $o);
		return true;
	}

	/**
	 * @param unknown $filename - url of gpx/kml file to load
	 * @param string $color - optional hex colour value for track (default to blue)
	 * @param boolean $fitbounds - if true adjust the map centre and zoom to fit the track
	 * @return boolean
	 * @desc renderTrack() adds a gpx or kml filename layer
	 * @author RogerCO added 22/6/2021 for v3.0.12
	 */
	public function renderTrack($filename, $color = '', $fitbounds = false) {
		$ext = parse_url($filename)['path'];
		//check we have a gpx or kml file (mime type not defined for these so use extension)
		$ext = substr($ext,strrpos($ext,'.'));
		if (($ext != '.gpx') && ($ext != '.kml')) {
			return false;
		}
		$mapname = 'map'.$this->name.$this->id;
		// colour layer doesn't seem to work for kml files
		if (($ext=='.gpx') && $color) {
			$this->output[] = " var customLayer = L.geoJson(null, { style: function(feature) {return {color: '".$color."'}; } });";
		} else {
			$this->output[] = "var customLayer = null;";
		}
		if ($fitbounds) {
			$this->output[] = "var runLayer = omnivore".$ext."('".$filename."', null, customLayer).on('ready', function() {
                                ".$mapname.".fitBounds(runLayer.getBounds());
                                }).addTo(".$mapname.")";
		} else {
			$this->output[] = "omnivore".$ext."('".$filename."', null, customLayer).addTo(".$mapname.")";
		}
		return true;
	}

	public function renderMap() {
		$o = array();
		$o[] = 'jQuery(document).ready(function() {';
		$o[] = implode("\n", $this->output);
		$o[] = '})';
		Factory::getDocument()->addScriptDeclaration(implode("\n", $o));
	}
}
?>
