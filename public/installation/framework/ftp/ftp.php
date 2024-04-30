<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die;

/**
 * This file may contain code from the Joomla! Platform, Copyright (c) 2005 -
 * 2012 Open Source Matters, Inc. This file is NOT part of the Joomla! Platform.
 * It is derivative work and clearly marked as such as per the provisions of the
 * GNU General Public License.
 */

// Error codes:
if(!defined('FTP_ERROR_CANNOTCONNECT'))
{
	define('FTP_ERROR_CANNOTCONNECT',	30); // Unable to connect to host
	define('FTP_ERROR_NOTCONENCTED',	31); // Not connected
	define('FTP_ERROR_CANTSENDCOMMAND',	32); // Unable to send command to server
	define('FTP_ERROR_BADUSERNAME',		33); // Bad username
	define('FTP_ERROR_BADPASSWORD',		34); // Bad password
	define('FTP_ERROR_BADRESPONSE',		35); // Bad password
	define('FTP_ERROR_PASVFAILED',		36); // Passive mode failed
	define('FTP_ERROR_DATATRANSFER',	37); // Data transfer error
	define('FTP_ERROR_LOCALFS',			38); // Local filesystem error
	define('FTP_ERROR_SOFT',			39); // Miscellaneous FTP issue (soft error)
}

if (!defined('CRLF'))
{
	define('CRLF', "\r\n");
}
if (!defined("FTP_AUTOASCII"))
{
	define("FTP_AUTOASCII", -1);
}
if (!defined("FTP_BINARY"))
{
	define("FTP_BINARY", 1);
}
if (!defined("FTP_ASCII"))
{
	define("FTP_ASCII", 0);
}

// Is FTP extension loaded?  If not try to load it
if (!extension_loaded('ftp'))
{
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
	{
		@ dl('php_ftp.dll');
	}
	else
	{
		@ dl('ftp.so');
	}
}

// Force load ABuffer
class_exists('AUtilsBuffer', true);

/**
 * FTP client class
 */
class AFtp
{
	/**
	 * @var    resource  Socket resource
	 */
	private $_conn = null;

	/**
	 * @var    resource  Data port connection resource
	 */
	private $_dataconn = null;

	/**
	 * @var    array  Passive connection information
	 */
	private $_pasv = null;

	/**
	 * @var    string  Response Message
	 */
	private $_response = null;

	/**
	 * @var    integer  Timeout limit
	 */
	private $_timeout = 15;

	/**
	 * @var    integer  Transfer Type
	 */
	private $_type = null;

	/**
	 * @var    string  Native OS Type
	 */
	private $_OS = null;

	/**
	 * @var    array  Array to hold ascii format file extensions
	 */
	private $_autoAscii = array(
		"asp",
		"bat",
		"c",
		"cpp",
		"csv",
		"h",
		"htm",
		"html",
		"shtml",
		"ini",
		"inc",
		"log",
		"php",
		"php3",
		"pl",
		"perl",
		"sh",
		"sql",
		"txt",
		"xhtml",
		"xml");

	/**
	 * @var    array  AFTP instances container.
	 */
	protected static $instances = array();

	/**
	 * AFTP object constructor
	 *
	 * @param   array  $options  Associative array of options to set
	 */
	public function __construct($options = array())
	{
		// If default transfer type is not set, set it to autoascii detect
		if (!isset($options['type']))
		{
			$options['type'] = FTP_BINARY;
		}
		$this->setOptions($options);

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
		{
			$this->_OS = 'WIN';
		}
		elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'MAC')
		{
			$this->_OS = 'MAC';
		}
		else
		{
			$this->_OS = 'UNIX';
		}
	}

	/**
	 * AFTP object destructor
	 *
	 * Closes an existing connection, if we have one
	 */
	public function __destruct()
	{
		if (is_resource($this->_conn))
		{
			$this->quit();
		}
	}

	/**
	 * Returns the global FTP connector object, only creating it
	 * if it doesn't already exist.
	 *
	 * You may optionally specify a username and password in the parameters. If you do so,
	 * you may not login() again with different credentials using the same object.
	 * If you do not use this option, you must quit() the current connection when you
	 * are done, to free it for use by others.
	 *
	 * @param   string  $host     Host to connect to
	 * @param   string  $port     Port to connect to
	 * @param   array   $options  Array with any of these options: type=>[FTP_AUTOASCII|FTP_ASCII|FTP_BINARY], timeout=>(int)
	 * @param   string  $user     Username to use for a connection
	 * @param   string  $pass     Password to use for a connection
	 *
	 * @return  AFtp    The FTP Client object.
	 */
	public static function getInstance($host = '127.0.0.1', $port = '21', $options = null, $user = null, $pass = null)
	{
		$signature = $user . ':' . $pass . '@' . $host . ":" . $port;

		// Create a new instance, or set the options of an existing one
		if (!isset(self::$instances[$signature]) || !is_object(self::$instances[$signature]))
		{
			self::$instances[$signature] = new AFtp($options);
		}
		else
		{
			self::$instances[$signature]->setOptions($options);
		}

		// Connect to the server, and login, if requested
		if (!self::$instances[$signature]->isConnected())
		{
			$return = self::$instances[$signature]->connect($host, $port);
			if ($return && $user !== null && $pass !== null)
			{
				self::$instances[$signature]->login($user, $pass);
			}
		}

		return self::$instances[$signature];
	}

	/**
	 * Set client options
	 *
	 * @param   array  $options  Associative array of options to set
	 *
	 * @return  boolean  True if successful
	 */
	public function setOptions($options)
	{
		if (isset($options['type']))
		{
			$this->_type = $options['type'];
		}
		if (isset($options['timeout']))
		{
			$this->_timeout = $options['timeout'];
		}
		return true;
	}

	/**
	 * Method to connect to a FTP server
	 *
	 * @param   string  $host  Host to connect to [Default: 127.0.0.1]
	 * @param   string  $port  Port to connect on [Default: port 21]
	 *
	 * @return  boolean  True if successful
	 */
	public function connect($host = '127.0.0.1', $port = 21)
	{
		// Initialise variables.
		$errno = null;
		$err = null;

		// If already connected, return
		if (is_resource($this->_conn))
		{
			return true;
		}

		$this->_conn = @ftp_connect($host, $port, $this->_timeout);
		if ($this->_conn === false)
		{
			throw new Exception(AText::sprintf('ANGI_CLIENT_ERROR_AFTP_NO_CONNECT', $host, $port) ,30);
			return false;
		}
		// Set the timeout for this connection
		ftp_set_option($this->_conn, FTP_TIMEOUT_SEC, $this->_timeout);
		return true;
	}

	/**
	 * Method to determine if the object is connected to an FTP server
	 *
	 * @return  boolean  True if connected
	 */
	public function isConnected()
	{
		return is_resource($this->_conn);
	}

	/**
	 * Method to login to a server once connected
	 *
	 * @param   string  $user  Username to login to the server
	 * @param   string  $pass  Password to login to the server
	 *
	 * @return  boolean  True if successful
	 */
	public function login($user = 'anonymous', $pass = 'aftp@akeeba.com')
	{
		if (@ftp_login($this->_conn, $user, $pass) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_NOLOGIN') ,30);
			return false;
		}
		return true;
	}

	/**
	 * Method to quit and close the connection
	 *
	 * @return  boolean  True if successful
	 */
	public function quit()
	{
		// If native FTP support is enabled lets use it...
		@ftp_close($this->_conn);
		return true;
	}

	/**
	 * Method to retrieve the current working directory on the FTP server
	 *
	 * @return  string   Current working directory
	 */
	public function pwd()
	{
		if (($ret = @ftp_pwd($this->_conn)) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_PWD_BAD_RESPONSE'), 35);
			return false;
		}
		return $ret;
	}

	/**
	 * Method to system string from the FTP server
	 *
	 * @return  string   System identifier string
	 */
	public function syst()
	{
		if (($ret = @ftp_systype($this->_conn)) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_SYS_BAD_RESPONSE'), 35);
			return false;
		}

		// Match the system string to an OS
		if (strpos(strtoupper($ret), 'MAC') !== false)
		{
			$ret = 'MAC';
		}
		elseif (strpos(strtoupper($ret), 'WIN') !== false)
		{
			$ret = 'WIN';
		}
		else
		{
			$ret = 'UNIX';
		}

		// Return the os type
		return $ret;
	}

	/**
	 * Method to change the current working directory on the FTP server
	 *
	 * @param   string  $path  Path to change into on the server
	 *
	 * @return  boolean True if successful
	 */
	public function chdir($path)
	{
		if (@ftp_chdir($this->_conn, $path) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_CHDIR_BAD_RESPONSE'), 35);
			return false;
		}
		return true;
	}

	/**
	 * Method to reinitialise the server, ie. need to login again
	 *
	 * NOTE: This command not available on all servers
	 *
	 * @return  boolean  True if successful
	 */
	public function reinit()
	{
		if (@ftp_site($this->_conn, 'REIN') === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_REINIT_BAD_RESPONSE'), 35);
			return false;
		}
		return true;
	}

	/**
	 * Method to rename a file/folder on the FTP server
	 *
	 * @param   string  $from  Path to change file/folder from
	 * @param   string  $to    Path to change file/folder to
	 *
	 * @return  boolean  True if successful
	 */
	public function rename($from, $to)
	{
		// If native FTP support is enabled let's use it...
		if (@ftp_rename($this->_conn, $from, $to) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_RENAME_BAD_RESPONSE'), 35);
			return false;
		}
		return true;
	}

	/**
	 * Method to change mode for a path on the FTP server
	 *
	 * @param   string  $path  Path to change mode on
	 * @param   mixed   $mode  Octal value to change mode to, e.g. '0123', 0123 or 345 (string or integer)
	 *
	 * @return  boolean  True if successful
	 */
	public function chmod($path, $mode)
	{
		// If no filename is given, we assume the current directory is the target
		if ($path == '')
		{
			$path = '.';
		}

		// Convert the mode to a string
		if (is_int($mode))
		{
			$mode = decoct($mode);
		}

		// If native FTP support is enabled let's use it...
		if (@ftp_site($this->_conn, 'CHMOD ' . $mode . ' ' . $path) === false)
		{
			if ($this->_OS != 'WIN')
			{
				throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_CHMOD_BAD_RESPONSE'), 35);
			}
			return false;
		}
		return true;
	}

	/**
	 * Method to delete a path [file/folder] on the FTP server
	 *
	 * @param   string  $path  Path to delete
	 *
	 * @return  boolean  True if successful
	 */
	public function delete($path)
	{
		// If native FTP support is enabled let's use it...
		if (@ftp_delete($this->_conn, $path) === false)
		{
			if (@ftp_rmdir($this->_conn, $path) === false)
			{
				throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_DELETE_BAD_RESPONSE'), 35);
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to create a directory on the FTP server
	 *
	 * @param   string  $path  Directory to create
	 *
	 * @return  boolean  True if successful
	 */
	public function mkdir($path)
	{
		if (@ftp_mkdir($this->_conn, $path) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_MKDIR_BAD_RESPONSE'), 35);
			return false;
		}
		return true;
	}

	/**
	 * Method to restart data transfer at a given byte
	 *
	 * @param   integer  $point  Byte to restart transfer at
	 *
	 * @return  boolean  True if successful
	 */
	public function restart($point)
	{
		if (@ftp_site($this->_conn, 'REST ' . $point) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_RESTART_BAD_RESPONSE'), 35);
			return false;
		}
		return true;
	}

	/**
	 * Method to create an empty file on the FTP server
	 *
	 * @param   string  $path  Path local file to store on the FTP server
	 *
	 * @return  boolean  True if successful
	 */
	public function create($path)
	{
		// turn passive mode on
		if (@ftp_pasv($this->_conn, true) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_CREATE_BAD_RESPONSE_PASSIVE'), 36);
			return false;
		}

		$buffer = fopen('buffer://tmp', 'r');
		if (@ftp_fput($this->_conn, $path, $buffer, FTP_ASCII) === false)
		{
			fclose($buffer);
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_CREATE_BAD_RESPONSE_BUFFER'), 35);
			return false;
		}
		fclose($buffer);
		return true;
	}

	/**
	 * Method to read a file from the FTP server's contents into a buffer
	 *
	 * @param   string  $remote   Path to remote file to read on the FTP server
	 * @param   string  &$buffer  Buffer variable to read file contents into
	 *
	 * @return  boolean  True if successful
	 */
	public function read($remote, &$buffer)
	{
		// Determine file type
		$mode = $this->_findMode($remote);

		// Turn passive mode on
		if (@ftp_pasv($this->_conn, true) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_READ_BAD_RESPONSE_PASSIVE'), 36);
			return false;
		}

		$tmp = fopen('buffer://tmp', 'r+');
		if (@ftp_fget($this->_conn, $tmp, $remote, $mode) === false)
		{
			fclose($tmp);
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_READ_BAD_RESPONSE_BUFFER'), 35);
			return false;
		}
		// Read tmp buffer contents
		rewind($tmp);
		$buffer = '';
		while (!feof($tmp))
		{
			$buffer .= fread($tmp, 8192);
		}
		fclose($tmp);
		return true;
	}

	/**
	 * Method to get a file from the FTP server and save it to a local file
	 *
	 * @param   string  $local   Local path to save remote file to
	 * @param   string  $remote  Path to remote file to get on the FTP server
	 *
	 * @return  boolean  True if successful
	 */
	public function get($local, $remote)
	{
		// Determine file type
		$mode = $this->_findMode($remote);

		// Turn passive mode on
		if (@ftp_pasv($this->_conn, true) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_GET_PASSIVE'), 36);
			return false;
		}

		if (@ftp_get($this->_conn, $local, $remote, $mode) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_GET_BAD_RESPONSE'), 35);
			return false;
		}
		return true;
	}

	/**
	 * Method to store a file to the FTP server
	 *
	 * @param   string  $local   Path to local file to store on the FTP server
	 * @param   string  $remote  FTP path to file to create
	 *
	 * @return  boolean  True if successful
	 */
	public function store($local, $remote = null)
	{
		// If remote file is not given, use the filename of the local file in the current
		// working directory.
		if ($remote == null)
		{
			$remote = basename($local);
		}

		// Determine file type
		$mode = $this->_findMode($remote);

		// Turn passive mode on
		if (@ftp_pasv($this->_conn, true) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_STORE_PASSIVE'), 36);
			return false;
		}

		if (@ftp_put($this->_conn, $remote, $local, $mode) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_STORE_BAD_RESPONSE'), 35);
			return false;
		}
		return true;
	}

	/**
	 * Method to write a string to the FTP server
	 *
	 * @param   string  $remote  FTP path to file to write to
	 * @param   string  $buffer  Contents to write to the FTP server
	 *
	 * @return  boolean  True if successful
	 */
	public function write($remote, $buffer)
	{
		// Determine file type
		$mode = $this->_findMode($remote);

		// Turn passive mode on
		if (@ftp_pasv($this->_conn, true) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_WRITE_PASSIVE'), 36);
			return false;
		}

		$tmp = fopen('buffer://tmp', 'r+');
		fwrite($tmp, $buffer);
		rewind($tmp);
		if (@ftp_fput($this->_conn, $remote, $tmp, $mode) === false)
		{
			fclose($tmp);
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_WRITE_BAD_RESPONSE'), 35);
			return false;
		}
		fclose($tmp);
		return true;
	}

	/**
	 * Method to list the filenames of the contents of a directory on the FTP server
	 *
	 * Note: Some servers also return folder names. However, to be sure to list folders on all
	 * servers, you should use listDetails() instead if you also need to deal with folders
	 *
	 * @param   string  $path  Path local file to store on the FTP server
	 *
	 * @return  array  Directory listing
	 *
	 * @throws  Exception
	 */
	public function listNames($path = null)
	{
		// Initialise variables.
		$data = null;

		// Turn passive mode on
		if (@ftp_pasv($this->_conn, true) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_LISTNAMES_PASSIVE'), 36);
		}

		if (($list = @ftp_nlist($this->_conn, $path)) === false)
		{
			// Workaround for empty directories on some servers
			if ($this->listDetails($path, 'files') === [])
			{
				return [];
			}

			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_LISTNAMES_BAD_RESPONSE'), 35);
		}
		$list = preg_replace('#^' . preg_quote($path, '#') . '[/\\\\]?#', '', $list);
		if ($keys = array_merge(array_keys($list, '.'), array_keys($list, '..')))
		{
			foreach ($keys as $key)
			{
				unset($list[$key]);
			}
		}

		return $list;
	}

	/**
	 * Method to list the contents of a directory on the FTP server
	 *
	 * @param   string  $path  Path to the local file to be stored on the FTP server
	 * @param   string  $type  Return type [raw|all|folders|files]
	 *
	 * @return  mixed  If $type is raw: string Directory listing, otherwise array of string with file-names
	 */
	public function listDetails($path = null, $type = 'all')
	{
		// Initialise variables.
		$dir_list = array();
		$data = null;
		$regs = null;
		// TODO: Deal with recurse -- nightmare
		// For now we will just set it to false
		$recurse = false;

		// Turn passive mode on
		if (@ftp_pasv($this->_conn, true) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_LISTDETAILS_PASSIVE'), 36);
			return false;
		}

		if (($contents = @ftp_rawlist($this->_conn, $path)) === false)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_LISTDETAILS_BAD_RESPONSE'), 35);
			return false;
		}

		// If only raw output is requested we are done
		if ($type == 'raw')
		{
			return $data;
		}

		// If we received the listing of an empty directory, we are done as well
		if (empty($contents[0]))
		{
			return $dir_list;
		}

		// If the server returned the number of results in the first response, let's dump it
		if (strtolower(substr($contents[0], 0, 6)) == 'total ')
		{
			array_shift($contents);
			if (!isset($contents[0]) || empty($contents[0]))
			{
				return $dir_list;
			}
		}

		// Regular expressions for the directory listing parsing.
		$regexps = array(
			'UNIX' => '#([-dl][rwxstST-]+).* ([0-9]*) ([a-zA-Z0-9]+).* ([a-zA-Z0-9]+).* ([0-9]*)'
				. ' ([a-zA-Z]+[0-9: ]*[0-9])[ ]+(([0-9]{1,2}:[0-9]{2})|[0-9]{4}) (.+)#',
			'MAC' => '#([-dl][rwxstST-]+).* ?([0-9 ]*)?([a-zA-Z0-9]+).* ([a-zA-Z0-9]+).* ([0-9]*)'
				. ' ([a-zA-Z]+[0-9: ]*[0-9])[ ]+(([0-9]{2}:[0-9]{2})|[0-9]{4}) (.+)#',
			'WIN' => '#([0-9]{2})-([0-9]{2})-([0-9]{2}) +([0-9]{2}):([0-9]{2})(AM|PM) +([0-9]+|<DIR>) +(.+)#'
		);

		// Find out the format of the directory listing by matching one of the regexps
		$osType = null;
		foreach ($regexps as $k => $v)
		{
			if (@preg_match($v, $contents[0]))
			{
				$osType = $k;
				$regexp = $v;
				break;
			}
		}
		if (!$osType)
		{
			throw new Exception(AText::_('ANGI_CLIENT_ERROR_AFTP_LISTDETAILS_UNRECOGNISED') ,39);
			return false;
		}

		/*
		 * Here is where it is going to get dirty....
		 */
		if ($osType == 'UNIX')
		{
			foreach ($contents as $file)
			{
				$tmp_array = null;
				if (@preg_match($regexp, $file, $regs))
				{
					$fType = (int) strpos("-dl", $regs[1]{0});
					//$tmp_array['line'] = $regs[0];
					$tmp_array['type'] = $fType;
					$tmp_array['rights'] = $regs[1];
					//$tmp_array['number'] = $regs[2];
					$tmp_array['user'] = $regs[3];
					$tmp_array['group'] = $regs[4];
					$tmp_array['size'] = $regs[5];
					$tmp_array['date'] = @date("m-d", strtotime($regs[6]));
					$tmp_array['time'] = $regs[7];
					$tmp_array['name'] = $regs[9];
				}
				// If we just want files, do not add a folder
				if ($type == 'files' && $tmp_array['type'] == 1)
				{
					continue;
				}
				// If we just want folders, do not add a file
				if ($type == 'folders' && $tmp_array['type'] == 0)
				{
					continue;
				}
				if (is_array($tmp_array) && $tmp_array['name'] != '.' && $tmp_array['name'] != '..')
				{
					$dir_list[] = $tmp_array;
				}
			}
		}
		elseif ($osType == 'MAC')
		{
			foreach ($contents as $file)
			{
				$tmp_array = null;
				if (@preg_match($regexp, $file, $regs))
				{
					$fType = (int) strpos("-dl", $regs[1]{0});
					//$tmp_array['line'] = $regs[0];
					$tmp_array['type'] = $fType;
					$tmp_array['rights'] = $regs[1];
					//$tmp_array['number'] = $regs[2];
					$tmp_array['user'] = $regs[3];
					$tmp_array['group'] = $regs[4];
					$tmp_array['size'] = $regs[5];
					$tmp_array['date'] = date("m-d", strtotime($regs[6]));
					$tmp_array['time'] = $regs[7];
					$tmp_array['name'] = $regs[9];
				}
				// If we just want files, do not add a folder
				if ($type == 'files' && $tmp_array['type'] == 1)
				{
					continue;
				}
				// If we just want folders, do not add a file
				if ($type == 'folders' && $tmp_array['type'] == 0)
				{
					continue;
				}
				if (is_array($tmp_array) && $tmp_array['name'] != '.' && $tmp_array['name'] != '..')
				{
					$dir_list[] = $tmp_array;
				}
			}
		}
		else
		{
			foreach ($contents as $file)
			{
				$tmp_array = null;
				if (@preg_match($regexp, $file, $regs))
				{
					$fType = (int) ($regs[7] == '<DIR>');
					$timestamp = strtotime("$regs[3]-$regs[1]-$regs[2] $regs[4]:$regs[5]$regs[6]");
					//$tmp_array['line'] = $regs[0];
					$tmp_array['type'] = $fType;
					$tmp_array['rights'] = '';
					//$tmp_array['number'] = 0;
					$tmp_array['user'] = '';
					$tmp_array['group'] = '';
					$tmp_array['size'] = (int) $regs[7];
					$tmp_array['date'] = date('m-d', $timestamp);
					$tmp_array['time'] = date('H:i', $timestamp);
					$tmp_array['name'] = $regs[8];
				}
				// If we just want files, do not add a folder
				if ($type == 'files' && $tmp_array['type'] == 1)
				{
					continue;
				}
				// If we just want folders, do not add a file
				if ($type == 'folders' && $tmp_array['type'] == 0)
				{
					continue;
				}
				if (is_array($tmp_array) && $tmp_array['name'] != '.' && $tmp_array['name'] != '..')
				{
					$dir_list[] = $tmp_array;
				}
			}
		}

		return $dir_list;
	}

	/**
	 * Method to find out the correct transfer mode for a specific file
	 *
	 * @param   string  $fileName  Name of the file
	 *
	 * @return  integer Transfer-mode for this filetype [FTP_ASCII|FTP_BINARY]
	 */
	protected function _findMode($fileName)
	{
		if ($this->_type == FTP_AUTOASCII)
		{
			$dot = strrpos($fileName, '.') + 1;
			$ext = substr($fileName, $dot);

			if (in_array($ext, $this->_autoAscii))
			{
				$mode = FTP_ASCII;
			}
			else
			{
				$mode = FTP_BINARY;
			}
		}
		elseif ($this->_type == FTP_ASCII)
		{
			$mode = FTP_ASCII;
		}
		else
		{
			$mode = FTP_BINARY;
		}
		return $mode;
	}
}
