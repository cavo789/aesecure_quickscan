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

JLoader::import('com_tz_portfolio_plus.helpers.route', JPATH_SITE . '/components');

class modTZ_Portfolio_PlusArchiveHelper
{
    static function getList(&$params)
    {
        //get database
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $subQuery = $db->getQuery(true);

        $query->select('MONTH(created) AS created_month, created, id, title, YEAR(created) AS created_year');
        $query->from('#__tz_portfolio_plus_content');
        $query->where('checked_out = 0');
        $query->where('state = 1');
        $query->group('created_year DESC, created_month DESC');


        $subQuery->select('COUNT(*)');
        $subQuery->from('#__tz_portfolio_plus_content');
        $subQuery->where('checked_out = 0');
        $subQuery->where('MONTH(created) = created_month AND YEAR(created) = created_year');
        $subQuery->where('state = 1');
        $query->select('(' . $subQuery->__toString() . ') AS total');

        // Filter by language
        if (JFactory::getApplication()->getLanguageFilter()) {
            $query->where('language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
        }

        $db->setQuery($query, 0, $params->get('count'));
        $rows = (array)$db->loadObjectList();

        $i = 0;
        $lists = array();
        if ($rows) {
            foreach ($rows as $row) {
                $date = JFactory::getDate($row->created);

                $created_month = $date->format('n');
                $created_year = $date->format('Y');

                $created_year_cal = JHtml::_('date', $row->created, 'Y');
                $month_name_cal = JHtml::_('date', $row->created, 'F');

                $lists[$i] = new stdClass;


//                $lists[$i]->link = JRoute::_('index.php?option=com_tz_portfolio_plus&view=date&year='
//                    . $created_year . '&month=' . $created_month . '&Itemid=' . $params->get('tzmenuitem'));

                $lists[$i] -> link  = TZ_Portfolio_PlusHelperRoute::getDateRoute($created_year, $created_month, 0, $params -> get('tzmenuitem'));
                $lists[$i]->text = JText::sprintf('MOD_TZ_PORTFOLIO_ARTICLES_ARCHIVE_DATE', $month_name_cal, $created_year_cal);

                $lists[$i]->total = 0;
                if (isset($row->total)) {
                    $lists[$i]->total = $row->total;
                }
                $i++;
            }
        }

        return $lists;
    }
}
