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

// no direct access
defined('_JEXEC') or die('Restricted access');

$params = $this -> item -> params;
$tmpl           = JFactory::getApplication() -> input -> getString('tmpl');

if($params -> get('show_tags',1)){
    if($this -> listTags){
?>
<div class="tpp-item-tags mb-3">
    <span class="title"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_TAG_TITLE');?></span>
    <?php foreach($this -> listTags as $i => $item){ ?>
        <span class="tag-list<?php echo $i;  echo !$params -> get('link_tag', 0)?' label label-default badge badge-secondary':'';
        ?>" itemprop="keywords">
            <?php if($params -> get('link_tag', 1)){ ?>
              <a class="label label-default badge badge-secondary" href="<?php echo $item -> link; ?>"<?php
              if(isset($tmpl) AND !empty($tmpl)): echo ' target="_blank"'; endif;?>><?php } ?><?php
                  echo $item -> title;?><?php if($params -> get('link_tag', 1)){ ?></a><?php } ?>
        </span>
    <?php } ?>
</div>
<?php }
}
?>
