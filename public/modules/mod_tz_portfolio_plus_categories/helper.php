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

class modTZ_Portfolio_PlusCategoriesHelper
{
    protected static $lookup = null;
    protected static $sView = null;
    protected static $sCatIds = array();

    public static function getList(&$params)
    {
        $db = JFactory::getDbo();
        $categoryName = null;
        $total = null;
        $catIds = null;
        $catIds = $params->get('catid');

        $query = $db->getQuery(true);
        $query->select('a.*');
        $query->select('l.title AS language_title,ag.title AS access_level');
        $query->select('ua.name AS author_name');

        if ($params->get('show_total', 1)) {
            $subQuery   = $db -> getQuery(true);
            $subQuery -> select('COUNT(DISTINCT mc.contentid)');
            $subQuery -> from('#__tz_portfolio_plus_content_category_map AS mc');
            $subQuery -> join('INNER', '#__tz_portfolio_plus_content AS c ON c.id = mc.contentid AND c.state = 1');
            $subQuery -> where('mc.catid = a.id');
            $query -> select('('.(string) $subQuery.') AS total');
        }

        $query->from($db->quoteName('#__tz_portfolio_plus_categories') . ' AS a');
        $query->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');
        $query->join('LEFT', $db->quoteName('#__users') . ' AS uc ON uc.id=a.checked_out');
        $query->join('LEFT', $db->quoteName('#__viewlevels') . ' AS ag ON ag.id = a.access');
        $query->join('LEFT', $db->quoteName('#__users') . ' AS ua ON ua.id = a.created_user_id');
        $query->where('a.published = 1');

        if(is_array($catIds)) {
            $catIds = array_filter($catIds);
            if(count($catIds)){
                $query -> where('a.id IN('.implode(',', $catIds).')');
            }
        }else{
            $query -> where('a.id = '.$catIds);
        }

        $query -> where('extension = '. $db -> quote('com_tz_portfolio_plus'));

        $query->group('a.id');
        $query->order('a.lft ASC');

        $db->setQuery($query);
        if ($items = $db->loadObjectList()) {
            jimport('joomla.filesystem.file');
            foreach ($items as $item) {
                $item->link = JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($item->id));
                $item -> params = new JRegistry($item -> params);
            }

            return $items;
        }
        return false;
    }


}

?>
