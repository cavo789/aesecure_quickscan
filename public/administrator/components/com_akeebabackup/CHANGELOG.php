Akeeba Backup 9.6.2
================================================================================
~ Block uninstallation of child extensions
# [LOW] CLI backups no longer record an end date and time due to a change in Joomla's behavior
# [LOW] Backup On Update: Would always use profile 1
# [LOW] Backup On Update: Inversion of logic of the switches in its options page

Akeeba Backup 9.6.1
================================================================================
# [MEDIUM] HTTP PUT might fail on some servers
# [LOW] opcache_invalidate may not invalidate a file
# [LOW] Would not work on 32-bit versions of PHP

Akeeba Backup 9.6.0
================================================================================
+ Support for files and archives over 2GiB (JPA file format 1.3)
+ New JSON API endpoint, using the Joomla API Application
~ Disabled deprecated API methods
~ Improve the Schedule Automatic Backups page
# [MEDIUM] JSON API: deleteFiles method throws an exception due to a typo

Akeeba Backup 9.5.1
================================================================================
+ Restoration: handle Joomla 4.2+ MFA options
# [MEDIUM] Plugins not enabled on clean installation
# [MEDIUM] JSON API cannot delete backup records and profiles

Akeeba Backup 9.5.0
================================================================================
+ Option to treat failed uploads as a backup error

Akeeba Backup 9.4.8
================================================================================
! A packaging issue broke the restoration script in backup archives

Akeeba Backup 9.4.7
================================================================================
# [MEDIUM] Fixed drive selection for Google Drive post processing engine

Akeeba Backup 9.4.6
================================================================================
# [HIGH] Some password managers prevent successful submission of the Site Setup page (you get an error about a missing email address)
# [LOW] Wrong grammatical case (nominative instead of genitive) in months in some languages e.g. Greek
# [LOW] Push messages may be untranslated strings when a backup is taken over the API or the frontend backup URL

Akeeba Backup 9.4.5
================================================================================
# [HIGH] Unexpected behaviour in the backend when Joomla cache is enabled
# [HIGH] BackBlaze B2 single file uploads were broken

Akeeba Backup 9.4.4
================================================================================
+ ALICE button in the log view
# [HIGH] Migration from Akeeba Backup 8 fails since 9.4.0 added an access setting in backup profiles

Akeeba Backup 9.4.3
================================================================================
# [HIGH] Migration from Akeeba Backup 8 always shows an erroneous message that no compatible version has been detected.
# [MEDIUM] Restoration. Administrator email appears as "undefined" in the Site Setup page
# [LOW] Restoration: Wrong message about the emial address when the administrator passwords don't match

Akeeba Backup 9.4.2
================================================================================
! No access control applied in Include and Exclude Information features
# [HIGH] Class not found errors when trying to access some pages in Akeeba Backup

Akeeba Backup 9.4.1
================================================================================
! Immediate error on PHP 7.4 due to a missing method in the released version

Akeeba Backup 9.4.0
================================================================================
~ Requires Joomla 4.2 or later
~ Requires PHP 7.4.0 or later
~ Much simpler message if you try to run Akeeba Backup on an unsupported (too low) version of PHP.
~ Changed all warnings to much more compact DETAILS elements
+ Access levels in backup profiles
+ Option about including the latest backup in remote quotas
- Removed the PHP version warning. Joomla already warns you about EOL versions of PHP.
# [HIGH] Site Transfer Wizard will fail on a target site using PHP 8.1 or later by default
# [LOW] ZIP Archiver, invalid CRC32 calculated for some small files in the installation folder

Akeeba Backup 9.3.4
================================================================================
# [LOW] ZIP Archiver, invalid CRC32 calculated for some small files in the installation folder

Akeeba Backup 9.3.3
================================================================================
~ Better warnings about CRC32 for ZIP files on 32-bit versions of PHP
# [HIGH] Quota settings and emails are not processed at the end of the backup process
# [HIGH] Joomla Scheduled Tasks for Akeeba Backup may fail with a PHP error

Akeeba Backup 9.3.2
================================================================================
~ PHP notices are now only logged when Debug Site is enabled
~ Notify the user when the server does not support Web Push instead of just failing to subscribe to push notifications
# [MEDIUM] WebPush code tries to run when not selected resulting in an annoying, but harmless, warning
# [MEDIUM] Possible PHP fatal error if the server does not meet the Web Push minimum requirements
# [LOW] PHP 8 deprecated notices from the WebPush library

Akeeba Backup 9.3.1
================================================================================
+ Push notifications through the browser's Push API
+ ANGIE for Joomla: reset session and cache options in Site Setup
+ Support for ShowOn to conditionally show options in the Configuration page
~ Save and Save & Close buttons are now separate, as per Joomla 4.2 UI guidelines
# [HIGH] Single part uploads to Azure stopped working
# [LOW] “Field 'extra_query' doesn't have a default value” error on some broken installations
# [LOW] PHP warning about undefined $id in the Manage Backups page on some versions of PHP

Akeeba Backup 9.3.0
================================================================================
+ Upload to Swift: Support for Keystone v3
# [HIGH] Joomla broke database-aware models under the CLI. Working around the latest Joomla borkage, as we have always done.
# [MEDIUM] Command line options overrides don't work because of a typo
# [LOW] PHP 8.1 deprecated notice when checking if FOF is still installed
# [LOW] "Test FTP connection" button was not correctly applying the passive mode
# [LOW] CLI akeeba:profile:list was broken

Akeeba Backup 9.2.7
================================================================================
+ More informative error messages for database connection issues during restoration
~ Workaround for utf8_encode and _decode being deprecated in PHP 8.2
# [LOW] Restoration: You were shown separate port and socket options which were not taken into account
# [MEDIUM] Restoration: Using a custom port or socket might result in the wrong hostname being written in the restored site's configuration file
# [MEDIUM] Possible infinite loop on PHP 8 during DB restoration if a SQL file is missing
# [LOW] Invalid SQL dump if we cannot get the create commands for a function, procedure or trigger

Akeeba Backup 9.2.6
================================================================================
+ Restoration: Warn about missing mysqli / PDO MySQL and REFUSE to proceed
# [HIGH] Cannot download file from Amazon S3
# [LOW] PHP Warning when backing up a database (purely cosmetic issue)
# [LOW] Missing language strings from the CLI commands

Akeeba Backup 9.2.5
================================================================================
+ Restoration: Warn about missing mysqli / PDO MySQL and REFUSE to proceed
# [HIGH] Cannot download file from Amazon S3
# [LOW] PHP Warning when backing up a database (purely cosmetic issue)
# [LOW] Missing language strings from the CLI commands

Akeeba Backup 9.2.4
================================================================================
# [HIGH] Cannot connect to databases on localhost using the default named pipe
# [MEDIUM] Custom Amazon S3 regions would not work with custom endpoints

Akeeba Backup 9.2.3
================================================================================
+ Support for custom Amazon S3 regions
+ Support for MySQL SSL/TLS connections for backed up sites
+ Add Show Inline Help support in component options for Joomla 4.1
# [LOW] Weird interface for the CLI backup Scheduled Task type

Akeeba Backup 9.2.2
================================================================================
+ Improved Smart Search table filtering
+ Much improved FTP functions for uploading backup archives and transferring sites
+ Upload to Azure BLOB Storage now supports chunked uploads, files up to 190.7TB (up from 64Mb)
+ OneDrive for Business: you can now use Drives other than your personal
~ Ignore whitespace in the new site's URL in the Site Transfer Wizard
~ Stricter conditions for determining when to show the “Manage remotely stored files” button in Manage Backups
# [LOW] Upload to Remote Storage would transfer the first part file twice
# [MEDIUM] Fixed download of remote archives back to the server
# [MEDIUM] OneDrive: Uploads may fail if they are between 4Mb and 100Mb

Akeeba Backup 9.2.1
================================================================================
+ Restoration: ANGIE now applies very high memory and execution time limits to prevent some timeout / memory outage issues on most hosts.
+ Restoration: ANGIE now warns you if you leave the database connection information empty
+ Option to set a really large PHP memory limit during backup
~ Show an error if the temp file cannot be opened when importing from S3
# [HIGH] Sometimes you would not see the error when the Upload to Remote Storage failed
# [MEDIUM] ALICE would not list any logs, even for failed backups
# [LOW] The JPS archiver would show warnings about unreadable files when archiving directories without any files in them.
# [LOW] The directory browser in the Configuration page doesn't open the defined folder when it contains variables
# [LOW] Extra whitespace in the Upload to Remote Storage pages
# [LOW] Configure and Export in the backup profiles manager do not work because of backwards incompatible changes in Joomla 4.1.1

Akeeba Backup 9.2.0
================================================================================
+ Integration with Joomla 4.1's Scheduled Tasks
# [HIGH] Uploading to OVH is broken on many servers not using a proxy
# [LOW] Popover content does not display in the Configuration page

Akeeba Backup 9.1.1
================================================================================
# [HIGH] Wrong RewriteBase set up in the .htaccess Maker when restoring a Joomla site with Admin Tools Professional installed

Akeeba Backup 9.1.0
================================================================================
+ Allow using [REMOTESTATUS] in the email subject, not just the body
+ Warn about the Console – Akeeba Backup plugin being disabled in the Schedule Automatic Backups page
+ Joomla restoration: modify domains in the Admin Tools' Allowed Domains and server config maker features if necessary
~ Force the Quickicon plugin to always show in the Notifications area instead of the 3rd Party area
# [HIGH] Problems restoring if a table name ends in 0 when another table with an identical name EXCEPT the trailing zero is also being backed up
# [HIGH] Backing up to SQL: indices would not have the correct table name prefix
# [HIGH] Backing up as SQL: the query for finder_taxonomy does not use the correct prefix
# [MEDIUM] Log Priorities global configuration option got mangled restoring a Joomla 4 site
# [LOW] Restore backup admin menu does not work correctly with multiple backup profiles
# [LOW] RackSpace CloudFiles: some hosts change the case of HTTP headers
# [LOW] Test FTP button was not working
# [LOW] Fixed displaying multi-line backup comments in the Manage Backups page

Akeeba Backup 9.0.11
================================================================================
+ Support for MySQL 8 invisible columns
# [LOW] Rare type error under PHP 8 during restoration
# [LOW] Wrong translation string in backend menu item type
# [LOW] Wrong controls in Backup and Restore backend menu item types

Akeeba Backup 9.0.10
================================================================================
- Remove piecon (pie graph favicon showing the backup progress)
~ JSON API: Forcibly use the ‘json’ origin everywhere
~ JSON API: Throw an error if the backup ID sent to stepBackup does not exist
~ JSON API: Improved backup IDs prevent a number of JSON API issues
~ Auto–publish the Console plugin in the Professional version
# [LOW] JSON API: The wrong origin (‘frontend’ instead of ‘json’) was recorded
# [LOW] Manage Backups: The View Log button didn't take you to the correct log file

Akeeba Backup 9.0.9
================================================================================
- Removed iDriveSync; the service has been discontinued by the provider.
- Removed the “Archive integrity check” feature.
~ Ensure the correct collation of all database tables and columns used by the extension
~ Dropbox connector updated to require TLS v1.2
+ API requests: Prevent server cache
+ Better support for custom database drivers provided by third party extensions
# [LOW] Bootstrap 5.1.2 included in Joomla 4.0.4 broke the CSS for Control Panel icons
# [LOW] Check failed backups: All Super Users were notified even when an email was supplied

Akeeba Backup 9.0.8
================================================================================
# [MEDIUM] Wrong ACL check wouldn't allow non–Super User accounts from accessing the component
# [LOW] PHP 8 error if the output directory is empty

Akeeba Backup 9.0.7
================================================================================
~ Remove dash from automatically generated random values for archive naming
~ Adjusted padding in download backup modal
+ Increase the maximum Size Quota limit to 1Pb
+ Support for Joomla proxy configuration
# [MEDIUM] Cannot restore on PHP 8 if Two Factor Authentication is enabled in any user account
# [HIGH] Backing up to Box, Dropbox, Google Drive or OneDrive may not be possible if you are using an add-on Download ID

Akeeba Backup 9.0.6
================================================================================
# [HIGH] Legacy front-end backup fails to execute when stepping through the backup with a 404 error
# [MEDIUM] Could not enable encryption for configuration settings
# [LOW] The usage statistics model is not loaded in the control panel page
# [LOW] PHP Warning from the TriggerEvent trait
# [LOW] Added back button after backup completion
# [LOW] Wrong use of double quotes in CLI language file

Akeeba Backup 9.0.5
================================================================================
+ You are given the option to rerun the migration or uninstall Akeeba Backup 8 (with a nifty link) after migrating settings from Akeeba Backup 8.
+ Migration now also imports the Download ID from Akeeba Backup 8
# [HIGH] JavaScript errors due to strict mode in Configuration, Database Filters, Include Folders, Restoration, S3 Import and Transfer Wizard pages

Akeeba Backup 9.0.4
================================================================================
+ CLI Migration command
~ Completely removing the use of the Joomla CMS Filesystem API for writing / copying / moving files because it's too buggy
# [MEDIUM] JSON API getProfiles returns an empty array
# [HIGH] CLI backups always run with profile #1, even if you use the --profile parameter
# [LOW] Downgrading from Pro to Core didn't work correctly
# [LOW] Warning in Manage Backups page if you have deleted the backup profile used to take a backup listed there

Akeeba Backup 9.0.3
================================================================================
# [MEDIUM] Joomla Filesystem API (File / Folder) doesn't work on some servers; preferring native PHP functions instead.
# [HIGH] Does not work on Windows on the latest Joomla 4 RC versions
# [HIGH] Some internal links do not work because of lower/uppercase mix in file names

Akeeba Backup 9.0.2
================================================================================
~ Prevent installation on Joomla 3.
# [HIGH] Core version, regression: Call to a member function rebaseFiltersToSiteDirs() on bool
# [HIGH] yet another last minute, undocumented, backwards incompatible change in Joomla is breaking things.
# [MEDIUM] Extensions not enabled automatically on installation.

Akeeba Backup 9.0.1
================================================================================
# [HIGH] Akeeba Backup Core: immediate error coming from the Dispatcher

Akeeba Backup 9.0.0
================================================================================
! Rewritten with Joomla 4 Core MVC and Bootstrap 5 styling
+ Reset the configuration and filters of backup profiles from the Profiles page