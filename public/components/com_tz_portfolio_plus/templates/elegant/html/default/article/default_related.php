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
defined('_JEXEC') or die;

if (!$this->print) :
    $doc    = JFactory::getDocument();

    $lists  = $this -> itemsRelated;
    // Create shortcuts to some parameters.
    $params		= $this->item->params;
    $tmpl       = null;
    if($lists):
        if($params -> get('show_related_article',1)):
?>
<div class="tpRelated card">
    <?php if($params -> get('show_related_heading',1)):?>
        <?php
            $title    = JText::_('COM_TZ_PORTFOLIO_PLUS_RELATED_ARTICLE');
            if($params -> get('related_heading')){
                $title  = $params -> get('related_heading');
            }
        ?>
        <div class="card-header">
            <h4 class="reset-heading"><?php echo $title;?></h4>
        </div>
    <?php endif;?>
    <ul class="list-group list-group-flush"><?php foreach($lists as $i => $itemR):?>
    <li class="list-group-item<?php if($i == 0) echo ' first'; if($i == count($lists) - 1) echo ' last';?>">
        <?php
        if($params -> get('show_related_title',1)){
        ?><i class="tp tp-file-o"></i>
        <a href="<?php echo $itemR -> link;?>"
           class="tpTitle<?php if($params -> get('tz_use_lightbox',0) == 1){echo ' fancybox fancybox.iframe';}?>">
            <?php echo $itemR -> title;?>
        </a>
            <time class="float-right pull-right muted"><?php echo JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC'));?></time>
        <?php
        }?>
    </li>
    <?php endforeach;?>
    </ul>
</div>
 
        <?php endif;?>
    <?php endif;?>
<?php endif;?>