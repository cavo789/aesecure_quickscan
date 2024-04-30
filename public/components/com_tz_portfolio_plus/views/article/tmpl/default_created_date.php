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

$params = $this -> item -> params;

if($params -> get('show_create_date',1)){
    if(isset($this -> item -> created)){
        $tpParams   = TZ_Portfolio_PlusTemplate::getTemplate(true) -> params;
?>
<span class="tpp-item-created" itemprop="dateCreated">
    <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_CREATED_DATE_ON', JHtml::_('date', $this->item->created, $tpParams -> get('date_format', 'l, d F Y H:i'))); ?>
</span>
<?php }
}