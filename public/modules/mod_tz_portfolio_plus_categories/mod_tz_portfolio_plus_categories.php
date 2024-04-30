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

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

JLoader::import('com_tz_portfolio_plus.libraries.helper.modulehelper', JPATH_ADMINISTRATOR.'/components');

$document = JFactory::getDocument();

if($params -> get('enable_bootstrap', 0) && $params -> get('enable_bootstrap_js', 1)) {
    if( $params -> get('bootstrapversion', 3) == 4) {
        $document->addScript(TZ_Portfolio_PlusUri::base(true) . '/vendor/bootstrap/js/bootstrap.min.js',
            array('version' => 'auto'));
        $document->addScript(TZ_Portfolio_PlusUri::base(true) . '/vendor/bootstrap/js/bootstrap.bundle.min.js',
            array('version' => 'auto'));
    }else{
        $document -> addScript(TZ_Portfolio_PlusUri::base(true).'/bootstrap/js/bootstrap.min.js',
            array('version' => 'auto'));
    }
}

if($params -> get('enable_lazyload', 0)){
    $document -> addScript(TZ_Portfolio_PlusUri::base(true).'/js/jquery.lazyload.min.js',
        array('version' => 'auto'));
    $document -> addScriptDeclaration('
    (function($){
            $(document).ready(function(){
                if(typeof $.fn.lazyload !== "undefined"){
                    var $main = $(".category-menu"),
                        $imgs   = $main.find("img.lazyload");
                        
                    if(!$imgs.length){
                        $imgs = $main.find("img:not(.lazyloaded)").addClass("lazyload");
                    }

                    $imgs.attr("data-src", function () {
                        var _imgEl = $(this),
                            src = _imgEl.attr("src");
                        _imgEl.css({
                            "padding-top": function () {
                                return this.height;
                            },
                            "padding-left": function () {
                                return this.width;
                            }
                        });
                        return src;
                    });
                    $imgs.lazyload({
                        failure_limit: Math.max($imgs.length - 1, 0),
                        placeholder: "",
                        data_attribute: "src",
                        appear: function (elements_left, settings) {
                            if (!this.loaded) {
                                $(this).removeClass("lazyload").addClass("lazyloading");
                            }
                        },
                        load: function (elements_left, settings) {
                            if (this.loaded) {
                                $(this).removeClass("lazyloading").addClass("lazyloaded").css({
                                    "padding-top": "",
                                    "padding-left": ""
                                });
                            }
                        }
                    });
                }
            });
        })(jQuery);
    ');
}

$document->addStyleSheet(JUri::base(true).'/modules/mod_tz_portfolio_plus_categories/css/style.css');

$list = modTZ_Portfolio_PlusCategoriesHelper::getList($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_COMPAT, 'UTF-8');
require TZ_Portfolio_PlusModuleHelper::getTZLayoutPath($module, $params->get('layout', 'default'));
?>
