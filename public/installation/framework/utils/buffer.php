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

/**
 * Generic Buffer stream handler
 *
 * This class provides a generic buffer stream.  It can be used to store/retrieve/manipulate
 * string buffers with the standard PHP filesystem I/O methods.
 */
class AUtilsBuffer
{
	/**
	 * Buffer hash
	 *
	 * @var    array
	 */
	public static $buffers = [];

	/**
	 * Is it possible to register stream wrappers on this server?
	 *
	 * @var bool|null
	 */
	public static $canRegisterWrapper = null;

	/**
	 * Buffer name
	 *
	 * @var    string
	 */
	public $name = null;

	/**
	 * Stream position
	 *
	 * @var    integer
	 */
	public $position = 0;

	/**
	 * Should I register the awf:// stream wrapper
	 *
	 * @return  bool  True if the stream wrapper can be registered
	 */
	public static function canRegisterWrapper()
	{
		if (is_null(static::$canRegisterWrapper))
		{
			static::$canRegisterWrapper = false;

			// Maybe the host has disabled registering stream wrappers altogether?
			if (!function_exists('stream_wrapper_register'))
			{
				return false;
			}

			// Check for Suhosin
			if (function_exists('extension_loaded'))
			{
				$hasSuhosin = extension_loaded('suhosin');
			}
			else
			{
				$hasSuhosin = -1; // Can't detect
			}

			if ($hasSuhosin !== true)
			{
				$hasSuhosin = defined('SUHOSIN_PATCH') ? true : -1;
			}

			if ($hasSuhosin === -1)
			{
				if (function_exists('ini_get'))
				{
					$hasSuhosin = false;

					$maxIdLength = ini_get('suhosin.session.max_id_length');

					if ($maxIdLength !== false)
					{
						$hasSuhosin = ini_get('suhosin.session.max_id_length') !== '';
					}
				}
			}

			// If we can't detect whether Suhosin is installed we won't proceed to prevent a White Screen of Death
			if ($hasSuhosin === -1)
			{
				return false;
			}

			// If Suhosin is installed but ini_get is not available we won't proceed to prevent a WSoD
			if ($hasSuhosin && !function_exists('ini_get'))
			{
				return false;
			}

			// If Suhosin is installed check if awf:// is whitelisted
			if ($hasSuhosin)
			{
				$whiteList = ini_get('suhosin.executor.include.whitelist');

				// Nothing in the whitelist? I can't go on, sorry.
				if (empty($whiteList))
				{
					return false;
				}

				$whiteList = explode(',', $whiteList);
				$whiteList = array_map(function ($x) {
					return trim($x);
				}, $whiteList);

				if (!in_array('awf://', $whiteList))
				{
					return false;
				}
			}

			static::$canRegisterWrapper = true;
		}

		return static::$canRegisterWrapper;
	}

	/**
	 * Function to test for end of file pointer
	 *
	 * @return  boolean  True if the pointer is at the end of the stream
	 *
	 * @see     streamWrapper::stream_eof
	 * @since   11.1
	 */
	public function stream_eof()
	{
		return $this->position >= strlen(static::$buffers[$this->name]);
	}

	/**
	 * Function to open file or url
	 *
	 * @param   string   $path          The URL that was passed
	 * @param   string   $mode          Mode used to open the file @see fopen
	 * @param   integer  $options       Flags used by the API, may be STREAM_USE_PATH and
	 *                                  STREAM_REPORT_ERRORS
	 * @param   string  &$opened_path   Full path of the resource. Used with STREAM_USE_PATH option
	 *
	 * @return  boolean
	 *
	 * @see     streamWrapper::stream_open
	 */
	public function stream_open($path, $mode, $options, &$opened_path)
	{
		$url            = parse_url($path);
		$this->name     = $url['host'] . (isset($url['path']) ? $url['path'] : '');
		$this->position = 0;

		if (!isset(static::$buffers[$this->name]))
		{
			static::$buffers[$this->name] = null;
		}

		return true;
	}

	/**
	 * Read stream
	 *
	 * @param   integer  $count  How many bytes of data from the current position should be returned.
	 *
	 * @return  mixed    The data from the stream up to the specified number of bytes (all data if
	 *                   the total number of bytes in the stream is less than $count. Null if
	 *                   the stream is empty.
	 *
	 * @see     streamWrapper::stream_read
	 * @since   11.1
	 */
	public function stream_read($count)
	{
		$ret            = substr(static::$buffers[$this->name], $this->position, $count);
		$this->position += strlen($ret);

		return $ret;
	}

	/**
	 * The read write position updates in response to $offset and $whence
	 *
	 * @param   integer  $offset  The offset in bytes
	 * @param   integer  $whence  Position the offset is added to
	 *                            Options are SEEK_SET, SEEK_CUR, and SEEK_END
	 *
	 * @return  boolean  True if updated
	 *
	 * @see     streamWrapper::stream_seek
	 * @since   11.1
	 */
	public function stream_seek($offset, $whence)
	{
		switch ($whence)
		{
			case SEEK_SET:
				if ($offset < strlen(static::$buffers[$this->name]) && $offset >= 0)
				{
					$this->position = $offset;

					return true;
				}
				else
				{
					return false;
				}
				break;

			case SEEK_CUR:
				if ($offset >= 0)
				{
					$this->position += $offset;

					return true;
				}
				else
				{
					return false;
				}
				break;

			case SEEK_END:
				if (strlen(static::$buffers[$this->name]) + $offset >= 0)
				{
					$this->position = strlen(static::$buffers[$this->name]) + $offset;

					return true;
				}
				else
				{
					return false;
				}
				break;

			default:
				return false;
		}
	}

	public function stream_set_option($option, $arg1, $arg2)
	{
		return true;
	}

	public function stream_stat()
	{
		return [
			'dev'     => 0,
			'ino'     => 0,
			'mode'    => 0644,
			'nlink'   => 0,
			'uid'     => 0,
			'gid'     => 0,
			'rdev'    => 0,
			'size'    => strlen(static::$buffers[$this->name]),
			'atime'   => 0,
			'mtime'   => 0,
			'ctime'   => 0,
			'blksize' => -1,
			'blocks'  => -1,
		];
	}

	/**
	 * Function to get the current position of the stream
	 *
	 * @return  integer
	 *
	 * @see     streamWrapper::stream_tell
	 * @since   11.1
	 */
	public function stream_tell()
	{
		return $this->position;
	}

	/**
	 * Write stream
	 *
	 * @param   string  $data  The data to write to the stream.
	 *
	 * @return  integer
	 *
	 * @see     streamWrapper::stream_write
	 * @since   11.1
	 */
	public function stream_write($data)
	{
		$left                         = substr(static::$buffers[$this->name], 0, $this->position);
		$right                        = substr(static::$buffers[$this->name], $this->position + strlen($data));
		static::$buffers[$this->name] = $left . $data . $right;
		$this->position               += strlen($data);

		return strlen($data);
	}

	public function unlink($path)
	{
		$url  = parse_url($path);
		$name = $url['host'];

		if (isset(static::$buffers[$name]))
		{
			unset (static::$buffers[$name]);
		}
	}
}

// Register the stream
if (AUtilsBuffer::canRegisterWrapper())
{
	stream_wrapper_register("buffer", "AUtilsBuffer");
}
