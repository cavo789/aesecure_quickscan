<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieViewMain extends AView
{
	/** @var   array  Required settings */
	public $reqSettings = array();

	/** @var   bool  Are the required settings met? */
	public $reqMet = false;

	/** @var   array  Recommended settings */
	public $recommendedSettings = array();

	/** @var   array  Extra information about the backup */
	public $extraInfo = array();

	/** @var   string  Detected Joomla! version. Only used by ANGIE for Joomla!. */
	public $joomlaVersion = '0.0.0';

	/** @var   string  Version of the platform (application being restored). Used by all other ANGIE installers. */
	public $version = '0.0.0';

	public function onBeforeMain()
	{
		if ($this->input->get('layout') != 'init')
		{
			return true;
		}

		/** @var AngieModelBaseMain $model */
		$model = $this->getModel();

		/** @var ASession $session */
		$session = $this->container->session;

		// Assign the results of the various checks
		$this->reqSettings         = $model->getRequired();
		$this->reqMet              = $model->isRequiredMet();
		$this->recommendedSettings = $model->getRecommended();
		$this->extraInfo           = $model->getExtraInfo();
		$this->joomlaVersion       = $session->get('jversion');
		$this->version             = $session->get('version');

		// Am I restoring to a different site?
		$this->restoringToDifferentHost = false;

		if (isset($this->extraInfo['host']))
		{
			$uri                            = AUri::getInstance();
			$this->restoringToDifferentHost = $this->extraInfo['host']['current'] != $uri->getHost();
		}

		// If I am restoring to a different host blank out the database
		// connection information to prevent unpleasant situations, like a user
		// "accidentally" overwriting his original site's database...
		if ($this->restoringToDifferentHost && !$session->get('main.resetdbinfo', false))
		{
			$model->resetDatabaseConnectionInformation();
		}

		return true;
	}
}
