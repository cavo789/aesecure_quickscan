/**
 * ------------------------------------------------------------------------
 * resizeImage Plugin
 * ------------------------------------------------------------------------
 * Copyright (C) 2012-2013 SUNLAND Co., JSC. All Rights Reserved.
 * license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: Sonle
 * Email: sonlv@templaza.com
 * Websites: http://www.templaza.com
 * ------------------------------------------------------------------------
 */


(function($,window){
    'use strict';
    window.TzPortfolioPlusArticlesResizeImage = function (obj,wstage,hstage){
        var widthStage;
        var heightStage ;
        var widthImage;
        var heightImage;
        if(Object.keys(obj).length > 1) {
            obj.each(function (i, el) {
                heightStage = jQuery(this).height();
                widthStage = jQuery(this).width();
                if(wstage){
                    widthStage  = wstage;
                }
                if(hstage){
                    heightStage  = hstage;
                }
                var img_url = jQuery(this).find('img').attr('src');
                var image = new Image();
                image.src = img_url;

                widthImage = image.naturalWidth;
                heightImage = image.naturalHeight;
                var tzimg = new resizeImage(widthImage, heightImage, widthStage, heightStage);
                jQuery(this).find('img').css({top: tzimg.top, left: tzimg.left, width: tzimg.width, height: tzimg.height});
            });
        }else{
            heightStage = obj.height();
            widthStage = obj.width();
            var img_url = obj.find('img').attr('src');
            var image = new Image();
            image.src = img_url;

            if(wstage){
                widthStage  = wstage;
            }
            if(hstage){
                heightStage  = hstage;
            }

            widthImage = image.naturalWidth;
            heightImage = image.naturalHeight;
            // console.log('widthImage='+widthImage+'heightImage='+heightImage+'heightStage='+heightStage);
            var tzimg = new resizeImage(widthImage, heightImage, widthStage, heightStage);
            obj.find('img').css({top: tzimg.top, left: tzimg.left, width: tzimg.width, height: tzimg.height});
        }
    };

    var resizeImage = function (widthImage, heightImage, widthStage, heightStage) {

        var escImageX = widthStage / widthImage;
        var escImageY = heightStage / heightImage;

        var escalaImage = (escImageX > escImageY) ? escImageX : escImageY;

        var widthV = widthImage * escalaImage;
        var heightV = heightImage * escalaImage;
        var posImageY = 0;
        var posImageX = 0;

        if (heightV > heightStage) {
            posImageY = (heightStage - heightV) / 2;
        }

        if (widthV > widthStage) {
            posImageX = (widthStage - widthV) / 2;
        }
        return { top: posImageY, left: posImageX, width: widthV, height: heightV };
    };
})(jQuery, window);