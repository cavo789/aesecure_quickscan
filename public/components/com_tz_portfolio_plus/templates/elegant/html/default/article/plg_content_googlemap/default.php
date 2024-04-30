<?php
/*------------------------------------------------------------------------

# Google Map Addon

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2016 tzportfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Family Website: http://www.templaza.com

# Technical Support:  Forum - http://tzportfolio.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

if(isset($this -> item) && $this -> item){
    $params = $this -> params;
    $doc        = JFactory::getDocument();

    $map_center = $params->get('mapcenter','40.693996,-73.9854848');
    $map_icon   = $params->get('icon_map','');

    //    Get Color
    $color      = $params->get('color','#21C2F8');

    //    Option google map
    $zoom       = (int)$params->get('zoom_map',10);
    $swheel     = $params->get('scroll_wheel',0);
    if($swheel == 0) {
        $wheel  = 'false';
    }else {
        $wheel  = 'true';
    }
    $draggable  = $params->get('draggable',1);
    //    Style
    $height_map = $params->get('height_map','450px');
    $style = '#tpMap {'
        . 'display: block;'
        . 'height: '.$height_map.';'
        . 'position: relative;'
        . 'width: 100%;'
        . '}';
    $doc->addStyleDeclaration( $style );

    $location = '['.$map_center.',2]';
    ?>

<?php }
?>
<div class="tpGooglemap">
    <div id="tpMap"></div>
    <!-- Add script google map -->
    <script src="https://maps.google.com/maps/api/js<?php echo $params -> get('gmap_api_key')?'?key='.$params -> get('gmap_api_key'):'';?>"></script>
    <script type="text/javascript">
        var map = new google.maps.Map(document.getElementById('tpMap'), {
            zoom: <?php echo $zoom;?>,
            scrollwheel: <?php echo $wheel;?>,
            navigationControl: true,
            mapTypeControl: false,
            scaleControl: false,
            draggable: <?php echo $draggable;?>,
            styles: [ { "stylers": [ { "hue": "<?php echo $color;?>" }, { "gamma": 1 } ] } ],
            center: new google.maps.LatLng(<?php echo $map_center;?>),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var infowindow = new google.maps.InfoWindow();

        var marker, i;

        marker = new google.maps.Marker({
            position: new google.maps.LatLng(<?php echo $map_center;?>),
            map: map ,
            icon: '<?php echo $url = JURI::base().$map_icon;?>'
        });
    </script>
</div>
