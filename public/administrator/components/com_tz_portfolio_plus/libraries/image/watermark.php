<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2019 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TZ_Portfolio_Plus\Image;

tzportfolioplusimport('phpclass.PHPImageWorkshop.Exception.ImageWorkshopBaseException');
tzportfolioplusimport('phpclass.PHPImageWorkshop.Core.Exception.ImageWorkshopLayerException');
tzportfolioplusimport('phpclass.PHPImageWorkshop.Core.Exception.ImageWorkshopLibException');
tzportfolioplusimport('phpclass.PHPImageWorkshop.Core.ImageWorkshopLayer');
tzportfolioplusimport('phpclass.PHPImageWorkshop.Core.ImageWorkshopLib');
tzportfolioplusimport('phpclass.PHPImageWorkshop.Exception.ImageWorkshopException');
tzportfolioplusimport('phpclass.PHPImageWorkshop.Exif.ExifOrientations');
tzportfolioplusimport('phpclass.PHPImageWorkshop.ImageWorkshop');

// no direct access
use PHPImageWorkshop\ImageWorkshop;

defined('_JEXEC') or die;

class TppImageWaterMark extends ImageWorkshop{

    const POSITION_TOP_LEFT     = 'LT'; // Left Top
    const POSITION_TOP          = 'MT'; // Middle Top
    const POSITION_TOP_RIGHT    = 'RT'; // Right Top
    const POSITION_LEFT         = 'LM'; // Left Middle
    const POSITION_CENTER       = 'MM'; // Middle Middle
    const POSITION_RIGHT        = 'RM'; // Right middle
    const POSITION_BOTTOM_LEFT  = 'LB'; // Left Bottom
    const POSITION_BOTTOM       = 'MB'; // Middle Bottom
    const POSITION_BOTTOM_RIGHT = 'RB'; // Right Bottom
}