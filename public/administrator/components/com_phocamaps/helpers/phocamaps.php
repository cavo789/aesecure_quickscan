<?php
/*
 * @package Joomla 3.8
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
class PhocaMapsHelper
{
	public static function strTrimAll($input) {
		$output	= '';;
	    $input	= trim($input);
	    for($i=0;$i<strlen($input);$i++) {
	        if(substr($input, $i, 1) != " ") {
	            $output .= trim(substr($input, $i, 1));
	        } else {
	            $output .= " ";
	        }
	    }
	    return $output;
	}

	public static function getPhocaVersion($component = 'com_phocamaps') {
		$component = 'com_phocamaps';
		$folder = JPATH_ADMINISTRATOR . '/components/'.$component;

		if (Folder::exists($folder)) {
			$xmlFilesInDir = Folder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE . '/components/'.$component;
			if (Folder::exists($folder)) {
				$xmlFilesInDir = Folder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}

		$xml_items = array();
		if (!empty($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = JInstaller::parseXMLInstallFile($folder.'/'.$xmlfile)) {
					foreach($data as $key => $value) {
						$xml_items[$key] = $value;
					}
				}
			}
		}

		if (isset($xml_items['version']) && $xml_items['version'] != '' ) {
			return $xml_items['version'];
		} else {
			return '';
		}
	}

	public static function getAliasName($name) {

	}


	public static function fixImagePath($description) {

          $description = str_replace('<img src="'.JUri::root(true).'/', '', $description);// no double
          $description = str_replace('<img src="', '<img src="'.Uri::root(true).'/', $description);

          // correct possible problems with full paths
          $description = str_replace('<img src="'.JUri::root(true).'/http://', '<img src="http://', $description);
          $description = str_replace('<img src="/http://', '<img src="http://', $description);

		  $description = str_replace('<img src="'.JUri::root(true).'/https://', '<img src="https://', $description);
          $description = str_replace('<img src="/https://', '<img src="https://', $description);
          return $description;
       }

	public static function filterValue($string, $type = 'html') {

		switch ($type) {

			case 'url':
				return rawurlencode($string);
				break;

			case 'number':
				return preg_replace( '/[^.0-9]/', '', $string );
				break;

			case 'number2':
				//return preg_replace( '/[^0-9\.,+-]/', '', $string );
				return preg_replace( '/[^0-9\.,-]/', '', $string );
				break;

			case 'alphanumeric':
				return preg_replace("/[^a-zA-Z0-9]+/", '', $string);
				break;

			case 'alphanumeric2':
				return preg_replace("/[^\\w-]/", '', $string);// Alphanumeric plus _  -
				break;

			case 'alphanumeric3':
				return preg_replace("/[^\\w.-]/", '', $string);// Alphanumeric plus _ . -
				break;

			case 'folder':
			case 'file':
				$string =  preg_replace('/[\"\*\/\\\:\<\>\?\'\|]+/', '', $string);
				return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
				break;

			case 'folderpath':
			case 'filepath':
				$string = preg_replace('/[\"\*\:\<\>\?\'\|]+/', '', $string);
				return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
				break;

			case 'text':
				return htmlspecialchars(strip_tags($string), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
				break;

			case 'html':
			default:
				return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
				break;

		}

	}

	public static function getExtInfo() {

        PluginHelper::importPlugin('phocatools');
        $results = Factory::getApplication()->triggerEvent('onPhocatoolsOnDisplayInfo', array('NjI5NTcyMjc3MTE3'));
        if (isset($results[0]) && $results[0] === true) {
            return '';
        }

		return '<div style="text-align: right; color: rgb(211, 211, 211); clear: both; margin-top: 10px;margin-bottom:10px;">Powered by <a href="https://www.phoca.cz" style="text-decoration: none;" target="_blank" title="Phoca.cz">Phoca</a> <a href="https://www.phoca.cz/phocamaps" style="text-decoration: none;" target="_blank" title="Phoca Maps">Maps</a></div>';
	}
}
?>
