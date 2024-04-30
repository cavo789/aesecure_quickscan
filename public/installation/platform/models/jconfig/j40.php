<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */
class J40Config
{
	/* Site Settings */
	public $offline = false;
	public $offline_message = 'This site is down for maintenance.<br> Please check back again soon.';
	public $display_offline_message = 1;
	public $offline_image = '';
	public $sitename = 'Joomla!';            // Name of Joomla site
	public $editor = 'tinymce';
	public $captcha = 0;
	public $list_limit = 20;
	public $access = 1;
	public $frontediting = 1;

	/* Database Settings */
	public $dbtype = 'mysqli';               // Normally mysqli
	public $host = 'localhost';              // This is normally set to localhost
	public $user = '';                       // Database username
	public $password = '';                   // Database password
	public $db = '';                         // Database name
	public $dbprefix = 'jos_';               // Any random string ending with _
	public $dbencryption = 0;
	public $dbsslverifyservercert = false;
	public $dbsslkey = '';
	public $dbsslcert = '';
	public $dbsslca = '';
	public $dbsslcipher = '';

	/* Server Settings */
	public $secret = 'FBVtggIk5lAzEU9H';     // Change this to something more secure
	public $gzip = false;
	public $error_reporting = 'default';
	public $helpurl = 'https://help.joomla.org/proxy?keyref=Help{major}{minor}:{keyref}&lang={langcode}';
	public $ftp_host = '';
	public $ftp_port = '';
	public $ftp_user = '';
	public $ftp_pass = '';
	public $ftp_root = '';
	public $ftp_enable = false;
	public $tmp_path = '/tmp';                // This path needs to be writable by Joomla!
	public $log_path = '/administrator/logs'; // This path needs to be writable by Joomla!
	public $live_site = '';                   // Optional, full URL to Joomla install.
	public $force_ssl = 0;                    // Force areas of the site to be SSL ONLY.  0 = None, 1 = Administrator, 2 = Both Site and Administrator
	public $log_everything = 0;
	public $log_deprecated = 0;
	public $log_priorities = ['0' => 'all'];
	public $log_categories = '';
	public $log_category_mode = 0;
	public $cors = false;
	public $cors_allow_origin = '*';
	public $cors_allow_headers = 'Content-Type,X-Joomla-Token';
	public $cors_allow_methods = '';
	public $behind_loadbalancer = false;

	/* Locale Settings */
	public $offset = 'UTC';

	/* Session settings */
	public $lifetime = 15;                    // Session time
	public $session_handler = 'database';
	public $shared_session = false;
	public $session_metadata = false;
	public $session_metadata_for_guest = true;
	public $session_filesystem_path = '';
	public $session_memcached_server_host = 'localhost';
	public $session_memcached_server_port = 11211;
	public $session_redis_server_host = 'localhost';
	public $session_redis_server_port = 6379;
	public $session_redis_server_db = 0;
	public $session_redis_server_auth = '';
	public $session_redis_persist = false;

	/* Mail Settings */
	public $mailonline = true;
	public $mailer      = 'mail';
	public $mailfrom    = '';
	public $fromname    = '';
	public $massmailoff = false;
	public $replyto     = '';
	public $replytoname = '';
	public $sendmail    = '/usr/sbin/sendmail';
	public $smtpauth    = false;
	public $smtpuser    = '';
	public $smtppass    = '';
	public $smtphost    = 'localhost';
	public $smtpsecure = 'none';
	public $smtpport = 25;

	/* Cache Settings */
	public $caching = 0;
	public $cachetime = 15;
	public $cache_handler = 'file';
	public $cache_platformprefix = false;
	public $memcached_persist = true;
	public $memcached_compress = false;
	public $memcached_server_host = 'localhost';
	public $memcached_server_port = 11211;
	public $redis_persist = true;
	public $redis_server_host = 'localhost';
	public $redis_server_port = 6379;
	public $redis_server_auth = '';
	public $redis_server_db = 0;

	/* Proxy Settings */
	public $proxy_enable = false;
	public $proxy_host = '';
	public $proxy_port = '';
	public $proxy_user = '';
	public $proxy_pass = '';

	/* Debug Settings */
	public $debug = false;
	public $debug_lang = false;
	public $debug_lang_const = 1;

	/* Meta Settings */
	public $MetaDesc = 'Joomla! - the dynamic portal engine and content management system';
	public $MetaKeys = 'joomla, Joomla';
	public $MetaTitle = true;
	public $MetaAuthor = true;
	public $MetaVersion = false;
	public $MetaRights = '';
	public $robots = '';
	public $sitename_pagetitles = 0;

	/* SEO Settings */
	public $sef = true;
	public $sef_rewrite = false;
	public $sef_suffix = false;
	public $unicodeslugs = false;

	/* Feed Settings */
	public $feed_limit = 10;
	public $feed_email = 'none';

	/* Cookie Settings */
	public $cookie_domain = '';
	public $cookie_path = '';

	/* Miscellaneous Settings */
	public $asset_id = 1;
}
