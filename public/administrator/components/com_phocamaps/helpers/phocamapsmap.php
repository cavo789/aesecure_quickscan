<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Router\Route;
require_once( JPATH_ADMINISTRATOR.'/components/com_phocamaps/helpers/phocamapsicon.php' );
/* Google Maps Version 3 */
class PhocaMapsMap
{
	/*
	 * Map Name (id of element in html)
	 */
	public $_name			= 'phocaMap';

	/*
	 * Map ID - it is important e.g. for plugin when more instances are created
	 */
	public $_id			= '';
	public $_map		= 'mapPhocaMap';
	public $_latlng		= 'phocaLatLng';
	public $_options	= 'phocaOptions';
	public $_tst		= 'tstPhocaMap';
	public $_tstint		= 'tstIntPhocaMap';

	/*
	 * If you want to work only with one marker (administration), set TRUE for global marker so only with one marker id will be worked in the map
	 * You need to set:
	 * In createMap() method set TRUE for $globalMarker - global var will be created: var markerPhocaMarkerGlobal
	 * In setMarker() or exportMarker() set the id as "Global" - so the marker get the name markerPhocaMarkerGlobal
	 * If there is one global marker then there is one global window
	 */
	public $_marker		= FALSE;
	public $_window		= FALSE;
	public $_dirdisplay	= FALSE;
	public $_dirservice	= FALSE;
	public $_geocoder	= FALSE;

	/*
	 * Marker JS output
	 * There are two kinds of marker type (default and external), they can have the same id, so we must differentiate it
	 * Two images (icons, markers) can be used in one map, so the marker icon should be loaded only one time
	 */
	public $_iconArray		= array();

	function __construct($id = '') {
		$this->_id	= $id;
	}

	function startJScData() {
		return '<script type="text/javascript">//<![CDATA['."\n";
	}

	function endJScData($noScriptText = 'COM_PHOCAMAPS_GOOGLE_MAPS_ENABLE_JS') {
		return '//]]></script>'."\n"
			. '<noscript><p class="p-noscript">'.Text::_($noScriptText).'</p><p>&nbsp;</p></noscript>'."\n\n";
	}

	/*
	 * Loaded only one time per site (addScript)
	 */
	function loadAPI( $id = '', $lang = '') {
		$document = Factory::getDocument();

		$paramsC           = ComponentHelper::getParams('com_phocamaps');
		$key               = $paramsC->get('maps_api_key', '');
		$ssl               = $paramsC->get('load_api_ssl', 1);
		$marker_clustering = $paramsC->get('marker_clustering', 0);


		if ($ssl) {
			$h = 'https://';
		} else {
			$h = 'http://';
		}
		if ($key) {
			$k = '&key=' . PhocaMapsHelper::filterValue($key, 'text');
		} else {
			$k = '';
		}

		if ($lang != '') {
			$l = '&language=' . PhocaMapsHelper::filterValue($lang, 'text');

		} else {
			$l = '';
		}


		/*if ($ssl == 1) {
			$scriptLink	= 'https://www.google.com/'.$src;
		} else {
			$scriptLink	= 'http://www.google.com/'.$src;
		}*/


		$initMaps = 'initMaps' . $id;


		$s = '<script async defer src="' . $h . 'maps.googleapis.com/maps/api/js?callback=' . $initMaps . $k . $l . '" type="text/javascript"></script>';

		if ($marker_clustering == 1) {
			$s .= '<script async defer src="' . Uri::root(true) . '/media/com_phocamaps/js/gm/markerclustererplus.min.js"></script>';
		}
		//$document->addCustomTag($s);// must be loaded as last in the html, cannot be in header
		return $s;

	}

	function loadCoordinatesJS() {
		$document	= Factory::getDocument();
		$document->addScript(Uri::root(true).'/media/com_phocamaps/js/administrator/coordinates.js');
	}

	function loadGeoXMLJS() {

		return "";// GeoXML is not more used
		//$document	= JFactory::getDocument();
		//$document->addScript(JUri::root(true).'/components/com_phocamaps/assets/js/geoxml3.js');
		//$document->addScript(JUri::root(true).'/components/com_phocamaps/assets/js/ProjectedOverlay.js');
	}
	function loadBase64JS() {
		$document	= Factory::getDocument();
		$document->addScript(Uri::root(true).'/media/com_phocamaps/js/base64.js');
	}

	function addAjaxAPI($type = 'maps', $version = '3.x', $params = '') {

		return ""; // backward compatibility

		/* google.load("maps", "3.x", {"other_params":"sensor=false"}); */
		/*$js = 'function initMap() {'."\n"
			 .'   '.$this->_tst.'.setAttribute("oldValue'.$this->_id.'",0);'."\n"
		     .'   '.$this->_tst.'.setAttribute("refreshMap'.$this->_id.'",0);'."\n"
		     .'   '.$this->_tstint.' = setInterval("CheckPhocaMap'.$this->_id.'()",500);'."\n"
			.'}'."\n";
			//.'google.setOnLoadCallback(initMap);'."\n";



		return $js;

		/*if ($params == '') {
			return ' google.load("'.$type.'", "'.$version.'");'."\n";
		} else {
			return ' google.load("'.$type.'", "'.$version.'", '.$params.');'."\n";
		}*/
	}

	/*
	 * Create whole map (e.g. Map View)
	 */
	function createMap($name, $map, $latlng, $options, $tst, $tstint, $geocoder = FALSE, $globalMarker = FALSE, $direction = FALSE) {
		$this->_name	= $name . $this->_id;
		$this->_map 	= $map . $this->_id;
		$this->_latlng 	= $latlng . $this->_id;
		$this->_options = $options . $this->_id;
		$this->_tst 	= $tst . $this->_id;
		$this->_tstint 	= $tstint . $this->_id;
		$this->_markers = 'markers'. $map . $this->_id;

		$js = "\n" . ' var '.$this->_tst .' = document.getElementById(\''.$this->_name .'\');'."\n";

		$js .=' var '.$this->_tstint.';'."\n"
			 .' var '.$this->_map.';'."\n"
			 .' var '.$this->_markers.' = [];'."\n";

		if ($geocoder) {
			$this->_geocoder	= 'phocaGeoCoder'. $this->_id;
			$js .=	 ' var '.$this->_geocoder.';'."\n";
		}

		if ($globalMarker) {
			$this->_marker	= 'markerPhocaMarkerGlobal'. $this->_id;
			$this->_window	= 'infoPhocaWindowGlobal'. $this->_id;
			$js .= ' var '.$this->_marker.';'."\n";
			$js .= ' var '.$this->_window.';'."\n";

		}

		if ($direction) {
			$this->_dirdisplay = 'phocaDirDisplay'. $this->_id;
			$this->_dirservice = 'phocaDirService'. $this->_id;
			$js .= ' var '.$this->_dirdisplay.';'."\n";
			$js .= ' var '.$this->_dirservice.';'."\n";
		}
		return $js . "\n\n";
	}

	/*
	 * Create only direction (e.g. Route View)
	 */
	 function createDirection($name) {
		$this->_name		= $name. $this->_id;
		$js = '';
		$this->_dirdisplay = 'phocaDirDisplay'. $this->_id;
		$this->_dirservice = 'phocaDirService'. $this->_id;
		$js .= ' var '.$this->_dirdisplay.';'."\n";
		$js .= ' var '.$this->_dirservice.';'."\n";
		return $js . "\n\n";
	}

	function setCloseOpenedWindow() {
		return 'var PhocaOpenedWindow;';
	}

	function setMap() {
		// Not var as the map is global variable so not disable the global effect
		return $this->_map.' = new google.maps.Map(document.getElementById(\''.$this->_name.'\'), '.$this->_options.');'."\n";

	}

	function setDirectionDisplayService($directionPanel = 'PhocaDir') {
		$js = '';
		if ($this->_dirdisplay && $this->_dirservice) {
			$js .= ' '.$this->_dirservice.' = new google.maps.DirectionsService();'."\n";
			$js .= ' '.$this->_dirdisplay.' = new google.maps.DirectionsRenderer();'."\n";
			$js .= ' '.$this->_dirdisplay.'.setMap('.$this->_map.');'."\n";
			$js .= ' '.$this->_dirdisplay.'.setPanel(document.getElementById("'.$directionPanel.$this->_id.'"));'."\n";
		}
		return $js;
	}


	function setLatLng($latitude, $longitude) {
		return ' var '.$this->_latlng.' = new google.maps.LatLng('.PhocaMapsHelper::filterValue($latitude, 'number2') .', '. PhocaMapsHelper::filterValue($longitude, 'number2') .');'."\n";
	}


	function startMapOptions() {
		return ' var '.$this->_options.' = {'."\n";
	}

	function endMapOptions ($customOptions = ''){

		$o = '';
		if ($customOptions != '') {

			$o .= "\n" . ', '. strip_tags($customOptions);
		}

		$o .= ' };'."\n\n";
		return $o;
		//return ',tilt:0 };'."\n\n";
	}

	// Options
	function setMapOption($option, $value, $trueOrFalse = FALSE) {
		$js = '';
		if (!$trueOrFalse) {
			if ($value == '') {
				$js .= '   '.$option.': \'\'';
			} else {
				$js .= '   '.$option.': '.$value;
			}

		} else {
			if ($value == 0) {
				$js .= '   '.$option.': false';
			} else {
				$js .= '   '.$option.': true';
			}
		}
		return $js;
	}


	function setCenterOpt($comma = FALSE) {
		return '   center: '.$this->_latlng;
	}

	function setTypeControlOpt( $typeControl = 1, $typeControlPosition = 3 ) {
		$output = '';
		if ($typeControl == 0) {
			$output = 'mapTypeControl: false';
		} else {
			switch($typeControl) {
				case 2:
					$type = 'HORIZONTAL_BAR';
				break;
				case 3:
					$type = 'DROPDOWN_MENU';
				break;
				default:
				case 1:
					$type = 'DEFAULT';
				break;
			}

			$output = '   mapTypeControl: true,'."\n"
					 .'   mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.'.$type;

			if ((int)$typeControlPosition > 0) {
				$typePosition = $this->_setTypeControlPositionOpt($typeControlPosition);
				$output .= ', ' . "\n" . '   position: google.maps.ControlPosition.'.$typePosition;
			}
			$output	 .= ' }';

		}
		return $output;
	}


	function _setTypeControlPositionOpt( $typeControlPosition = 3 ) {
		$output = '';
		switch($typeControlPosition) {
			case 1:
				$output = 'TOP';
			break;
			case 2:
				$output = 'TOP_LEFT';
			break;
			case 4:
				$output = 'BOTTOM';
			break;
			case 5:
				$output = 'BOTTOM_LEFT';
			break;
			case 6:
				$output = 'BOTTOM_RIGHT';
			break;
			case 7:
				$output = 'LEFT';
			break;
			case 8:
				$output = 'RIGHT';
			break;

			default:
			case 3:
				$output = 'TOP_RIGHT';
			break;
		}
		return $output;
	}

	function setNavigationControlOpt( $navControl = 1) {
		$output = '';
		if ($navControl == 0) {
			$output = '   navigationControl: false';
		} else {
			switch($navControl) {
				case 2:
					$type = 'SMALL';
				break;
				case 3:
					$type = 'ZOOM_PAN';
				break;
				case 4:
					$type = 'ANDROID';
				break;
				default:
				case 1:
					$type = 'DEFAULT';
				break;
			}

			$output = '   navigationControl: true,'."\n"
					 .'   navigationControlOptions: {style: google.maps.NavigationControlStyle.'.$type.'}';
		}
		return $output;
	}


	function setMapTypeOpt( $mapType = 0 ) {
		$output = '';

		switch((int)$mapType) {
			case 1:
				$type = 'SATELLITE';
			break;
			case 2:
				$type = 'HYBRID';
			break;
			case 3:
				$type = 'TERRAIN';
			break;
			default:
			case 0:
				$type = 'ROADMAP';
			break;
		}

		$output = '   mapTypeId: google.maps.MapTypeId.'.$type;
		return $output;
	}


	function setMarker($name, $title, $description, $latitude, $longitude, $icon = 0, $iconId = '', $text = '', $width = '', $height = '', $open = 0, $iconShadow = 0, $iconShape = 0, $closeOpenedWindow = 0) {
		jimport('joomla.filter.output');

		$paramsC 	= ComponentHelper::getParams('com_phocamaps');
		$marker_clustering 		= $paramsC->get( 'marker_clustering', 0 );
		//phocagalleryimport('phocagallery.text.text');

		$style = '';
		if ($width != '') {
			$style .= 'width: '.(int)$width.'px;';
		}
		if ($height != '') {
			$style .= 'height: '.(int)$height.'px;';
		}



		$output = '';
		if ($text == '') {
			if ($title != ''){
				$hStyle = 'font-size:120%;margin: 5px 0px;font-weight:bold;';
				$text .= '<div style="'.$hStyle.'">' . PhocaMapsHelper::filterValue($title, 'text') . '</div>';
			}
			if ($description != '') {
				$text .=  '<div>'.PhocaMapsHelper::strTrimAll(PhocaMapsHelper::filterValue($description, 'text')).'</div>';
			}
		}

		if ($style != '') {
			$text = '<div style="'.$style.'">' . $text . '</div>';
		}

		$output .= ' var phocaPoint'.$name.$this->_id.' = new google.maps.LatLng('. PhocaMapsHelper::filterValue($latitude, 'number2').', ' .PhocaMapsHelper::filterValue($longitude, 'number2').');'."\n";

		// Global Marker is defined, don't define var here - the marker markerPhocaMarkerGlobal is defined in the beginning
		if ($name == 'Global') {
			$output .= ' markerPhocaMarker'.$name.$this->_id.' = new google.maps.Marker({title:"'.PhocaMapsHelper::filterValue($title, 'text').'"'."\n";
		} else {
			$output .= ' var markerPhocaMarker'.$name.$this->_id.' = new google.maps.Marker({' ."\n" . ' title:"'.PhocaMapsHelper::filterValue($title, 'text').'"';
		}

		if ($icon == 1) {
			$output .= ', '."\n".'   icon:phocaImage'.$iconId.$this->_id;
			if ($iconShadow == 1) {
				$output .= ', '."\n".'   shadow:phocaImageShadow'.$iconId.$this->_id;
			}
			if ($iconShape == 1) {
				$output .= ', '."\n".'   shape:phocaImageShape'.$iconId.$this->_id;
			}
		}

		$output .= ', '."\n".'   position: phocaPoint'.$name . $this->_id;
		$output .= ', '."\n".'   map: '.$this->_map."\n";
		$output .= ' });'."\n";

		// Push all markers to one array (because of possible clustering)
		if ($marker_clustering == 1) {
			$output .= $this->_markers . '.push(markerPhocaMarker' . $name . $this->_id . ');' . "\n";
		}


	/*	if ($name == 'Global') {
			$output .= ' infoPhocaWindow'.$name.$this->_id.' = new google.maps.InfoWindow({'."\n";
		} else {
			$output .= ' var infoPhocaWindow'.$name.$this->_id.' = new google.maps.InfoWindow({'."\n";
		}
		$output .= '   content: \''.$text.'\''."\n"
				  .' });'."\n";

		if ($closeOpenedWindow == 0) {
			$output .= ' google.maps.event.addListener(markerPhocaMarker'.$name.$this->_id.', \'click\', function() {'."\n"
			.'   infoPhocaWindow'.$name.$this->_id.'.open('.$this->_map.', markerPhocaMarker'.$name.$this->_id.' );'."\n"
			.' });'."\n";
		} else {
			$output .= ' google.maps.event.addListener(markerPhocaMarker'.$name.$this->_id.', \'click\', function() {'."\n"
			.'   if(PhocaOpenedWindow) PhocaOpenedWindow.close();'."\n"
			.'   infoPhocaWindow'.$name.$this->_id.'.open('.$this->_map.', markerPhocaMarker'.$name.$this->_id.' );'."\n"
			.'   PhocaOpenedWindow = infoPhocaWindow'.$name.$this->_id."\n"
			.' });'."\n";
		}

		if ($open) {
			$output .= '   google.maps.event.trigger(markerPhocaMarker'.$name.$this->_id.', \'click\');'."\n";
		}
		return $output;*/

		if($open != 2){
			if ($name == 'Global') {
				$output .= ' infoPhocaWindow'.$name.$this->_id.' = new google.maps.InfoWindow({'."\n";
			} else {
				$output .= ' var infoPhocaWindow'.$name.$this->_id.' = new google.maps.InfoWindow({'."\n";
			}
			$output .= '   content: \''.$text.'\''."\n"
					  .' });'."\n";

			if ($closeOpenedWindow == 0) {
				$output .= ' google.maps.event.addListener(markerPhocaMarker'.$name.$this->_id.', \'click\', function() {'."\n"
				.'   infoPhocaWindow'.$name.$this->_id.'.open('.$this->_map.', markerPhocaMarker'.$name.$this->_id.' );'."\n"
				.' });'."\n";
			} else {
				$output .= ' google.maps.event.addListener(markerPhocaMarker'.$name.$this->_id.', \'click\', function() {'."\n"
				.'   if(PhocaOpenedWindow) PhocaOpenedWindow.close();'."\n"
				.'   infoPhocaWindow'.$name.$this->_id.'.open('.$this->_map.', markerPhocaMarker'.$name.$this->_id.' );'."\n"
				.'   PhocaOpenedWindow = infoPhocaWindow'.$name.$this->_id."\n"
				.' });'."\n";
			}

			if ($open) {
				$output .= '   google.maps.event.trigger(markerPhocaMarker'.$name.$this->_id.', \'click\');'."\n";
			}
 		}

 		return $output;
	}

	/*
	 * Icon has no this->_id as this will be set in Marker
	 */

	function setMarkerIcon($icon, $iconExt = 0, $extIconUrl = '', $iObj = '', $extIconShadowUrl = '', $iObjShadow = '', $iObjShape = '') {

		$output['icon']	= 0;
		$output['js'] 	= '';
		$output['iconshadow'] = 0;
		$output['iconshape'] = 0;

		if ((int)$iconExt > 0) {
			// EXTERNAL ICON
			$js =' var phocaImage'.$iconExt.$this->_id.' = new google.maps.MarkerImage(\''.$extIconUrl.'\'';
			if ($iObj != '') {
				$iObjA = explode(';', $iObj);
				$js.=',';
				if (isset($iObjA[0])) {$js.=' new google.maps.Size('.$iObjA[0].'),'."\n";} else { $js.=' \'\',';}
				if (isset($iObjA[1])) {$js.=' new google.maps.Point('.$iObjA[1].'),'."\n";}else { $js.=' \'\',';}
				if (isset($iObjA[2])) {$js.=' new google.maps.Point('.$iObjA[2].')'."\n";}else { $js.=' \'\'';}
			}
			$js.= ');'. "\n";

			if ($extIconShadowUrl != '') {
				$js.=' var phocaImageShadow'.$iconExt.$this->_id.' = new google.maps.MarkerImage(\''.$extIconShadowUrl.'\'';
				if ($iObjShadow != '') {
					$iObjAS = explode(';', $iObjShadow);
					$js.=',';
					if (isset($iObjAS[0])) {$js.=' new google.maps.Size('.$iObjAS[0].'),'."\n";} else { $js.=' \'\',';}
					if (isset($iObjAS[1])) {$js.=' new google.maps.Point('.$iObjAS[1].'),'."\n";}else { $js.=' \'\',';}
					if (isset($iObjAS[2])) {$js.=' new google.maps.Point('.$iObjAS[2].')'."\n";}else { $js.=' \'\'';}

				}
				$js.= ');'. "\n";
				$output['iconshadow'] = 1;
			} else {
				//$js.=' var phocaImageShadow'.$iconExt.$this->_id.' = new google.maps.MarkerImage();'."\n";
			}


			if ($iObjShape != '') {
				$js.=' var phocaImageShape'.$iconExt.$this->_id.' = {'."\n";
				$iObjSh = explode(';', $iObjShape);
				if (isset($iObjSh[1])) {$js.='   coord: ['.$iObjSh[1].'],'."\n";} else { $js.=' \'\',';}
				if (isset($iObjSh[0])) {$js.='   type: \''.$iObjSh[0].'\' '."\n";} else { $js.=' \'\' ';}
				$js.=' };'."\n";
				$output['iconshape'] = 1;

			}

			$output['icon']		= 1;
			$output['js'] 		= $js;
			$output['iconid'] 	= $iconExt;

		} else if ((int)$icon > 0) {
			// DEFAULT ICON
			$i = PhocaMapsIcon::getIconData($icon);
			$icon = 'default'.$icon;// Add specific prefix to not conflict with external icons

			$imagePath = Uri::root(true).'/media/com_phocamaps/images/'.$i['name'].'/';

			$js =' var phocaImage'.$icon.$this->_id.' = new google.maps.MarkerImage(\''.$imagePath.'image.png\','."\n";
			$js.=' new google.maps.Size('.$i['size'].'),'."\n";
			$js.=' new google.maps.Point('.$i['point1'].'),'."\n";
			$js.=' new google.maps.Point('.$i['point2'].'));'."\n";

			$js.=' var phocaImageShadow'.$icon.$this->_id.' = new google.maps.MarkerImage(\''.$imagePath.'shadow.png\','."\n";
			$js.=' new google.maps.Size('.$i['sizes'].'),'."\n";
			$js.=' new google.maps.Point('.$i['point1s'].'),'."\n";
			$js.=' new google.maps.Point('.$i['point2s'].'));'."\n";


			$js.=' var phocaImageShape'.$icon.$this->_id.' = {'."\n";
			$js.='   coord: '.$i['cord'].','."\n";
			$js.='   type: \''.$i['type'].'\' '."\n";
			$js.=' };'."\n";

			$output['icon']		= 1;
			$output['js'] 		= $js;
			$output['iconid'] 	= $icon;
			$output['iconshadow'] = 1;
			$output['iconshape'] = 1;
		} else {
			$output['icon']		= 0;
			$output['js'] 		= '';
			// Make the icon ID so if e.g. more markers are using the same icon,
			// don't create for every marker instance ($this->_id is not used as this info goes back)
			$output['iconid'] 	= 0;
			$output['iconshadow'] = 0;
			$output['iconshape'] = 0;
		}

		return $output;
	}

	function outputMarkerJs($output, $icon, $iconExt) {
		if((int)$iconExt > 0) {
			if (!in_array($iconExt, $this->_iconArray)) {
				$this->_iconArray[] = $iconExt;
				return $output;
			} else {
				return '';
			}
		} else {
			if (!in_array('default'.$icon, $this->_iconArray)) {
				$this->_iconArray[] = 'default'.$icon;
				return $output;
			} else {
				return '';
			}
		}
		return '';
	}


	function setMarkerClusterer() {

		/*$js = ' var markerCluster'.$this->_id.'Styles = [
    MarkerClusterer.withDefaultStyle({
          url: "'.Uri::root(true).'/media/com_phocamaps/images/markerclusterer/m'.'",
          width: 56,
          height:56,
          textSize:25,
          textColor:"white",
          anchorText: [-4, 0]
    })];';
		$js .= ' var markerCluster'.$this->_id.'Options = {styles: markerCluster'.$this->_id.'Styles, gridSize: 50, maxZoom: 14,imagePath: "'.JUri::root(true).'/media/com_phocamaps/images/markerclusterer/m'.'"};';*/

		$paramsC 	= ComponentHelper::getParams('com_phocamaps');
		$marker_clustering 		= $paramsC->get( 'marker_clustering', 0 );
		$js = '';
		if ($marker_clustering == 1) {
			$js = ' var markerCluster' . $this->_id . 'Options = {averageCenter: true, gridSize: 50, maxZoom: 14, imagePath: "' . Uri::root(true) . '/media/com_phocamaps/images/markerclusterer/m' . '"};';
			$js .= ' var markerCluster' . $this->_id . ' = new MarkerClusterer(' . $this->_map . ', ' . $this->_markers . ', markerCluster' . $this->_id . 'Options );' . "\n";
		}
		return $js;
	}


	/* We have divided one function into two:
	* setInitializeFunctionSpecificMap() + setInitializeFunction()
	* because of more instances in plugin
	* Google Maps API does not like to load the api script more times
	* so we have only one initMaps() which then:
	*  - in component calls initMap() only once
	*  - in plugin calls initMap1(), initMap2(), initMap3() - more times
	* All calls will be set at the end of all plugin instances in body function initMaps()
	* Be aware - in blog view we get more articles displayed togehter
	* it is not possible to collect the initMap1(), initMap2(), initMap3() of more articles together
	* so we call the api more times - for each article and this throws google maps api warning but without
	* this calling maps will be not displayed
	*              Article1               Aritcle2
	*                /\                      /\
    *         plugin1 plugin2	    plugin1(3) plugin2(4)
	* we can work with more instances in one article so we collect the function calls to one block
	* but we cannot do the same for more instances of article
	*/

	function setInitializeFunctionSpecificMap() {
		$js = 'function initMap'.$this->_id.'() {'."\n"
				 .'   '.$this->_tst.'.setAttribute("oldValue'.$this->_id.'",0);'."\n"
				 .'   '.$this->_tst.'.setAttribute("refreshMap'.$this->_id.'",0);'."\n"
				 .'   '.$this->_tstint.' = setInterval("CheckPhocaMap'.$this->_id.'()",500);'."\n"
				.'}'."\n";
		return $js;
	}

	function setInitializeFunction() {

		$js = 'function initMaps() {'."\n";
			$js .= '   '.'initMap'.$this->_id.'();'."\n";
		$js .= '}'."\n";
		return $js;
	}



	function setListener() {
		$js = ' google.maps.event.addDomListener('.$this->_tst.', \'DOMMouseScroll\', CancelEventPhocaMap'.$this->_id.');'."\n"
		     .' google.maps.event.addDomListener('.$this->_tst.', \'mousewheel\', CancelEventPhocaMap'.$this->_id.');';
		return $js;
	}

	function checkMapFunction() {
		$js =' function CheckPhocaMap'.$this->_id.'() {'."\n"
			.'   if ('.$this->_tst.') {'."\n"
			.'      if ('.$this->_tst.'.offsetWidth != '.$this->_tst.'.getAttribute("oldValue'.$this->_id.'")) {'."\n"
			.'         '.$this->_tst.'.setAttribute("oldValue'.$this->_id.'",'.$this->_tst.'.offsetWidth);'."\n"
			.'             if ('.$this->_tst.'.getAttribute("refreshMap'.$this->_id.'")==0) {'."\n"
			.'                if ('.$this->_tst.'.offsetWidth > 0) {'."\n"
			.'                   clearInterval('.$this->_tstint.');'."\n"
			.'                   getPhocaMap'.$this->_id.'();'."\n"
			.'                  '.$this->_tst.'.setAttribute("refreshMap'.$this->_id.'", 1);'."\n"
			.'                } '."\n"
			.'             }'."\n"
			.'         }'."\n"
			.'     }'."\n"
			.' }'."\n\n";
		return $js;
	}


	function cancelEventFunction() {
		$js =' function CancelEventPhocaMap'.$this->_id.'(event) { '."\n"
			.'   var e = event; '."\n"
			.'   if (typeof e.preventDefault == \'function\') e.preventDefault(); '."\n"
			.'   if (typeof e.stopPropagation == \'function\') e.stopPropagation(); '."\n"
			.'   if (window.event) { '."\n"
			.'      window.event.cancelBubble = true; /* for IE */'."\n"
			.'      window.event.returnValue = false; /* for IE */'."\n"
			.'   } '."\n"
			.' }'."\n\n";
		return $js;
	}

	function startMapFunction() {
		$js = ' function getPhocaMap'.$this->_id.'(){'."\n"
			 .'   if ('.$this->_tst.'.offsetWidth > 0) {'."\n\n";
		return $js;
	}

	function endMapFunction() {
		$js = '   }'."\n"
			 .' }'."\n\n";
		return $js;
	}

	function setGeoCoder() {
		$js = $this->_geocoder .' = new google.maps.Geocoder();'."\n";
		return $js;
	}
	/*
	function exportZoom($zoom, $value = '', $jForm = '') {
		$js ='var phocaStartZoom 	= '.$zoom.';'."\n"
			.'var phocaZoom 		= null;'."\n"
			.'google.maps.event.addListener('.$this->_map.', "zoom_changed", function(phocaStartZoom, phocaZoom) {'."\n"
			.'phocaZoom = '.$this->_map.'.getZoom();'."\n";
			if ($value != '') {
				$js .= '   '.$value.'.value = phocaZoom;'."\n";
			} else if ($jForm != '') {
				$js .= '   if (window.parent) window.parent.'.$jForm.'(phocaZoom);'."\n";
			}
			$js .= '});'."\n";
		return $js;
	}*/

	function exportZoom($zoom, $value, $jForm = '') {
		$js =' var phocaStartZoom'.$this->_id.' = '.$zoom.';'."\n"
			.' var phocaZoom'.$this->_id.' = null;'."\n"
			.' google.maps.event.addListener('.$this->_map.', "zoom_changed", function(phocaStartZoom'.$this->_id.', phocaZoom'.$this->_id.') {'."\n"
			.'   phocaZoom'.$this->_id.' = '.$this->_map.'.getZoom();'."\n";

			if ($value != '') {
				$js .= '   '.$value.'.value = phocaZoom'.$this->_id.';'."\n";// value has no id (used in admin)
			} else if ($jForm != '') {
				$js .= '   if (window.parent) window.parent.'.$jForm.'(phocaZoom'.$this->_id.');'."\n";
			}
			$js .= '});'."\n";
		return $js;
	}


	function exportMarker($name, $type, $latitude, $longitude, $valueLat = '', $valueLng = '', $jFormLat = '', $jFormLng = '', $jFormLatGPS = '', $jFormLngGPS = '') {

		$js = ' var phocaPoint'.$name.$this->_id.' = new google.maps.LatLng('. PhocaMapsHelper::filterValue($latitude, 'number2').', ' .PhocaMapsHelper::filterValue($longitude, 'number2').');'."\n";

		if ($name == 'Global') {
			$js .= ' markerPhocaMarker'.$name.$this->_id.' = new google.maps.Marker({'."\n";
		} else {
			$js .= ' var markerPhocaMarker'.$name.$this->_id.' = new google.maps.Marker({'."\n";
		}
		$js	.= '   position: phocaPoint'.$name.$this->_id.','."\n"
			  .'   map: '.$this->_map.','."\n"
			  .'   draggable: true'."\n"
		      .' });'."\n\n";

		if ($name == 'Global') {
			$js .= ' infoPhocaWindow'.$name.$this->_id.' = new google.maps.InfoWindow({'."\n";
		} else {
			$js .= ' var infoPhocaWindow'.$name.$this->_id.' = new google.maps.InfoWindow({'."\n";
		}

		$js .='   content: markerPhocaMarker'.$name.$this->_id.'.getPosition().toUrlValue(6)'."\n"
			  .' });'."\n\n";



		// Events
		$js .= ' google.maps.event.addListener(markerPhocaMarker'.$name.$this->_id.', \'dragend\', function() {'."\n"
			.'   var phocaPointTmp'.$this->_id.' = markerPhocaMarker'.$name.$this->_id.'.getPosition();'."\n"
			.'   markerPhocaMarker'.$name.$this->_id.'.setPosition(phocaPointTmp'.$this->_id.');'."\n"
			.'   closeMarkerInfo'.$name.$this->_id.'();'."\n"
			.'   exportPoint'.$name.$this->_id.'(phocaPointTmp'.$this->_id.');'."\n"
			.' });'."\n\n";

		// The only one place which needs to be edited to work with more markers
		// Comment it for working with more markers
		// Or add new behaviour to work with adding new marker to the map
		$js .= ' google.maps.event.addListener('.$this->_map.', \'click\', function(event) {'."\n"
			.'   var phocaPointTmp2'.$this->_id.' = event.latLng;'."\n"
			.'   markerPhocaMarker'.$name.$this->_id.'.setPosition(phocaPointTmp2'.$this->_id.');'."\n"
			.'   closeMarkerInfo'.$name.$this->_id.'();'."\n"
			.'   exportPoint'.$name.$this->_id.'(phocaPointTmp2'.$this->_id.');'."\n"
		   .' });'."\n\n";

		$js .= ' google.maps.event.addListener(markerPhocaMarker'.$name.$this->_id.', \'click\', function(event) {'."\n"
				.'   openMarkerInfo'.$name.$this->_id.'();'."\n"
				.' });'."\n\n";

		$js .= ' function openMarkerInfo'.$name.$this->_id.'() {'."\n"
				.'   infoPhocaWindow'.$name.$this->_id.'.content = markerPhocaMarker'.$name.$this->_id.'.getPosition().toUrlValue(6);'."\n"
				.'   infoPhocaWindow'.$name.$this->_id.'.open('.$this->_map.', markerPhocaMarker'.$name.$this->_id.' );'."\n"
				.' }'."\n\n";
		$js .= ' function closeMarkerInfo'.$name.$this->_id.'() {'."\n"
				.'   infoPhocaWindow'.$name.$this->_id.'.close('.$this->_map.', markerPhocaMarker'.$name.$this->_id.' );'."\n"
				.' }'."\n\n";



		$js .= 'function exportPoint'.$name.$this->_id.'(phocaPointTmp3'.$this->_id.') {'."\n";
		if ($valueLat != '') {
			$js .= '   '.$valueLat.'.value = phocaPointTmp3'.$this->_id.'.lat();'."\n";
		}
		if ($valueLng != '') {
			$js .= '   '.$valueLng.'.value = phocaPointTmp3'.$this->_id.'.lng();'."\n";
		}

		if ($jFormLat != '') {
			$js .= '   if (window.parent) window.parent.'.$jFormLat.'(phocaPointTmp3'.$this->_id.'.lat());'."\n";
		}
		if ($jFormLng != '') {
			$js .= '   if (window.parent) window.parent.'.$jFormLng.'(phocaPointTmp3'.$this->_id.'.lng());'."\n";
		}

		if ($type == 'marker') {

			if ($valueLat != '') {
				$js .='   setPMGPSLatitude(phocaPointTmp3'.$this->_id.'.lat());'."\n";// no id - global function
			}
			if ($valueLng != '') {
				$js .='   setPMGPSLongitude(phocaPointTmp3'.$this->_id.'.lng());'."\n";// no id - global function
			}

			if ($jFormLatGPS != '') {
				//$js .= '   setPMGPSLatitudeJForm(\''.$idLatGPS.'\', phocaPointTmp3'.$this->_id.'.lat());'."\n";
				$js .= '   if (window.parent) setPMGPSLatitudeJForm( phocaPointTmp3'.$this->_id.'.lat());'."\n";
			}
			if ($jFormLngGPS != '') {
				//$js .= '   setPMGPSLongitudeJForm(\''.$idLngGPS.'\', phocaPointTmp3'.$this->_id.'.lng());'."\n";
				$js .= '   if (window.parent) setPMGPSLongitudeJForm( phocaPointTmp3'.$this->_id.'.lng());'."\n";
			}
		}
		$js.=' }'."\n\n";

		return $js;
	}


	/*
	function exportMarker($name, $type, $latitude, $longitude, $valueLat, $valueLng) {

		$js = ' var phocaPoint'.$name.$this->_id.' = new google.maps.LatLng('. $latitude.', ' .$longitude.');'."\n";

		if ($name == 'Global') {
			$js .= ' markerPhocaMarker'.$name.$this->_id.' = new google.maps.Marker({'."\n";
		} else {
			$js .= ' var markerPhocaMarker'.$name.$this->_id.' = new google.maps.Marker({'."\n";
		}
		$js	.= '   position: phocaPoint'.$name.$this->_id.','."\n"
			  .'   map: '.$this->_map.','."\n"
			  .'   draggable: true'."\n"
		      .' });'."\n\n";

		if ($name == 'Global') {
			$js .= ' infoPhocaWindow'.$name.$this->_id.' = new google.maps.InfoWindow({'."\n";
		} else {
			$js .= ' var infoPhocaWindow'.$name.$this->_id.' = new google.maps.InfoWindow({'."\n";
		}

		$js .='   content: markerPhocaMarker'.$name.$this->_id.'.getPosition().toUrlValue(6)'."\n"
			  .' });'."\n\n";

		// Events
		$js .= ' google.maps.event.addListener(markerPhocaMarker'.$name.$this->_id.', \'dragend\', function() {'."\n"
			.'   var phocaPointTmp'.$this->_id.' = markerPhocaMarker'.$name.$this->_id.'.getPosition();'."\n"
			.'   markerPhocaMarker'.$name.$this->_id.'.setPosition(phocaPointTmp'.$this->_id.');'."\n"
			.'   closeMarkerInfo'.$name.$this->_id.'();'."\n"
			.'   exportPoint'.$name.$this->_id.'(phocaPointTmp'.$this->_id.');'."\n"
			.' });'."\n\n";

		// The only one place which needs to be edited to work with more markers
		// Comment it for working with more markers
		// Or add new behaviour to work with adding new marker to the map
		$js .= ' google.maps.event.addListener('.$this->_map.', \'click\', function(event) {'."\n"
			.'   var phocaPointTmp2'.$this->_id.' = event.latLng;'."\n"
			.'   markerPhocaMarker'.$name.$this->_id.'.setPosition(phocaPointTmp2'.$this->_id.');'."\n"
			.'   closeMarkerInfo'.$name.$this->_id.'();'."\n"
			.'   exportPoint'.$name.$this->_id.'(phocaPointTmp2'.$this->_id.');'."\n"
		   .' });'."\n\n";

		$js .= ' google.maps.event.addListener(markerPhocaMarker'.$name.$this->_id.', \'click\', function(event) {'."\n"
				.'   openMarkerInfo'.$name.$this->_id.'();'."\n"
				.' });'."\n\n";

		$js .= ' function openMarkerInfo'.$name.$this->_id.'() {'."\n"
				.'   infoPhocaWindow'.$name.$this->_id.'.content = markerPhocaMarker'.$name.$this->_id.'.getPosition().toUrlValue(6);'."\n"
				.'   infoPhocaWindow'.$name.$this->_id.'.open('.$this->_map.', markerPhocaMarker'.$name.$this->_id.' );'."\n"
				.' }'."\n\n";
		 $js .= ' function closeMarkerInfo'.$name.$this->_id.'() {'."\n"
				.'   infoPhocaWindow'.$name.$this->_id.'.close('.$this->_map.', markerPhocaMarker'.$name.$this->_id.' );'."\n"
				.' }'."\n\n";

		$js .= ' function exportPoint'.$name.$this->_id.'(phocaPointTmp3'.$this->_id.') {'."\n"
				.'   '.$valueLat.'.value = phocaPointTmp3'.$this->_id.'.lat();'."\n"  // valueLat has no id (used in admin)
				.'   '.$valueLng.'.value = phocaPointTmp3'.$this->_id.'.lng();'."\n"; // valueLng has no id (used in admin)
		if ($type == 'marker') {
			$js .='   setPMGPSLatitude(phocaPointTmp3'.$this->_id.'.lat());'."\n"// no id - global function
				 .'   setPMGPSLongitude(phocaPointTmp3'.$this->_id.'.lng());'."\n";// no id - global function
		}
		$js.=' }'."\n\n";

		return $js;
	}*/

	function addAddressToMapFunction($name, $elementId = 'phocaAddressEl', $type = '', $valueLat = '', $valueLng = '', $jFormLat = '', $jFormLng = '', $jFormLatGPS = '', $jFormLngGPS = '' ) {
		$js ='function addAddressToMap'.$this->_id.'() {'."\n"
		.'   var phocaAddress'.$this->_id.' = document.getElementById("'.$elementId.$this->_id.'").value;'."\n"
		.'   if ('.$this->_geocoder.') {'."\n"
		.'      '.$this->_geocoder.'.geocode( { \'address\': phocaAddress'.$this->_id.'}, function(results'.$this->_id.', status'.$this->_id.') {'."\n"
		.'         if (status'.$this->_id.' == google.maps.GeocoderStatus.OK) {'."\n"
		.'            var phocaLocation'.$this->_id.' = results'.$this->_id.'[0].geometry.location;'."\n"
		.'            var phocaLocationAddress'.$this->_id.' = results'.$this->_id.'[0].formatted_address'."\n"
		.'            '.$this->_map.'.setCenter(phocaLocation'.$this->_id.');'."\n"
		.'            markerPhocaMarker'.$name.$this->_id.'.setPosition(phocaLocation'.$this->_id.');'."\n"
		.'            infoPhocaWindow'.$name.$this->_id.'.content = \'<div>\'+ phocaLocationAddress'.$this->_id.' +\'</div><div>&nbsp;</div><div>\'+ phocaLocation'.$this->_id.' +\'</div>\';'."\n"
		.'            infoPhocaWindow'.$name.$this->_id.'.open('.$this->_map.', markerPhocaMarker'.$name.$this->_id.' );'."\n";

		if ($valueLat != '') {
			$js .= '            '.PhocaMapsHelper::filterValue($valueLat).'.value = phocaLocation'.$this->_id.'.lat();'."\n";// valueLat has no id (used in admin)
		}
		if ($valueLng != '') {
			$js .= '            '.PhocaMapsHelper::filterValue($valueLat).'.value = phocaLocation'.$this->_id.'.lng();'."\n";// valueLat has no id (used in admin)
		}

		if ($jFormLat != '') {
			$js .= '            if (window.parent) window.parent.'.PhocaMapsHelper::filterValue($jFormLat).'(phocaLocation'.$this->_id.'.lat());'."\n";
		}
		if ($jFormLng != '') {
			$js .= '            if (window.parent) window.parent.'.PhocaMapsHelper::filterValue($jFormLng).'(phocaLocation'.$this->_id.'.lng());'."\n";
		}




		if ($type == 'marker') {

			if ($valueLat != '') {
				$js .='   setPMGPSLatitude(phocaLocation'.$this->_id.'.lat());'."\n";// no id - global function
			}
			if ($valueLng != '') {
				$js .='   setPMGPSLongitude(phocaLocation'.$this->_id.'.lng());'."\n";// no id - global function
			}

			if ($jFormLatGPS != '') {
				//$js .= '   setPMGPSLatitudeJForm(\''.$idLatGPS.'\', phocaPointTmp3'.$this->_id.'.lat());'."\n";
				$js .= '   if (window.parent) setPMGPSLatitudeJForm( phocaLocation'.$this->_id.'.lat());'."\n";
			}
			if ($jFormLngGPS != '') {
				//$js .= '   setPMGPSLongitudeJForm(\''.$idLngGPS.'\', phocaPointTmp3'.$this->_id.'.lng());'."\n";
				$js .= '   if (window.parent) setPMGPSLongitudeJForm( phocaLocation'.$this->_id.'.lng());'."\n";
			}
		}

		$js .='         } else {'."\n"
		.'            alert("'.Text::_('COM_PHOCAMAPS_GEOCODE_NOT_FOUND').' (" + status'.$this->_id.' + ")");'."\n"
		.'         }'."\n"
		.'      });'."\n"
		.'   }'."\n"
		.'}'."\n\n";

		return $js;
	}

	function setDirectionFunction($printIcon = 0, $mapId = '', $mapAlias = '', $lang = '') {
		$js ='function setPhocaDir'.$this->_id.'(fromPMAddress'.$this->_id.', toPMAddress'.$this->_id.') {'."\n"
		.'   var request'.$this->_id.' = {'."\n"
		.'      origin:		fromPMAddress'.$this->_id.', '."\n"
		.'      destination:	toPMAddress'.$this->_id.','."\n"
		.'      travelMode: 	google.maps.DirectionsTravelMode.DRIVING'."\n"
		.'   };'."\n\n";

		$js .='   '.$this->_dirservice.'.route(request'.$this->_id.', function(response'.$this->_id.', status'.$this->_id.') {'."\n"
		.'   '."\n"
		.'    if (status'.$this->_id.' == google.maps.DirectionsStatus.OK) {'."\n";

		// In route view we don't need to create link to itself - to route view and we don't need the mapId
		// this is why $mapId = '' is as default in this function
		if($printIcon) {
			$js .='      pPI'.$this->_id.' = document.getElementById(\'phocaMapsPrintIcon'.$this->_id.'\');'. "\n"
				.'      pPI'.$this->_id.'.style.display=\'block\';'. "\n"
				.'      var from64'.$this->_id.' = Base64.encode(fromPMAddress'.$this->_id.').toString();'. "\n"
				.'      var to64'.$this->_id.'   = Base64.encode(toPMAddress'.$this->_id.').toString();'. "\n"
				.'      pPI'.$this->_id.'.innerHTML = \''.$this->getIconPrint($mapId, $mapAlias, $lang).'\';'. "\n\n";
		}

		$js .='      '.$this->_dirdisplay.'.setDirections(response'.$this->_id.');'."\n"
		.'   } else if (google.maps.DirectionsStatus.NOT_FOND) {'."\n"
		.'      alert("'. Text::_('COM_PHOCAMAPS_NOT_FOUND').'");'."\n"
		.'   } else if (google.maps.DirectionsStatus.ZERO_RESULTS) {'."\n"
		.'      alert("'. Text::_('COM_PHOCAMAPS_ZERO_RESULTS').'");'."\n"
		.'   } else if (google.maps.DirectionsStatus.MAX_WAYPOINTS_EXCEEDED) {'."\n"
		.'      alert("'. Text::_('COM_PHOCAMAPS_MAX_WAYPOINTS_EXCEEDED').'");'."\n"
		.'   } else if (google.maps.DirectionsStatus.OVER_QUERY_LIMIT) {'."\n"
		.'      alert("'. Text::_('COM_PHOCAMAPS_OVER_QUERY_LIMIT').'");'."\n"
		.'   } else if (google.maps.DirectionsStatus.INVALID_REQUEST) {'."\n"
		.'      alert("'. Text::_('COM_PHOCAMAPS_INVALID_REQUEST').'");'."\n"
		.'   } else if (google.maps.DirectionsStatus.REQUEST_DENIED) {'."\n"
		.'      alert("'. Text::_('COM_PHOCAMAPS_REQUEST_DENIED').'");'."\n"
		.'   } else if (google.maps.DirectionsStatus.UNKNOWN_ERROR) {'."\n"
		.'      alert("'. Text::_('COM_PHOCAMAPS_UNKNOWN_ERROR').'");'."\n"
		.'   } else {'."\n"
		.'      alert("'. Text::_('COM_PHOCAMAPS_UNKNOWN_ERROR').'");'."\n"
		.'   } '."\n"
		.'  });'."\n"
		.'}'."\n\n";

		return $js;
	}

	function directionInitializeFunctionSpecificMap($from, $to){
		$js ='function initMap'.$this->_id.'() {'."\n"

		/*.'   '.$this->_tst.'.setAttribute("oldValue",0);'."\n"
		.'   '.$this->_tst.'.setAttribute("refreshMap",0);'."\n"
		.'   '.$this->_tstint.' = setInterval("CheckPhocaMap()",500);'."\n"*/

		.'     '.$this->_dirdisplay.' = new google.maps.DirectionsRenderer();'."\n"
		.'     '.$this->_dirservice.' = new google.maps.DirectionsService();'."\n"
		.'     '.$this->_dirdisplay.'.setPanel(document.getElementById("directionsPanel'.$this->_id.'"));'."\n"
		.'     setPhocaDir'.$this->_id.'(\''.htmlspecialchars(base64_decode($from), ENT_QUOTES).'\', \''.htmlspecialchars(base64_decode($to), ENT_QUOTES).'\');'."\n"
		.'}'."\n\n";
		//.'google.setOnLoadCallback(initialize'.$this->_id.');'."\n";
		return $js;

	}

	function directionInitializeFunction() {

		$js = 'function initMaps() {'."\n";
			$js .= '   '.'initMap'.$this->_id.'();'."\n";
		$js .= '}'."\n";
		return $js;
	}


	function getIconPrint($idMap, $idMapAlias = '', $lang = '') {

		$suffix	= 'tmpl=component&print=1';
		//$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
		$status = 'width=640,height=480,menubar=yes,resizable=yes,scrollbars=yes,resizable=yes';

		$link 		= PhocaMapsHelperRoute::getPrintRouteRoute( $idMap, $idMapAlias, $suffix);
		$link		= Route::_( $link );
		$isThereQM 	= false;
		$isThereQM 	= preg_match("/\?/i", $link);

		if ($isThereQM) {
			$amp = '&amp;';
		} else {
			$amp = '?';
		}
		$link	= $link . $amp . 'from=\'+from64'.$this->_id.'+\'&amp;to=\'+to64'.$this->_id.'+\'';

		if ($lang != '') {
			$link = $link . '&amp;lang='.$lang.'';
		}

		$output = '<div class="pmprintroutelink">'
		.'<a href=\\u0022'.$link.'\\u0022 rel=\\u0022nofollow\\u0022 onclick=\\u0022window.open(this.href,\\\'phocaMapRoute\\\',\\\''.$status.'\\\'); return false;\\u0022 >'.Text::_('COM_PHOCAMAPS_PRINT_ROUTE', true, true, false).'</a>'
		.'</div>'
		.'<div style="clear:both"></div>';

		return $output;

	}

	function getIconPrintScreen() {
		$output = '<div class="pmprintscreen"><a class="pmprintscreena" href="javascript: void()" onclick="window.print();return false;">'.Text::_('COM_PHOCAMAPS_PRINT').'</a>'
		.'&nbsp; <a class="pmprintscreena" href="javascript: void window.close()">'.Text::_( 'COM_PHOCAMAPS_CLOSE_WINDOW' ). '</a></div><div style="clear:both;"></div>';
		return $output;
	}
	/*
	function setKMLFile($kmlFile) {
		$js =' var phocaGeoXml'.$this->_id.' = new geoXML3.parser({map: '.$this->_map.'});'."\n"
			.' phocaGeoXml'.$this->_id.'.parse(\''.$kmlFile.'\');'."\n"; // File is checked in View (after loading from Model)
		return $js;
	}
	*/


	function setKMLFile($kmlFile) {


		//$suffix = time() + (10 * 365 * 24 * 60 * 60);
		//$kmlFile = $kmlFile . '?sid='. (string)$suffix;

		//$js = ' var kmlLayer'.$this->_id.' = new google.maps.KmlLayer(\''.$kmlFile.'\');'."\n"
		//		.' kmlLayer'.$this->_id.'.setMap('.$this->_map.');'."\n";

		$js = ' var kmlLayer'.$this->_id.' = new google.maps.KmlLayer({ url: \''.$kmlFile.'\', suppressInfoWindows: true, preserveViewport: true, map: '.$this->_map.'});'."\n";
				//.' kmlLayer'.$this->_id.'.setMap('.$this->_map.');'."\n";
		return $js;
	}
}
?>
