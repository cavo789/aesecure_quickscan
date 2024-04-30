<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

class J25Config {
	public $offline = '0';
	public $offline_message = 'This site is down for maintenance.<br /> Please check back again soon.';
	public $sitename = 'Your Joomla! Site';
	public $editor = 'tinymce';
	public $list_limit = '20';
	public $access = '1';
	public $debug = '0';
	public $debug_lang = '0';
	public $dbtype = 'mysqli';
	public $host = '';
	public $user = '';
	public $password = '';
	public $db = '';
	public $dbprefix = 'jos_';
	public $live_site = '';
	public $secret = '';
	public $gzip = '';
	public $error_reporting = 'none';
	public $helpurl = 'http://help.joomla.org/proxy/index.php?option=com_help&keyref=Help{major}{minor}:{keyref}';
	public $ftp_host = '';
	public $ftp_port = '';
	public $ftp_user = '';
	public $ftp_pass = '';
	public $ftp_root = '';
	public $ftp_enable = '0';
	public $offset = 'UTC';
	public $offset_user = 'UTC';
	public $mailer = 'mail';
	public $mailfrom = '';
	public $fromname = 'Your Joomla! Site';
	public $sendmail = '/usr/sbin/sendmail';
	public $smtpauth = '0';
	public $smtpuser = '';
	public $smtppass = '';
	public $smtphost = '';
	public $smtpsecure = 'none';
	public $smtpport = '25';
	public $caching = '0';
	public $cache_handler = 'file';
	public $cachetime = '15';
	public $MetaDesc = '';
	public $MetaKeys = '';
	public $MetaTitle = '1';
	public $MetaAuthor = '1';
	public $sef = '1';
	public $sef_rewrite = '0';
	public $sef_suffix = '0';
	public $unicodeslugs = '0';
	public $feed_limit = '10';
	public $log_path = '';
	public $tmp_path = '';
	public $lifetime = '15';
	public $session_handler = 'database';
	public $MetaRights = '';
	public $sitename_pagetitles = '1';
	public $force_ssl = '0';
	public $feed_email = 'author';
	public $cookie_domain = '';
	public $cookie_path = '';
	public $memcache_persist = '1';
	public $memcache_compress = '1';
	public $memcache_server_host = 'localhost';
	public $memcache_server_port = '11211';
	public $display_offline_message = '1';
	public $robots = '';
	public $offline_image = '';
	public $captcha = '0';
	public $MetaVersion = '0';
}