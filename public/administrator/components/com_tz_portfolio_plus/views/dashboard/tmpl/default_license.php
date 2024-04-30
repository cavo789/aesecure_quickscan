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

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$doc    = Factory::getApplication() -> getDocument();
$params = JComponentHelper::getParams('com_tz_portfolio_plus');

$doc -> addScriptDeclaration('
    (function($, Joomla){
       $(document).ready(function(){
           $(".tp-license [data-tp-license-delete]").on("click", function(){
               if(confirm("'.htmlspecialchars(JText::_('COM_TZ_PORTFOLIO_PLUS_DELETE_LICENSE_CONFIRM')).'")) {
                   $.ajax({
                       type: "POST",
                       url: "index.php?option=com_tz_portfolio_plus",
                       data: {
                           "task": "license.deletelicense",
                           "license": $("[data-source-license]").val()
                       }
                   }).done(function (result) {
                       $("[data-tp-license-loading]").addClass("hide");

                       if (result.state == 400) {
                           Joomla.renderMessages({"error": [result.message]});
                           return false;
                       }

                       if (result.state == 200) {
                           window.location = "index.php?option=com_tz_portfolio_plus";
                       }
                   });
               }
           });
       });
    })(jQuery, Joomla);');
?>
<?php if($license = $this -> license){ ?>
<div class="tp-widget tp-license<?php echo $this -> license?' tp-pro':''; ?>">
    <h4 class="title text-uppercase"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LICENSE_INFO'); ?></h4>
    <ul class="inside">
        <li class="text-success"><b><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_IS_VERSION', JText::_('COM_TZ_PORTFOLIO_PLUS_PRO')); ?></b></li>
        <li>
            <div class="name"><?php echo JText::_('JGLOBAL_TITLE'); ?>:</div>
            <div class="value"><?php echo $license -> title; ?></div>
        </li>
        <li>
            <div class="name"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LICENSE'); ?>:</div>
            <div class="value"><?php echo $license -> reference; ?></div></li>
        <li>
            <div class="name"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DATE_EXPIRY'); ?>:</div>
            <div class="value"><?php echo $license -> expire; ?><?php
                if(TZ_Portfolio_PlusHelper::isLicenseExpired('expire')){
                    ?><span class="expired text-danger"><i class="icon-warning"></i><?php
                    echo JText::_('COM_TZ_PORTFOLIO_PLUS_EXPIRED'); ?></span><?php
                } ?>
            </div>
        </li>
        <li>
            <div class="name"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SUPPORT_VALID'); ?>:</div>
            <div class="value"><?php echo $license -> support_expire; ?><?php
                if(TZ_Portfolio_PlusHelper::isLicenseExpired('support_expire')){
                ?><span class="expired text-danger"><i class="icon-warning"></i><?php
                    echo JText::_('COM_TZ_PORTFOLIO_PLUS_EXPIRED'); ?></span><?php
                } ?>
            </div>
        </li>
        <li class="actions">
            <a href="javascript:" class="btn btn-danger btn-large" data-tp-license-delete><i class="tps tp-times"></i> <?php echo JText::_('JACTION_DELETE'); ?></a>
        </li>
    </ul>
</div>
<?php } ?>