<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

class J15Config {
	/**
	* -------------------------------------------------------------------------
	* Site configuration section
	* -------------------------------------------------------------------------
	*/
	/* Site Settings */
	public $offline = '0';
	public $offline_message = 'This site is down for maintenance.<br /> Please check back again soon.';
	public $sitename = 'Joomla!';			// Name of Joomla site
	public $editor = 'tinymce';
	public $list_limit = '20';
	public $legacy = '0';

	/**
	* -------------------------------------------------------------------------
	* Database configuration section
	* -------------------------------------------------------------------------
	*/
	/* Database Settings */
	public $dbtype = 'mysql';					// Normally mysql
	public $host = 'localhost';				// This is normally set to localhost
	public $user = '';							// MySQL username
	public $password = '';						// MySQL password
	public $db = '';							// MySQL database name
	public $dbprefix = 'jos_';					// Do not change unless you need to!

	/* Server Settings */
	public $secret = 'FBVtggIk5lAzEU9H'; 		//Change this to something more secure
	public $gzip = '0';
	public $error_reporting = '-1';
	public $helpurl = 'http://help.joomla.org';
	public $xmlrpc_server = '1';
	public $ftp_host = '';
	public $ftp_port = '';
	public $ftp_user = '';
	public $ftp_pass = '';
	public $ftp_root = '';
	public $ftp_enable = '';
	public $tmp_path	= '/tmp';
	public $log_path	= '/var/logs';
	public $offset = '0';
	public $live_site = ''; 					// Optional, Full url to Joomla install.
	public $force_ssl = 0;		//Force areas of the site to be SSL ONLY.  0 = None, 1 = Administrator, 2 = Both Site and Administrator

	/* Session settings */
	public $lifetime = '15';					// Session time
	public $session_handler = 'database';

	/* Mail Settings */
	public $mailer = 'mail';
	public $mailfrom = '';
	public $fromname = '';
	public $sendmail = '/usr/sbin/sendmail';
	public $smtpauth = '0';
	public $smtpuser = '';
	public $smtppass = '';
	public $smtphost = 'localhost';

	/* Cache Settings */
	public $caching = '0';
	public $cachetime = '15';
	public $cache_handler = 'file';

	/* Debug Settings */
	public $debug      = '0';
	public $debug_db 	= '0';
	public $debug_lang = '0';

	/* Meta Settings */
	public $MetaDesc = 'Joomla! - the dynamic portal engine and content management system';
	public $MetaKeys = 'joomla, Joomla';
	public $MetaTitle = '1';
	public $MetaAuthor = '1';

	/* SEO Settings */
	public $sef = '0';
	public $sef_rewrite = '0';
	public $sef_suffix = '';

	/* Feed Settings */
	public $feed_limit   = 10;
	public $feed_email   = 'author';
}
?>
