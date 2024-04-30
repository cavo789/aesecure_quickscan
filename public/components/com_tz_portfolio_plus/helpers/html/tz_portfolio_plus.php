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

/**
 * Utility class to fire onContentPrepare for non-article based content.
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
class JHtmlTZ_Portfolio_Plus
{
    public static function taskLink($value, $i, $prefix = '', $enabled = true, $checkbox = 'cb',
                                    $publish_up = null, $publish_down = null, $attributes = array())
    {
        $states = array(
            1 => array('unpublish', 'COM_TZ_PORTFOLIO_PLUS_UNPUBLISH'),
            0 => array('publish', 'COM_TZ_PORTFOLIO_PLUS_PUBLISH'),
            -2 => array('publish', 'COM_TZ_PORTFOLIO_PLUS_RESTORE'),
            -3 => array('trash', 'JTRASH'),
            3 => array('trash', 'JTRASH'),
        );
        if(in_array($value, array(-3, 3))){
            return self::trashLink($i, $prefix, $enabled, $checkbox, $attributes);
        }
        return JHtml::link('javascript:void(0);', JText::_($states[$value][1]), array('onclick' => 'return listItemTask(\''
            .$checkbox.$i.'\',\''.$prefix.'.'.$states[$value][0].'\')'));
    }
    public static function trashLink($i, $prefix = '', $enabled = true, $checkbox = 'cb', $attributes = array())
    {
        if(!$enabled){
            return false;
        }
        $_attributes2   = array('class' => 'text-danger text-error');
        $_attributes    = array('onclick' => 'return listItemTask(\''
            .$checkbox.$i.'\',\''.$prefix.'.trash\')');
        $_attributes2   = array_merge($_attributes2, $attributes);
        $_attributes    = array_merge($_attributes2, $_attributes);

        return JHtml::link('javascript:void(0);', JText::_('JTRASH'), $_attributes);
    }
    public static function deleteLink($i, $prefix = '', $enabled = true, $checkbox = 'cb', $attributes = array())
    {
        if(!$enabled){
            return false;
        }
        $_attributes2   = array('class' => 'text-danger text-error');
        $_attributes    = array('onclick' => 'return listItemTask(\''
            .$checkbox.$i.'\',\''.$prefix.'.delete\')');
        $_attributes2   = array_merge($_attributes2, $attributes);
        $_attributes    = array_merge($_attributes2, $_attributes);

        return JHtml::link('javascript:void(0);', JText::_('COM_TZ_PORTFOLIO_PLUS_DELETE_PERMANENTLY'), $_attributes);
    }
    public static function approveLink($value, $i, $prefix = '', $enabled = true, $checkbox = 'cb')
    {
        $value  = $value != null?$value:3;
        if(!$enabled){
            return false;
        }

        if(!in_array($value, array(3, 4))){
            return false;
        }

        return JHtml::link('javascript:void(0);', JText::_('COM_TZ_PORTFOLIO_PLUS_APPROVE'), array('onclick' => 'return listItemTask(\''
            . $checkbox . $i . '\',\'' . $prefix . '.approve\')'));

    }
    public static function rejectLink($i, $prefix = '', $enabled = true, $checkbox = 'cb', $publish_up = null, $publish_down = null)
    {
        if(!$enabled){
            return false;
        }

        return JHtml::link('javascript:void(0);', JText::_('COM_TZ_PORTFOLIO_PLUS_REJECT'), array('onclick' => 'return listItemTask(\''
            . $checkbox . $i . '\',\'' . $prefix . '.reject\')', 'class' => 'text-error text-danger'));

    }
    public static function featuredLink($value, $i, $prefix = '', $checkbox = 'cb', $canChange = true)
    {
        $value  = $value != null?$value:0;
        $states = array(
            0 => array('featured', 'COM_TZ_PORTFOLIO_PLUS_FEATURE'),
            1 => array('unfeatured', 'COM_TZ_PORTFOLIO_PLUS_UNFEATURE')
        );
        return JHtml::link('javascript:void(0);', JText::_($states[$value][1]), array('onclick' => 'return listItemTask(\''
            .$checkbox.$i.'\',\''.$prefix.'.'.$states[$value][0].'\')'));
    }


    /**
     * Returns a checked-out icon
     *
     * @param   integer       $i           The row index.
     * @param   string        $editorName  The name of the editor.
     * @param   string        $time        The time that the object was checked out.
     * @param   string|array  $prefix      An optional task prefix or an array of options
     * @param   boolean       $enabled     True to enable the action.
     * @param   string        $checkbox    An optional prefix for checkboxes.
     *
     * @return  string  The HTML markup
     *
     * @since   1.6
     */
    public static function checkedout($i, $editorName, $time, $prefix = '', $enabled = false, $checkbox = 'cb')
    {
        $html   = JHtml::_('jgrid.checkedout', $i, $editorName, $time, $prefix, $enabled, $checkbox);

        // Replace icon
        if(!preg_match('/<[span|i].*?class=["|\'].*?(tps tp-lock).*?["|\']/', $html) && preg_match('/icon-checkedout/', $html)) {
            $html   = preg_replace('/(<[span|i].*?class=["|\'].*?)(icon-checkedout)(.*?["|\'])/','$1tps tp-lock$3', $html);
        }
        // Add btn-outline-secondary class
        if(!preg_match('/<[a|button].*?class=["|\'].*?(btn-outline-secondary).*?["|\']/', $html)){
            $html   = preg_replace('/(<[a|button].*?class=["|\'])(.*?["|\'])/','$1btn-outline-secondary $2', $html);
        }
        // Replace btn-sm class
        if(!preg_match('/<[a|button].*?class=["|\'].*?(btn-sm).*?["|\']/', $html)){
            $html   = preg_replace('/(<[a|button].*?class=["|\'])(.*?["|\'])/','$1btn-sm $2', $html);
        }
        return $html;
    }
}