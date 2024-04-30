<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class ATimer
{
	/**
	 * Minimum execution time for step
	 *
	 * @var int
	 */
	private $min_exec_time = 0;

	/** 
	 * Maximum execution time allowance per step
	 * 
	 * @var  int
	 */
	private $max_exec_time = null;

	/** 
	 * Timestamp of execution start
	 * 
	 * @var  int 
	 */
	private $start_time = null;

	/**
	 * Public constructor, creates the timer object and calculates the execution
	 * time limits.
	 *
	 * @param   int $min_exec_time  Minimum execution time
	 * @param   int $max_exec_time  Maximum execution time
	 * @param   int $runtime_bias   Runtime bias
	 */
	public function __construct($min_exec_time = 0, $max_exec_time = 5, $runtime_bias = 75)
	{
		ALog::_(ANGIE_LOG_DEBUG, __METHOD__ . '(' . $max_exec_time . ', ' . $runtime_bias . ')');
		
		// Initialize start time
		$this->start_time = $this->microtime_float();
		
		$this->max_exec_time = $max_exec_time * $runtime_bias / 100;
		$this->min_exec_time = $min_exec_time;
	}

	/**
	 * Wake-up function to reset internal timer when we get unserialized
	 */
	public function __wakeup()
	{
		// Re-initialize start time on wake-up
		$this->start_time = $this->microtime_float();
	}

	/**
	 * Gets the number of seconds left, before we hit the "must break" threshold
	 * 
	 * @return  float
	 */
	public function getTimeLeft()
	{
		return $this->max_exec_time - $this->getRunningTime();
	}

	/**
	 * Gets the time elapsed since object creation/unserialization, effectively
	 * how long this step is running
	 * 
	 * @return  float
	 */
	public function getRunningTime()
	{
		return $this->microtime_float() - $this->start_time;
	}

	/**
	 * Returns the current timestamp in decimal seconds
	 * 
	 * @return  float
	 */
	private function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	/**
	 * Enforce the minimum execution time
	 *
	 * @param    bool $log             Should I log what I'm doing? Default is true.
	 * @param    bool $serverSideSleep Should I sleep on the server side? If false we return the amount of time to wait in msec
	 *
	 * @return  int Wait time to reach min_execution_time in msec
	 */
	public function enforce_min_exec_time($log = true, $serverSideSleep = true)
	{
		$php_max_exec = 10;

		// Try to get a sane value for PHP's maximum_execution_time INI parameter
		if (@function_exists('ini_get'))
		{
			$php_max_exec = @ini_get("maximum_execution_time");
		}

		if (($php_max_exec == "") || ($php_max_exec == 0))
		{
			$php_max_exec = 10;
		}
		
		// Decrease $php_max_exec time by 500 msec we need (approx.) to tear down
		// the application, as well as another 500msec added for rounding
		// error purposes. Also make sure this is never gonna be less than 0.
		$php_max_exec = max($php_max_exec * 1000 - 1000, 0);
		$minexectime  = $this->min_exec_time * 1000;

		if (!is_numeric($minexectime))
		{
			$minexectime = 0;
		}

		// Make sure we are not over PHP's time limit!
		if ($minexectime > $php_max_exec)
		{
			$minexectime = $php_max_exec;
		}

		// Get current running time
		$elapsed_time = $this->getRunningTime() * 1000;

		$clientSideSleep = 0;

		// Only run a sleep delay if we haven't reached the minexectime execution time
		if (($minexectime > $elapsed_time) && ($elapsed_time > 0))
		{
			$sleep_msec = (int)($minexectime - $elapsed_time);

			if (!$serverSideSleep)
			{
				ALog::_(ANGIE_LOG_DEBUG, "Asking client to sleep for $sleep_msec msec");
				$clientSideSleep = $sleep_msec;
			}
			elseif (function_exists('usleep'))
			{
				if ($log)
				{
					ALog::_(ANGIE_LOG_DEBUG, "Sleeping for $sleep_msec msec, using usleep()");
				}

				usleep(1000 * $sleep_msec);
			}
			elseif (function_exists('time_nanosleep'))
			{
				if ($log)
				{
					ALog::_(ANGIE_LOG_DEBUG, "Sleeping for $sleep_msec msec, using time_nanosleep()");
				}
				$sleep_sec  = floor($sleep_msec / 1000);
				$sleep_nsec = 1000000 * ($sleep_msec - ($sleep_sec * 1000));

				time_nanosleep($sleep_sec, $sleep_nsec);
			}
			elseif (function_exists('time_sleep_until'))
			{
				if ($log)
				{
					ALog::_(ANGIE_LOG_DEBUG, "Sleeping for $sleep_msec msec, using time_sleep_until()");
				}

				$until_timestamp = time() + $sleep_msec / 1000;

				time_sleep_until($until_timestamp);
			}
			elseif (function_exists('sleep'))
			{
				$sleep_sec = ceil($sleep_msec / 1000);

				if ($log)
				{
					ALog::_(ANGIE_LOG_DEBUG, "Sleeping for $sleep_sec seconds, using sleep()");
				}

				sleep($sleep_sec);
			}
		}
		elseif ($elapsed_time > 0)
		{
			// No sleep required, even if user configured us to be able to do so.
			if ($log)
			{
				ALog::_(ANGIE_LOG_DEBUG, "No need to sleep; execution time: $elapsed_time msec; min. exec. time: $minexectime msec");
			}
		}

		return $clientSideSleep;
	}

	/**
	 * Reset the timer
	 */
	public function resetTime()
	{
		$this->start_time = $this->microtime_float();
	}

}
