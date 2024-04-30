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

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;

abstract class JHtmlTZSortablelist
{

    protected static $loaded = array();

    public static function sortable($tableId, $formId, $sortDir = 'asc', $saveOrderingUrl = null, $proceedSaveOrderButton = true, $nestedList = false)
    {
        // Only load once
        if (isset(static::$loaded[__METHOD__])) {
            return;
        }

        // Note: $i is required but has to be an optional argument in the function call due to argument order
        if (null === $saveOrderingUrl) {
            throw new InvalidArgumentException('$saveOrderingUrl is a required argument in JHtmlSortablelist::sortable');
        }

        // Depends on jQuery UI
        JHtml::_('jquery.ui', array('core', 'sortable'));

        JHtml::_('script', TZ_Portfolio_PlusUri::base(false, true).'/js/sortablelist.min.js', false, true);
        JHtml::_('stylesheet', 'jui/sortablelist.css', false, true, false);

        // Attach sortable to document
        Factory::getApplication() -> getDocument()->addScriptDeclaration("
			(function ($){
				$(document).ready(function (){
					var sortableList = new $.TZPortfolioPlusSortableList('#"
            . $tableId . " tbody','" . $formId . "','" . $sortDir . "' , '" . $saveOrderingUrl . "','','" . $nestedList . "');
				});
			})(jQuery);
			"
        );

        if ($proceedSaveOrderButton) {
            static::_proceedSaveOrderButton();
        }

        // Set static array
        static::$loaded[__METHOD__] = true;

        return;
    }

    public static function _proceedSaveOrderButton()
    {
        Factory::getApplication() -> getDocument()->addScriptDeclaration(
            "(function ($){
				$(document).ready(function (){
					var saveOrderButton = $('.saveorder');
					saveOrderButton.css({'opacity':'0.2', 'cursor':'default'}).attr('onclick','return false;');
					var oldOrderingValue = '';
					$('.text-area-order').focus(function ()
					{
						oldOrderingValue = $(this).attr('value');
					})
					.keyup(function (){
						var newOrderingValue = $(this).attr('value');
						if (oldOrderingValue != newOrderingValue)
						{
							saveOrderButton.css({'opacity':'1', 'cursor':'pointer'}).removeAttr('onclick')
						}
					});
				});
			})(jQuery);"
        );

        return;
    }
}