<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

abstract class AText
{
	private static $strings = array();

    /** @var   array[callable]  Callables to use to process translation strings after laoding them */
    private static $iniProcessCallbacks = array();

    /**
     * Adds an INI process callback to the stack
     *
     * @param   callable   $callable  The processing callback to add
     *
     * @return  void
     */
    public static function addIniProcessCallback($callable)
    {
        static::$iniProcessCallbacks[] = $callable;
    }

	public static function loadLanguage($langCode = null)
	{
		if (is_null($langCode))
		{
			$langCode = self::detectLanguage();
		}

		// If we are asked to load a non-default language, load the English (Great Britain) base translation first
		if ($langCode != 'en-GB')
		{
			static::loadLanguage('en-GB');
		}

		// Main file
		$filename = APATH_INSTALLATION . '/' . (AApplication::getInstance()->getName()) . '/language/' . $langCode . '.ini';
		$strings = AngieHelperIni::parse_ini_file($filename, false);
		self::$strings = array_merge(self::$strings, $strings);

		// Platform override file
		$filename = APATH_INSTALLATION . '/' . (AApplication::getInstance()->getName()) . '/platform/language/' . $langCode . '.ini';

		if (!@file_exists($filename))
		{
			$filename = APATH_INSTALLATION . '/platform/language/' . $langCode . '.ini';
		}

		if (@file_exists($filename))
		{
			$strings = AngieHelperIni::parse_ini_file($filename, false);
			$strings = self::replaceQQ($strings);
			self::$strings = array_merge(self::$strings, $strings);
		}

        // Performs callback on loaded strings
        if (!empty(static::$iniProcessCallbacks) && !empty(self::$strings))
        {
            foreach (static::$iniProcessCallbacks as $callback)
            {
                $ret = call_user_func($callback, $filename, self::$strings);

                if ($ret === false)
                {
                    return;
                }
                elseif (is_array($ret))
                {
                    self::$strings = $ret;
                }
            }
        }
	}

	public static function detectLanguage()
	{
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$languages = strtolower( $_SERVER["HTTP_ACCEPT_LANGUAGE"] );
			// $languages = ' fr-ch;q=0.3, da, en-us;q=0.8, en;q=0.5, fr;q=0.3';
			// need to remove spaces from strings to avoid error
			$languages = str_replace( ' ', '', $languages );
			$languages = explode( ",", $languages );

			foreach ( $languages as $language_list )
			{
				// pull out the language, place languages into array of full and primary
				// string structure:
				$temp_array = array();
				// slice out the part before ; on first step, the part before - on second, place into array
				$temp_array[0] = substr( $language_list, 0, strcspn( $language_list, ';' ) );//full language
				$temp_array[1] = substr( $language_list, 0, 2 );// cut out primary language
				if( (strlen($temp_array[0]) == 5) && ( (substr($temp_array[0],2,1) == '-') || (substr($temp_array[0],2,1) == '_') ) )
				{
					$langLocation = strtoupper(substr($temp_array[0],3,2));
					$temp_array[0] = $temp_array[1].'-'.$langLocation;
				}
				//place this array into main $user_languages language array
				$user_languages[] = $temp_array;
			}

			$baseName = APATH_INSTALLATION . '/' . (AApplication::getInstance()->getName()) . '/language/';
			foreach($user_languages as $languageStruct) {
				// Search for exact language
				$langFilename = $baseName.$languageStruct[0].'.ini';
				if(!file_exists($langFilename)) {
					$langFilename = '';
					if(function_exists('glob')) {
						$allFiles = glob($baseName.$languageStruct[1].'-*.ini');
						if(count($allFiles)) {
							$langFilename = array_shift($allFiles);
						}
					}
				}

				if(!empty($langFilename) && file_exists($langFilename)) {
					return basename($langFilename, '.ini');
				}
			}
		}

		return 'en-GB';
	}

	public static function _($key)
	{
		if (empty(self::$strings))
		{
			self::loadLanguage('en-GB');
			self::loadLanguage();
		}

		$key = strtoupper($key);

		if (array_key_exists($key, self::$strings))
		{
			return self::$strings[$key];
		}
		else
		{
			return $key;
		}
	}

	/**
	 * Passes a string thru a sprintf.
	 *
	 * Note that this method can take a mixed number of arguments as for the sprintf function.
	 *
	 * @param   string  $string  The format string.
	 *
	 * @return  string  The translated strings
	 */
	public static function sprintf($string)
	{
		$args = func_get_args();
		$count = count($args);
		if ($count > 0)
		{
			$args[0] = self::_($string);
			return call_user_func_array('sprintf', $args);
		}
		return '';
	}

	/**
	 * @param $strings
	 *
	 * @return array
	 */
	protected static function replaceQQ($strings)
	{
		return array_map(function ($value) {
			return str_replace('"_QQ_"', '&quot;', $value);
		}, $strings);
	}
}
