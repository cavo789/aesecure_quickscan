<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieModelJoomlaMain extends AngieModelBaseMain
{
	/**
	 * Try to detect the Joomla! version in use
	 */
	public function detectVersion()
	{
		$ret = '2.5.0';

		$filename = APATH_LIBRARIES . '/cms/version/version.php';

		if (file_exists($filename))
		{
			include_once $filename;

			$jv  = new JVersion();
			$ret =$jv->getShortVersion();
		}
		elseif (file_exists(APATH_LIBRARIES . '/src/Version.php'))
		{
			if (!defined('JPATH_PLATFORM'))
			{
				define('JPATH_PLATFORM', 1);
			}

			include_once APATH_LIBRARIES . '/src/Version.php';

			$jv  = new \Joomla\CMS\Version();
			$ret = $jv->getShortVersion();
		}

		$this->container->session->set('jversion', $ret);
		$this->container->session->saveData();
	}

	/**
	 * Get the required settings analysis
	 *
	 * @return  array
	 */
	public function getRequired()
	{
		static $phpOptions = array();

		if (empty($phpOptions))
		{
			$jVersion = $this->container->session->get('jversion');

			if (version_compare($jVersion, '3.0.0', 'lt'))
			{
				$minPHPVersion = '5.2.4';
			}
			elseif (version_compare($jVersion, '3.2.0', 'lt'))
			{
				$minPHPVersion = '5.2.4';
			}
			elseif (version_compare($jVersion, '4.0.0', 'lt'))
			{
				$minPHPVersion = '5.3.1';
			}
			else
			{
				$minPHPVersion = '7.2.0';
			}

			$phpOptions[] = array (
				'label'		=> AText::sprintf('MAIN_LBL_REQ_PHP_VERSION', $minPHPVersion),
				'current'	=> version_compare(phpversion(), $minPHPVersion, 'ge'),
				'warning'	=> false,
			);

			if(version_compare($jVersion, '3.0.0', 'gt'))
			{
				$phpOptions[] = array (
					'label'		=> AText::_('MAIN_LBL_REQ_MCGPCOFF'),
					'current'	=> (ini_get('magic_quotes_gpc') == false),
					'warning'	=> false,
				);

				$phpOptions[] = array (
					'label'		=> AText::_('MAIN_LBL_REQ_REGGLOBALS'),
					'current'	=> (ini_get('register_globals') == false),
					'warning'	=> false,
				);
			}

			$phpOptions[] = array (
				'label'		=> AText::_('MAIN_LBL_REQ_ZLIB'),
				'current'	=> extension_loaded('zlib'),
				'warning'	=> false,
			);

			$phpOptions[] = array (
				'label'		=> AText::_('MAIN_LBL_REQ_XML'),
				'current'	=> extension_loaded('xml'),
				'warning'	=> false,
			);

			$phpOptions[] = array (
				'label'		=> AText::_('MAIN_LBL_REQ_DATABASE'),
				'current'	=> $this->hasDatabaseSupport(),
				'warning'	=> false,
			);

			if (extension_loaded( 'mbstring' ))
			{
				$option = array (
					'label'		=> AText::_( 'MAIN_REQ_MBLANGISDEFAULT' ),
					'current'	=> (strtolower(ini_get('mbstring.language')) == 'neutral'),
					'warning'	=> false,
				);
				$option['notice'] = $option['current'] ? null : AText::_('MAIN_MSG_NOTICEMBLANGNOTDEFAULT');
				$phpOptions[] = $option;

				$option = array (
					'label'		=> AText::_('MAIN_REQ_MBSTRINGOVERLOAD'),
					'current'	=> (ini_get('mbstring.func_overload') == 0),
					'warning'	=> false,
				);
				$option['notice'] = $option['current'] ? null : AText::_('MAIN_MSG_NOTICEMBSTRINGOVERLOAD');
				$phpOptions[] = $option;
			}

			$phpOptions[] = array (
				'label'		=> AText::_('MAIN_LBL_REQ_INIPARSER'),
				'current'	=> $this->getIniParserAvailability(),
				'warning'	=> false,
			);

			$phpOptions[] = array (
				'label'		=> AText::_('MAIN_LBL_REQ_JSON'),
				'current'	=> function_exists('json_encode') && function_exists('json_decode'),
				'warning'	=> false,
			);

			$cW = (@ file_exists('../configuration.php') && @is_writable('../configuration.php')) || @is_writable('../');
			$phpOptions['config-writable'] = array (
				'label'		=> AText::_('MAIN_LBL_REQ_CONFIGURATIONPHP'),
				'current'	=> $cW,
				'notice'	=> $cW ? null : AText::_('MAIN_MSG_CONFIGURATIONPHP'),
				'warning'	=> true
			);
		}

		return $phpOptions;
	}

	public function getRecommended()
	{
		static $phpOptions = array();

		if (empty($phpOptions))
		{
			$jVersion = $this->container->session->get('jversion');

			$phpOptions[] = array(
				'label'			=> AText::_('MAIN_REC_SAFEMODE'),
				'current'		=> (bool) ini_get('safe_mode'),
				'recommended'	=> false,
			);

			$phpOptions[] = array(
				'label'			=> AText::_('MAIN_REC_DISPERRORS'),
				'current'		=> (bool) ini_get('display_errors'),
				'recommended'	=> false,
			);

			$phpOptions[] = array(
				'label'			=> AText::_('MAIN_REC_UPLOADS'),
				'current'		=> (bool) ini_get('file_uploads'),
				'recommended'	=> true,
			);

			$phpOptions[] = array(
				'label'			=> AText::_('MAIN_REC_MCR'),
				'current'		=> (bool) ini_get('magic_quotes_runtime'),
				'recommended'	=> false,
			);

			if (version_compare($jVersion, '3.0.0', 'lt'))
			{
				$phpOptions[] = array(
					'label'			=> AText::_('MAIN_REC_MCGPC'),
					'current'		=> (bool) ini_get('magic_quotes_gpc'),
					'recommended'	=> false,
				);
			}

			$phpOptions[] = array(
				'label'			=> AText::_('MAIN_REC_OUTBUF'),
				'current'		=> (bool) ini_get('output_buffering'),
				'recommended'	=> false,
			);

			$phpOptions[] = array(
				'label'			=> AText::_('MAIN_REC_SESSIONAUTO'),
				'current'		=> (bool) ini_get('session.auto_start'),
				'recommended'	=> false,
			);

			$phpOptions[] = array(
				'label'			=> AText::_('MAIN_REC_NATIVEZIP'),
				'current'		=> function_exists('zip_open') && function_exists('zip_read'),
				'recommended'	=> true,
			);

		}

		return $phpOptions;
	}
}
