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

class JHtmlTppGrid extends JHtmlJGrid {

    public static function status($value, $i, $status = '', $prefix = '', $enabled = true, $checkbox = 'cb', $publish_up = null, $publish_down = null)
    {
        if (is_array($prefix))
        {
            $options = $prefix;
            $enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
            $checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
            $prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
        }

        $states = array(
            1 => array('unpublish', 'JPUBLISHED', 'JLIB_HTML_UNPUBLISH_ITEM', 'JPUBLISHED', true, 'publish', 'publish'),
            0 => array('publish', 'JUNPUBLISHED', 'JLIB_HTML_PUBLISH_ITEM', 'JUNPUBLISHED', true, 'unpublish', 'unpublish'),
            2 => array('unpublish', 'JARCHIVED', 'JLIB_HTML_UNPUBLISH_ITEM', 'JARCHIVED', true, 'archive', 'archive'),
            -2 => array('publish', 'JTRASHED', 'JLIB_HTML_PUBLISH_ITEM', 'JTRASHED', true, 'trash', 'trash'),
        );


        $statuses   = array(
            1 => 'JLIB_HTML_PUBLISH_ITEM',
            0 => 'JLIB_HTML_UNPUBLISH_ITEM',
            2 => 'JARCHIVED',
            -3 => 'COM_TZ_PORTFOLIO_PLUS_DRAFT',
        );

        if($value == -2 && $status != '' && $status != null){
            $states[$value][2]  = $statuses[$status];
        }

        // Special state for dates
        if ($publish_up || $publish_down)
        {
            $nullDate = Factory::getDbo()->getNullDate();
            $nowDate = Factory::getDate()->toUnix();

            $tz = Factory::getUser()->getTimezone();

            $publish_up = ($publish_up != $nullDate) ? Factory::getDate($publish_up, 'UTC')->setTimeZone($tz) : false;
            $publish_down = ($publish_down != $nullDate) ? Factory::getDate($publish_down, 'UTC')->setTimeZone($tz) : false;

            // Create tip text, only we have publish up or down settings
            $tips = array();

            if ($publish_up)
            {
                $tips[] = JText::sprintf('JLIB_HTML_PUBLISHED_START', JHtml::_('date', $publish_up, JText::_('DATE_FORMAT_LC5'), 'UTC'));
            }

            if ($publish_down)
            {
                $tips[] = JText::sprintf('JLIB_HTML_PUBLISHED_FINISHED', JHtml::_('date', $publish_down, JText::_('DATE_FORMAT_LC5'), 'UTC'));
            }

            $tip = empty($tips) ? false : implode('<br />', $tips);

            // Add tips and special titles
            foreach ($states as $key => $state)
            {
                // Create special titles for published items
                if ($key == 1)
                {
                    $states[$key][2] = $states[$key][3] = 'JLIB_HTML_PUBLISHED_ITEM';

                    if ($publish_up > $nullDate && $nowDate < $publish_up->toUnix())
                    {
                        $states[$key][2] = $states[$key][3] = 'JLIB_HTML_PUBLISHED_PENDING_ITEM';
                        $states[$key][5] = $states[$key][6] = 'pending';
                    }

                    if ($publish_down > $nullDate && $nowDate > $publish_down->toUnix())
                    {
                        $states[$key][2] = $states[$key][3] = 'JLIB_HTML_PUBLISHED_EXPIRED_ITEM';
                        $states[$key][5] = $states[$key][6] = 'expired';
                    }
                }

                // Add tips to titles
                if ($tip)
                {
                    $states[$key][1] = JText::_($states[$key][1]);
                    $states[$key][2] = JText::_($states[$key][2]) . '<br />' . $tip;
                    $states[$key][3] = JText::_($states[$key][3]) . '<br />' . $tip;
                    $states[$key][4] = true;
                }
            }

            return static::state($states, $value, $i, array('prefix' => $prefix, 'translate' => !$tip), $enabled, true, $checkbox);
        }

        return static::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
    }

    public static function approve($i, $prefix = '', $enabled = true, $checkbox = 'cb'){

        if (is_array($prefix))
        {
            $options = $prefix;
            $prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
        }

        return static::action(
            $i, 'approve', $prefix, 'COM_TZ_PORTFOLIO_PLUS_APPROVE', 'COM_TZ_PORTFOLIO_PLUS_APPROVE', '', true,
            'checkmark-2 text-success', '', $enabled, true, $checkbox
        );
    }

    public static function reject($i, $prefix = '', $enabled = true, $checkbox = 'cb'){

        if (is_array($prefix))
        {
            $options = $prefix;
            $prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
        }

        return static::action(
            $i, 'reject', $prefix, 'COM_TZ_PORTFOLIO_PLUS_REJECT', 'COM_TZ_PORTFOLIO_PLUS_REJECT', '', true,
            'minus text-danger text-error', '', $enabled, true, $checkbox
        );
    }
}