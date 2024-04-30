(function ($) {
    "use strict";
    $.tppServerList   = function(el, options){
        var $tppServer   = $(el),
            settings    = $.extend(true,$.tppServerList.defaults,options);
        var serverList = function(){
            var pagItems    = $tppServer.find(settings.pagSelector + " " + settings.pagItemSelector);
            if(pagItems.length){
                pagItems.each(function(){
                    var $this = $(this);
                    $this.on("click", function(event){
                        event.preventDefault();
                        event.stopPropagation();
                        $tppServer.find(settings.loadingSelector).show();
                        serverListAjax($this);
                    });
                });
            }
        };

        // // var myModal = new bootstrap.Modal($(".modal"));
        // // var myModal = new bootstrap.Modal(document.getElementById('tpp-addon__modal-test'));
        // var myModal = new bootstrap.Modal($('#tpp-addon__modal-test'));
        // // console.log(myModal);
        // // console.log(document.getElementById('tpp-addon__modal-test'));
        // $("[data-bs-toggle=tz_modal]").on("click", function(){
        //    myModal.toggle();
        // });

        var serverListAjax = function(elPagination){
            var ajax = settings.ajax;

            ajax.url        = settings.url;
            ajax.data.view  = settings.view;

            if(typeof serverList !== "undefined" && typeof elPagination !== "undefined"){
                ajax.data.limitstart  = elPagination.data("tpp-limitstart");
            }

            if(typeof $.fn.tpSerializeObject !== "undefined") {
                $.extend(ajax.data, $tppServer.find(settings.filListSelector).tpSerializeObject());
            }

            if(settings.formToken) {
                ajax.data[settings.formToken] = 1;
            }
            $tppServer.find(settings.filterSelector).hide();

            ajax.success    = function(result){
                if(result && result.data && result.data.html){
                    var exList  = $tppServer.find(settings.listSelector);
                    exList.html(result.data.html);
                    $tppServer.find(settings.pagSelector).html(result.data.pagination);
                    $tppServer.find(settings.filterSelector).show();

                    exList.find(".modal").on("show.bs.modal", function () {
                        if(typeof $(this).attr("data-iframe") !== "undefined"){
                            var modalBody = $(this).find(".modal-body"),
                                iframeHtml = $($(this).attr("data-iframe"));

                            modalBody.find("iframe").remove();
                            modalBody.prepend(iframeHtml);
                        }else {
                            var url = exList.find(".action-links [data-toggle=modal][href=\"#" + $(this).attr("id") + "\"]").data("url");

                            var modalBody = $(this).find(".modal-body"),
                                iframeHtml = $(settings.iframeHtml);
                            iframeHtml.attr("src", url);

                            modalBody.find("iframe").remove();
                            modalBody.prepend(iframeHtml);
                        }
                    }).on("shown.bs.modal", function() {
                        if(typeof $(this).attr("data-iframe") === "undefined"){
                            var modal  = $(this),
                                iframe  = $(this).find("iframe"),
                                modalHeight = modal.outerHeight(true),
                                modalHeaderHeight = modal.find("div.modal-header:visible").outerHeight(true),
                                modalBodyHeightOuter = modal.find("div.modal-body:visible").outerHeight(true),
                                modalBodyHeight = modal.find("div.modal-body:visible").height(),
                                modalFooterHeight = modal.find("div.modal-footer:visible").outerHeight(true),
                                padding = modal.position().top,
                                maxModalHeight = ($(window).height()-(padding*2)),
                                modalBodyPadding = (modalBodyHeightOuter-modalBodyHeight),
                                maxModalBodyHeight = maxModalHeight-(modalHeaderHeight+modalFooterHeight+modalBodyPadding);

                            var iframeHeight = iframe.height();

                            if (iframeHeight > maxModalBodyHeight){
                                modal.find(".modal-body").css({"max-height": maxModalBodyHeight, "overflow-y": "initial"});
                                iframe.css("max-height", maxModalBodyHeight-modalBodyPadding);
                            }
                        }
                    });

                    if(typeof serverList === "function"){
                        serverList();
                    }
                    serverListInstallNow();
                    $tppServer.find(settings.loadingSelector).hide();
                }else{
                    $tppServer.find(settings.errorSelector).show();
                    $tppServer.find(settings.loadingSelector).hide();
                    $tppServer.find(settings.filterSelector).show();
                }
            };

            ajax.error   = function(){
                $tppServer.find(settings.filterSelector).hide();
                $tppServer.find(settings.loadingSelector).hide();
                $tppServer.find(settings.errorSelector).show();
            };

            $.ajax(ajax);
        };
        serverListAjax();

        var serverListInstallNow   = function(){
            $tppServer.on("click", settings.listSelector + " " + settings.installSelector, function(e){
                e.preventDefault();
                var $this   = $(this),
                    installNow = settings.installNow,
                    href    = $this.attr("href"),
                    loading = $(installNow.loadingHtml);
                if(!$this.hasClass("installing")) {
                    $this.html(loading).addClass("installing");

                    var ajax = installNow.ajax;

                    ajax.url = settings.url;
                    ajax.data.pProduceUrl = href;
                    ajax.data[settings.formToken] = 1;
                    ajax.success = function(result){
                        if(result.success){
                            $this.addClass("disabled").html(installNow.installedHtml);
                        }

                        // Always redirect that can show message queue from session
                        if (result.data.redirect) {
                            location.href = result.data.redirect;
                        } else {
                            location.href = settings.url + "&view="+ settings.view
                                + "&layout=" + settings.layout;
                        }
                    };

                    $.ajax(ajax);
                }
            });
        };
        serverListInstallNow();
    };
    $.tppServerList.defaults    = {
        "url"               : "index.php?option=com_tz_portfolio_plus",
        "view"              : "addon",
        "layout"            : "upload",
        "loading"           : "",
        "iframeHtml"        : "",
        "formToken"         : "",
        "filterSelector"    : ".js-stools",
        "filListSelector"   : "[name^=list]",
        "loadingSelector"   : "[data-tpp-loading]",
        "errorSelector"     : "[data-tpp-error]",
        "pagSelector"       : "[data-tpp-pagination]",
        "pagItemSelector"   : "[data-tpp-limitstart]",
        "listSelector"      : "[data-tpp-extension-list]",
        "installSelector"   : ".install-now",
        "ajax"              : {
            "type"          : "POST",
            "dataType"      : "json",
            "data"          : {
                "format"    : "ajax",
                "layout"    : "upload_list_item"
            }
        },
        "installNow"        : {
            "loadingHtml"   : "",
            "installedHtml" : "",
            "ajax"          : {
                "type"      : "POST",
                "dataType"  : "json",
                "data"      : {
                    "task"  : "addon.ajax_install"
                }
            }
        }
    };
    $.fn.tppServerList  = function(options){
        if(options === undefined) options   = {};
        if(typeof options === 'object'){
            // Call function
            return this.each(function() {
                // Call function
                if ($(this).data("tppServerList") === undefined) {
                    new $.tppServerList(this, options);
                }else{
                    $(this).data('tppServerList');
                }
            });
        }
    }
})(jQuery);