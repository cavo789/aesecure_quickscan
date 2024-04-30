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

// No direct access
defined('_JEXEC') or die;

use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class TZ_Portfolio_PlusContentHelper{
    protected static $cache = array();

    public static function getArticleById($article_id, $resetCache = false, $articleObject = null)
    {
        if (!$article_id) {
            return null;
        }

        $storeId = md5(__METHOD__ . "::" . $article_id);
        if (!isset(self::$cache[$storeId]) || $resetCache) {

            if (!is_object($articleObject)) {
                $db     = TZ_Portfolio_PlusDatabase::getDbo();
                $query  = $db->getQuery(true);
                $query  -> select('article.*, m.catid');
                $query  -> from('#__tz_portfolio_plus_content AS article');
                $query  -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = article.id');
                $query  -> join('LEFT', '#__tz_portfolio_plus_categories AS c ON c.id = m.catid');
                $query  -> where('article.id = ' . $article_id);
                $query  -> where('m.main = 1');
                $db     -> setQuery($query);

                $articleObject = $db->loadObject();
            }

            if ($articleObject && $articleObject->catid > 0) {
                self::$cache[$storeId] = $articleObject;
            } else {
                return $articleObject;
            }
        }

        return self::$cache[$storeId];
    }

    public static function getLetters($filters = array()){

        $storeId    = __METHOD__;
        $storeId   .= ':'.serialize($filters);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('DISTINCT ASCII(SUBSTR(LOWER(c.title),1,1)) AS letterKey');
        $query -> from('#__tz_portfolio_plus_content AS c');
        $query -> join('LEFT', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = c.id');
        $query -> join('LEFT', '#__tz_portfolio_plus_categories AS cc ON cc.id=m.catid');
        $query -> join('LEFT', '#__tz_portfolio_plus_tag_content_map AS x ON x.contentid=c.id');
        $query -> join('LEFT', '#__tz_portfolio_plus_tags AS t ON t.id=x.tagsid');
        $query -> join('LEFT', '#__users AS u ON c.created_by=u.id');

        $query -> where('c.state=1');

        if(isset($filters['catid']) && ($catids = $filters['catid'])){

            if(is_array($catids)){
                $catids = array_filter($catids);
                if(count($catids)) {
                    $query->where('cc.id IN(' . implode(',', $catids) . ')');
                }
            }else{
                $query -> where('cc.id ='.(int) $catids);
            }
        }

        if(isset($filters['featured']) && ($featured = $filters['featured'])){
            if(is_array($featured)){
                $query -> where('c.featured IN('.implode(',',$featured).')');
            }else{
                $query -> where('c.featured ='.(int) $featured);
            }
        }

        if(isset($filters['tagId']) && ($tagId = $filters['tagId'])){
            if(is_array($tagId)){
                $query -> where('t.id IN('.implode(',',$tagId).')');
            }else{
                $query -> where('t.id ='.(int) $tagId);
            }
        }

        if(isset($filters['userId']) && ($userId = $filters['userId'])){
            if(is_array($userId)){
                $query -> where('c.created_by IN('.implode(',',$userId).')');
            }else{
                $query -> where('c.created_by ='.(int) $userId);
            }
        }
        if(isset($filters['year']) && ($year = $filters['year'])){
            $query -> where('YEAR(c.created) ='.$year);
        }
        if(isset($filters['month']) && ($month = $filters['month'])){
            $query -> where('MONTH(c.created) ='.$month);
        }

        $db -> setQuery($query);

        if($result = $db -> loadColumn()){
            self::$cache[$storeId]   = $result;
            return $result;
        }
        return false;
    }


    public static function getArticleCountsByAuthorId($authorId, $options = array())
    {
        if (!$authorId) {
            return null;
        }

        $storeId = __METHOD__ . ':' . $authorId;
        $storeId .= ':'.serialize($options);
        $storeId = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $db     = TZ_Portfolio_PlusDatabase::getDbo();
        $query  = $db->getQuery(true);
        $query  -> select('COUNT(article.id)');
        $query  -> from('#__tz_portfolio_plus_content AS article');
        $query  -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = article.id');
        $query  -> join('LEFT', '#__tz_portfolio_plus_categories AS c ON c.id = m.catid');
        $query  -> join('INNER', '#__users AS ua ON ua.id = article.created_by');

        $query -> where('article.created_by ='.$authorId);

        if(isset($options['filter.published'])) {
            if(is_array($options['filter.published'])){
                $query->where('article.state IN('.implode(',', $options['filter.published']).')');
            }else{
                $query->where('article.state = ' . (int) $options['filter.published']);
            }
        }
        $db     -> setQuery($query);

        if($count = $db->loadResult()){
            self::$cache[$storeId]  = (int) $count;
            return (int) $count;
        }

        return false;
    }

    public static function getBootstrapColumns($numOfColumns)
    {
        switch ($numOfColumns)
        {
            case 1:
                return array(12);
                break;
            case 2:
                return array(6, 6);
                break;
            case 3:
                return array(4, 4, 4);
                break;
            case 4:
                return array(3, 3, 3, 3);
                break;
            case 5:
                return array(3, 3, 2, 2, 2);
                break;
            case 6:
                return array(2, 2, 2, 2, 2, 2);
                break;
            case 7:
                return array(2, 2, 2, 2, 2, 1, 1);
                break;
            case 8:
                return array(2, 2, 2, 2, 1, 1, 1, 1);
                break;
            case 9:
                return array(2, 2, 2, 1, 1, 1, 1, 1, 1);
                break;
            case 10:
                return array(2, 2, 1, 1, 1, 1, 1, 1, 1, 1);
                break;
            case 11:
                return array(2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
                break;
            case 12:
                return array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
                break;
            default:
                return array(12);
                break;
        }
    }

    public static function padding_margin($data, $type = 'padding') {
        if ($data) {
            $paddingmargin        =   json_decode($data);
            $style          =   new stdClass();
            $style->md  =  (isset($paddingmargin->md->top) && $paddingmargin->md->top) ? $type."-top: " . $paddingmargin->md->top . ";" : "";
            $style->md  .=  (isset($paddingmargin->md->right) && $paddingmargin->md->right) ? $type."-right: " . $paddingmargin->md->right . ";" : "";
            $style->md  .=  (isset($paddingmargin->md->bottom) && $paddingmargin->md->bottom) ? $type."-bottom: " . $paddingmargin->md->bottom . ";" : "";
            $style->md  .=  (isset($paddingmargin->md->left) && $paddingmargin->md->left) ? $type."-left: " . $paddingmargin->md->left . ";" : "";

            $style->sm  =  (isset($paddingmargin->sm->left) && $paddingmargin->sm->top) ? $type."-top: " . $paddingmargin->sm->top . ";" : "";
            $style->sm  .=  (isset($paddingmargin->sm->right) && $paddingmargin->sm->right) ? $type."-right: " . $paddingmargin->sm->right . ";" : "";
            $style->sm  .=  (isset($paddingmargin->sm->bottom) && $paddingmargin->sm->bottom) ? $type."-bottom: " . $paddingmargin->sm->bottom . ";" : "";
            $style->sm  .=  (isset($paddingmargin->sm->left) && $paddingmargin->sm->left) ? $type."-left: " . $paddingmargin->sm->left . ";" : "";

            $style->xs  =  (isset($paddingmargin->xs->left) && $paddingmargin->xs->top) ? $type."-top: " . $paddingmargin->xs->top . ";" : "";
            $style->xs  .=  (isset($paddingmargin->xs->right) && $paddingmargin->xs->right) ? $type."-right: " . $paddingmargin->xs->right . ";" : "";
            $style->xs  .=  (isset($paddingmargin->xs->bottom) && $paddingmargin->xs->bottom) ? $type."-bottom: " . $paddingmargin->xs->bottom . ";" : "";
            $style->xs  .=  (isset($paddingmargin->xs->left) && $paddingmargin->xs->left) ? $type."-left: " . $paddingmargin->xs->left . ";" : "";
            return $style;
        }
        return false;
    }

    public static function font_style($data) {
        $font           =       json_decode($data);
        $standard_font  =       array('Verdana','Georgia','Arial','Impact','Tahoma','Trebuchet MS','Arial Black','Times New Roman','Palatino Linotype','Lucida Sans Unicode','MS Serif','Comic Sans MS','Courier New','Lucida Console');
        if (isset($font->fontFamily) && $font->fontFamily && !in_array($font->fontFamily, $standard_font)) {
            $doc = JFactory::getDocument();
            $google_font = '//fonts.googleapis.com/css?family=' . str_replace(' ', '+', $font->fontFamily) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic&display=swap';
            $doc->addStyleSheet($google_font);
        }
        $text_style     =       isset($font->fontFamily) && $font->fontFamily ? 'font-family:'.$font->fontFamily.';' : '';
        $text_style     .=      isset($font->fontWeight) && $font->fontWeight ? 'font-weight:'.$font->fontWeight.';' : '';
        $text_style     .=      isset($font->lineHeight) && $font->lineHeight ? 'line-height:'.$font->lineHeight.';' : '';
        $text_style     .=      isset($font->fontStyle) && $font->fontStyle ? 'font-style:'.$font->fontStyle.';' : '';
        $text_style     .=      isset($font->textTransform) && $font->textTransform ? 'text-transform:'.$font->textTransform.';' : '';
        $text_style     .=      isset($font->textDecoration) && $font->textDecoration ? 'text-decoration:'.$font->textDecoration.';' : '';
        $text_style     .=      isset($font->letterSpacing) && $font->letterSpacing ? 'letter-spacing:'.$font->letterSpacing.';' : '';
        $text_style     .=      isset($font->fontSize) && $font->fontSize ? 'font-size:'.$font->fontSize.';' : '';
        return $text_style;
    }

    public static function responsive_box_data($data, $string = '', $key='{key}') {
        if ($data && $key && $string) {
            $obj        =   json_decode($data);
            $style      =   new stdClass();
            $style->md  =  (isset($obj->md->top) && $obj->md->top) ? str_replace($key, 'top', $string).": " . $obj->md->top . ";" : "";
            $style->md  .=  (isset($obj->md->right) && $obj->md->right) ? str_replace($key, 'right', $string).": " . $obj->md->right . ";" : "";
            $style->md  .=  (isset($obj->md->bottom) && $obj->md->bottom) ? str_replace($key, 'bottom', $string).": " . $obj->md->bottom . ";" : "";
            $style->md  .=  (isset($obj->md->left) && $obj->md->left) ? str_replace($key, 'left', $string).": " . $obj->md->left . ";" : "";

            $style->sm  =  (isset($obj->sm->left) && $obj->sm->top) ? str_replace($key, 'top', $string).": " . $obj->sm->top . ";" : "";
            $style->sm  .=  (isset($obj->sm->right) && $obj->sm->right) ? str_replace($key, 'right', $string).": " . $obj->sm->right . ";" : "";
            $style->sm  .=  (isset($obj->sm->bottom) && $obj->sm->bottom) ? str_replace($key, 'bottom', $string).": " . $obj->sm->bottom . ";" : "";
            $style->sm  .=  (isset($obj->sm->left) && $obj->sm->left) ? str_replace($key, 'left', $string).": " . $obj->sm->left . ";" : "";

            $style->xs  =  (isset($obj->xs->left) && $obj->xs->top) ? str_replace($key, 'top', $string).": " . $obj->xs->top . ";" : "";
            $style->xs  .=  (isset($obj->xs->right) && $obj->xs->right) ? str_replace($key, 'right', $string).": " . $obj->xs->right . ";" : "";
            $style->xs  .=  (isset($obj->xs->bottom) && $obj->xs->bottom) ? str_replace($key, 'bottom', $string).": " . $obj->xs->bottom . ";" : "";
            $style->xs  .=  (isset($obj->xs->left) && $obj->xs->left) ? str_replace($key, 'left', $string).": " . $obj->xs->left . ";" : "";
            return $style;
        }
        return false;
    }
}