/*------------------------------------------------------------------------
# plg_extravote - ExtraVote Plugin
-------------------------------------------------------------------------*/

// Only define the TZPortfolioPlus namespace if not defined.
TZ_Portfolio_PlusAddOnContentVote = window.TZ_Portfolio_PlusAddOnContentVote || {
    "basePath": "",
    "addonId": 0,
    "articleId": 0,
    "referView": "",
    "referModule": "",
    "referLayout": ""
};


(function($, document, window, Joomla, TZ_Portfolio_Plus, addOnContentVote){
	"use strict";

	$.tzPortfolioPlusAddOnVote	= function(el, options){
		var addonVote = $.tzPortfolioPlusAddOnVote,
            $el	= $(el), reg = /\/$/,
            settings = $.extend(true,$.extend(true,{},$.tzPortfolioPlusAddOnVote.defaults),options),
            clicked = false, loadingHtml = settings.loadingTemplate,
            notifyOptions = settings.notification,
            basePath = settings.basePath;

		if(!basePath){
		    basePath    = addOnContentVote.basePath;
        }

        if (!reg.test(basePath)) {
            basePath += "/";
        }
        loadingHtml = loadingHtml.replace("{basepath}", basePath);

        $el.processVote = function($item){
            // Before voting
            settings.beforeVote();

            var url = "",
                $data = {}, loading = $(loadingHtml);

            // Adding loading
            $item.parents(settings.mainSelector).append(loading);

            if (settings.ajaxUrl) {
                url = settings.ajaxUrl;
            } else {
                url = "index.php?option=com_tz_portfolio_plus&view=addon&addon_task=vote.vote";
                $data["addon_id"] = $item.data("rating-addon-id") ? $item.data("rating-addon-id") : settings.addonId;

                if(!$item.data("rating-addon-id") && !options.addonId){
                    $data["addon_id"]   = addOnContentVote.addonId;
                }

                $data["cid"] = $item.data("rating-article-id") ? $item.data("rating-article-id") : settings.articleId;

                if(!$item.data("rating-article-id") && !options.articleId){
                    $data["cid"]   = addOnContentVote.articleId;
                }

                $data["user_rating"] = $item.data("rating-point") ? $item.data("rating-point") : settings.ratingPoint;

                if (settings.referView) {
                    $data["referview"] = settings.referView;
                }
                if(!options.referView && addOnContentVote.referView){
                    $data["referview"]   = addOnContentVote.referView;
                }
                if (settings.referModule) {
                    $data["refermodule"] = settings.referModule;
                }
                if(!options.referModule && addOnContentVote.referModule){
                    $data["refermodule"]   = addOnContentVote.referModule;
                }
                if (settings.referLayout) {
                    $data["referlayout"] = settings.referLayout;
                }
                if(!options.referLayout && addOnContentVote.referLayout){
                    $data["referlayout"]   = addOnContentVote.referLayout;
                }
                if (settings.Itemid) {
                    $data["Itemid"] = settings.Itemid;
                }
            }

            if(addonVote._notification !== undefined) {
                addonVote._notification.dismiss();
            }

            // Voting
            $.ajax({
                url: url,
                type: settings.ajaxType,
                data: $data,
                dataType: settings.ajaxDataType
            }).done(function (result) {

                if (result.success == true && !$.isEmptyObject(result.data)) {

                    var rEl = $item.parents(settings.mainSelector),
                        newRatingCount = result.data.rating_count,
                        newRatingPoint = (result.data.rating_sum /(newRatingCount));


                    // Set html when voted
                    var rItem  = rEl.find(settings.itemSelector),
                        rVotedIcon = $(settings.votedTemplate?settings.votedTemplate:"<span></span>");

                    if(!settings.votedTemplate) {
                        rVotedIcon.addClass(rItem.clone().removeClass(settings.votedClass).attr("class"));
                    }

                    rItem.removeClass(settings.votedClass)
                        .html("")
                        .not(":lt("+(5 - Math.ceil(newRatingPoint))+")")
                        .addClass(settings.votedClass);

                    if((newRatingPoint - parseInt(newRatingPoint)) > 0) {
                        rVotedIcon.css("width", ((100 - Math.round((newRatingPoint - parseInt(newRatingPoint)) * 100))) + "%");
                        rItem.eq(5 - Math.ceil(newRatingPoint)).html(rVotedIcon);
                    }

                    // Set voted count.
                    if(rEl.find(settings.counterSelector).length) {
                        var strCounter = Joomla.JText._('PLG_CONTENT_VOTE_VOTES'),
                            htmlCounter = settings.votedCounterTpl;
                        if(result.data.rating_count < 2){
                            strCounter  = Joomla.JText._('PLG_CONTENT_VOTE_VOTES_1');
                        }
                        strCounter  = strCounter.replace("%d", result.data.rating_count);
                        htmlCounter = htmlCounter.replace("{votedcounter}", strCounter);
                        rEl.find(settings.counterSelector).html(htmlCounter);
                    }


                    // If Javascript is not old version
                    if(!addonVote.originalJS) {
                        $el.itemClick(rEl.find(settings.itemSelector));
                    }
                }


                // Ajax voting complete
                settings.ajaxComplete(result, rEl, newRatingCount, newRatingPoint);

                var notification = new window.NotificationFx({
                    wrapper: (notifyOptions.wrapper?notifyOptions.wrapper:document.body),
                    message: result.message,
                    layout: notifyOptions.layout,
                    effect: notifyOptions.effect,
                    type: ((result.success != true) ? 'success' : 'error'),
                    ttl: notifyOptions.ttl,
                    onOpen: function () {
                        var notice = $(notification.options.wrapper).find(notification.ntf);
                        notice.width("auto");

                        // Call callback open
                        notifyOptions.onOpen();
                    },
                    onClose: function () {
                        // Call callback open
                        notifyOptions.onClose();
                    }
                });

                // Store cache of notification to disable it when click other button
                addonVote._notification = notification;

                // show the notification
                notification.show();

                // Remove loading
                loading.remove();

                clicked = false;
            });

            // After voting
            settings.afterVote($el);
        };

        $el.itemClick = function(obj){
            obj.off("click").on("click", function(e){
                e.preventDefault();

                if(clicked) {
                    return;
                }

                clicked = true;

                var $item = $(this);

                $el.processVote($item);
            });
        };

        if(!addonVote.originalJS) {
            $el.itemClick($el.find(settings.itemSelector));
        }else{
            $el.processVote($el);
        }

        // Call click method when ajaxloaded
        if(typeof TZ_Portfolio_Plus.infiniteScroll !== undefined) {
            if(typeof TZ_Portfolio_Plus.infiniteScroll.addAjaxComplete !== "undefined") {
                TZ_Portfolio_Plus.infiniteScroll.addAjaxComplete(function (newElements, masonryContainer) {
                    var $container = newElements.find(settings.mainSelector);

                    $container.tzPortfolioPlusAddOnVote(settings);
                    // Call back scroll ajax
                    settings.ajaxScrollComplete($container, newElements, masonryContainer);
                });
            }
        }

        $el.vars    = settings;
        $.data(el,"tzPortfolioPlusAddOnVote", $el);

        return this;
	};
    $.tzPortfolioPlusAddOnVote.originalJS   = false;

	$.tzPortfolioPlusAddOnVote.defaults	= {
	    basePath: "",
		ajaxUrl: "",
        ajaxType: "POST",
        ajaxDataType: "json",
		counterSelector: ".js-tpp-counter",
        mainSelector: ".content_rating",
		itemSelector: ".rating > a",
        addonId: 0,
        referModule: "",
        referView: "",
        referLayout: "",
        articleId: 0,
        ratingPoint: 0,
        ratingCount: 0,
        ratingTotal: 0,
        ratingMax: 5,
        votedClass: "voted",
        votedTemplate: "",
        votedCounterTpl: "<small>({votedcounter})</small>",
        // showCounter: 1,
		loadingTemplate: "<img src=\"{basepath}images/loading.gif\" class=\"loading\" border=\"0\" align=\"absmiddle\" /> ",
        notification: {
            wrapper : document.body,
            layout : 'growl',
            effect : 'scale',
            ttl : 3000,
            // callbacks
            onClose : function() { return false; },
            onOpen : function() { return false; }
        },
		beforeVote: function(){},
		afterVote: function(){},
        ajaxComplete: function(data){},
        ajaxScrollComplete: function(addonContainer, newElements, container){}
	};

	$.fn.tzPortfolioPlusAddOnVote = function (options) {
        if (options === undefined) options = {};
        if (typeof options === "object") {
            // Call function
            return this.each(function(index, value) {
                // Call function
                if ($(this).data("tzPortfolioPlusAddOnVote") === undefined) {
                    new $.tzPortfolioPlusAddOnVote(this, options);
                }else{
                    $(this).data('tzPortfolioPlusAddOnVote');
                }
            });
        }
    };


	// Function JUXVote for old version.
    window.JVXVote = function(obj,artId,ratePoint,rateTotal, rateCount,showCounter,styleType) {
        $.tzPortfolioPlusAddOnVote.originalJS   = true;

        var $item   = $(obj),
            $vote = $item.data("tzPortfolioPlusAddOnVote");
        if($vote !== undefined){
            $vote.processVote($item);
        }
        $item.tzPortfolioPlusAddOnVote({
            mainSelector: ".content_rating",
            itemSelector: ".rating > a",
            counterSelector: "#TzVote_"+artId,
            articleId: artId,
            ratingPoint: ratePoint,
            ratingTotal: rateTotal,
            ratingCount: rateCount
        });
    };

})(jQuery, document, window, Joomla, TZ_Portfolio_Plus, TZ_Portfolio_PlusAddOnContentVote);