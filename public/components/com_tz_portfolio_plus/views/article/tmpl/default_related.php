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
<div class="tpp-item-related card rounded-0 mb-2">
    <?php if($params -> get('show_related_heading',1)):?>
        <?php
            $title    = JText::_('COM_TZ_PORTFOLIO_PLUS_RELATED_ARTICLE');
            if($params -> get('related_heading')){
                $title  = $params -> get('related_heading');
            }
        ?>
        <h3 class="title card-header mt-0 mb-0"><?php echo $title;?></h3>
    <?php endif;?>
    <ul class="list-unstyled mt-3 ml-3 mr-3 mb-1">

    <?php foreach($lists as $i => $itemR){?>
    <li class="tpp-item-related__item<?php if($i == 0) echo ' first'; if($i == count($lists) - 1) echo ' last';?> mb-2">
        <?php
        if($itemR->event->onContentDisplayMediaType && !empty($itemR->event->onContentDisplayMediaType)) {
            echo $itemR->event->onContentDisplayMediaType;
        }

        if(!isset($itemR -> mediatypes) || (isset($itemR -> mediatypes) && !in_array($itemR -> type,$itemR -> mediatypes))){
            if($params -> get('show_related_title',1)){
        ?>
        <a href="<?php echo $itemR -> link;?>"
           class="TzTitle">
            <?php echo $itemR -> title;?>
        </a>
        <?php }
        }?>
    </li>

    <?php }?>
    </ul>
</div>
 
        <?php endif;?>
    <?php endif;?>
<?php endif;?>