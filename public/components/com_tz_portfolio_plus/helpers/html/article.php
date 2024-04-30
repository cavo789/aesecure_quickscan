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
class JHtmlArticle
{
    public static function tzprepare($text, $params = null,$pluginParams = null, $context = 'text')
	{
		if ($params === null)
		{
			$params = new JObject;
		}

		$app    = JFactory::getApplication();
		$article = new stdClass;
		$article->text = $text;

		JPluginHelper::importPlugin('tz_portfolio_plus');
        $app -> triggerEvent('onTZPluginPrepare', array($context, &$article, &$params,&$pluginParams, 0));

		return $article->text;
	}
}