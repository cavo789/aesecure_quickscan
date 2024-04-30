<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

class J30Config {
	public $MetaAuthor = '1';
	public $MetaDesc = '';
	public $MetaKeys = '';
	public $MetaRights = '';
	public $MetaTitle = '1';
	public $MetaVersion = '0';
	public $access = '1';
	public $cache_handler = 'file';
	public $cachetime = '15';
	public $caching = '0';
	public $captcha = '0';
	public $cookie_domain = '';
	public $cookie_path = '';
	public $db = '';
	public $dbprefix = 'jos_';
	public $dbtype = 'mysqli';
	public $debug = '0';
	public $debug_lang = '0';
	public $display_offline_message = '1';
	public $editor = 'tinymce';
	public $error_reporting = 'none';
	public $feed_email = 'author';
	public $feed_limit = '10';
	public $force_ssl = '0';
	public $fromname = 'Your Joomla! Site';
	public $ftp_enable = '0';
	public $ftp_host = '';
	public $ftp_pass = '';
	public $ftp_port = '';
	public $ftp_root = '';
	public $ftp_user = '';
	public $gzip = '';
	public $helpurl = 'http://help.joomla.org/proxy/index.php?option=com_help&keyref=Help{major}{minor}:{keyref}';
	public $host = '';
	public $lifetime = '15';
	public $list_limit = '20';
	public $live_site = '';
	public $log_path = '';
	public $mailer = 'mail';
	public $mailfrom = '';
	public $memcache_compress = '1';
	public $memcache_persist = '1';
	public $memcache_server_host = 'localhost';
	public $memcache_server_port = '11211';
	public $offline = '0';
	public $offline_image = '';
	public $offline_message = 'This site is down for maintenance.<br /> Please check back again soon.';
	public $offset = 'UTC';
	public $offset_user = 'UTC';
	public $password = '';
	public $robots = '';
	public $secret = '';
	public $sef = '1';
	public $sef_rewrite = '0';
	public $sef_suffix = '0';
	public $sendmail = '/usr/sbin/sendmail';
	public $session_handler = 'database';
	public $sitename = 'Your Joomla! Site';
	public $sitename_pagetitles = '1';
	public $smtpauth = '0';
	public $smtphost = '';
	public $smtppass = '';
	public $smtpport = '25';
	public $smtpsecure = 'none';
	public $smtpuser = '';
	public $tmp_path = '';
	public $unicodeslugs = '0';
	public $user = '';
}