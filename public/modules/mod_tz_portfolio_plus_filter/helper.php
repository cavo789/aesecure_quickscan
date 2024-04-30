<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2016 tzportfolio.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Family Website: http://www.templaza.com

# Technical Support:  Forum - http://tzportfolio.com/Forum

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

// Get article's extrafields
JLoader::import('extrafields', JPATH_SITE.'/components/com_tz_portfolio_plus/helpers');
JLoader::import('route', JPATH_SITE.'/components/com_tz_portfolio_plus/helpers');
JLoader::import('categories', JPATH_SITE.'/components/com_tz_portfolio_plus/helpers');

class modTZ_Portfolio_PlusFilterHelper
{
    public static function getAdvFilterFields($params){
        if($advfilter = TZ_Portfolio_PlusFrontHelperExtraFields::getAdvFilterFields($params -> get('fields'))) {
            return $advfilter;
        }
        return false;
    }

    public static function getCategoriesOptions($params){

        $leveltmp   = 1;
        $options    = array();
        $option     = new stdClass();

        $option->text = JText::_('JOPTION_SELECT_CATEGORY');
        $option->value = '';
        $options[] = $option;

        if($parentid = $params -> get('parent_cat', 0)){
            if($categories = TZ_Portfolio_PlusFrontHelperCategories::getSubCategoriesByParentId((int) $parentid)){

                $leveltmp   = $categories[0] -> level - 1;

                foreach($categories as $i => $item){
                    if(!$params -> get('show_parent_root', 1) && $parentid == $item -> id){
                        if(isset($categories[$i + 1]) && $categories[$i + 1]) {
                            $leveltmp = $categories[$i + 1] -> level - 1;
                        }
                        unset($categories[$i]);
                        continue;
                    }
                    $option = new stdClass();

                    $repeat = ($item->level - $leveltmp - 1 >= 0) ? $item->level - $leveltmp - 1 : 0;
                    $title  = str_repeat('- ', $repeat) . $item->title;
                    $option -> text     = $title;
                    $option -> value    = $item -> id;

                    $options[]  = $option;
                }
            }
        }else{
            JHtml::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tz_portfolio_plus/helpers/html');
            $_options   = JHtml::_('tzcategory.options', 'com_tz_portfolio_plus',array('filter.published' => 1));
            $options    = array_merge($options, $_options);
        }
        return $options;
    }
}