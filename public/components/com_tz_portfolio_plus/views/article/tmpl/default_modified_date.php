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

if($params -> get('show_modify_date',1)){
    if(isset($this -> item -> modified)) {
        $tpParams   = TZ_Portfolio_PlusTemplate::getTemplate(true) -> params;
    ?>
<span class="tpp-item-modified">
    <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_LAST_UPDATED', JHtml::_('date', $this->item->modified, $tpParams -> get('date_format', 'l, d F Y H:i'))); ?>
</span>
    <?php
    }
}