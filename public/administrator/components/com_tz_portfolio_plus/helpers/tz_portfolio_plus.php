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
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Path;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class TZ_Portfolio_PlusHelper
{
	public static $extension        = 'com_tz_portfolio_plus';
	protected static $submenus		= array();
	protected static $cache         = array();

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
	    $user   = Factory::getUser();
        $class  = 'JHtmlSidebar';

        call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_PLUS_DASHBOARD'),
            'index.php?option=com_tz_portfolio_plus&view=dashboard',
            $vName == 'dashboard'));

        if($user -> authorise('core.manage.article', 'com_tz_portfolio_plus')) {
            call_user_func_array($class . '::addEntry', array(JText::_('COM_TZ_PORTFOLIO_PLUS_ARTICLES'),
                'index.php?option=com_tz_portfolio_plus&view=articles',
                $vName == 'articles'));
        }

        if($user -> authorise( 'core.manage.category', 'com_tz_portfolio_plus')) {
            call_user_func_array($class . '::addEntry', array(JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES'),
                'index.php?option=com_tz_portfolio_plus&view=categories',
                $vName == 'categories'));
        }

        if($user -> authorise('core.manage.article', 'com_tz_portfolio_plus')) {
            call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_PLUS_SUBMENU_FEATURED_ARTICLES'),
                'index.php?option=com_tz_portfolio_plus&view=featured',
                $vName == 'featured'));
        }

        if($user -> authorise( 'core.manage.field', 'com_tz_portfolio_plus')) {
            call_user_func_array($class . '::addEntry', array(JText::_('COM_TZ_PORTFOLIO_PLUS_FIELDS'),
                'index.php?option=com_tz_portfolio_plus&view=fields',
                $vName == 'fields'));
        }

        if($user -> authorise( 'core.manage.group', 'com_tz_portfolio_plus')) {
            call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_GROUPS'),
                'index.php?option=com_tz_portfolio_plus&view=groups',
                $vName == 'groups'));
        }
        if($user -> authorise( 'core.manage.tag', 'com_tz_portfolio_plus')) {
            call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_PLUS_TAGS'),
                        'index.php?option=com_tz_portfolio_plus&view=tags',
                        $vName == 'tags'));
        }
        if($user -> authorise( 'core.manage.addon', 'com_tz_portfolio_plus')) {
            call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_PLUS_ADDONS'),
                'index.php?option=com_tz_portfolio_plus&view=addons',
                $vName == 'addons'));
        }
        if($user -> authorise( 'core.manage.style', 'com_tz_portfolio_plus')) {
            call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE_STYLES'),
                'index.php?option=com_tz_portfolio_plus&view=template_styles',
                $vName == 'template_styles'));
        }
        if($user -> authorise( 'core.manage.template', 'com_tz_portfolio_plus')) {
            call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATES'),
                'index.php?option=com_tz_portfolio_plus&view=templates',
                $vName == 'templates'));
        }
        if($user -> authorise( 'core.manage.extension', 'com_tz_portfolio_plus')) {
            call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_PLUS_EXTENSIONS'),
                'index.php?option=com_tz_portfolio_plus&view=extension&layout=upload',
                $vName == 'extension'));
        }
        if($user -> authorise( 'core.manage.acl', 'com_tz_portfolio_plus')) {
            call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_PLUS_ACL'),
                'index.php?option=com_tz_portfolio_plus&view=acls',
                $vName == 'acls'));
        }
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 * @param	int		The article ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */

	public static function _getActions($categoryId = 0, $id = 0, $assetName = '')
	{
		// Log usage of deprecated function
		JLog::add(__METHOD__ . '() is deprecated, use JHelperContent::getActions() with new arguments order instead.', JLog::WARNING, 'deprecated');

		// Reverted a change for version 2.5.6
		$user	= Factory::getUser();
		$result	= new JObject;

		$path = JPATH_ADMINISTRATOR . '/components/com_tz_portfolio_plus/access.xml';

		if (empty($id) && empty($categoryId))
		{
			$section = 'component';
		}
		elseif (empty($id))
		{
			$section = 'category';
			$assetName .= '.category.' . (int) $categoryId;
		}
		else
		{
			// Used only in com_content
			$section = 'article';
			$assetName .= '.article.' . (int) $id;
		}

		$actions = JAccess::getActionsFromFile($path, "/access/section[@name='" . $section . "']/");

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}

	public static function getActions($component = '', $section = '', $id = 0, $parent_section = null)
	{
		// Check for deprecated arguments order
		if (is_int($component) || is_null($component))
		{
			$result = self::_getActions($component, $section, $id);

			return $result;
		}

		$user	= Factory::getUser();
		$result	= new JObject;

		$path = JPATH_ADMINISTRATOR . '/components/com_tz_portfolio_plus/access.xml';

        $assetName = $component;

		if ($section && $id)
		{
			$assetName = $component . '.' . $section . '.' . (int) $id;

            $tblAsset   = JTable::getInstance('Asset', 'JTable');
            if(!$tblAsset -> loadByName($assetName)){
                $assetName  = $component . '.' . $parent_section;
            }
		}elseif (empty($id))
        {
            $assetName = $component . '.' . $section;
        }

		$actions = JAccess::getActionsFromFile($path, "/access/section[@name='component']/");

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}

	/**
	* Applies the content tag filters to arbitrary text as per settings for current user group
	* @param text The string to filter
	* @return string The filtered string
	*/
	public static function filterText($text)
	{
		// Filter settings
		$config		= JComponentHelper::getParams('com_config');
		$user		= Factory::getUser();
		$userGroups	= JAccess::getGroupsByUser($user->get('id'));

		$filters = $config->get('filters');

		$blackListTags			= array();
		$blackListAttributes	= array();

		$customListTags			= array();
		$customListAttributes	= array();

		$whiteListTags			= array();
		$whiteListAttributes	= array();

		$noHtml				= false;
		$whiteList			= false;
		$blackList			= false;
		$customList			= false;
		$unfiltered			= false;

		// Cycle through each of the user groups the user is in.
		// Remember they are included in the Public group as well.
		foreach ($userGroups as $groupId)
		{
			// May have added a group but not saved the filters.
			if (!isset($filters->$groupId)) {
				continue;
			}

			// Each group the user is in could have different filtering properties.
			$filterData = $filters->$groupId;
			$filterType	= strtoupper($filterData->filter_type);

			if ($filterType == 'NH') {
				// Maximum HTML filtering.
				$noHtml = true;
			}
			elseif ($filterType == 'NONE') {
				// No HTML filtering.
				$unfiltered = true;
			}
			else {
				// Black, white or custom list.
				// Preprocess the tags and attributes.
				$tags			= explode(',', $filterData->filter_tags);
				$attributes		= explode(',', $filterData->filter_attributes);
				$tempTags		= array();
				$tempAttributes	= array();

				foreach ($tags as $tag)
				{
					$tag = trim($tag);

					if ($tag) {
						$tempTags[] = $tag;
					}
				}

				foreach ($attributes as $attribute)
				{
					$attribute = trim($attribute);

					if ($attribute) {
						$tempAttributes[] = $attribute;
					}
				}

				// Collect the black or white list tags and attributes.
				// Each lists is cummulative.
				if ($filterType == 'BL') {
					$blackList				= true;
					$blackListTags			= array_merge($blackListTags, $tempTags);
					$blackListAttributes	= array_merge($blackListAttributes, $tempAttributes);
				}
				elseif ($filterType == 'CBL') {
					// Only set to true if Tags or Attributes were added
					if ($tempTags || $tempAttributes) {
						$customList				= true;
						$customListTags			= array_merge($customListTags, $tempTags);
						$customListAttributes	= array_merge($customListAttributes, $tempAttributes);
					}
				}
				elseif ($filterType == 'WL') {
					$whiteList				= true;
					$whiteListTags			= array_merge($whiteListTags, $tempTags);
					$whiteListAttributes	= array_merge($whiteListAttributes, $tempAttributes);
				}
			}
		}

		// Remove duplicates before processing (because the black list uses both sets of arrays).
		$blackListTags			= array_unique($blackListTags);
		$blackListAttributes	= array_unique($blackListAttributes);
		$customListTags			= array_unique($customListTags);
		$customListAttributes	= array_unique($customListAttributes);
		$whiteListTags			= array_unique($whiteListTags);
		$whiteListAttributes	= array_unique($whiteListAttributes);

		// Unfiltered assumes first priority.
		if ($unfiltered) {
			// Dont apply filtering.
		}
		else {
			// Custom blacklist precedes Default blacklist
			if ($customList) {
				$filter = JFilterInput::getInstance(array(), array(), 1, 1);

				// Override filter's default blacklist tags and attributes
				if ($customListTags) {
					$filter->tagBlacklist = $customListTags;
				}
				if ($customListAttributes) {
					$filter->attrBlacklist = $customListAttributes;
				}
			}
			// Black lists take third precedence.
			elseif ($blackList) {
				// Remove the white-listed attributes from the black-list.
				$filter = JFilterInput::getInstance(
					array_diff($blackListTags, $whiteListTags), 			// blacklisted tags
					array_diff($blackListAttributes, $whiteListAttributes), // blacklisted attributes
					1,														// blacklist tags
					1														// blacklist attributes
				);
				// Remove white listed tags from filter's default blacklist
				if ($whiteListTags) {
					$filter->tagBlacklist = array_diff($filter->tagBlacklist, $whiteListTags);
				}
				// Remove white listed attributes from filter's default blacklist
				if ($whiteListAttributes) {
					$filter->attrBlacklist = array_diff($filter->attrBlacklist);
				}

			}
			// White lists take fourth precedence.
			elseif ($whiteList) {
				$filter	= JFilterInput::getInstance($whiteListTags, $whiteListAttributes, 0, 0, 0);  // turn off xss auto clean
			}
			// No HTML takes last place.
			else {
				$filter = JFilterInput::getInstance();
			}

			$text = $filter->clean($text, 'html');
		}

		return $text;
	}

	public static function getMenuLinks($menuType = null, $parentId = 0, $mode = 0, $published = array(), $languages = array())
	{
        $db     = TZ_Portfolio_PlusDatabase::getDbo();
		$query = $db->getQuery(true)
			->select('a.id AS value, a.title AS text, a.alias, a.level, a.component_id,'
				.' a.menutype, a.type, a.template_style_id, a.checked_out, a.params')
			->from('#__menu AS a')
			->join('LEFT', $db->quoteName('#__menu') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			-> join('LEFT', $db -> quoteName('#__extensions').' AS e ON e.extension_id = a.component_id')
			-> where('e.name='.$db -> quote('com_tz_portfolio_plus'));

		// Filter by the type
		if ($menuType)
		{
			$query->where('(a.menutype = ' . $db->quote($menuType) . ' OR a.parent_id = 0)');
		}

		if ($parentId)
		{
			if ($mode == 2)
			{
				// Prevent the parent and children from showing.
				$query->join('LEFT', '#__menu AS p ON p.id = ' . (int) $parentId)
					->where('(a.lft <= p.lft OR a.rgt >= p.rgt)');
			}
		}

		if (!empty($languages))
		{
			if (is_array($languages))
			{
				$languages = '(' . implode(',', array_map(array($db, 'quote'), $languages)) . ')';
			}

			$query->where('a.language IN ' . $languages);
		}

		if (!empty($published))
		{
			if (is_array($published))
			{
				$published = '(' . implode(',', $published) . ')';
			}

			$query->where('a.published IN ' . $published);
		}

		$query->where('a.published != -2')
			->group('a.id, a.title, a.alias, a.level, a.menutype, a.type,a.template_style_id')
			->group('a.checked_out, a.lft, a.component_id, a.params')
			->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$links = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication() -> enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		if (empty($menuType))
		{
			// If the menutype is empty, group the items by menutype.
			$query->clear()
				->select('*')
				->from('#__menu_types')
				->where('menutype <> ' . $db->quote(''))
				->order('title, menutype');
			$db->setQuery($query);

			try
			{
				$menuTypes = $db->loadObjectList();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication() -> enqueueMessage($e->getMessage(), 'error');

				return false;
			}

			// Create a reverse lookup and aggregate the links.
			$rlu = array();

			foreach ($menuTypes as &$type)
			{
				$rlu[$type->menutype] = & $type;
				$type->links = array();
			}

			// Loop through the list of menu links.
			foreach ($links as $i => &$link)
			{
				$registry       = new JRegistry($link -> params);
				$link -> params = $registry;
				if (isset($rlu[$link->menutype]))
				{
					$rlu[$link->menutype]->links[] = &$link;

					// Cleanup garbage.
					unset($link->menutype);
				}
			}

			// Remove all menus group don't have menu items
			if(count($menuTypes)){
				foreach($menuTypes as $i => $item){
					if(!$item -> links || ($item -> links && !count($item -> links))){
						unset($menuTypes[$i]);
					}
				}
			}

			return $menuTypes;
		}
		else
		{
			return $links;
		}
	}

	public static function checkConnectServer($url, $method = 'get'){

        $url    = trim($url);

	    $store  = __METHOD__.'::'.md5($url);
	    $store2 = __CLASS__.'::getDataFromServer::'.$url;

	    if(!isset(self::$cache[$store])){
	        self::$cache[$store]    = false;
        }

        try {
	        if($method == 'post'){
                $response = \JHttpFactory::getHttp()->post($url, array());
            }else {
                $response = \JHttpFactory::getHttp()->get($url, array());
            }
            self::$cache[$store2]   = $response;
        }
        catch (\RuntimeException $exception){

            self::$cache[$store]    = false;
            \JLog::add(\JText::sprintf('JLIB_INSTALLER_ERROR_DOWNLOAD_SERVER_CONNECT', $exception->getMessage()), \JLog::WARNING, 'jerror');

            return false;
        }

        if (200 == $response->code)
        {
            self::$cache[$store]    = true;
        }

        return self::$cache[$store];
    }

    public static function getDataFromServer($url = null, $method = 'get'){

        $url    = trim($url);
	    $storeId    = __METHOD__.'::'.$url;

	    if(!isset(self::$cache[$storeId])){
	        self::$cache[$storeId]  = false;
        }

	    if($url){
	        if(!$check = self::checkConnectServer($url, $method)){
	            return $check;
            }
        }
        return self::$cache[$storeId];
    }

    public static function getXMLData($file, $class_name = "SimpleXMLElement", $options = 0, $ns = "", $is_prefix = false){

        $storeId    = __METHOD__;
        $storeId   .= ':'.$file;
        $storeId   .= ':'.$class_name;
        $storeId   .= ':'.$options;
        $storeId   .= ':'.$ns;
        $storeId   .= ':'.$is_prefix;

        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        if(!$file || ($file && !File::exists($file))){
            return false;
        }
        $xml	= simplexml_load_file($file, $class_name, $options, $ns, $is_prefix);

        self::$cache[$storeId]  = $xml;

        return $xml;
    }

//    public static function prepareUpdate(&$update, &$table){
//        $params         = JComponentHelper::getParams('com_tz_portfolio_plus');
//        $downloadUrl    = $update -> get('downloadurl');
//        $url            = $downloadUrl -> _data;
//        if(strpos($url, 'level='.COM_TZ_PORTFOLIO_PLUS_EDITION)){
//            if($tokenKey = $params -> get('token_key')){
//                if(strpos($url, 'token_key=')){
//                    $url    = str_replace('token_key=', 'token_key='.$tokenKey, $url);
//                }
//                $downloadUrl -> _data   = $url;
//                $update -> set('downloadurl', $downloadUrl);
//            }
//        }
//    }

    public static function introGuideSkipped($view){
	    if(!$view){
	        return false;
        }

        $filePath   = \JPath::clean(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/cache'.'/introguide.json');

        if(!File::exists($filePath)){
            return false;
        }

        $introGuide = file_get_contents($filePath);
        $introGuide = json_decode($introGuide);
        if($introGuide && isset($introGuide -> $view) && $introGuide -> $view) {
            return true;
        }

        return false;
    }

    /*
     *  Get license info
     *  @since v2.2.7
     * */
    public static function getLicense(){

        $storeId    = __METHOD__;

        $file    = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/includes/license.php';

        $storeId   .= ':'.$file;
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        if(File::exists($file)){
            $license    = @file_get_contents($file);
            $license    = str_replace('<?php die("Access Denied"); ?>#x#', '', $license);
            $license    = unserialize(trim($license));

            self::$cache[$storeId]  = $license;

            return $license;
        }

        return false;
    }

    /*
     *  Check license expired
     *  @since v2.2.7
     * */
    public static function isLicenseExpired($type){

        $license    = static::getLicense();

        $storeId    = __METHOD__;
        $storeId   .= ':'.$type;
        $storeId   .= ':'.serialize($license);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        if($license){
            $nowDate    = Factory::getDate() -> toSql();
            if($license -> $type < $nowDate){
                return true;
            }
        }
        return false;
    }
}
