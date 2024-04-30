<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2011-2019 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;

$adoTotal       = 0;
$stlTotal       = 0;
$stlInstTotal   = 0;
$adoInstTotal   = 0;
$adosUpdateTotal= 0;
$stlsUpdateTotal= 0;
?>
<script>
    (function($){
        "use strict";
        $(document).ready(function(){
           $.ajax({
               "url": "index.php?option=com_tz_portfolio_plus",
               "type": "POST",
               "dataType": "json",
               "data": {
                   "task": "dashboard.statistics"
               },
               success: function(result){
                   if(result && result.success && result.data) {
                       var statistcs = result.data,
                           tpStatistic  = $(".tp-statistic");

                       tpStatistic.find("[data-addon-total]").html(statistcs.addons.total);
                       tpStatistic.find("[data-addon-update]").html(statistcs.addons.update);
                       tpStatistic.find("[data-addon-installed]").html(statistcs.addons.installed);

                       tpStatistic.find("[data-style-total]").html(statistcs.styles.total);
                       tpStatistic.find("[data-style-update]").html(statistcs.styles.update);
                       tpStatistic.find("[data-style-installed]").html(statistcs.styles.installed);

                       tpStatistic.find("[data-statistic-checking]").hide();
                   }
               }
           });
        });
    })(jQuery);
</script>
<div class="tp-statistic">
    <?php echo JHtml::_('tzbootstrap.addrow');?>
        <div class="span6 col-md-6">
            <div class="tp-widget">
                <h4 class="title text-uppercase"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON_STATISTICS'); ?>
                    <small class="small" data-statistic-checking>
                        <i class="tps tp-circle-notch tp-spin"></i> <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_CHECKING');?>...</small></h4>
                <ul class="inside">
                    <li>
                        <span class="name"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_STATISTIC_TOTAL',
                                JText::_('COM_TZ_PORTFOLIO_PLUS_ADDONS'))?>:</span>
                        <span class="value badge badge-info bg-info rounded" data-addon-total>0</span>
                    </li>
                    <li>
                        <span class="name"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_STATISTIC_TOTAL_INSTALLED',
                                JText::_('COM_TZ_PORTFOLIO_PLUS_ADDONS'))?>:</span>
                        <a href="index.php?option=com_tz_portfolio_plus&view=addons" class="value badge badge-success bg-success rounded" data-addon-installed>0</a>
                    </li>
                    <li>
                        <span class="name"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_STATISTIC_TOTAL_NEED_UPDATE',
                                JText::_('COM_TZ_PORTFOLIO_PLUS_ADDONS'))?>:</span>
                        <a href="index.php?option=com_tz_portfolio_plus&view=addon&layout=upload" class="value badge badge-important bg-danger rounded" data-addon-update>0</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="span6 col-md-6">
            <div class="tp-widget">
                <h4 class="title text-uppercase"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_STYLE_STATISTICS'); ?>
                    <small class="small" data-statistic-checking>
                        <i class="tps tp-circle-notch tp-spin"></i> <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_CHECKING');?>...</small>
                </h4>
                <ul class="inside">
                    <li>
                        <span class="name"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_STATISTIC_TOTAL',
                                JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATES'))?>:</span>
                        <span class="value badge badge-info bg-info rounded" data-style-total>0</span>
                    </li>
                    <li>
                        <span class="name"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_STATISTIC_TOTAL_INSTALLED',
                                JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATES'))?>:</span>
                        <a href="index.php?option=com_tz_portfolio_plus&view=templates" class="value badge badge-success bg-success rounded" data-style-installed>0</a>
                    </li>
                    <li>
                        <span class="name"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_STATISTIC_TOTAL_NEED_UPDATE',
                                JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATES'))?>:</span>
                        <a href="index.php?option=com_tz_portfolio_plus&view=template&layout=upload" class="value badge badge-important badge-danger bg-danger rounded" data-style-update>0</a>
                    </li>
                </ul>
            </div>
        </div>
    <?php echo JHtml::_('tzbootstrap.endrow');?>
</div>


