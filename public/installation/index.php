<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Uncomment the following line to enable Debug Mode
//define('AKEEBA_DEBUG', 1);

// Uncomment the following line to prevent setting very high memory and runtime limits
// define('AKEEBA_DISABLE_HIGH_LIMITS', 1);

// Debug mode is automatically enabled on localhost and our development sites
if (!defined('AKEEBA_DEBUG') && isset($_SERVER['HTTP_HOST']) && (($_SERVER['HTTP_HOST'] === 'localhost') || (substr($_SERVER['HTTP_HOST'], -10) == '.local.web')))
{
	define('AKEEBA_DEBUG', 1);
}

if (defined('AKEEBA_DEBUG'))
{
	error_reporting(E_ALL | E_NOTICE | E_DEPRECATED);

	if (function_exists('ini_set'))
	{
		ini_set('display_errors', 1);
	}
}

// Apply ridiculously high memory and time limits. This prevents some easily preventable issues on MOST hosts.
if (function_exists('ini_set') && !defined('AKEEBA_DISABLE_HIGH_LIMITS'))
{
	@ini_set('memory_limit', '1G');
	@ini_set('max_execution_time', 86400);
}

// Define ourselves as a parent file
define('_AKEEBA', 1);

// Used by our version.php file and by ANGIE for Joomla!.
define('_JEXEC', 1);

// Minimum PHP version check.
$minPHP         = '5.4.0';
$recommendedPHP = '7.4';

if (version_compare(PHP_VERSION, $minPHP, 'lt'))
{
	$versionFile  = dirname(__FILE__) . '/version.php';
	$platformFile = dirname(__FILE__) . '/platform/views/php_version.php';
	$masterFile   = dirname(__FILE__) . '/template/flat/php_version.php';
	$reqFile      = dirname(__FILE__) . '/framework/utils/servertechnology.php';

	@include_once $reqFile;

	if (file_exists($versionFile))
	{
		include $versionFile;
	}

	if (file_exists($platformFile) && file_exists($reqFile))
	{
		include $platformFile;
	}
	elseif (file_exists($masterFile) && file_exists($reqFile))
	{
		include $masterFile;
	}
	else
	{
		echo sprintf("ANGIE requires PHP version 5.4.0 or later. Your server reports that you are currently using %s. Please fix this issue and retry running this script.", PHP_VERSION);
	}

	exit(0);
}

// Required for some older lang strings.
define('_QQ_', '&quot;');

// Load the required INI parser
require_once __DIR__ . '/angie/helpers/ini.php';

// Load the framework autoloader
require_once __DIR__ . '/framework/autoloader.php';
// Load PSR-4 autoloader
require_once __DIR__ . '/framework/Autoloader/Autoloader.php';

require_once __DIR__ . '/defines.php';

// Load Angie autoloader
require_once __DIR__. '/angie/autoloader.php';

// Required by the Joomla! CMS version file (mind. blown!)
if (!defined('JPATH_PLATFORM'))
{
	define('JPATH_PLATFORM', APATH_LIBRARIES);
}

/**
 * Main application loop
 */
function mainLoop()
{
	global $application;

	if (@file_exists(__DIR__ . '/platform/defines.php'))
	{
		require_once __DIR__ . '/platform/defines.php';
	}

	if (!defined('ANGIE_INSTALLER_NAME'))
	{
		define('ANGIE_INSTALLER_NAME', 'Generic');
	}

	$container = new AContainer([
		'application_name' => 'angie',
	]);

	// Create the application
	$application = $container->application;

	// Initialise the application
	$application->initialise();

	// Dispatch the application
	$application->dispatch();

	// Render
	$application->render();

	// Clean-up and shut down
	$application->close();
}

/**
 * Error trap for the ANGIE application
 *
 * @param Throwable|Exception $exc
 */
function errorTrap($exc)
{
	global $application;

	if (defined('AKEEBA_DEBUG') && AKEEBA_DEBUG)
	{
		// Try to write to a log file
		$logFile = APATH_TEMPINSTALL . '/error.log';
		$content = <<< END
ANGIE Application Error -- Debug Log File
================================================================================


When requesting support please put this log file and all the other files in this
tmp folder into a ZIP archive. Attach the ZIP archive to your support ticket / 
bug report to help us help you. Thank you.


Error message
--------------------------------------------------------------------------------
{$exc->getMessage()}

File and line
--------------------------------------------------------------------------------
{$exc->getFile()} :: L{$exc->getLine()}

Traceback
--------------------------------------------------------------------------------
{$exc->getTraceAsString()}


END;
		@file_put_contents($logFile, $content);
	}

	$filename = null;

	if (isset($application) && ($application instanceof AApplication))
	{
		$template = $application->getTemplate();

		if (file_exists(APATH_THEMES . '/' . $template . '/error.php'))
		{
			$filename = APATH_THEMES . '/' . $template . '/error.php';
		}
	}

	if (!is_null($filename))
	{
		@ob_start();
	}

	// An uncaught application error occurred
	$exceptionClass = get_class($exc);
	echo <<< HTML
<h1>Application Error</h1>

<p>Please submit the following error message and trace in its entirety when requesting support</p>

<div class="akeeba-block--failure">
	<h5>$exceptionClass &mdash; {$exc->getMessage()}</h5>
	<p>
		{$exc->getFile()}::L{$exc->getLine()}
	</p>
</div>

<pre>{$exc->getTraceAsString()}</pre>

HTML;

	if (!is_null($filename))
	{
		$error_message = @ob_get_clean();
		include $filename;
	}
}

/**
 * We have two slightly different versions of the main loop and error trap, depending on your PHP version.
 *
 * The first branch is for PHP 5. We can only trap exceptions. PHP errors will simply cause a crash.
 *
 * The second branch if for PHP 7 and later. We can trap both exceptions (Exception) and core PHP errors (Throwable).
 * In this case we can provide better context on the error if something at the PHP core level didn't work, e.g. a typo
 * in a file or a missing but required PHP extension.
 */
if (version_compare(PHP_VERSION, '7.0.0', 'lt'))
{
	try
	{
		mainLoop();
	}
	catch (Exception $exc)
	{
		errorTrap($exc);
	}
}
else
{
	try
	{
		mainLoop();
	}
	catch (Throwable $exc)
	{
		errorTrap($exc);
	}
}

