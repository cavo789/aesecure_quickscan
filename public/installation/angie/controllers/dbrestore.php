<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieControllerDbrestore extends AController
{
	public function start()
	{
		$key             = $this->input->get('key', null);
		$data            = $this->input->get('dbinfo', null, 'array');
		$specific_tables = $this->input->get('specific_tables', [], 'array');

		if (empty($key) || empty($data['dbtype']))
		{
			$result = [
				'percent'  => 0,
				'restored' => 0,
				'total'    => 0,
				'eta'      => 0,
				'error'    => AText::_('DATABASE_ERR_INVALIDKEY'),
				'done'     => 1,
			];
			@ob_clean();
			echo json_encode($result);

			return;
		}

		/** @var AngieModelDatabase $model */
		$model     = AModel::getAnInstance('Database', 'AngieModel', [], $this->container);
		$savedData = $model->getDatabaseInfo($key);

		if (is_object($savedData))
		{
			$savedData = (array) $savedData;
		}

		if (!is_array($savedData))
		{
			$savedData = [];
		}

		$data = array_merge($savedData, $data);

		$model->setDatabaseInfo($key, $data);
		$model->saveDatabasesJson();

		try
		{
			$restoreEngine = ADatabaseRestore::getInstance($key, $data, $this->container);

			// First of all let's prime the restoration engine
			$restoreEngine->removeInformationFromStorage();
			$restoreEngine->removeLog();

			// Then set the list of tables we want to restore
			$restoreEngine->setSpecificEntities($specific_tables);

			$result = [
				'percent'  => 0,
				'restored' => 0,
				'total'    => $restoreEngine->getTotalSize(true),
				'eta'      => '–––',
				'error'    => '',
				'done'     => 0,
			];
		}
		catch (Exception $exc)
		{
			$result = $this->exceptionToResultArray($exc);
		}

		@ob_clean();
		echo json_encode($result);
	}

	public function step()
	{
		$key = $this->input->get('key', null);

		/** @var AngieModelDatabase $model */
		$model = AModel::getAnInstance('Database', 'AngieModel', [], $this->container);
		$data  = $model->getDatabaseInfo($key);

		try
		{
			$restoreEngine = ADatabaseRestore::getInstance($key, $data, $this->container);
			$restoreEngine->getTimer()->resetTime();
			$result = $restoreEngine->stepRestoration();
		}
		catch (Exception $exc)
		{
			$result = $this->exceptionToResultArray($exc);
		}

		@ob_clean();
		echo json_encode($result);
	}

	private function exceptionToResultArray(Exception $exc)
	{
		$result = [
			'percent'    => 0,
			'restored'   => 0,
			'total'      => 0,
			'eta'        => 0,
			'error'      => '',
			'stopError'  => 0,
			'done'       => 1,
		];

		try
		{
			@ob_start();

			$this->layout = 'default';

			if ($exc instanceof ADatabaseRestoreExceptionDbuser)
			{
				$this->layout = 'dbuser';
				$result['stopError'] = 1;
			}
			elseif ($exc instanceof ADatabaseRestoreExceptionDbname)
			{
				$this->layout = 'dbname';
				$result['stopError'] = 1;
			}

			$view = $this->getThisView();
			$view->task = $this->task;
			$view->doTask = $this->doTask;
			$view->setLayout(is_null($this->layout) ? 'default' : $this->layout);
			$view->exception = $exc;
			$view->display();

			$errorMessage = @ob_get_clean();
		}
		catch (Exception $e)
		{
			$errorMessage = '';
		}

		$result['error'] = $errorMessage ?: $exc->getMessage();

		return $result;
	}
}
