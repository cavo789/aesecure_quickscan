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

if($params -> get('show_create_date',1)) {
    if (isset($this->item->created)) {
        ?>
        <div class="tpDate hasTooltip" title="<?php
        echo JText::_('TPL_ELEGANT_CREATED_DATE');?>">
            <i class="tp tp-clock-o"></i>
            <time class="tpCreated" itemprop="datePublished">
                <?php echo JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC')); ?>
            </time>
        </div>


    <?php }
}