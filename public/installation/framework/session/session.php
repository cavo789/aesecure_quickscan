<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

#[\AllowDynamicProperties]
class ASession
{
	/** @var string Where temporary data is stored when using file storage */
	private $storagefile;

	/** @var array The session data, as an associative array */
	private $data;

	/** @var string The session storage key */
	private $sessionkey = null;

	/**
	 * Should I enable extra session security?
	 *
	 * When this is enabled ANGIE will check if there's another session. If so, it will refuse to run until you remove
	 * the session file for all other sessions from the tmp directory. Implemented in Akeeba Engine 5.4.0 I had to
	 * disable it in 5.4.2 because either people couldn't read or their servers are screwed up.
	 *
	 * So here's the deal: if you do not want ANYONE ON THE FREAKING INTERNET to find out all the gory details about
	 * your site during restoration, including your database password, JUST USE THE ANGIE PASSWORD FEATURE. This is what
	 * we wrote that feature for.
	 */

	const ENABLE_EXTRA_SECURITY = false;

	/**
	 * Singleton implementation
	 *
	 * @return  ASession
	 */
	static function &getInstance()
	{
		static $instance = null;

		if (!is_object($instance))
		{
			$instance = new ASession();
		}

		return $instance;
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Calculate the session key
		// -- Get the user's IP
		AUtilsIp::workaroundIPIssues();
		$ip = AUtilsIp::getUserIP();

		// -- Get the HTTPS status
		$httpsstatus = empty($_SERVER['HTTPS']) ? 'off' : $_SERVER['HTTPS'];

		// -- Calculate the session key
		if (array_key_exists('LOCAL_ADDR', $_SERVER))
		{
			$server_ip = $_SERVER['LOCAL_ADDR'];
		}
		elseif (array_key_exists('SERVER_ADDR', $_SERVER))
		{
			$server_ip = $_SERVER['SERVER_ADDR'];
		}
		else
		{
			$server_ip = '';
		}

		/**
		 * I removed server IP because of cases like the (private) ticket #28313  The user has a server farm with four
		 * nodes. Each node has the same SERVER_NAME but a different (internal network) LOCAL_ADDR. As a result the
		 * session key is different depending on which nodes responds.
		 *
		 * If session lock is enabled this will kick you out unless your requst is handled by the one node that
		 * originally replied to your request.
		 *
		 * If session lock is disabled the restoration will go through BUT the database connection information will not
		 * be remembered betweeen page loads (since they are saved in the session which depends on the server IP). This
		 * WILL cause database restoration mayhem if you are restoring on the same db server as the original site: some
		 * of the nodes will have a blank session which "remembers" the original site's db connection information,
		 * therefore they will OVERWRITE your main site.
		 *
		 * If you run a server farm you MUST have /administrator and /installation (as well as kickstart.php) served
		 * always from one specific node. You should then rsync that server's files to all other servers in the group.
		 * You should probably do the same for pages that allow uploads from clients but that's beyond the scope of what
		 * we do here.
		 */
		// $this->sessionkey = md5($ip . $_SERVER['HTTP_USER_AGENT'] . $httpsstatus . $server_ip . $_SERVER['SERVER_NAME']);
		$this->sessionkey = md5($ip . $_SERVER['HTTP_USER_AGENT'] . $httpsstatus . $_SERVER['SERVER_NAME']);

		if (defined('ANGIE_FORCED_SESSION_KEY') && ANGIE_FORCED_SESSION_KEY)
		{
			if ($this->sessionkey != ANGIE_FORCED_SESSION_KEY)
			{
				die(AText::_('SESSIONBLOCKED_HEADER_IN_USE'));
			}
		}

		$storagefile       = APATH_INSTALLATION . '/tmp/storagedata-' . $this->sessionkey . '.dat';
		$this->storagefile = $storagefile;

		/**
		 * If there is another storagedata-* file we unset the value for ourselves. This allows us to warn the user that
		 * the restoration is already in progress by someone else.
		 */
		if (self::ENABLE_EXTRA_SECURITY)
		{
			try
			{
				$baseNameSelf     = basename($storagefile);

				$di = new DirectoryIterator(APATH_INSTALLATION . '/tmp');

				foreach ($di as $file)
				{
					if (!$file->isFile())
					{
						continue;
					}

					if ($file->isDot())
					{
						continue;
					}

					$basename = $file->getBasename();

					if ($basename == $baseNameSelf)
					{
						continue;
					}

					if (substr($basename, -4) != '.dat')
					{
						continue;
					}

					// Another storage file found. Whoopsie! You are doing something wrong here, pal.
					if (substr($basename, 0, 12) == 'storagedata-')
					{
						/**
						 * If the user has not overridden the session key to lock it to their browser we unset the
						 * storagefile property, causing the session to error out. This triggers the "Oops! The installer
						 * is already in use." page.
						 */
						if (!defined('ANGIE_FORCED_SESSION_KEY') || !ANGIE_FORCED_SESSION_KEY)
						{
							$this->storagefile = '';

							break;
						}

						/**
						 * If, however, the user has edited defines.php to force the session key we can simply delete the
						 * extra session files.
						 */
						@unlink($file->getPathname());
					}
				}
			}
			catch (Exception $e)
			{
				// Do nothing; unreadable / unwriteable sessions are caught elsewhere
			}
		}

		$this->loadData();
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		$this->saveData();
	}

	/**
	 * Is the storage class able to save the data between page loads?
	 *
	 * @return  bool  True if everything works properly
	 */
	public function isStorageWorking()
	{
		if (!file_exists($this->storagefile))
		{
			$fp = @fopen($this->storagefile, 'w');

			if ($fp === false)
			{
				return false;
			}

			@fclose($fp);
			@unlink($this->storagefile);

			return true;
		}

		return @is_writable($this->storagefile);
	}

	/**
	 * Resets the internal storage
	 */
	public function reset()
	{
		$this->data = array();
	}

	/**
	 * Loads session data from a file or a session variable (auto detect)
	 */
	public function loadData()
	{
		$file = @fopen($this->storagefile, 'r');

		if ($file === false)
		{
			$this->data = array();

			return;
		}

        // Do not try to read a 0 amount of bytes otherwise PHP 8 will raise a fatal error
        $raw_data   = filesize($this->storagefile) ? fread($file, filesize($this->storagefile)) : '';
		$this->data = array();

		if (@strlen($raw_data) > 0)
		{
			$this->decode_data($raw_data);
		}
	}

	/**
	 * Saves session data to a file or a session variable (auto detect)
	 *
	 * @return  bool  True if the session storage filename is set
	 */
	public function saveData()
	{
		if (empty($this->storagefile))
		{
			return false;
		}

		$data = $this->encode_data();
		$fp   = @fopen($this->storagefile, 'w');

		if ($fp === false)
		{
			return false;
		}

		@fwrite($fp, $data);
		@fclose($fp);

		return true;
	}

	/**
	 * Sets or updates the value of a session variable
	 *
	 * @param   $key    string  The variable's name
	 * @param   $value  string  The value to store
	 */
	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 * Returns the value of a temporary variable
	 *
	 * @param   $key      string  The variable's name
	 * @param   $default  mixed   The default value, null if not specified
	 *
	 * @return  mixed  The variable's value
	 */
	public function get($key, $default = null)
	{
		if (array_key_exists($key, $this->data))
		{
			return $this->data[$key];
		}

		return $default;
	}

	/**
	 * Removes a variable from the storage
	 *
	 * @param   $key  string  The name of the variable to remove
	 */
	public function remove($key)
	{
		if (array_key_exists($key, $this->data))
		{
			unset($this->data[$key]);
		}
	}

	/**
	 * Do we have a storage file for the session? If not, it means that ANGIE has detected another active session, i.e.
	 * someone else is using it already to restore a site. This method is used by the Dispatcher to block the request
	 * and warn the user of the issue.
	 *
	 * @return  bool
	 */
	public function hasStorageFile()
	{
		return !empty($this->storagefile);
	}

	/**
	 * Returns the session key file. Used to display the message in view=session&layout=blocked which is displayed when
	 * the user is trying to access ANGIE while someone else is already using it.
	 *
	 * @return  string
	 */
	public function getSessionKey()
	{
		return $this->sessionkey;
	}

	/**
	 * Disable saving the storage data. This is used by the password view to prevent starting a new session when a
	 * password has not been entered. This way, if the installer is password-protected, a random visitor getting to the
	 * installer before the site administrator will NOT cause the administrator to be locked out of the installer,
	 * therefore won't require the administrator to have to delete the session storage files from tmp to get access to
	 * their site's installer.
	 *
	 * @return  void
	 */
	public function disableSave()
	{
		$this->storagefile = '';
	}

	/**
	 * Returns a serialized form of the temporary data
	 * @return string The serialized data
	 */
	private function encode_data()
	{
		$data = serialize($this->data);

		if (function_exists('base64_encode') && function_exists('base64_decode'))
		{
			// Prefer Βαse64 encoding of data
			return base64_encode($data);
		}

		if (function_exists('convert_uuencode') && function_exists('convert_uudecode'))
		{
			// UUEncode is just as good if Βαse64 is not available
			return convert_uuencode($data);
		}

		if (function_exists('bin2hex') && function_exists('pack'))
		{
			// Ugh! Let's use plain hex encoding
			return bin2hex($data);
		}

		// Note: on such a badly configure server we might end up with raw data; all bets are off!
		return $data;
	}

	/**
	 * Loads the temporary data off their serialized form
	 *
	 * @param   string $data
	 */
	private function decode_data($data)
	{
		$this->data = array();
		$data       = $this->internalDecode($data);
		$temp       = @unserialize($data);

		if (is_array($temp))
		{
			$this->data = $temp;
		}
	}

	/**
	 * The symmetric method to encode_data
	 *
	 * @param   string $data
	 *
	 * @return  string
	 */
	private function internalDecode($data)
	{
		if (function_exists('base64_encode') && function_exists('base64_decode'))
		{
			return base64_decode($data);
		}

		if (function_exists('convert_uuencode') && function_exists('convert_uudecode'))
		{
			return convert_uudecode($data);
		}

		if (function_exists('bin2hex') && function_exists('pack'))
		{
			// Ugh! Let's use plain hex encoding
			return pack("H*", $data);
		}

		return $data;
	}
}
