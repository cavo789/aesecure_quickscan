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

abstract class JHtmlTZBatch
{
    public static function item($extension)
    {
        // Create the copy/move options.
        $options = array(
            JHtml::_('select.option', 'c', JText::_('JLIB_HTML_BATCH_COPY')),
            JHtml::_('select.option', 'm', JText::_('JLIB_HTML_BATCH_MOVE'))
        );

        // Create the batch selector to change select the category by which to move or copy.
        return
            '<label id="batch-choose-action-lbl" for="batch-choose-action">' . JText::_('JLIB_HTML_BATCH_MENU_LABEL') . '</label>'
            . '<div id="batch-choose-action" class="control-group">'
            . '<select name="batch[category_id]" class="inputbox" id="batch-category-id">'
            . '<option value="">' . JText::_('JLIB_HTML_BATCH_NO_CATEGORY') . '</option>'
            . JHtml::_('select.options', JHtml::_('tzcategory.options', $extension))
            . '</select>'
            . '</div>'
            . '<div id="batch-copy-move" class="control-group radio">'
            . JText::_('JLIB_HTML_BATCH_MOVE_QUESTION')
            . JHtml::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm')
            . '</div>';
    }
}