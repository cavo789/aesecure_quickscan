<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TZ_Portfolio_PlusBackEndHelperAssociation', COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH
	.DIRECTORY_SEPARATOR.'association.php');

/**
 * Content Component Association Helper
 *
 * @package     Joomla.Site
 * @subpackage  com_content
 * @since       3.0
 */
abstract class TZ_Portfolio_PlusHelperAssociation extends TZ_Portfolio_PlusBackEndHelperAssociation
{

	/**
	 * Method to get the associations for a given item
	 *
	 * @param   integer  $id    Id of the item
	 * @param   string   $view  Name of the view
	 *
	 * @return  array   Array of associations for the item
	 *
	 * @since  3.0
	 */

	public static function getAssociations($id = 0, $view = null)
	{
		jimport('helper.route', JPATH_COMPONENT_SITE);

		$app = JFactory::getApplication();
		$jinput = $app->input;
		$view = is_null($view) ? $jinput->get('view') : $view;
		$id = empty($id) ? $jinput->getInt('id') : $id;

		if ($view == 'article')
		{
			if ($id)
			{
//				$associations = JLanguageAssociations::getAssociations('com_tz_portfolio_plus',
//					'#__tz_portfolio_plus_content', '#__tz_portfolio_plus.item', $id);
				$associations = self::getArticleAssociations($id);

				$return = array();

				foreach ($associations as $tag => $item)
				{
					$return[$tag] = TZ_Portfolio_PlusHelperRoute::getArticleRoute($item->id, $item->catid, $item->language);
				}

				return $return;
			}
		}

		if ($view == 'category' || $view == 'categories')
		{
			return self::getCategoryAssociations($id, 'com_tz_portfolio_plus');
		}

		return array();

	}
}
