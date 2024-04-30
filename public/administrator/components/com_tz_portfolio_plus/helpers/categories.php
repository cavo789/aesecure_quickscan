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

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class TZ_Portfolio_PlusHelperCategories
{
	protected static $cache	= array();

    public static function getAssociations($pk, $extension = 'com_tz_portfolio_plus')
    {
        $associations = array();
        try{
            $db = TZ_Portfolio_PlusDatabase::getDbo();

            $query = $db->getQuery(true)
                ->from('#__tz_portfolio_plus_categories as c')
                ->join('INNER', '#__associations as a ON a.id = c.id AND a.context=' . $db->quote('com_tz_portfolio_plus.categories.item'))
                ->join('INNER', '#__associations as a2 ON a.key = a2.key')
                ->join('INNER', '#__tz_portfolio_plus_categories as c2 ON a2.id = c2.id AND c2.extension = ' . $db->quote($extension))
                ->where('c.id =' . (int)$pk)
                ->where('c.extension = ' . $db->quote($extension));

            $select = array(
                'c2.language',
                $query->concatenate(array('c2.id', 'c2.alias'), ':') . ' AS id'
            );
            $query->select($select);
            $db->setQuery($query);
            $contentitems = $db->loadObjectList('language');

            foreach ($contentitems as $tag => $item)
            {
                // Do not return itself as result
                if ((int) $item->id != $pk)
                {
                    $associations[$tag] = $item->id;
                }
            }

        }catch (\InvalidArgumentException $e)
        {
            Factory::getApplication()  -> enqueueMessage($e->getMessage(), 'error');
            return false;
        }

        return $associations;
    }

	public static function getCategoriesById($catid, $options = array()){
		if($catid) {
			if(is_array($catid)) {
				$storeId = md5(__METHOD__ . '::'.implode(',', $catid).'::'.implode(',',$options));
			}else{
				$storeId = md5(__METHOD__ . '::'.$catid.'::'.implode(',',$options));
			}

			if(!isset(self::$cache[$storeId])){
                $db = TZ_Portfolio_PlusDatabase::getDbo();
				$query  =  $db -> getQuery(true);
				$query  -> select('*');
				$query  -> from('#__tz_portfolio_plus_categories');

				if(is_array($catid)) {
					$query -> where('cc.id IN('.implode(',', $catid) .')');
				}else{
					$query -> where('cc.id = '.$catid);
				}

				if(count($options)){
					if(isset($options['orderby'])){
						if(!empty($order)) {
							$query->order($options['orderby']);
						}
					}
				}

				$db -> setQuery($query);
				if($categories = $db -> loadObjectList()){
					self::$cache[$storeId]  = $categories;
					return $categories;
				}

				self::$cache[$storeId]  = false;
			}

			return self::$cache[$storeId];
		}
		return false;
	}

	public static function getCategoriesByArticleId($articleId, $main = false, $options = array()){
		if($articleId) {
			$_options	= '';
			if(count($options)) {
				$_options = new Registry();
				$_options -> loadArray($options);
				$_options	= $_options -> toString('ini');
			}
			if(is_array($articleId)) {
				$storeId = md5(__METHOD__ . '::'.implode(',', $articleId).'::'.$main.'::'.$_options);
			}else{
				$storeId = md5(__METHOD__ . '::'.$articleId.'::'.$main.'::'.$_options);
			}

			if(!isset(self::$cache[$storeId])){
                $db = TZ_Portfolio_PlusDatabase::getDbo();
				$query  =  $db -> getQuery(true);
				$query  -> select('c.*');
				$query  -> from('#__tz_portfolio_plus_categories AS c');
				$query  -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.catid = c.id');
				$query  -> join('LEFT', '#__tz_portfolio_plus_content AS cc ON cc.id = m.contentid');

				if(is_array($articleId)) {
					$query -> where('cc.id IN('.implode(',', $articleId) .')');
				}else{
					$query -> where('cc.id = '.$articleId);
				}

				if($main){
					$query -> where('m.main = 1');
				}else{
					$query -> where('m.main = 0');
				}

				if(count($options)){
					if(isset($options['condition']) && $options['condition']){
						$query -> where($options['condition']);
					}
					if(isset($options['orderby']) && isset($options['orderby'])){
						$query->order($options['orderby']);
					}
				}

//				$query -> group('c.*');

				$db -> setQuery($query);
				if($categories = $db -> loadObjectList()){
					if($main){
						$categories	= array_shift($categories);
					}
					self::$cache[$storeId]  = $categories;
					return $categories;
				}

				self::$cache[$storeId]  = false;
			}

			return self::$cache[$storeId];
		}
		return false;
	}

	public static function getMainCategoryByArticleId($articleId){

		$storeId	= md5(__METHOD__.'::'.$articleId);

		if($articleId){
			if(!isset(self::$cache[$storeId])){
				if($articleId){
                    $db = TZ_Portfolio_PlusDatabase::getDbo();
					$query	= $db -> getQuery(true);
					$query -> select('c.*');
					$query -> from('#__tz_portfolio_plus_categories AS c');
					$query -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.catid = c.id');
					$query -> join('LEFT', '#__tz_portfolio_plus_content AS cc ON cc.id = m.contentid');
					if(is_array($articleId)){
						$query -> where('cc.id IN('.implode(',',$articleId).')');
					}else{
						$query -> where('cc.id = '.$articleId);
					}
					$query -> where('m.main = 1');

					$db -> setQuery($query);
					if($items = $db -> loadObjectList()){
						self::$cache[$storeId]	= $items;
						return self::$cache[$storeId];
					}
				}

				self::$cache[$storeId]	= false;
			}
		}
		return self::$cache[$storeId];
	}

	public static function resetCache(){
		if(count(self::$cache)){
			self::$cache	= array();
		}
		return true;
	}

	public static function getCategoriesByGroupId($groupid){
		if(!$groupid){
			return false;
		}
		$storeId	= __METHOD__;
		if(is_array($groupid) || is_object($groupid)){
			$storeId	.= '::'.json_encode($groupid);
		}else{
			$storeId	.= '::'.$groupid;
		}

		$storeId	= md5($storeId);

		if(!isset(self::$cache[$storeId])) {
            $db = TZ_Portfolio_PlusDatabase::getDbo();
			$query = $db->getQuery(true);
			$query -> select('c.*');
			$query -> from('#__tz_portfolio_plus_categories AS c');
			$query -> join('INNER', '#__tz_portfolio_plus_fieldgroups AS fg ON c.groupid = fg.id');
			if (is_numeric($groupid)) {
				$query -> where('fg.id = '.$groupid);
			}elseif(is_array($groupid) && count($groupid)){
				$query -> where('fg.id IN('.implode(',', $groupid).')');
			}

			$db -> setQuery($query);
			if($data = $db -> loadObjectList()){
				self::$cache[$storeId]	= $data;
				return $data;
			}
			self::$cache[$storeId]	= false;
		}
		return self::$cache[$storeId];
	}
}
