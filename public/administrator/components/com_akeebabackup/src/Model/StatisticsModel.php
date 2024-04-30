<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Administrator\Model;

defined('_JEXEC') || die;

use Akeeba\Component\AkeebaBackup\Administrator\Mixin\ModelStateFixTrait;
use Akeeba\Component\AkeebaBackup\Administrator\Service\ComponentParameters;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Exception;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Factory as JoomlaFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\ParameterType;
use Joomla\Database\QueryInterface;

#[\AllowDynamicProperties]
class StatisticsModel extends ListModel
{
	use ModelStateFixTrait;
	
	public function __construct($config = [], MVCFactoryInterface $factory = null)
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = [
				'search',
				'from',
				'to',
				'origin',
				'profile',
				'frozen',
			];
		}

		parent::__construct($config, $factory);

		$this->filterFormName = 'filter_manage';
	}

	public function unfuck()
	{
		$this->__state_set = true;
	}

	/**
	 * Is this string a valid remote filename?
	 *
	 * We've had reports that some servers return a bogus, non-empty string for some remote_filename columns, causing
	 * the "Manage remote stored files" column to appear even for locally stored files. By applying more rigorous tests
	 * for the remote_filename column we can avoid this problem.
	 *
	 * @param   string|null  $filename
	 *
	 * @return  bool
	 *
	 * @since   9.2.2
	 */
	public function isRemoteFilename(string $filename = null): bool
	{
		// A remote filename has to be a string which is does not consist solely of whitespace
		if (!is_string($filename) || trim($filename) === '')
		{
			return false;
		}

		// Let's remote whitespace just in case
		$filename = trim($filename);

		// A remote filename must be in the format engine://path
		if (strpos($filename, '://') === false)
		{
			return false;
		}

		// Get the engine and path
		[$engine, $path] = explode('://', $filename, 2);
		$engine = trim($engine);
		$path   = trim($path);

		// Both engine and path must be non-empty
		if (empty($engine) || empty($path))
		{
			return false;
		}

		// The engine must be known to the backup engine
		$classname = 'Akeeba\\Engine\\Postproc\\' . ucfirst($engine);

		return class_exists($classname);
	}

	/**
	 * Returns the same list as getStatisticsList(), but includes an extra field
	 * named 'meta' which categorises attempts based on their backup archive status
	 *
	 * @param   bool   $overrideLimits  Should I disregard limit, limitStart and filters?
	 * @param   array  $filters         Filters to apply. See Platform::get_statistics_list
	 * @param   array  $order           Results ordering. The accepted keys are by (column name) and order (ASC or DESC)
	 *
	 * @return  array  An array of arrays. Each inner array is one backup record.
	 */
	public function &getStatisticsListWithMeta($overrideLimits = false, $filters = null, $order = null)
	{
		$limitstart = $overrideLimits ? 0 : $this->getState('list.start', 0);
		$limit      = $overrideLimits ? 0 : $this->getState('list.limit', 10);
		$filters    = $overrideLimits ? null : $filters;

		if (is_array($order) && isset($order['order']))
		{
			$order['order'] = strtoupper($order['order']) === 'ASC' ? 'asc' : 'desc';
		}

		$allStats = Platform::getInstance()->get_statistics_list([
			'limitstart' => $limitstart,
			'limit'      => $limit,
			'filters'    => $filters,
			'order'      => $order,
		]);

		$validRecords          = Platform::getInstance()->get_valid_backup_records() ?: [];
		$updateObsoleteRecords = [];
		$ret                   = array_map(function (array $stat) use (&$updateObsoleteRecords, $validRecords) {
			$hasRemoteFiles = false;

			// Translate backup status and the existence of a remote filename to the backup record's "meta" status.
			switch ($stat['status'])
			{
				case 'run':
					$stat['meta'] = 'pending';
					break;

				case 'fail':
					$stat['meta'] = 'fail';
					break;

				default:
					$hasRemoteFiles = $this->isRemoteFilename($stat['remote_filename']);
					$stat['meta']   = $hasRemoteFiles ? 'remote' : 'obsolete';
					break;
			}

			$stat['hasRemoteFiles'] = $hasRemoteFiles;

			// If the backup is reported to have files still stored on the server we need to investigate further
			if (in_array($stat['id'], $validRecords))
			{
				$archives      = Factory::getStatistics()->get_all_filenames($stat);
				$hasLocalFiles = (is_array($archives) ? count($archives) : 0) > 0;
				$stat['meta']  = $hasLocalFiles ? 'ok' : ($hasRemoteFiles ? 'remote' : 'obsolete');

				// The archives exist. Set $stat['size'] to the total size of the backup archives.
				if ($hasLocalFiles)
				{
					$stat['size'] = $stat['total_size']
						?: array_reduce(
							$archives,
							function ($carry, $filename) {
								return $carry += @filesize($filename) ?: 0;
							},
							0
						);

					return $stat;
				}

				// The archives do not exist or we can't find them. If the record says otherwise we need to update it.
				if ($stat['filesexist'])
				{
					$updateObsoleteRecords[] = $stat['id'];
				}

				// Does the backup record report a total size even though our files no longer exist?
				if ($stat['total_size'])
				{
					$stat['size'] = $stat['total_size'];
				}
			}

			return $stat;
		}, $allStats);

		// Update records which report that their files exist on the server but, in fact, they don't.
		Platform::getInstance()->invalidate_backup_records($updateObsoleteRecords);

		return $ret;
	}

	/**
	 * Send an email notification for failed backups
	 *
	 * @return  array  See the CLI script
	 */
	public function notifyFailed()
	{
		$cParams = ComponentHelper::getParams('com_akeebabackup');

		// Invalidate stale backups
		try
		{
			Factory::resetState([
				'global' => true,
				'log'    => false,
				'maxrun' => $cParams->get('failure_timeout', 180),
			]);
		}
		catch (Exception $e)
		{
			// This will die if the output directory is invalid. Let it die, then.
		}

		// Get the last execution and search for failed backups AFTER that date
		$last = $this->getLastCheck();

		// Get failed backups
		$filters = [
			['field' => 'status', 'operand' => '=', 'value' => 'fail'],
			['field' => 'backupstart', 'operand' => '>', 'value' => $last],
		];

		$failed = Platform::getInstance()->get_statistics_list(['filters' => $filters]);

		// Well, everything went ok.
		if (!$failed)
		{
			return [
				'message' => ["No need to run: no failed backups or notifications were already sent."],
				'result'  => true,
			];
		}

		// Whops! Something went wrong, let's start notifing
		$superAdmins     = [];
		$superAdminEmail = $cParams->get('failure_email_address', '');

		if (!empty($superAdminEmail))
		{
			$superAdmins = $this->getSuperUsers($superAdminEmail);
		}

		if (empty($superAdmins))
		{
			$superAdmins = $this->getSuperUsers();
		}

		if (empty($superAdmins))
		{
			return [
				'message' => ["Failed backup(s) detected, but there are no configured Super Administrators to receive notifications"],
				'result'  => false,
			];
		}

		$failedReport = [];

		foreach ($failed as $fail)
		{
			$string = "Description : " . $fail['description'] . "\n";
			$string .= "Start time  : " . $fail['backupstart'] . "\n";
			$string .= "Origin      : " . $fail['origin'] . "\n";
			$string .= "Type        : " . $fail['type'] . "\n";
			$string .= "Profile ID  : " . $fail['profile_id'] . "\n";
			$string .= "Backup ID   : " . $fail['id'];

			$failedReport[] = $string;
		}

		$failedReport = implode("\n#-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+#\n", $failedReport);

		$email_subject = $cParams->get('failure_email_subject', '');

		if (!$email_subject)
		{
			$email_subject = <<<ENDSUBJECT
THIS EMAIL IS SENT FROM YOUR SITE "[SITENAME]" - Failed backup(s) detected
ENDSUBJECT;
		}

		$email_body = $cParams->get('failure_email_body', '');

		if (!$email_body)
		{
			$email_body = <<<ENDBODY
================================================================================
FAILED BACKUP ALERT
================================================================================

Your site has determined that there are failed backups.

The following backups are found to be failing:

[FAILEDLIST]

================================================================================
WHY AM I RECEIVING THIS EMAIL?
================================================================================

This email has been automatically sent by scritp you, or the person who built
or manages your site, has installed and explicitly configured. This script looks
for failed backups and sends an email notification to all Super Users.

If you do not understand what this means, please do not contact the authors of
the software. They are NOT sending you this email and they cannot help you.
Instead, please contact the person who built or manages your site.

================================================================================
WHO SENT ME THIS EMAIL?
================================================================================

This email is sent to you by your own site, [SITENAME]

ENDBODY;
		}

		$app = JoomlaFactory::getApplication();

		$mailfrom = $app->get('mailfrom');
		$fromname = $app->get('fromname');

		$email_subject = Factory::getFilesystemTools()->replace_archive_name_variables($email_subject);
		$email_body    = Factory::getFilesystemTools()->replace_archive_name_variables($email_body);
		$email_body    = str_replace('[FAILEDLIST]', $failedReport, $email_body);

		foreach ($superAdmins as $sa)
		{
			try
			{
				$mailer = JFactory::getMailer();

				$mailer->setSender([$mailfrom, $fromname]);
				$mailer->addRecipient($sa->email);
				$mailer->setSubject($email_subject);
				$mailer->setBody($email_body);
				$mailer->Send();
			}
			catch (Exception $e)
			{
				// Joomla! 3.5 is written by incompetent bonobos
			}
		}

		// Let's update the last time we check, so we will avoid to send
		// the same notification several times
		$this->updateLastCheck(intval($last));

		return [
			'message' => [
				sprintf(
					'Found %d failed backup(s)',
					(is_array($failed) || $failed instanceof \Countable)
						? count($failed)
						: 0
				),
				sprintf(
					"Sent %s notifications",
					count($superAdmins)
				),
			],
			'result'  => false,
		];
	}

	/**
	 * Set the flag to hide the restoration instructions modal from the Manage Backups page
	 *
	 * @param   ComponentParameters  $componentParametersService
	 *
	 * @return  void
	 */
	public function hideRestorationInstructionsModal(ComponentParameters $componentParametersService)
	{
		$cParams = ComponentHelper::getParams('com_akeebabackup');
		$cParams->set('show_howtorestoremodal', 0);

		$componentParametersService->save($cParams);
	}

	/**
	 * Get a Joomla! pagination object
	 *
	 * @param   array  $filters  Filters to apply. See Platform::get_statistics_list
	 *
	 * @return  Pagination
	 */
	public function getFilteredPagination($filters = null)
	{
		// Get a storage key.
		$store = $this->getStoreId('getPagination:' . md5(serialize($filters)));

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Prepare pagination values
		$total      = Platform::getInstance()->get_statistics_count($filters);
		$limitstart = $this->getState('list.start', 0);
		$limit      = $this->getState('list.limit', 10);

		// Create the pagination object
		$this->cache[$store] = new Pagination($total, $limitstart, $limit);

		return $this->cache[$store];
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Call the parent method
		parent::populateState($ordering, $direction);

		$app = JoomlaFactory::getApplication();

		if ($app->isClient('site'))
		{
			$this->setState('list.start', 0);
			$this->setState('list.limit', 0);
		}
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return DatabaseQuery|QueryInterface
	 *
	 * @throws  Exception
	 * @since   9.0.0
	 */
	protected function getListQuery()
	{
		/** @var DatabaseDriver $db */
		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
		            ->select('*')
		            ->from($db->qn('#__akeebabackup_backups'));

		// Description / ID search filter
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$ids = (int) substr($search, 3);
				$query->where($db->quoteName('id') . ' = :id')
				      ->bind(':id', $ids, ParameterType::INTEGER);
			}
			else
			{
				$search = '%' . $search . '%';
				$query->where($db->qn('description') . ' LIKE :search')
				      ->bind(':search', $search);
			}
		}

		// Dates from and to filters
		$from = $this->getState('filter.from');
		$to   = $this->getState('filter.to');

		if (!empty($from) && !empty($to))
		{
			$from = (clone JoomlaFactory::getDate($from))->toSql();
			$to   = (clone JoomlaFactory::getDate($to))->toSql();
			$query->where($db->qn('backupstart') . ' BETWEEN :from AND :to')
			      ->bind(':from', $from, ParameterType::STRING)
			      ->bind(':to', $to, ParameterType::STRING);
		}
		elseif (!empty($from))
		{
			$from = (clone JoomlaFactory::getDate($from))->toSql();
			$query->where($db->qn('backupstart') . ' >= :from')
			      ->bind(':from', $from, ParameterType::STRING);
		}
		elseif (!empty($to))
		{
			$to = (clone JoomlaFactory::getDate($to))->toSql();
			$query->where($db->qn('backupstart') . ' <= :to')
			      ->bind(':to', $to, ParameterType::STRING);
		}

		// origin filter
		$origin = $this->getState('filter.origin');

		if (is_string($origin) && !empty($origin))
		{
			$query->where($db->qn('origin') . ' = :origin')
			      ->bind(':origin', $origin, ParameterType::STRING);
		}

		// profile filter
		$profile = $this->getState('filter.profile');

		if (is_numeric($profile))
		{
			$query->where($db->qn('profile_id') . ' = :profile')
			      ->bind(':profile', $profile, ParameterType::INTEGER);
		}

		// frozen filter
		$frozen = $this->getState('filter.frozen');

		if (is_numeric($frozen))
		{
			// Option 2 is non-frozen which is 0 in the database
			$frozen = $frozen == 2 ? 0 : $frozen;
			$query->where($db->qn('frozen') . ' = :frozen')
			      ->bind(':frozen', $frozen, ParameterType::INTEGER);
		}

		return $query;
	}

	/**
	 * Returns the Super Users' email information. If you provide a comma separated $email list we will check that these
	 * emails do belong to Super Users and that they have not blocked reception of system emails.
	 *
	 * @param   null|string  $email  A list of Super Users to email, null for all Super Users
	 *
	 * @return  User[]  The list of Super User objects
	 */
	private function getSuperUsers($email = null)
	{
		// Convert the email list to an array
		$emails = [];

		if (!empty($email))
		{
			$temp   = explode(',', $email);
			$emails = [];

			foreach ($temp as $entry)
			{
				$emails[] = trim($entry);
			}

			$emails = array_unique($emails);
			$emails = array_map('strtolower', $emails);
		}

		// Get all usergroups with Super User access
		$db     = $this->getDatabase();
		$q      = $db->getQuery(true)
		             ->select([$db->qn('id')])
		             ->from($db->qn('#__usergroups'));
		$groups = $db->setQuery($q)->loadColumn();

		// Get the groups that are Super Users
		$groups = array_filter($groups, function ($gid) {
			return Access::checkGroup($gid, 'core.admin');
		});

		$userList = [];

		foreach ($groups as $gid)
		{
			$uids = Access::getUsersByGroup($gid);

			array_walk($uids, function ($uid, $index) use (&$userList) {
				$userList[$uid] = JoomlaFactory::getContainer()->get(UserFactoryInterface::class)->loadUserById($uid);
			});

		}

		if (empty($emails))
		{
			return $userList;
		}

		$userList = array_filter($userList, function (User $user) use ($emails) {
			return in_array(strtolower($user->email), $emails);
		});

		return $userList;
	}

	/**
	 * Update the time we last checked for failed backups
	 *
	 * @param   int  $exists  Any non zero value means that we update, not insert, the record
	 *
	 * @return  void
	 */
	private function updateLastCheck($exists)
	{
		$db = $this->getDatabase();

		$now      = clone JoomlaFactory::getDate();
		$nowToSql = $now->toSql();

		$query = $db->getQuery(true)
		            ->insert($db->qn('#__akeebabackup_storage'))
		            ->columns([$db->qn('tag'), $db->qn('lastupdate')])
		            ->values($db->q('akeeba_checkfailed') . ', :lastupdate')
		            ->bind(':lastupdate', $nowToSql);

		if ($exists)
		{
			$query = $db->getQuery(true)
			            ->update($db->qn('#__akeebabackup_storage'))
			            ->set($db->qn('lastupdate') . ' = :lastupdate')
			            ->where($db->qn('tag') . ' = ' . $db->q('akeeba_checkfailed'))
			            ->bind(':lastupdate', $nowToSql);
		}

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (Exception $exc)
		{
		}
	}

	/**
	 * Get the last update check date and time stamp
	 *
	 * @return  string
	 */
	private function getLastCheck()
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
		            ->select($db->qn('lastupdate'))
		            ->from($db->qn('#__akeebabackup_storage'))
		            ->where($db->qn('tag') . ' = ' . $db->q('akeeba_checkfailed'));

		$datetime = $db->setQuery($query)->loadResult();

		if (!intval($datetime))
		{
			$datetime = $db->getNullDate();
		}

		return $datetime;
	}

}