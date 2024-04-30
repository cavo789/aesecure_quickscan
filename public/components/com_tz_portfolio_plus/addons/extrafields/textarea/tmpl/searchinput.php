<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access');

$params = $this -> params;

if($params -> get('show_label', 1)){ ?>
    <label class="group-label"><?php echo $this -> getTitle();?></label>
<?php } ?>
<textarea name="<?php echo $this -> getSearchName();?>" id="<?php echo $this -> getSearchId();?>" rows="10" cols="50"<?php
 echo $this->getAttribute(null, null, 'search');?>><?php echo $value;?></textarea>