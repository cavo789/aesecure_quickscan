<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

$id		= uniqid();
$map	= new PhocaMapsMapOsm($id);
$map->loadAPI();
if ($this->type == 'marker') {
	$map->loadCoordinatesJS();
}


$map->createMap($this->latitude, $this->longitude, $this->zoom);
$map->setMapType();
$map->setMarker($id, '', '', $this->latitude, $this->longitude);

// Export, Move, Input, renderSearch are dependent
$map->moveMarker();
if ($this->type == 'marker') {
	$map->inputMarker('jform_latitude_id', 'jform_longitude_id', '', 1);
} else {
	$map->inputMarker('jform_latitude_id', 'jform_longitude_id', 'jform_zoom_id');
}
$map->exportMarker($id);
$map->renderSearch($id);

$map->renderFullScreenControl();
$map->renderCurrentPosition($id);

$map->renderMap();

echo '<div id="phocamaps" style="margin:0;padding:0;">';
echo '<div align="center" style="margin:0;padding:0">';
echo '<div id="phocaMap'.$id.'" style="margin:0;padding:0;width:100%;height:97vh"></div></div>';
/*
$marker	= array();
$marker[0] = new stdClass();
$marker[0]->latitude = 40;
$marker[0]->longitude = 12;
$marker[0]->title = 'Marker';
$marker[0]->description = 'Description';


$o = '';
$id = uniqid();

		$data_attributes = 'data-unique="' . $id . '"' .
				  ' data-lon="' . $this->longitude . '"' .
				  ' data-lat="' . $this->latitude . '"' .
				  ' data-zoom="' . $this->zoom . '"' .
				  //' data-scale="' . $this->map->scalecontrol . '"' .
				' data-scale="1"' .
				  ' data-show_fullscreencontrol="1" ' .
				    ' data-fullscreencontrol_viewfullscreen="' . Text::_('PLG_AGGPXTRACK_VIEW_FULLSCREEN') . '"' .
				' data-fullscreencontrol_exitfullscreen="' . Text::_('PLG_AGGPXTRACK_EXIT_FULLSCREEN') . '"' .
				' data-fullscreencontrol_position="topright"' .
				' data-currentposition_position="topright"' .
				//' data-show_omnivore="' . $this->t['load_kml'] . '"' .
				//' data-omnivore_file="' . JUri::base() . "phocamapskml/" . $this->map->kmlfile . '"' .
				' data-show_omnivore=""' .
				' data-omnivore_file=""' .
				' data-omnivore_icon="home"' .
				' data-omnivore_markercolor="blue"' .
				' data-omnivore_iconcolor="#FFFFFF"' .
				' data-omnivore_spin="false"' .
				' data-omnivore_extraclasses=""' .
			//	' data-specialpins="' . htmlspecialchars(json_encode($this->marker), ENT_QUOTES, 'UTF-8') . '"' .
				' data-specialpins="' . htmlspecialchars(json_encode($marker), ENT_QUOTES, 'UTF-8') . '"' .
				' data-maptype="osmorg"';



			$o .= '<div id="phocamaps-box-leaflet"><div class="pmbox"><div><div><div>';

				$o .= '<div id="map' . $id . '" class="phMap" style="width:100%;height: 400px;border: 1px solid red"></div>';

			$o .= '</div></div></div></div></div>';

		$o .= '<input type="text" id="coordinates" value="" />';
echo $o;
/*$id		= '';
$map	= new PhocaMapsMap($id);
if ($this->type == 'marker') {
	$map->loadCoordinatesJS();
}
//$map->loadAPI();
echo '<div align="center" style="margin:0;padding:0">';
echo '<div id="phocaMap'.$id.'" style="margin:0;padding:0;width:750px;height:480px"></div></div>';

echo $map->startJScData();

	//echo $map->addAjaxAPI('maps', '3.x', '{"other_params":"sensor=false"}');
	echo $map->addAjaxAPI('maps', '3.x', '');
	echo $map->addAjaxAPI('search', '1');

	echo $map->createMap('phocaMap', 'mapPhocaMap', 'phocaLatLng', 'phocaOptions','tstPhocaMap', 'tstIntPhocaMap', 'phocaGeoCoder', TRUE);
	echo $map->cancelEventFunction();
	echo $map->checkMapFunction();

	echo $map->startMapFunction();

		echo $map->setLatLng( $this->latitude, $this->longitude );
		echo $map->startMapOptions();
		echo $map->setMapOption('zoom', $this->zoom).','."\n";
		echo $map->setCenterOpt().','."\n";
		echo $map->setTypeControlOpt().','."\n";
		echo $map->setNavigationControlOpt().','."\n";
		echo $map->setMapOption('scaleControl', 1, TRUE ).','."\n";
		echo $map->setMapOption('scrollwheel', 1, TRUE).','."\n";
		echo $map->setMapOption('disableDoubleClickZoom', 0).','."\n";
	//	echo $map->setMapOption('googleBar', $this->map->googlebar).','."\n";// Not ready yet
	//	echo $map->setMapOption('continuousZoom', $this->map->continuouszoom).','."\n";// Not ready yet
		echo $map->setMapTypeOpt()."\n";
		echo $map->endMapOptions();
		echo $map->setMap();

	//	echo $map->exportZoom($this->zoom, 'window.top.document.forms.adminForm.elements.zoom');
	//	echo $map->exportMarker('Global', $this->type, $this->latitude, $this->longitude, 'window.top.document.forms.adminForm.elements.latitude', 'window.top.document.forms.adminForm.elements.longitude');
		if ($this->type != 'marker') {
			echo $map->exportZoom($this->zoom, '', 'phocaSelectMap_jform_zoom');
		}

		if ($this->type == 'marker') {
			echo $map->exportMarker('Global', $this->type, $this->latitude, $this->longitude, '', '', 'phocaSelectMap_jform_latitude', 'phocaSelectMap_jform_longitude','phocaSelectMap_jform_gpslatitude', 'phocaSelectMap_jform_gpslongitude');
		} else {
			echo $map->exportMarker('Global', $this->type, $this->latitude, $this->longitude, '', '', 'phocaSelectMap_jform_latitude', 'phocaSelectMap_jform_longitude');
		}

		//if($map->scrollwheelzoom != 0){
			echo $map->setListener();
		//}
		echo $map->setGeoCoder();
		echo $map->endMapFunction();

	if ($this->type == 'marker') {
		echo $map->addAddressToMapFunction('Global', 'phocaAddressEl', $this->type, '', '', 'phocaSelectMap_jform_latitude', 'phocaSelectMap_jform_longitude','phocaSelectMap_jform_gpslatitude', 'phocaSelectMap_jform_gpslongitude');// no '.id.' - it is set in class
	} else {
		echo $map->addAddressToMapFunction('Global', 'phocaAddressEl', $this->type, '', '', 'phocaSelectMap_jform_latitude', 'phocaSelectMap_jform_longitude');// no '.id.' - it is set in class
	}

	echo $map->setInitializeFunctionSpecificMap();
	echo $map->setInitializeFunction();

echo $map->endJScData();
echo $map->loadAPI();// must be loaded as last

echo '<div class="p-add-address">'
. '<form class="form-inline" action="#" onsubmit="addAddressToMap'.$id.'(); return false;">'
. '<span>'.Text::_('COM_PHOCAMAPS_SET_COORDINATES_BY_ADDRESS').' : </span>'
. ' <input type="text" name="phocaAddressNameEl'.$id.'" id="phocaAddressEl'.$id.'" value="" class="" style="display:inline;" size="30" />'
. ' <input type="submit" class="btn" name="find" value="'. Text::_('COM_PHOCAMAPS_SET').'" />'
. '</form>'
. '</div>';*/


echo '</div>';
?>
