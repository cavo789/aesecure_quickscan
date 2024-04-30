<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Administrator\Controller\Mixin;

defined('_JEXEC') || die;

use Joomla\CMS\MVC\Model\BaseModel;

trait AjaxAware
{
	protected $decodeJsonAsArray = false;

	public function ajax()
	{
		// Parse the JSON data and reset the action query param to the resulting array
		$action_json = $this->input->get('action', '', 'raw');
		$action      = json_decode($action_json, $this->decodeJsonAsArray);

		/** @var BaseModel $model */
		$model = $this->getModel($this->getName(), 'Administrator');

		$model->setState('action', $action);

		$ret = $model->doAjax();

		@ob_end_clean();
		echo '###' . json_encode($ret) . '###';
		flush();

		$this->app->close();
	}

}