<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieViewDatabase extends AView
{
	use ADatabaseFixmysql;

	public $db;

	/** @var int Do we have a flag for large tables? */
	public $large_tables = 0;

	public $maxPacketSize = 0;

	public $number_of_substeps = 0;

	public $recommendedPacketSize = 0;

	public $substep = '';

	/**
	 * Select list for restoring only specific tables
	 *
	 * @var string
	 */
	public $table_list = '';

	public function onBeforeMain()
	{
		$this->loadHelper('select');

		/** @var AngieModelSteps $stepsModel */
		$stepsModel = AModel::getAnInstance('Steps', 'AngieModel', [], $this->container);
		/** @var AngieModelDatabase $dbModel */
		$dbModel = AModel::getAnInstance('Database', 'AngieModel', [], $this->container);

		$this->substep            = $stepsModel->getActiveSubstep();
		$this->number_of_substeps = $stepsModel->getNumberOfSubsteps();
		$this->db                 = $dbModel->getDatabaseInfo($this->substep);

		// Show an error if there are no database connectors
		if (!AngieHelperSelect::getAvailableConnectors($this->db->dbtech))
		{
			$stepsModel->resetSteps();
			$this->setLayout('noconnectors');

			return true;
		}

		$this->combinedHost       = $this->createMySQLHostname($this->db->dbhost, $this->db->dbport, $this->db->dbsocket, $this->db->dbencryption);
		$this->large_tables       = $dbModel->getLargeTablesDetectedValue();
		$maxPacket                = $dbModel->getCurrentMaxPacketSize($this->db);
		$this->large_tables       = ($this->large_tables < $maxPacket) ? 0 : $this->large_tables;

		// Do we have a list of tables? If so let's display them to the user
		$tables = isset($this->db->tables) ? $this->db->tables : '';

		if ($tables)
		{
			$table_data = [];

			foreach ($tables as $table)
			{
				$table_data[] = AngieHelperSelect::option($table, $table);
			}

			$select_attribs   = [
				'data-placeholder' => AText::_('DATABASE_LBL_SPECIFICTABLES_LBL'), 'multiple' => 'true', 'size' => 10,
				'style'            => 'height: 100px; width: 100%',
			];
			$this->table_list = AngieHelperSelect::genericlist($table_data, 'specific_tables', $select_attribs, 'value', 'text');
		}

		// Do I have large tables?
		if ($this->large_tables)
		{
			$this->maxPacketSize         = round($maxPacket / (1024 * 1024), 2);
			$this->recommendedPacketSize = ceil($this->large_tables / (1024 * 1024));
			$this->large_tables          = round($this->large_tables / (1024 * 1024), 2);
		}

		// Joomla-specific configuration
		if (defined('ANGIE_INSTALLER_NAME') && ANGIE_INSTALLER_NAME == 'Joomla')
		{
			$jVersion = $this->container->session->get('jversion', '2.5.0');

			// Joomla 4: Supports MySQL SSL/TLS connections
			if (version_compare($jVersion, '4.0.0', 'ge'))
			{
				define('ANGIE_DB_ALLOW_SSL', 1);
			}
		}

		/**
		 * All installer scripts: Joomla and WordPress include the port/socket in the hostname.
		 */
		if (!defined('ANGIE_DB_ALLOW_PORT_SOCKET') && ($this->db->dbport || $this->db->dbsocket))
		{
			$this->db->host     = $this->createMySQLHostname($this->db->dbhost, $this->db->dbport, $this->db->dbsocket);
			$this->db->dbport   = null;
			$this->db->dbsocket = null;
		}

		return true;
	}
}
