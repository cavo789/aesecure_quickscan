<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @plugin Phoca Plugin
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Object\CMSObject;

defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.application.component.helper' );

class plgContentPhocaMaps extends JPlugin
{
	protected $_plgPhocaMapsNr	= 0;
	protected $_loadedBootstrap = 0;


	public function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function _setPhocaMapsPluginNumber() {
		$this->_plgPhocaMapsNr = (int)$this->_plgPhocaMapsNr + 1;
	}

	public function _setPhocaMapsPluginLoadedBootstrap() {
		$this->_loadedBootstrap = (int)$this->_loadedBootstrap + 1;
	}

	public function onContentPrepare($context, &$article, &$params, $page = 0) {

		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer') {
			return true;
		}

		$app 	= Factory::getApplication();
		$view	= $app->input->get('view');

		if ($view == 'tag') { return; }

		$param['display_map_description'] = $this->params->get('display_map_description', 0);


		// Start Plugin
		$regex_one		= '/({phocamaps\s*)(.*?)(})/si';
		$regex_all		= '/{phocamaps\s*.*?}/si';
		$matches 		= array();
		$count_matches	= preg_match_all($regex_all,$article->text,$matches,PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);

		$lang = Factory::getLanguage();
		$lang->load('com_phocamaps.sys');
		$lang->load('com_phocamaps');

		// Start if count_matches

		if ($count_matches != 0) {

			if (!ComponentHelper::isEnabled('com_phocamaps', true)) {
				Text::_('PLG_CONTENT_PHOCAMAPS_PLUGIN_REQUIRE_COMPONENT');
				return true;
			}

			$document		= Factory::getDocument();
			$db 			= Factory::getDBO();
			//$menu 			= &JSite::getMenu();
			//$plugin 		= &JPluginHelper::getPlugin('content', 'phocamaps');
			//$paramsP 		= new JParameter( $plugin->params );

			$paramsP		= $this->params;
			require_once( JPATH_ROOT.'/components/com_phocamaps/helpers/route.php' );
			require_once( JPATH_ADMINISTRATOR.'/components/com_phocamaps/helpers/phocamapspath.php' );
			require_once( JPATH_ADMINISTRATOR.'/components/com_phocamaps/helpers/phocamaps.php' );
			require_once( JPATH_ADMINISTRATOR.'/components/com_phocamaps/helpers/phocamapsmap.php' );
			require_once( JPATH_ADMINISTRATOR.'/components/com_phocamaps/helpers/phocamapsmaposm.php' );
			//$component 		= 'com_phocamaps';
			//$table 			=& JTable::getInstance('component');
			//$table->loadByOption( $component );
			//$paramsC	 	= new JParameter( $table->params );

			$component			=	'com_phocamaps';
			$paramsC			= JComponentHelper::getParams($component) ;

			$tmpl			= array();

			JHtml::_('jquery.framework', false);

			$document->addStyleSheet(JURI::base(true).'/media/com_phocamaps/css/phocamaps.css');
			$document->addStyleSheet(JURI::base(true).'/media/plg_content_phocamaps/css/default.css');

			$allIds = array();

			for($i = 0; $i < $count_matches; $i++) {

				// MUST BE HERE - defined for each instance
				$tmpl['enable_kml']				= $paramsC->get( 'enable_kml', 0 );
				$tmpl['display_print_route']	= $paramsC->get( 'display_print_route', 1 );
				$tmpl['close_opened_window']	= $paramsC->get( 'close_opened_window', 0 );
				$tmpl['map_type']				= $paramsC->get( 'map_type', 2 );
				$tmpl['osm_map_type']			= $paramsC->get( 'osm_map_type', 'osm' );
				$tmpl['osm_search']				= $paramsC->get( 'osm_search', 0 );
				$tmpl['osm_easyprint'] 			= $paramsC->get( 'osm_easyprint', 0 );

				$this->_setPhocaMapsPluginNumber();
				// Only loaded when the type is really map not a link - see below view=map YES, view=link NO
				//$id	= 'PlgPM'.(int)$this->_plgPhocaMapsNr;
				//$allIds[] = $id;

				$view	= '';
				$idMap	= '';
				$text	= '';
				//$lang   = '';

				// Get plugin parameters
				$phocaMaps	= $matches[0][$i][0];
				preg_match($regex_one,$phocaMaps,$phocaMaps_parts);
				$parts			= explode("|", $phocaMaps_parts[2]);
				$values_replace = array ("/^'/", "/'$/", "/^&#39;/", "/&#39;$/", "/<br \/>/");


				foreach($parts as $key => $value) {
					$values = explode("=", $value, 2);
					foreach ($values_replace as $key2 => $values2) {
						$values = preg_replace($values2, '', $values);
					}

					// Get plugin parameters from article
						 if($values[0]=='view')				{$view							= $values[1];}
					else if($values[0]=='id')				{$idMap							= $values[1];}
					else if($values[0]=='text')				{$text							= $values[1];}
					//else if($values[0]=='lang')			{$lang							= $values[1];}
					else if($values[0]=='kmlfile')			{$tmpl['enable_kml']			= $values[1];}
					else if($values[0]=='printroute')		{$tmpl['display_print_route']	= $values[1];}
					else if($values[0]=='maptype')			{$tmpl['map_type']				= $values[1];}
					else if($values[0]=='osmmaptype')		{$tmpl['osm_map_type']			= $values[1];}

					else if($values[0]=='iframesrc')		{$tmpl['iframesrc']				= $values[1];}
					else if($values[0]=='iframewidth')		{$tmpl['iframewidth']			= $values[1];}
					else if($values[0]=='iframeheight')		{$tmpl['iframeheight']			= $values[1];}

				}

				$output = '';

				switch($view) {

					// - - - - - - - - - - - - - - - -
					// Map
					// - - - - - - - - - - - - - - - -
					case 'map':

						$id	= 'PlgPM' . (int)$this->_plgPhocaMapsNr;

						// Javascript for all Google Maps
						if ($tmpl['map_type'] == 1) {
							$allIds[] = $id;
						}

						$query = 'SELECT a.*'
							.' FROM #__phocamaps_map AS a'
							.' WHERE a.id = '.(int) $idMap;
						$db->setQuery($query);
						$mapp = $db->loadObject();


						if (empty($mapp)) {
							echo '<div class="alert alert-error">'. Text::_('PLG_CONTENT_PHOCAMAPS_PLUGIN_ERROR') . ' - '. Text::_('PLG_CONTENT_PHOCAMAPS_MAP_NOT_EXISTS') . ' (ID = '.$idMap.')</div>';
							return false;
						}


						$query = 'SELECT a.*, i.id as iconid, i.url as iurl, i.urls as iurls, i.object as iobject, i.objects as iobjects, i.objectshape as iobjectshape'
								.' FROM #__phocamaps_marker AS a'
								.' LEFT JOIN #__phocamaps_map AS c ON c.id = a.catid '
								.' LEFT JOIN #__phocamaps_icon AS i ON i.id = a.iconext '
								.' WHERE c.id = '.(int) $idMap
								.' AND a.published = 1'
								.' ORDER BY a.ordering ASC';
						$db->setQuery($query);
						$markerp = $db->loadObjectList();

						// Parameters
						$tmpl['apikey']					= $paramsC->get( 'google_maps_api_key', '' );
						$tmpl['displayphocainfo']		= $paramsC->get( 'display_phoca_info', 1 );
						$tmpl['displaymapdescription']	= $paramsP->get( 'display_map_description', 0 );
// - - - - - - - - - - - - - - -
// RENDER
// - - - - - - - - - - - - - - -
// Display Description
$tmpl['description'] = '';

if (isset($mapp->description) && $mapp->description != '' && $param['display_map_description'] == 1) {
	$tmpl['description'] = '<div class="pm-desc">'.$mapp->description.'</div>';
}

// Check Width and Height
$tmpl['fullwidth'] = 0;
if (!isset($mapp->width)) {
	$mapp->width = '100%';
}
if (isset($mapp->width) && (int)$mapp->width < 1) {
	$tmpl['fullwidth'] = 1;
}
if (!isset($mapp->height) || (isset($mapp->height) && (int)$mapp->height < 1)) {
	$mapp->height = '50vh';
}
if (!isset($mapp->zoom) || (isset($mapp->zoom) && (int)$mapp->zoom < 1)) {
	$mapp->zoom = 2;
}

if (is_numeric($mapp->width)) {
	$mapp->width = $mapp->width . 'px';
}

if (is_numeric($mapp->height)) {
	$mapp->height = $mapp->height . 'px';
}



// Map Langugage
$tmpl['params'] = '';
if (!isset($mapp->lang) || (isset($mapp->lang) && $mapp->lang == '')) {
	//$tmpl['params'] 		= '{other_params:"sensor=false"}';
	$tmpl['params'] 		= '';
	$tmpl['paramssearch'] 	= '';
	$tmpl['lang']			= '';
} else {
	//$tmpl['params'] 		= '{other_params:"sensor=false&language='.$mapp->lang.'"}';
	$tmpl['params'] 		= '{other_params:"language='.$mapp->lang.'"}';
	$tmpl['paramssearch'] 	= '{"language":"'.$mapp->lang.'"}';
	$tmpl['lang']			= $mapp->lang;
}


// Design
$tmpl['border'] = '';
if (isset($mapp->border)) {
	switch ($mapp->border) {
		case 1:
			$tmpl['border'] = '-grey';
		break;
		case 2:
			$tmpl['border'] = '-greywb';
		break;
		case 3:
			$tmpl['border'] = '-greyrc';
		break;
		case 4:
			$tmpl['border'] = '-black';
		break;
	}
}

// Plugin - no border
$tmpl['stylesite'] 	= 'margin:0;padding:0;margin-top:10px;';

$tmpl['stylesitewidth']	= '';
if ($tmpl['fullwidth'] == 1) {
	$tmpl['stylesitewidth'] = 'style="width:100%"';
}

// Parameters
if (isset($mapp->continuouszoom) && (int)$mapp->continuouszoom == 1) {
	$mapp->continuouszoom = 1;
} else {
	$mapp->continuouszoom = 0;
}

if (isset($mapp->doubleclickzoom) && (int)$mapp->doubleclickzoom == 1) {
	$mapp->disabledoubleclickzoom = 0;
} else {
	$mapp->disabledoubleclickzoom = 1;
}

if (isset($mapp->scrollwheelzoom) && (int)$mapp->scrollwheelzoom == 1) {
	$mapp->scrollwheelzoom = 1;
} else {
	$mapp->scrollwheelzoom = 0;
}

// Since 1.1.0 zoomcontrol is alias for navigationcontrol
if (empty($mapp->zoomcontrol)) {
	$mapp->zoomcontrol = 0;
}

if (empty($mapp->scalecontrol)) {
	$mapp->scalecontrol = 0;
}

if (empty($mapp->typecontrol)) {
	$mapp->typecontrol = 0;
}
if (empty($mapp->typecontrolposition)) {
	$mapp->typecontrolposition = 0;
}


if (empty($mapp->typeid)) {
	$mapp->typeid = 0;
}


// Display Direction
$tmpl['displaydir'] = 0;
if (isset($mapp->displayroute) && $mapp->displayroute == 1) {
	if (isset($markerp) && !empty($markerp)) {
		$tmpl['displaydir'] = 1;
	}
}

// KML Support
$tmpl['load_kml'] = FALSE;
if($tmpl['enable_kml'] == 1) {
	jimport( 'joomla.filesystem.folder' );
	jimport( 'joomla.filesystem.file' );
	$path = PhocaMapsPath::getPath();
	if (isset($mapp->kmlfile) && File::exists($path->kml_abs . $mapp->kmlfile)) {
		$tmpl['load_kml'] = $path->kml_rel_full . $mapp->kmlfile;
	}
}

$output .= '<div class="phocamaps">';

if ((!isset($mapp->longitude))
		|| (!isset($mapp->latitude))
		|| (isset($mapp->longitude) && $mapp->longitude == '')
		|| (isset($mapp->latitude) && $mapp->latitude == '')) {
	$output .= '<p>' . Text::_('COM_PHOCAMAPS_MAP_ERROR_FRONT') . '</p>';
} else {


	#########################
	# ALL MAPS - Google Maps, OpenStreetMap
	#########################

	$output .= $tmpl['description'];

	// Map Box
	if ($tmpl['border'] == '') {
		$output .= '<div class="phocamaps-box" align="center" style="'.$tmpl['stylesite'].'">';
		if ($tmpl['fullwidth'] == 1) {
			$output .= '<div id="phocaMap'.$id.'" style="margin:0;padding:0;width:100%;height:'.$mapp->height.'"></div>';
		} else {
			$output .= '<div id="phocaMap'.$id.'" style="margin:0;padding:0;width:'.$mapp->width.';height:'.$mapp->height.'"></div>';
		}
		$output .= '</div>';
	} else {
		$output .= '<div class="phocamaps-box phocamaps-box-border'.$tmpl['border'].'" align="center" style="'.$tmpl['stylesite'].'">';
		if ($tmpl['fullwidth'] == 1) {
			$output .= '<div id="phocaMap'.$id.'" class="phocamaps-map" style="width:100%;height:'.$mapp->height.'"></div>';
		} else {
			$output .= '<div id="phocaMap'.$id.'" class="phocamaps-map" style="width:'.$mapp->width.';height:'.$mapp->height.'"></div>';
		}
		$output .= '</div>';
		//echo '</div></div></div></div></div>';
	}


	###########################
	# GOOGLE MAPS
	###########################
	if ($tmpl['map_type'] == 1) {


		//$id		= '';
		$map	= new PhocaMapsMap($id);
		//$map->loadAPI();
		//$map->loadAPI('jsapi',$paramsC->get( 'load_api_ssl',0));
		//$map->loadAPI($article->id);//must be loaded at the end
		$map->loadGeoXMLJS();
		$map->loadBase64JS();


		// Direction
		if ($tmpl['displaydir']) {

			$countMarker 	= count($markerp);
			$form 			= '';
			if ((int)$countMarker > 1) {

				$form .= ' ' . Text::_('PLG_CONTENT_PHOCAMAPS_TO').': <select name="pmto'.$id.'" id="toPMAddress'.$id.'">';
				foreach ($markerp as $key => $markerV) {
					if ((isset($markerV->longitude) && $markerV->longitude != '')
					&& (isset($markerV->latitude) && $markerV->latitude != '')) {
						$form .= '<option value="'.$markerV->latitude.','.$markerV->longitude.'">'.$markerV->title.'</option>';
					}
				}
				$form .= '</select>';
			} else if ((int)$countMarker == 1) {

				foreach ($markerp as $key => $markerV) {
					if ((isset($markerV->longitude) && $markerV->longitude != '')
					&& (isset($markerV->latitude) && $markerV->latitude != '')) {
						$form .= '<input name="pmto'.$id.'" id="toPMAddress'.$id.'" type="hidden" value="'.$markerV->latitude.','.$markerV->longitude.'" />';
					}
				}

			}

			if ($form != '') {
				/*$output .= '<div class="pmroute"><form action="#" onsubmit="setPhocaDir'.$id.'(this.pmfrom'.$id.'.value, this.pmto'.$id.'.value); return false;">';
				$output .= Text::_('PLG_CONTENT_PHOCAMAPS_FROM_ADDRESS').': <input type="text" size="30" id="fromPMAddress'.$id.'" name="pmfrom'.$id.'" value=""/>';
				$output .= $form;
				$output .= ' <input name="pmsubmit'.$id.'" type="submit" value="'.Text::_('PLG_CONTENT_PHOCAMAPS_GET_ROUTE').'" /></form></div>';
				$output .= '<div id="phocaDir'.$id.'">';
				if ($tmpl['display_print_route'] == 1) {
					$output .= '<div id="phocaMapsPrintIcon'.$id.'" style="display:none"></div>';
				}
				$output .= '</div>';*/

				$output .= '<div class="pmroute">';
				$output .= '<form class="form-inline input-group" action="#" onsubmit="setPhocaDir'.$id.'(this.pmfrom'.$id.'.value, this.pmto'.$id.'.value); return false;">';
				$output .= Text::_('PLG_CONTENT_PHOCAMAPS_FROM_ADDRESS').': <input class="pm-input-route input form-control" type="text" size="30" id="fromPMAddress'.$id.'" name="pmfrom'.$id.'" value=""/>';
				$output .= $form;
				$output .= ' <input name="pmsubmit'.$id.'" type="submit" class="pm-input-route-btn btn btn-primary" value="'.Text::_('PLG_CONTENT_PHOCAMAPS_GET_ROUTE').'" />';
				$output .= '</form></div>';
				$output .= '<div id="phocaDir'.$id.'">';
				if ($tmpl['display_print_route'] == 1) {
					$output .= '<div id="phocaMapsPrintIcon'.$id.'" style="display:none"></div>';
				}
				$output .= '</div>';
			}
		}

		// $id is not used anymore as this is added in methods of Phoca Maps Class
		// e.g. 'phocaMap' will be not 'phocaMap'.$id as the id will be set in methods

		$output .= $map->startJScData();
		$output .= $map->addAjaxAPI('maps', '3', $tmpl['params']);
		$output .= $map->addAjaxAPI('search', '1', $tmpl['paramssearch']);

		$output .= $map->createMap('phocaMap', 'mapPhocaMap', 'phocaLatLng', 'phocaOptions','tstPhocaMap', 'tstIntPhocaMap', FALSE, FALSE, $tmpl['displaydir']);
		$output .= $map->cancelEventFunction();
		$output .= $map->checkMapFunction();
		$output .= $map->startMapFunction();

			$output .= $map->setLatLng( $mapp->latitude, $mapp->longitude );

			$output .= $map->startMapOptions();
			$output .= $map->setMapOption('zoom', $mapp->zoom).','."\n";
			$output .= $map->setCenterOpt().','."\n";
			$output .= $map->setTypeControlOpt($mapp->typecontrol, $mapp->typecontrolposition).','."\n";
			$output .= $map->setNavigationControlOpt($mapp->zoomcontrol).','."\n";
			$output .= $map->setMapOption('scaleControl', $mapp->scalecontrol, TRUE ).','."\n";
			//$output .= $map->setMapOption('scrollwheel', $mapp->scrollwheelzoom, TRUE).','."\n";

			if ($mapp->gesturehandling != '') {
			   $output .= $map->setMapOption('gestureHandling', '"' . $mapp->gesturehandling . '"').','."\n";
			} else {
			   $output .= $map->setMapOption('scrollwheel', $mapp->scrollwheelzoom, TRUE).','."\n";
			}

			$output .= $map->setMapOption('disableDoubleClickZoom', $mapp->disabledoubleclickzoom).','."\n";
		//	$output .= $map->setMapOption('googleBar', $mapp->googlebar).','."\n";// Not ready yet
		//	$output .= $map->setMapOption('continuousZoom', $mapp->continuouszoom).','."\n";// Not ready yet

			if (isset($mapp->map_styles)) {
				$output .= $map->setMapOption('styles', $mapp->map_styles).','."\n";
			}
			$output .= $map->setMapTypeOpt($mapp->typeid)."\n";
			if (isset($mapp->custom_options)) {
				$output .= $map->endMapOptions($mapp->custom_options);
			} else {
				$output .= $map->endMapOptions();
			}
			if ($tmpl['close_opened_window'] == 1) {
				$output .= $map->setCloseOpenedWindow();
			}
			$output .= $map->setMap();

			// Markers
			jimport('joomla.filter.output');
			if (isset($markerp) && !empty($markerp)) {

				$iconArray = array(); // add information about created icons to array and check it so no duplicity icons js code will be created
				foreach ($markerp as $key => $markerV) {

					if ((isset($markerV->longitude) && $markerV->longitude != '')
					&& (isset($markerV->latitude) && $markerV->latitude != '')) {



						$hStyle = 'font-size:120%;margin: 5px 0px;font-weight:bold;';
						$text = '<div style="'.$hStyle.'">' . addslashes($markerV->title) . '</div>';
						// Try to correct images in description
						$markerV->description = PhocaMapsHelper::fixImagePath($markerV->description);
						$markerV->description = str_replace('@', '&#64;', $markerV->description);
						//$markerV->description = str_replace("/", '&#47;', $markerV->description);
						$markerV->description = str_replace("'", '&#39;', $markerV->description);
						//$markerV->description = str_replace('"', '&#34;', $markerV->description);

						//$markerV->description = htmlentities($markerV->description);
						$text .= '<div>'. PhocaMapsHelper::strTrimAll(addslashes($markerV->description)).'</div>';


						if ($markerV->displaygps == 1) {
							$text .= '<div class="pmgps"><table border="0"><tr><td><strong>'. Text::_('PLG_CONTENT_PHOCAMAPS_GPS') . ': </strong></td>'
									.'<td>'.PhocaMapsHelper::strTrimAll(addslashes($markerV->gpslatitude)).'</td></tr>'
									.'<tr><td></td>'
									.'<td>'.PhocaMapsHelper::strTrimAll(addslashes($markerV->gpslongitude)).'</td></tr></table></div>';
						}



						if(empty($markerV->icon)) {
							$markerV->icon = 0;
						}
						if(empty($markerV->title)){
							$markerV->title = '';
						}
						if(empty($markerV->description)){
							$markerV->description = '';
						}


						$iconOutput = $map->setMarkerIcon($markerV->icon, $markerV->iconext, $markerV->iurl, $markerV->iobject, $markerV->iurls, $markerV->iobjects, $markerV->iobjectshape);
						$output .= $map->outputMarkerJs($iconOutput['js'], $markerV->icon, $markerV->iconext);

						$output .= $map->setMarker($markerV->id,$markerV->title,$markerV->description,$markerV->latitude, $markerV->longitude, $iconOutput['icon'], $iconOutput['iconid'], $text, $markerV->contentwidth, $markerV->contentheight,  $markerV->markerwindow,  $iconOutput['iconshadow'], $iconOutput['iconshape'], $tmpl['close_opened_window']);

					}
				}
				$output .= $map->setMarkerClusterer();
			}

			if ($tmpl['load_kml']) {
				$output .= $map->setKMLFile($tmpl['load_kml']);
			}

			if ($tmpl['displaydir']) {
				$output .= $map->setDirectionDisplayService('phocaDir');
			}
			if(isset($mapp->scrollwheelzoom) && $mapp->scrollwheelzoom != 0){
				$output .= $map->setListener();
			}
			$output .= $map->endMapFunction();

			if ($tmpl['displaydir']) {
				$output .= $map->setDirectionFunction($tmpl['display_print_route'], $mapp->id, $mapp->alias, $tmpl['lang']);
			}

			//if ((int)$this->_plgPhocaMapsNr < 2) {

				//$output .= $map->setInitializeFunction();// will be set at bottom for all items - Add init for all maps
				$output .= $map->setInitializeFunctionSpecificMap();
			//}
		$output .= $map->endJScData();


	########################### END GOOGLE MAPS

	###########################
	# OPENSTREETMAP
	###########################

	} else {

		//OSM tracks
		if ($tmpl['map_type'] == 2) {

		    $tmpl['fitbounds']   	= $mapp->fitbounds_osm;
			$textarea               = $mapp->trackfiles_osm;
			$textarea               = str_replace(array("\r\n", "\n", "\r"),'',$textarea);
			$tracks                 = explode(",",$textarea);

			$textarea               = $mapp->trackcolors_osm;
			$textarea               = str_replace(array("\r\n", "\n", "\r"),'',$textarea);
			//$colors                 = explode(",",$textarea);
			$colors                 = array_map('trim', explode(',', $textarea));

			$tracksA = array();
			foreach ($tracks as $k => $v) {
				$v = trim($v);
				$ext = pathinfo($v,PATHINFO_EXTENSION);

				if (($ext != 'gpx') && ($ext != 'kml')) {
					$v = '';
				} else {
					//if no path specified add default path (hardcoded to /phocamapskml for now)
					if (strpos($v,'/') === false) {
						$v = 'phocamapskml/'.$v;
					}
					$v = trim($v,'/');

					$tracksA[$k] = array();
					$tracksA[$k]['file'] = File::exists(JPATH_ROOT.'/'.$v) ? JURI::base().$v : '';
					$tracksA[$k]['color'] = isset($colors[$k]) ? $colors[$k] : '';
				}
			}
			$tmpl['tracks'] = $tracksA;
		} else {
			$tmpl['tracks'] = array();
		}



		$map	= new PhocaMapsMapOsm($id);


		$map->osmmaptype = $tmpl['osm_map_type'];

		$map->loadAPI();
		$map->loadCoordinatesJS();
		$map->createMap($mapp->latitude, $mapp->longitude, $mapp->zoom);

		$map->setMapType();


		// Markers
		jimport('joomla.filter.output');
		$iM = 0;
		if (isset($markerp) && !empty($markerp)) {

			$iconArray = array(); // add information about created icons to array and check it so no duplicity icons js code will be created
			foreach ($markerp as $key => $markerV) {

				if ((isset($markerV->longitude) && $markerV->longitude != '')
				&& (isset($markerV->latitude) && $markerV->latitude != '')) {
					if ($iM == 0) {
						// Get info about first marker to use it in routing plan
						$firstMarker = $markerV;
					}

					$hStyle = 'font-size:120%;margin: 5px 0px;font-weight:bold;';
					$text = '<div style="'.$hStyle.'">' . addslashes($markerV->title) . '</div>';

					// Try to correct images in description
					$markerV->description = PhocaMapsHelper::fixImagePath($markerV->description);
					$markerV->description = str_replace('@', '&#64;', $markerV->description);
					$text .= '<div>'. PhocaMapsHelper::strTrimAll(addslashes($markerV->description)).'</div>';
					if ($markerV->displaygps == 1) {
						$text .= '<div class="pmgps"><table border="0"><tr><td><strong>'. Text::_('COM_PHOCAMAPS_GPS') . ': </strong></td>'
								.'<td>'.PhocaMapsHelper::strTrimAll(addslashes($markerV->gpslatitude)).'</td></tr>'
								.'<tr><td></td>'
								.'<td>'.PhocaMapsHelper::strTrimAll(addslashes($markerV->gpslongitude)).'</td></tr></table></div>';
					}


					if(empty($markerV->icon)) {
						$markerV->icon = 0;
					}
					if(empty($markerV->title)){
						$markerV->title = '';
					}
					if(empty($markerV->description)){
						$markerV->description = '';
					}






					$map->setMarker($id . 'm'.$markerV->id, $markerV->title, $markerV->description, $markerV->latitude, $markerV->longitude, $text, $markerV->contentwidth, $markerV->contentheight, $markerV->markerwindow, $tmpl['close_opened_window']);

					$markerIconOptions = array();

					if (isset($markerV->osm_icon) && $markerV->osm_icon != '') {
						$markerIconOptions = $map->setMarkerIcon($id . 'm'.$markerV->id, $markerV->osm_icon, $markerV->osm_marker_color, $markerV->osm_icon_color, $markerV->osm_icon_prefix, $markerV->osm_icon_spin, $markerV->osm_icon_class);
					}

					if ($iM == 0) {
						// Get info about first marker to use it in routing plan
						// so we get the same icons for markers in Options like the first marker has
						$firstMarker->markericonoptions = $markerIconOptions;
					}
					$iM++;
				}
			}
			$map->setMarkerClusterer();
		}

		$map->renderFullScreenControl();
		$map->renderCurrentPosition();

		if ($tmpl['osm_search'] == 1) {
			$map->renderSearch('', 'topleft');
		}
		// Get Lat and Lng TO (first marker)
		$lat = $lng = 0;
		$mId = '';
		$markerIconOptions = array();
		if (isset($firstMarker->latitude)) {
			$lat = $firstMarker->latitude;
		}
		if (isset($firstMarker->longitude)) {
			$lng = $firstMarker->longitude;
		}
		if (isset($firstMarker->id)) {
			$mId = $id . 'm'.$firstMarker->id;
		}
		if (isset($firstMarker->markericonoptions)) {
			$markerIconOptions = $firstMarker->markericonoptions;
		}
		$map->renderRouting(0,0,$lat,$lng, $mId, $markerIconOptions, $mapp->lang);
		if ($tmpl['osm_easyprint'] == 1) {
			$map->renderEasyPrint();
		}

		if (!empty($tmpl['tracks'])) {
			foreach ($tmpl['tracks'] as $ky=>$trk) {
				$fitbounds = $ky==0 ? $tmpl['fitbounds'] : false;
				if (isset($trk['file'])) {
					$map->renderTrack($trk['file'], $trk['color'], $fitbounds);
				}
			}
		}


		$map->renderMap();

		########################### END OPENSTREETMAP
	}
}


$output .= '<div style="clear:both"></div>';
$output .= '</div>';


// END RENDER
// - - - - - - - - - - - - - - -



					break;

					// - - - - - - - - - - - - - - - -
					// Link
					// - - - - - - - - - - - - - - - -
					case 'link':
						if ((int)$idMap > 0) {

							$query = 'SELECT a.*,'
							. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug'
							.' FROM #__phocamaps_map AS a'
							.' WHERE a.id = '.(int) $idMap;
							$db->setQuery($query);
							$mapp = $db->loadObject();

							if (empty($mapp)) {


								echo '<div class="alert alert-error">'. Text::_('PLG_CONTENT_PHOCAMAPS_PLUGIN_ERROR') . ' - '. Text::_('PLG_CONTENT_PHOCAMAPS_MAP_NOT_EXISTS') . ' (ID = '.$idMap.')</div>';
								return false;
							}

							$query = 'SELECT a.id'
								.' FROM #__phocamaps_marker AS a'
								.' LEFT JOIN #__phocamaps_map AS c ON c.id = a.catid '
								.' WHERE c.id = '.(int) $idMap
								.' AND a.published = 1';
							$db->setQuery($query);
							$markerp = $db->loadObjectList();


							$linkMap 		= PhocaMapsHelperRoute::getMapRoute( $mapp->id, $mapp->alias);
							if ($text =='') {
								$text = Text::_('PLG_CONTENT_PHOCAMAPS_LINK_TO_MAP');
							}

							// Parameters
							$tmpl['detailwindow']		= $paramsP->get( 'detail_window', 0 );
							$tmpl['mbbordercolor']		= $paramsP->get( 'modal_box_border_color', '#6b6b6b' );
							$tmpl['mbborderwidth']		= $paramsP->get( 'modal_box_border_width', 2 );
							$tmpl['mboverlaycolor']		= $paramsP->get( 'modal_box_overlay_color', '#000000' );
							$tmpl['mboverlayopacity']	= $paramsP->get( 'modal_box_overlay_opacity', 0.3 );


							if ($mapp->width > 0) {
								$tmpl['windowwidth'] = (int)$mapp->width + 20;
							} else {
								$tmpl['windowwidth'] = 640;
							}
							if ($mapp->width > 0) {
								$tmpl['windowheight'] = (int)$mapp->height + 20;
							} else {
								$tmpl['windowheight'] = 360;
							}



							//Route
							if (isset($mapp->displayroute) && $mapp->displayroute == 1) {
								if (isset($markerp) && !empty($markerp)) {
									$tmpl['windowheight'] = (int)$tmpl['windowheight'] + 40;
								}
							}

							if ($tmpl['detailwindow'] == 1) {

								$button = new CMSObject();
								$button->set('name', 'phocamaps');
								$button->set('methodname', 'js-button');
								$button->set('options', "window.open(this.href,'win2','width=".$tmpl['windowwidth'].",height=".$tmpl['windowheight'].",menubar=no,resizable=yes'); return false;");
								$output .= '<a title="'.$text.'"  href="'.JRoute::_($linkMap . '&tmpl=component').'" onclick="'. $button->options.'">'.$text.'</a>';


							} else if ($tmpl['detailwindow'] == 0) {

								// Button
								JHTML::_('behavior.modal', 'a.modal-button');
								$cssSbox = " #sbox-window {background-color:".$tmpl['mbbordercolor'].";padding:".$tmpl['mbborderwidth']."px} \n"
								." #sbox-overlay {background-color:".$tmpl['mboverlaycolor'].";} \n";

								$document->addCustomTag( "<style type=\"text/css\">\n" . $cssSbox . "\n" . " </style>\n");

								$button = new CMSObject();
								$button->set('name', 'phocamaps');
								$button->set('modal', true);
								$button->set('methodname', 'modal-button');
								$button->set('options', "{handler: 'iframe', size: {x: ".$tmpl['windowwidth'].", y: ".$tmpl['windowheight']."}, overlayOpacity: ".$tmpl['mboverlayopacity'].", classWindow: 'phocamaps-plugin-window', classOverlay: 'phocamaps-plugin-overlay'}");

								$output .= '<a class="modal-button" title="'.$text.'"  href="'.JRoute::_($linkMap . '&tmpl=component').'" rel="'. $button->options.'">'.$text.'</a>';
							} else if ($tmpl['detailwindow'] == 2) {

								// Bootstrap Modal
								$item 		= 'phPlgMapsModalDetail' . $this->_plgPhocaMapsNr;

								if($this->_loadedBootstrap == 0) {
									HTMLHelper::_('script', 'media/plg_content_phocamaps/js/main.js', array('version' => 'auto'));
									Factory::getApplication()
										->getDocument()
										->getWebAssetManager()
										->useScript('bootstrap.modal');

									$output .= '<div id="pmPlgModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="pmPlgModal">
											  <div class="modal-dialog" role="document" id="' . $item . 'Dialog">
												<div class="modal-content">
												  <div class="modal-header">
													
													<h4 class="modal-title" id="pmPlgModalLabel">' . Text::_('COM_PHOCAMAPS_MAP') . '</h4>
													<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="' . Text::_('COM_PHOCAMAPS_CLOSE') . '"></button>
												  </div>
												  <div class="modal-body"><iframe id="pmPlgModalIframe" height="100%" frameborder="0"></iframe></div>
												  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . Text::_('COM_PHOCAMAPS_CLOSE') . '</button></div>
												</div>
											  </div>
											</div>';


									$this->_setPhocaMapsPluginLoadedBootstrap();

								}

								$output .= '<a class="pm-plg-bs-modal-button" title="'.$text.'"  href="'.JRoute::_($linkMap . '&tmpl=component').'" data-bs-toggle="modal" data-title="' . $text. '" data-id="' . $this->_plgPhocaMapsNr . '" data-href="'.JRoute::_($linkMap . '&tmpl=component').'"  data-height='.$tmpl['windowheight'].' data-width='.$tmpl['windowwidth'].'" data-bs-target="#'.$item.'">'.$text.'</a>';


							}

						}
					break;


					case 'iframe':

						$output = '';
						$width = isset($tmpl['iframewidth']) && $tmpl['iframewidth'] != '' ? $tmpl['iframewidth'] : '100%';
						$height = isset($tmpl['iframeheight']) && $tmpl['iframeheight'] != '' ? $tmpl['iframeheight'] : '450';
						if (isset($tmpl['iframesrc']) && $tmpl['iframesrc'] != '') {

							$output = '<iframe src="'.strip_tags(htmlspecialchars($tmpl['iframesrc'])).'" width="'.strip_tags($width).'" height="'.strip_tags($height).'" frameborder="0" style="border:0" allowfullscreen></iframe>';

						}


					break;

				}


				$article->text = preg_replace($regex_all, $output, $article->text, 1);
			} // end foreach



			// Add init for all Google Maps
			$iDi = '';

			if (!empty($allIds)) {
				$jsI = '<script type="text/javascript">//<![CDATA['."\n";
				// Article view = All OK
				// Blog view = we get warning from google maps api that the api is loaded twice or more times but the map will be displayed
				// so we can load all maps with warning or no map

				if (isset($article->id)) {
					// We need to load google maps javascript for whole article - even there are more plugin instances
					// this javascript must be loaded as last
					// we run only loadAPI function which does not set any other variables for other functions
					//$mapA	= new PhocaMapsMap();
					//$jsI	.= $mapA->loadAPI($article->id);
					$iDi = $context . $article->id;
				} else {
					$iDi = $context;
				}
				$iDi = str_replace('_', '', $iDi);
				$iDi = str_replace('.', '', $iDi);
				$iDi = str_replace('-', '', $iDi);
				$iDi = strip_tags($iDi);
				$iDi = ucfirst($iDi);

				//$jsI .= 'function initMaps() {'."\n"; // NO WARNING BUT MAPS IN BLOG WILL BE LOADED ONLY IN ONE ARTICLE

				$jsI .= 'function initMaps'.$iDi.'() {'."\n";// WARNING BUT MAPS WILL BE LOADED IN ALL ARTICLES IN BLOG VIEW


				foreach($allIds as $k => $v){
					$jsI .= '   '.'initMap'.$v.'();'."\n";
				}
				$jsI .= '}'."\n";
				$jsI .= '//]]></script>'."\n";





				$mapA	= new PhocaMapsMap($iDi);
				$jsI	.= $mapA->loadAPI($iDi, $mapp->lang);

				$article->text = $article->text . $jsI;

			}
		}// end if count_matches
		return true;
	}
}
?>
