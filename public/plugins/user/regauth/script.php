<?php
/**
 * @package		Registration Authorization User Plugin
 * @copyright	(C) 2016-2023 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 * @since		1.4.0
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class plgUserRegauthInstallerScript
{
	/**
	 * Method to install the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	public function install ($parent) 
	{
		$this->convertParams();
		return true;
	}

	/**
	 * Method to uninstall the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	public function uninstall ($parent) 
	{
		return true;
	}

	/**
	 * Method to update the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	public function update ($parent) 
	{
		$this->convertParams();
		echo '<p>The <em>regauth</em> plugin has been updated to version' . $parent->get('manifest')->version . '.</p>';
		return true;
	}

	/**
	 * Method to run before an install/update/uninstall method
	 * $parent is the class calling this method
	 * $type is the type of change (install, update or discover_install)
	 *
	 * @return void
	 */
	public function preflight ($type, $parent) 
	{
	//	echo $type,'<p>Anything here happens before the installation/update/uninstallation of the module.</p>';
	//	if ($type=='uninstall') return true;
	//	$this->convertParams();
	//	return false;
		return true;
	}

	/**
	 * Method to run after an install/update/uninstall method
	 * $parent is the class calling this method
	 * $type is the type of change (install, update or discover_install)
	 *
	 * @return void
	 */
	public function postflight ($type, $parent) 
	{
		return true;
	}

	// convert any old plugin parameters to new style
	private function convertParams ()
	{
		$db = Factory::getDbo();
		$db->setQuery("SELECT params FROM #__extensions WHERE name = 'plg_user_regauth'");
		$json = $db->loadResult();
		if ($json) {
			$prms = json_decode($json, true);
			if (isset($prms['authcode1'])) {
				$authcodes = [];
				foreach (['authcode1','authcode2','authcode3','authcode4','authcode5','authcode6'] as $c) {
					if (!empty($prms[$c])) {
						$k = substr($c, -1);
						$authcodes['authcode'.$k] = ['code'=>$prms[$c]];
						if (!empty($prms['groups'.$k])) $authcodes['authcode'.$k]['groups'] = $prms['groups'.$k];
					}
				}
				$db->setQuery("UPDATE #__extensions SET params = ".$db->quote(json_encode(['authcode' => $authcodes]))." WHERE name = 'plg_user_regauth'");
				$db->query();
				Factory::getApplication()->enqueueMessage('The <em>regauth</em> plugin parameters have been upgraded to a new format. Please ensure that the plugin configuration is correct.', 'warning');
			}
		}
	}

}
