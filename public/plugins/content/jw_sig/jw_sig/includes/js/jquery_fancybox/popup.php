<?php
/**
 * @version      4.2
 * @package      Simple Image Gallery (plugin)
 * @author       JoomlaWorks - https://www.joomlaworks.net
 * @copyright    Copyright (c) 2006 - 2022 JoomlaWorks Ltd. All rights reserved.
 * @license      GNU/GPL license: https://www.gnu.org/licenses/gpl.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$extraClass = 'fancybox-gallery';
$customLinkAttributes = 'data-fancybox="gallery'.$gal_id.'"';

$stylesheets = array(
    'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css'
);
$stylesheetDeclarations = array();
$scripts = array(
    'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js'
);

if(!defined('PE_FANCYBOX_LOADED')){
    define('PE_FANCYBOX_LOADED', true);
    $scriptDeclarations = array("
        (function($) {
            $(document).ready(function() {
                $.fancybox.defaults.i18n.en = {
                    CLOSE: '".JText::_('JW_PLG_SIG_FB_CLOSE')."',
                    NEXT: '".JText::_('JW_PLG_SIG_FB_NEXT')."',
                    PREV: '".JText::_('JW_PLG_SIG_FB_PREVIOUS')."',
                    ERROR: '".JText::_('JW_PLG_SIG_FB_REQUEST_CANNOT_BE_LOADED')."',
                    PLAY_START: '".JText::_('JW_PLG_SIG_FB_START_SLIDESHOW')."',
                    PLAY_STOP: '".JText::_('JW_PLG_SIG_FB_PAUSE_SLIDESHOW')."',
                    FULL_SCREEN: '".JText::_('JW_PLG_SIG_FB_FULL_SCREEN')."',
                    THUMBS: '".JText::_('JW_PLG_SIG_FB_THUMBS')."',
                    DOWNLOAD: '".JText::_('JW_PLG_SIG_FB_DOWNLOAD')."',
                    SHARE: '".JText::_('JW_PLG_SIG_FB_SHARE')."',
                    ZOOM: '".JText::_('JW_PLG_SIG_FB_ZOOM')."'
                };
                $.fancybox.defaults.lang = 'en';
                $('a.fancybox-gallery').fancybox({
                    buttons: [
                        'slideShow',
                        'fullScreen',
                        'thumbs',
                        'share',
                        'download',
                        //'zoom',
                        'close'
                    ],
                    beforeShow: function(instance, current) {
                        if (current.type === 'image') {
                            var title = current.opts.\$orig.attr('title');
                            current.opts.caption = (title.length ? '<b class=\"fancyboxCounter\">".JText::_('JW_PLG_SIG_FB_IMAGE')." ' + (current.index + 1) + ' ".JText::_('JW_PLG_SIG_FB_OF')." ' + instance.group.length + '</b>' + ' | ' + title : '');
                        }
                    }
                });
            });
        })(jQuery);
    ");
} else {
    $scriptDeclarations = array();
}
