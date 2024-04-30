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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;

class plgQuickiconTZ_Portfolio_Plus extends CMSPlugin
{

    protected $autoloadLanguage = true;

	/**
	 * Display TZ Portfolio Plus backend icon in Joomla
	 *
	 * @param   string $context context
	 *
	 * @return array|null
	 * @throws Exception
	 */
	public function onGetIcons($context)
	{
		if ($context != $this->params->get('context', 'mod_quickicon')
            || !Factory::getUser()->authorise('core.manage', 'com_tz_portfolio_plus'))
		{
			return null;
		}
        $this->loadLanguage('com_tz_portfolio_plus.sys');

		$updateInfo = null;

        /* Setup */
        $file   = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/setup/index.php';

        $xml    = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/tz_portfolio_plus.xml');

		if (!JFile::exists($file) && Factory::getUser()->authorise('core.manage', 'com_installer'))
		{
			$updateSite = '%tzportfolio.com/tzupdates%';
			$db         = Factory::getDbo();

			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__updates'))
				->where($db->qn('extension_id') . ' > 0')
				->where($db->qn('detailsurl') . ' LIKE ' . $db->q($updateSite));
			$db->setQuery($query);
			$list = (array) $db->loadObjectList();

			if ($list)
			{
				$updateInfo          = new stdClass;
				$updateInfo->addons  = 0;
				$updateInfo->version = 0;

				foreach ($list as $item)
				{
					if ($item->element == 'com_tz_portfolio_plus')
					{
						$updateInfo->version = $item->version;
					}
					else
					{
						$updateInfo->addons++;
					}
				}
			}
			else
			{
				$query = $db->getQuery(true)
					->select('update_site_id')
					->from($db->qn('#__update_sites'))
					->where($db->qn('enabled') . ' = 0')
					->where($db->qn('location') . ' LIKE ' . $db->q($updateSite));
				$db->setQuery($query);
				$updateInfo = !$db->loadResult();
			}
		}

		$link = 'index.php?option=com_tz_portfolio_plus';

		$useIcons = version_compare(JVERSION, '3.0', '>');

		if (JFile::exists($file))
		{
			$icon = 'warning';

			if (version_compare(JVERSION, '4.0', '>'))
			{
				$icon = 'fa fa-warning';
			}

			// Not fully installed
			$img  = $useIcons ? $icon : 'header/icon-48-download.png';
			$icon = 'header/icon-48-download.png';
			$text = Text::_('PLG_QUICKICON_TZ_PORTFOLIO_PLUS_COMPLETE_INSTALLATION');
		}
		elseif ($updateInfo === null)
		{
			// Unsupported
			$icon = 'remove';

			if (version_compare(JVERSION, '4.0', '>'))
			{
				$icon = 'fa fa-remove';
			}

			$img  = $useIcons ? $icon : 'header/icon-48-download.png';
			$icon = 'header/icon-48-download.png';
			$text = Text::_('COM_TZ_PORTFOLIO_PLUS');
		}
		elseif ($updateInfo === false)
		{
			// Disabled
			$icon = 'minus';

			if (version_compare(JVERSION, '4.0', '>'))
			{
				$icon = 'fa fa-minus';
			}

			$img  = $useIcons ? $icon : 'header/icon-48-download.png';
			$icon = 'header/icon-48-download.png';
			$text = Text::_('COM_TZ_PORTFOLIO_PLUS') . '<br />' . Text::_('PLG_QUICKICON_TZ_PORTFOLIO_PLUS_UPDATE_DISABLED');
		}
		elseif (!empty($updateInfo->version) && version_compare($xml -> version, $updateInfo->version, '<'))
		{
			// Has updates
			$icon = 'download';

			if (version_compare(JVERSION, '4.0', '>'))
			{
				$icon = 'fa fa-download';
			}

			$img  = $useIcons ? $icon : 'header/icon-48-download.png';
			$icon = 'header/icon-48-download.png';
			$text = Text::_('COM_TZ_PORTFOLIO_PLUS').' <span class="label label-important">'
                . $updateInfo->version . '</span><br />' . Text::_('PLG_QUICKICON_TZ_PORTFOLIO_PLUS_UPDATE_NOW');
			$link = 'index.php?option=com_installer&view=update&filter_search=TZ Portfolio Plus';
		}
		else
		{
            $doc    = JFactory::getDocument();
            $doc -> addStyleSheet(JUri::base(true).'/components/com_tz_portfolio_plus/css/tppicon.min.css',
                array('version' => 'auto'));
            $doc -> addStyleDeclaration('[class^="icon-"][class^="tpp-icon-"]:before,
             [class*="icon-"][class*="tpp-icon-"]:before{
                font-family: \'TZ-Portfolio-Plus-Icons\';
            }');
			$icon = ' tpp-icon-portfolio';

			$img  = $useIcons ? $icon : 'header/icon-48-download.png';
			$icon = 'header/icon-48-download.png';
			$text = Text::_('COM_TZ_PORTFOLIO_PLUS');
		}

		// Use one line in J!3.0.
		if (version_compare(JVERSION, '3.0', '>'))
		{
			$text = preg_replace('|<br />|', ' - ', $text);
		}

		return array(array(
			'link'   => Route::_($link),
			'image'  => $img,
			'text'   => $text,
			'icon'   => $icon,
			'access' => array('core.manage', 'com_tz_portfolio_plus'),
			'id'     => 'plg_quickicon_com_tz_portfolio_plus'));
	}
}
