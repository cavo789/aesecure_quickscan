<?php

/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    Sonny

# copyright Copyright (C) 2020 tzportfolio.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum.html

-------------------------------------------------------------------------*/

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 *
 * Provides a pop up date picker linked to a button.
 * Optionally may be filtered to use user's or server's time zone.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldTZMargin extends JFormField
{

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    public $type = 'TZMargin';

    /**
     * Method to get the field input markup.
     *
     * @return  string   The field input markup.
     *
     * @since   11.1
     */
    protected function getInput()
    {
        return JHtml::_('TZResponsiveBox.generation', 'margin', $this->name, $this->id, $this->value);
    }
}