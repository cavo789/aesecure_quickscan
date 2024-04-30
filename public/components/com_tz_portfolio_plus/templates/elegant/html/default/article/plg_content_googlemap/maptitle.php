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
if(isset($this -> item) && $this -> item):
$params = $this -> params;
$address = $params->get('address_title');

    if(isset($address) && $address != ''):
?>
    <span class="map_title">
        <i class="fa fa-map-marker"></i>
        <?php echo $address;?>
    </span>
<?php
    endif;
endif;
