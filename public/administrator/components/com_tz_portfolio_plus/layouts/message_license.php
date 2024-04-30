<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

if(COM_TZ_PORTFOLIO_PLUS_EDITION == 'free'){

    $modalId    = 'tpp-modal__licenses';
    $params     = JComponentHelper::getParams('com_tz_portfolio_plus');
    $xml        = simplexml_load_file(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/tz_portfolio_plus.xml');

    $doc        = Factory::getApplication() -> getDocument();

    $doc -> addScriptDeclaration('(function($, Joomla){
            $(document).ready(function(){
                $(".tpp-upgrade-pro [data-tp-license-active]").on("click", function(){

                    var $this    = $(this),
                        $action = $.data($this[0], "license-action");

                    $("[data-tp-license-loading]").removeClass("hide");
                    $this.addClass("hide");

                    $.ajax({
                        type: "POST",
                        url: "index.php?option=com_tz_portfolio_plus",
                        data: {
                            "task": "license.verify",
                            "token_key": "'.$params -> get('token_key').'"
                        }
                    }).done(function(result){
                        if (result.state == 400) {
                            Joomla.renderMessages({"error": [result.message]});
                            $("[data-tp-license-loading]").addClass("hide");
                            $this.removeClass("hide");
                            return false;
                        }
                        // Valid licenses
                        if (result.state == 200) {
                            $("[data-licenses-placeholder]").html(result.html);
                            if(result.licenses.length > 1) {
                                $("#tpp-modal__licenses").modal("show");
                            }
                            if(result.licenses.length === 1) {
                                // $.data($this[0], "license-action", "active");
                                $("#'.$modalId.' [data-tp-license-accept-active]").trigger("click");
                            }else {
                                $("[data-tp-license-loading]").addClass("hide");
                                $("[data-tp-license-active]").removeClass("hide");
                            }
                        }
                    });
                });
                $("#'.$modalId.' [data-tp-license-accept-active]").on("click", function(){
                    var $this   = $(this);
                    if($this.hasClass("activating")){
                        return;
                    }
                    $this.addClass("activating").html("'.htmlspecialchars(JText::_('COM_TZ_PORTFOLIO_PLUS_ACTIVING')).'<span class=\"progress progress-loading\"></span>");
                    $.ajax({
                        type: "POST",
                        url: "index.php?option=com_tz_portfolio_plus",
                        data: {
                            "task": "license.activepro",
                            "license": $("[data-source-license]").val()
                        }
                    }).done(function (result) {

                        if (result.state == 400) {

                            Joomla.renderMessages({"error": [result.message]});
                            $("[data-tp-license-loading]").addClass("hide");
                            $("[data-tp-license-active]").removeClass("hide");
                            return false;
                        }

                        if (result.state == 200) {
                            // $("[data-licenses]").addClass("hide");
                            window.location = "index.php?option=com_tz_portfolio_plus";
                        } else {
                            $this.removeClass("hide");
                        }
                    });
                });
                $("#'.$modalId.' [data-tp-license-dismiss]").on("click", function () {
                    $("#'.$modalId.' [data-tp-license-accept-active]").removeClass("activating").text("'.htmlspecialchars(JText::_('COM_TZ_PORTFOLIO_PLUS_ACTIVE')).'");
                    $("#'.$modalId.' [data-licenses-placeholder]").html("");
                    $("#'.$modalId.'").modal("hide");
                });
            });
        })(jQuery, Joomla);')
?>

<div class="alert tp-widget tpp-upgrade-pro">
    <div class="content ps-0">
        <span class="tps tp-flag-checkered tpp-upgrade-pro__icon"></span>
        <div class="content__inner">
            <h4><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_IS_VERSION', JText::_('COM_TZ_PORTFOLIO_PLUS_FREE')); ?></h4>
            <p><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_FREE_DESC', $xml -> pricingUrl, $xml -> tokenUrl);?></p>
        </div>

        <a href="javascript:" class="btn btn-danger btn-large" data-tp-license-active><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ACTIVE_NOW'); ?></a>
        <a href="javascript:void(0);" class="btn btn-danger btn-large loading hide disabled" data-tp-license-loading>
            <span><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SETUP_LOADING'); ?></span>
            <span class="progress progress-loading"></span>
        </a>
    </div>
</div>

    <?php ob_start(); ?>
    <div data-licenses>
        <p class="alert alert-info"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SETUP_METHOD_SELECT_LICENSE_INFO');?></p>
        <div data-licenses-placeholder></div>
    </div>
    <?php $bodyLicenses = ob_get_contents();
    ob_end_clean(); ?>
    <?php echo JHtml::_(
        'bootstrap.renderModal',
        $modalId,
        array(
            'title'       => 'Licenses',
            'height'      => '400px',
            'width'       => '800px',
            'bodyHeight'  => '70',
            'modalWidth'  => '40',
            'footer'      => '<a role="button" class="btn btn-primary" data-tp-license-accept-active>' . JText::_('COM_TZ_PORTFOLIO_PLUS_ACTIVE') . '</a> <a role="button" class="btn" data-tp-license-dismiss aria-hidden="true">' . JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
        ), $bodyLicenses
    );?>
<?php }