<?php

/**
 * Name          : aeSecure QuickScan - Free scanner
 * Description   : Scan your website for possible hacks, viruses, malwares, SEO black hat and exploits
 * Version       : 2.0
 * Date          : November 2018
 * Last update   : August 2023
 * Author        : AVONTURE Christophe (christophe@avonture.be)
 * Author website: https://www.avonture.be.
 *
 * --------------------------------------------------------------------------------------------------------
 * aeSecure QuickScan - Malware Scan.
 *
 * This script will make a quick and *superficial*, not deeply, scan and will detect the presence of
 * a few patterns in files present on your website. If such files are found, they will be reported.
 *
 * This script is a quick scan tool: only a very few patterns will be scanned and if you find
 * viruses with it, consider making a full and deeply scan to search for other malware scripts.
 *
 * If no files are reported by the script, here too, it's possible that other type of virus are
 * present.
 *
 * If you wish a full scan, contact me by surfing on https://www.avonture.be and take a look on my
 * services.
 *
 * Changelog:
 *
 * version 2.0
 *    + PHP 8.2 compatibility
 *    + look for hashes in hashes directory
 *
 * version 1.2
 *    + Rewrite for downloading all settings and signatures files from GitHub
 *    + Add a lot more signatures in these lists: blacklist, whitelist, other and edited json
 *    + Ad more patterns for viruses detection
 *    + Reformat the code of the scanner
 *
 * version 1.1.12
 *    + Add support for Grr, mediawiki, piwik and pmb
 *    + Solve an issue with session_start() for some hosts
 *
 * version 1.1.11
 *    + Add support for Grav
 *
 * version 1.1.10
 *    + Add support for phpMyAdmin
 *
 * version 1.1.9
 *    + Solve an error with session_start (on some hoster, the creation of the session gives a fatal error due to incorrect path)
 *
 * version 1.1.8
 *    + Solve an error with the link to the FAQ
 *    + Better handling of languages files
 *
 * version 1.1.7
 *    + Add localizations (class aeSecureLanguage)
 *
 * version 1.1.6
 *    + Improve the detection of the list of files by immediatly skipping whitelisted files.  On a site of 4.900 files, the scanner will be able to detect that
 *      only 11 files should be scanned if 4.889 are already white listed.  This way, the scanner will be really fast.
 *
 * version 1.1.5
 *    + Small change to correctly handle Joomla 3.5.0 with a newer way to determine the version number (no more dollar sign before variables name)
 *
 * version 1.1.4
 *    + Add aesecure_quickscan.whitelist.json as a file to download from avonture.be to speed up the processing and reduce the number of false positive
 *    + Add a lot of new signatures in the blacklist
 *
 * version 1.1.3
 *    + Add a timeout for the CURL request
 *
 * version 1.1.2
 *    + Support of concrete5, contao (aka previously called Typolight), dolibarr, eFront, EspoCRM, formaLMS, phpBB, phpList,
 *         SilverStripe and x3cms
 *
 * version 1.1.1
 *    + Monitored folders for Joomla: files present in a native Joomla's folder (part of the CMS) will
 *      be analysed
 *          - If not part of the distribution (intrusion)
 *          - If part of the distribution but with an another hash (hacked file or, at least, altered one)
 *
 * version 1.1.0
 *    + Support CakePHP, Drupal, Magento, PrestaShop (on top of Joomla and WordPress)
 *    + Improved security by no more loading core Joomla files
 *    + Advanced menu (left side)
 *        + Allow to activate debug and expert mode (without any changes in the code)
 *        + Allow to specify how many files to process by cycle (without any changes in the code)
 *        + Allow to specify with type of files to ignore (archives, images, medias, ...)
 *
 * Avoid __DIR__.
 *
 *      __DIR__ is the folder where the running script is started so, perhaps, things like
 *      c:/sites/hacked/. In most of case, it's correct because the script file has been
 *      saved there... but not always: think to symbolic links.
 *      The file can be saved f.i. in c:/repository/aesecure_quickscan/aesecure_quickscan.php
 *      and a symlink has been made in c:/sites/hacked/. We want that __DIR__ points to the
 *      hacked site but won't be the case with symlink. __DIR__ is where the file IS REALLY.
 *
 *      So, don't use __DIR__ but c:/sites/hacked/
 */
define('REPO', 'https://github.com/cavo789/aesecure_quickscan');

define('DIR', str_replace('/', DIRECTORY_SEPARATOR, dirname((string) $_SERVER['SCRIPT_FILENAME'])));
define('FILE', str_replace('/', DIRECTORY_SEPARATOR, basename((string) $_SERVER['SCRIPT_FILENAME'])));

// Don't allow to kill this script when demo mode is enabled
// Don't show the "Enable expert mode" checkbox in Demo mode
define('DEMO', false);

define('DEBUG', false);              // Enable debugging (Note: there is no progress bar in debug mode)
define('FULLDEBUG', false);          // Output a lot of information
define('VERSION', '2.0');            // Version number of this script
define('EXPERT', false);             // Display Kill file button and allow to specify a folder
define('MAX_SIZE', 1 * 1024 * 1024); // One megabyte: skip files when filesize is greater than this max size.
define('MAXFILESBYCYCLE', 500);      // Number of files to process by cycle, reduce this figure if you receive HTTP error 504 - Gateway timeout
define('CONTEXT_NBRCHARS', 100);     // When a suspicious pattern is found, the portion of code where this pattern is found will be displayed.  The portion is xxx characters before the pattern; the pattern and the same number of characters after it.
define('SHOWMD5', false);            // Allow to generate a hash file
define('PROGRESSBARFREQUENCY', 3);   // Frequency of updates for the progress bar. In seconds.
define('MEMORY_LIMIT', '256M');      // DEBUG MODE ONLY - Maximum memory limit that will be used
define('CURL_TIMEOUT', 2);           // Max number of seconds before the timeout when requesting a JSON file from avonture.be

// Download URL for the file with CMS hashes
define('DOWNLOAD_URL', 'https://raw.githubusercontent.com/cavo789/aesecure_quickscan/master/');
define('MD5', '');
define('DIRNOTFOUND', 'Directory not found');

// List of extensions, by "category". Add an extension if you want to skip that files when
// skipping the category
define('ExtArchives', '7z, bak, gz, gzip, jpa, tar, zip');
define('ExtDocuments', 'doc, docx, pdf, ppt, pptx, xls, xlsx');
define('ExtFonts', 'eot, otf, ttf, ttf2, woff, woff2');
define('ExtImages', 'bmp, eps, gif, ico, icon, jpeg, jpg, png, psd, svg, tiff, webp');
define('ExtMedia', 'css, js, less');
define('ExtSoundMovies', 'aiff, asf, avi, fla, flv, f4v, m4v, mkv, mov, mp3, mp4, mpeg, mpg, ogg, ogv, swf, wav, webm, wma');
define('ExtText', 'ini, json, log, md, mo, po, sql, text, txt, xml, xsl');

define('CRLF', "\r\n");
define('DS', DIRECTORY_SEPARATOR);

// Register error handling functions
set_error_handler(function ($code, $string, $file, $line): never {
    throw new ErrorException($string, 0, $code, $file, $line);
});

register_shutdown_function(function () {
    $memory = 'ini_get memory_limit=' . ini_get('memory_limit') . ' | ' .
        'memory used=' . aeSecureFct::getMemoryUsed();

    $error = error_get_last();
});

class aeSecureDebug
{
    /**
     * Debugging mode state (On / Off).
     *
     *
     * @access private
     */
    private static bool $debugMode = false;

    /**
     * Instantiate the class.
     *
     * @param bool $debugMode False will hide errors in the browser
     *                        True will activate a verbose mode
     *
     * @return void
     */
    public function __construct($debugMode = false)
    {
        // Informs PHP where to store errors
        ini_set('error_log', DIR . 'aesecure_quickscan_error_log');

        // Initialize the debug mode
        self::setDebugMode($debugMode);
    }

    /**
     * Set the debugging mode.
     *
     * @param bool $onOff
     *
     * @return void
     */
    public static function setDebugMode($onOff = false)
    {
        static::$debugMode = $onOff;

        // When debug mode is on, we want to see every messages; even notice.
        if (true === static::$debugMode) {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
            ini_set('html_errors', '1');
            ini_set('docref_root', 'http://www.php.net/');

            ini_set(
                'error_prepend_string',
                "<div style='color:red; font-family:verdana;" .
                    "border:1px solid red; padding:5px;'>"
            );
            ini_set('error_append_string', '</div>');
            error_reporting(E_ALL);
        } else {
            error_reporting(E_ALL & ~E_NOTICE);
        }
    }
}

class Download
{
    // Timeout delay in seconds
    final public const CURL_TIMEOUT = 2;
    final public const ERROR_CURL   = 1001;

    private static $sAppName              = '';
    private static string $sFileName      = '';
    private static string $sSourceURL     = '';
    private static bool $bDebug           = false;
    private static string $sDebugFileName = '';

    public function __construct($ApplicationName)
    {
        static::$bDebug   = false;
        static::$sAppName = $ApplicationName;
    }

    /**
     * Enable the debug mode for this class.
     *
     * @param mixed $bOnOff
     */
    public function debugMode($bOnOff)
    {
        static::$bDebug = $bOnOff;
        if ($bOnOff) {
            // A debug.log file will be created in
            // the folder of the calling script
            static::$sDebugFileName = DIR . 'debug.log';
        }
    }

    // URL where the script will find a file to download
    public function setURL($sURL)
    {
        static::$sSourceURL = trim((string) $sURL);
    }

    /**
     * Once download, a file will be created on the disk.
     * Use this property to specify the name of that file.
     *
     * @param mixed $sName
     */
    public function setFileName($sName)
    {
        static::$sFileName = trim((string) $sName);
    }

    /**
     * Download the application package ZIP file.
     *
     * @param type $url
     * @param type $file
     *
     * @return string
     */
    public function download()
    {
        $wError = 0;

        // Try to use CURL, if installed
        if (self::iscURLEnabled()) {
            // $sFileName is the fullname of the file to create f.i.
            // /home/www/username/rootweb/downloaded-file.zip

            $fp = @fopen(static::$sFileName, 'w');
            if (!$fp) {
                throw new Exception(static::$sAppName . ' - Could not open the file!');
            }

            if (!file_exists(static::$sFileName)) {
                $wError = self::ERROR_CURL;
            } else {
                @fclose($fp);
                @chmod(static::$sFileName, 0644);
            }

            if (0 === $wError) {
                // Ok, try to download the file
                $ch = curl_init(static::$sSourceURL);

                if ($ch) {
                    // Start the download process
                    @set_time_limit(0);
                    $fp = @fopen(static::$sFileName, 'w');

                    if (!curl_setopt($ch, CURLOPT_URL, static::$sSourceURL)) {
                        fclose($fp);
                        curl_close($ch);
                        $wError = self::ERROR_CURL;
                    } else {
                        // Download

                        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) ' .
                            'AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 ' .
                            'Safari/537.36 FirePHP/4Chrome');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CURL_TIMEOUT);

                        // Output curl debugging messages into a text file
                        if (static::$bDebug) {
                            // output debugging info in a txt file
                            curl_setopt($ch, CURLOPT_VERBOSE, true);
                            $fdebug = fopen(static::$sDebugFileName, 'w');
                            curl_setopt($ch, CURLOPT_STDERR, $fdebug);
                        }

                        // Add CURLOPT_SSL if the protocol is https
                        if ('https' == substr((string) static::$sSourceURL, 0, 5)) {
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                        }

                        curl_setopt($ch, CURLOPT_HEADER, false);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                        curl_setopt($ch, CURLOPT_FILE, $fp);
                        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

                        $rc = curl_exec($ch);

                        curl_close($ch);
                        fclose($fp);

                        if (!$rc) {
                            $wError = self::ERROR_CURL;
                        }

                        @chmod(static::$sFileName, 0644);
                    }
                }
            }
        }

        self::removeIfNull();

        if (!file_exists(static::$sFileName)) {
            // Unsuccessful, try with fopen()
            // Use a context to be able to define a timeout
            $context = stream_context_create(
                ['http' => ['timeout' => self::CURL_TIMEOUT]]
            );

            // Get the content if fopen() is enabled
            $content = @fopen(static::$sSourceURL, 'r', false, $context);
            if ('' !== $content) {
                @file_put_contents(static::$sFileName, $content);
            }

            self::removeIfNull();

            if (file_exists(static::$sFileName)) {
                $wError = 0;
            }
        }

        return $wError;
    }

    /**
     * Return a text for the encountered error.
     *
     * @param mixed $code
     */
    public function getErrorMessage($code)
    {
        $sReturn =
            '<p>Your system configuration doesn\'t allow to download the file.</p>' .
            '<p>Please click ' .
            '<a href="' . static::$sSourceURL . '">here</a> to ' .
            'manually download the file, then open your ' .
            'FTP client and send the downloaded file to your ' .
            'website folder.</p>' .
            '<p>Once this is done, just refresh this page.</p>' .
            '<p><em>Note: the filename should be ' . static::$sFileName . '</em></p>';

        return $sReturn;
    }

    /**
     * Detect if the CURL library is loaded.
     */
    private function iscURLEnabled()
    {
        return  (!function_exists('curl_init') && !function_exists('curl_setopt') &&
            !function_exists('curl_exec') && !function_exists('curl_close')) ? false : true;
    }

    /**
     * If the file is there and has a size of 0 byte,
     * it's a failure, the file wasn't downloaded.
     */
    private function removeIfNull()
    {
        if (file_exists(static::$sFileName)) {
            if (filesize(static::$sFileName) < 1000) {
                unlink(static::$sFileName);
            }
        }
    }
}

class aeSecureDownload
{
    /**
     * Download a file from GitHub like "aesecure_quickscan_pattern.json", ...
     * See the DOWNLOAD_URL constant for the URL.
     *
     * @param [type] $file
     * @param mixed $uri
     *
     * @return void
     */
    public static function get($file, $uri)
    {
        try {
            // Try to download
            $aeDownload = new Download('Quickscan');
            $aeDownload->debugMode(DEBUG);

            // Be sure to have only one "/" and not two
            if (trim('' !== $uri)) {
                $uri = ltrim(rtrim((string) $uri, '/'), '/') . '/';
            }

            $url = rtrim(DOWNLOAD_URL, '/') . '/' . $uri . basename((string) $file);

            $aeDownload->setURL($url);
            $aeDownload->setFileName($file);
            $wReturn = $aeDownload->download();

            if (0 !== $wReturn) {
                $sErrorMsg = $aeDownload->getErrorMessage($wReturn);
            }
        } catch (Exception $e) {
            $wReturn   = 1001;
            $sErrorMsg = $e->getMessage();
        }

        unset($aeDownload);
    }
}

/**
 * Add localization; read an external json file with translations.
 */
class aeSecureLanguage
{
    final public const DEFAULT_LANGUAGE = 'en-GB';

    // Filename pattern for languages files
    final public const LANG_FILE = 'aesecure_quickscan_lang_%s.json';

    // Hard-coded list of supported languages
    // @See https://github.com/cavo789/aesecure_quickscan for xxx_lang_xxxx.json files
    final public const SUPPORTED_LANGUAGES = 'en;en-GB;fr;fr-FR;nl;nl-BE';

    private string $_filename              = '';
    private $_lang                         = null;
    private $_arrLanguage                  = null;
    private bool $_bLoaded                 = false;
    private $supportedLanguages            = null;
    private $browserLanguages              = null;

    protected static $instance = null;

    public function __construct($lang = null)
    {
        $aeSession = aeSecureSession::getInstance();

        if (null == $lang) {
            $lang = str_replace('_', '-', (string) aeSecureFct::getParam('lang', 'string', '', 5));
        }

        // Initialize the list of supported languages
        $this->supportedLanguages = explode(';', self::SUPPORTED_LANGUAGES);

        // Get the list of languages supported by the Browser and by aeSecure
        // (presence of the language's file)
        self::getBrowserLanguage();

        if (in_array($lang, $this->supportedLanguages)) {
            // Perfect match
            // The language (f.i. nl-BE) is supported; we've a nl-BE.json file; use it
            $result = $lang;
        } elseif (in_array(substr((string) $lang, 0, 2), $this->supportedLanguages)) {
            // If the user ask for f.i. en-US and we've a file for "en", use that file.
            // For instance en-GB
            $result = substr((string) $lang, 0, 2);
        } else {
            // No, not found. Use the languages supported by the browser and check if aeSecure
            // support that language
            $result = '';

            // Search for a perfect match so if the language is en_US, try to find en_US.json
            // and not en_GB.json

            foreach ($this->browserLanguages as $lang => $value) {
                if (in_array($lang, $this->supportedLanguages)) {
                    $result = $lang;

                    break;
                }
            }

            // If $result is still empty, no perfect match so search on the language and not
            // language and country. So, if the language is en-US and if a file en-GB is found, get it.
            if ('' == $result) {
                $result = 'en-GB';
                foreach ($this->browserLanguages as $lang => $value) {
                    // Check if there is a language file (f.i. if $lang is "fr"
                    // (and not "fr_FR"), the glob function will return
                    // the list of files like fr*.json
                    if (in_array(substr((string) $lang, 0, 2), $this->supportedLanguages)) {
                        $result = substr((string) $lang, 0, 2);

                        break;
                    }
                }
            }

            // Still not? Use en-GB by default
            if ('' == $result) {
                $result = self::DEFAULT_LANGUAGE;
            }
        }

        $aeSession->set('Lang', $lang);

        // Max 5 characters
        $lang = substr((string) $lang, 0, 5);

        // Just be sure to have en-GB and not f.i. EN-GB or en_gb
        $lang = strtolower(substr($lang, 0, 2)) . '-' . strtoupper(substr($lang, -2));

        if ('en-US' == $lang) {
            $lang = 'en-GB';
        }

        $this->_lang     = $lang;
        $this->_filename = DIR . DS . sprintf(self::LANG_FILE, $this->_lang);

        $this->_bLoaded = false;

        if (!file_exists($this->_filename)) {
            // Try to download if not present
            aeSecureDownload::get($this->_filename, 'settings/');
        }

        if (file_exists($this->_filename)) {
            $string = file_get_contents($this->_filename);
            $string = str_replace('\\u', '\u', $string);

            if (null === json_decode($string, true, 512, JSON_THROW_ON_ERROR)) {
                die('There is a problem in ' . $this->_filename .
                    '. Probably an invalid json file <pre>' .
                    html_entity_decode($string) . '</pre>');
            }

            $this->_arrLanguage = json_decode($string, true, 512, JSON_THROW_ON_ERROR);
            $this->_bLoaded     = true;
        }

        // If the parametrized file isn't found (f.i. the user set fr-FR has
        // preferred language and the file is not
        // present), then use by default en-GB
        if (!$this->_bLoaded) {
            // Try to download if not present
            $this->_filename = DIR . DS . sprintf(self::LANG_FILE, self::DEFAULT_LANGUAGE);

            if (!file_exists($this->_filename)) {
                aeSecureDownload::get($this->_filename, 'settings/');
            }

            if (file_exists($this->_filename)) {
                $string = file_get_contents($this->_filename);
                $string = str_replace('\\u', '\u', $string);

                if (null === json_decode($string, true, 512, JSON_THROW_ON_ERROR)) {
                    die('There is a problem in ' . $this->_filename . '. ' .
                        'Probably an invalid json file <pre>' .
                        html_entity_decode($string) . '</pre>');
                }

                $this->_arrLanguage = json_decode($string, true, 512, JSON_THROW_ON_ERROR);
                $this->_bLoaded     = true;
            }
        }

        // Still not? Use the first language file that is present
        if ((!$this->_bLoaded) && (count($this->supportedLanguages) > 0)) {
            foreach ($this->supportedLanguages as $key => $value) {
                $this->_filename = DIR . DS . sprintf(self::LANG_FILE, $value);

                if (file_exists($this->_filename)) {
                    $string = file_get_contents($this->_filename);
                    $string = str_replace('\\u', '\u', $string);

                    if (null === json_decode($string, true, 512, JSON_THROW_ON_ERROR)) {
                        die('There is a problem in ' . $this->_filename .
                            '. Probably an invalid json file <pre>' .
                            html_entity_decode($string) . '</pre>');
                    }

                    $this->_arrLanguage = json_decode($string, true, 512, JSON_THROW_ON_ERROR);
                    $this->_bLoaded     = true;
                    $this->_lang        = $value;

                    break;
                }
            }
        }

        return true;
    }

    public function ready(): bool
    {
        return $this->_bLoaded;
    }

    /**
     * Translation functionality, search the CODE in the json file and returns its
     * value (the translated text).
     */
    public function get(string $code): string
    {
        $sText = '';
        if (isset($this->_arrLanguage[$code])) {
            $sText = $this->_arrLanguage[$code];
        }

        return $sText;
    }

    public function getlang(): string
    {
        return $this->_lang;
    }

    /**
     * $language can be initialized or not.  If not, the script will detect supported
     * languages as defined in the user's browser. If initialized, should be something
     * like 'en-GB', 'fr-FR', ...
     */
    public static function getInstance(?string $lang = null): self
    {
        if (null === self::$instance) {
            self::$instance = new aeSecureLanguage($lang);
        }

        return self::$instance;
    }

    /**
     * Read the HTTP_ACCEPT_LANGUAGE browser info to determine the best language
     * to use for aeSecure based on the browser's preferences.
     *
     * @return string Returns f.i. en-GB, fr-FR, nl-NL, ...
     */
    private function getBrowserLanguage(): string
    {
        $default       = null;
        $httplanguages = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

        if (empty($httplanguages)) {
            return $default;
        }

        $this->browserLanguages     = [];
        $result                     = '';

        // $this->browserLanguages is an array, sorted by priority order, of the
        // supported languages; for instance:
        // array
        //   'fr' => float 1
        //   'en_US' => float 0.8
        //   'en' => float 0.6

        foreach (preg_split('/,\s*/', (string) $httplanguages) as $accept) {
            $result = preg_match('/^([a-z]{1,8}(?:[-_][a-z]{1,8})*)(?:;\s*' .
                'q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i', (string) $accept, $match);

            if (!$result) {
                continue;
            }

            $quality = (isset($match[2]) ? (float)$match[2] : 1.0);

            $countries   = explode('-', $match[1]);
            $region      = array_shift($countries);
            $country_sub = explode('_', $region);
            $region      = array_shift($country_sub);

            foreach ($countries as $country) {
                $this->browserLanguages[$region . '-' . strtoupper($country)] = $quality;
            }
            foreach ($country_sub as $country) {
                $this->browserLanguages[$region . '-' . strtoupper($country)] = $quality;
            }

            $this->browserLanguages[$region] = $quality;
        }

        return true;
    }
}

/**
 * A few helping functions.
 */
class aeSecureFct
{
    /**
     * Remove special characters, f.i clean('a|"bc!@£de^&$f g') will return 'abcdef-g'.
     */
    public static function sanitize(string $string): string
    {
        // Replaces all spaces with hyphens.
        $string = str_replace(' ', '-', $string);
        // Removes special chars.
        return (string) preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }

    /**
     * Generic function for adding a js in the HTML response.
     *
     * @param type  $localfile
     * @param type  $weblocation
     * @param mixed $defer
     *
     * @return string
     */
    public static function addJavascript($localfile, $weblocation = '', $defer = false)
    {
        $return = '';

        // Perhaps the script (aesecure_quickscan.php) is a symbolic link so __DIR__
        // is the folder where the real file can be found and SCRIPT_FILENAME his link,
        // the line below should therefore not be used anymore
        if (is_file(str_replace('/', DS, dirname((string) $_SERVER['SCRIPT_FILENAME'])) . DS . $localfile)) {
            $return = '<script ' . (true == $defer ? 'defer="defer" ' : '') .
                'type="text/javascript" src="../' . $localfile . '"></script>';
        } else {
            if ('' != $weblocation) {
                $return = '<script ' . (true == $defer ? 'defer="defer" ' : '') .
                    'type="text/javascript" src="' . $weblocation . '"></script>';
            }
        }

        return $return;
    }

    /**
     * Generic function for adding a css in the HTML response.
     *
     * @param type $localfile
     * @param type $weblocation
     *
     * @return string
     */
    public static function addStylesheet($localfile, $weblocation = '')
    {
        $return = '';

        // Perhaps the script (aesecure_quickscan.php) is a symbolic link so __DIR__ is the
        // folder where the real file can be found and SCRIPT_FILENAME his link, the line
        // below should therefore not be used anymore
        if (is_file(str_replace('/', DS, dirname((string) $_SERVER['SCRIPT_FILENAME'])) . DS . $localfile)) {
            $return = '<link href="../' . $localfile . '" rel="stylesheet" />';
        } else {
            if ('' != $weblocation) {
                $return = '<link href="' . $weblocation . '" rel="stylesheet" />';
            }
        }

        return $return;
    }

    public static function human_filesize($bytes, $decimals = 2)
    {
        $sz     = 'BKMGTP';
        $factor = intval(floor((strlen((string) $bytes) - 1) / 3));

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    /**
     * Return a string like '1 an 10 mois 6 jours 3 heures'... ie the age of f.i. a file.
     *
     *    echo aeSecureFct::time_elapsed_string(filemtime($filename))
     */
    public static function time_elapsed_string(int $ptime): string
    {
        $diff       = time() - $ptime;
        $calc_times = [];
        $timeleft   = [];

        // Prepare array, depending on the output we want to get.
        $calc_times[] = ['an',      'ans',      31557600];
        $calc_times[] = ['mois',    'mois',     2592000];
        $calc_times[] = ['jour',    'jour',     86400];
        $calc_times[] = ['heure',   'heures',   3600];
        $calc_times[] = ['minute',  'minutes',  60];
        $calc_times[] = ['seconde', 'secondes', 1];

        foreach ($calc_times as $timedata) {
            [$time_sing, $time_plur, $offset] = $timedata;

            if ($diff >= $offset) {
                $left = floor($diff / $offset);
                $diff -= ($left * $offset);
                $timeleft[] = "{$left} " . (1 == $left ? $time_sing : $time_plur);
            }
        }

        return $timeleft ? (time() > $ptime ? null : '-') . implode(' ', $timeleft) : 0;
    }

    /**
     * Return true when the call to the php script has been done through an ajax request.
     */
    public static function isAjaxRequest(): bool
    {
        $bAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            ('XMLHttpRequest' == $_SERVER['HTTP_X_REQUESTED_WITH']));

        return $bAjax;
    }

    /**
     * Return the memory usage when this function is called.  By calling this function
     * at different place in the code, it's then possible to determine which part is
     * eating a lot of memory.
     *
     * @return type
     */
    public static function getMemoryUsed()
    {
        $mem_usage = memory_get_peak_usage(true);

        return ($mem_usage < 1048576)
            ? round($mem_usage / 1024, 2) . ' kb'
            : round($mem_usage / 1048576, 2) . ' mb';
    }

    /**
     * Safely read values from posted forms ($_POST).
     *
     * @param mixed $type
     */
    public static function getParam(string $name, $type = 'string', mixed $default = '', int $maxlen = 0): mixed
    {
        $tmp    = '';
        $return = $default;

        if (isset($_POST[$name])) {
            if (in_array($type, ['int', 'integer'])) {
                $return = htmlspecialchars((string) $_POST[$name], ENT_QUOTES); // filter_input(INPUT_POST, $name, FILTER_SANITIZE_NUMBER_INT);
            } elseif ('boolean' == $type) {
                // false = 5 characters
                $tmp    = substr(htmlspecialchars((string) $_POST[$name], ENT_QUOTES), 0, 5); // substr(filter_input(INPUT_POST, $name, FILTER_SANITIZE_STRING), 0, 5);
                $return = (in_array(strtolower($tmp), ['on', 'true'])) ? true : false;
            } elseif ('string' == $type) {
                $return = htmlspecialchars((string) $_POST[$name], ENT_QUOTES); //filter_input(INPUT_POST, $name, FILTER_SANITIZE_STRING);
                if ($maxlen > 0) {
                    $return = substr($return, 0, $maxlen);
                }
            } elseif ('unsafe' == $type) {
                $return = $_POST[$name];
            }
        } else {
            $aeSession = aeSecureSession::getInstance();

            // Get from the $_GET only in debug mode or for very few parameters like "lang" (to allow to switch between
            // languages) and "aes" (boolean set to 1 when QuickScan is started from within the aeSecure Firewall interface)

            if ((true === $aeSession->get('Debug', DEBUG)) || in_array($name, ['aes', 'lang'])) {
                if (isset($_GET[$name])) {
                    if (in_array($type, ['int', 'integer'])) {
                        $return = htmlspecialchars((string) $_GET[$name], ENT_QUOTES); //filter_input(INPUT_GET, $name, FILTER_SANITIZE_NUMBER_INT);
                    } elseif ('boolean' == $type) {
                        // false = 5 characters
                        $tmp    = substr(htmlspecialchars((string) $_GET[$name], ENT_QUOTES), 0, 5);
                        $return = (in_array(strtolower($tmp), ['1', 'on', 'true'])) ? true : false;
                    } elseif ('string' == $type) {
                        $return = htmlspecialchars((string) $_GET[$name], ENT_QUOTES);
                    } elseif ('unsafe' == $type) {
                        $return = $_GET[$name];
                    }
                }
            }
        }

        if ('boolean' == $type) {
            $return = (in_array($return, ['on', '1']) ? true : false);
        }

        return $return;
    }
}

/**
 * Logging functionality.
 */
class aeSecureLog
{
    private $_sLogFile = null;

    protected static $instance = null;

    public function __construct($sLogFile, $killFile = true)
    {
        $this->_sLogFile = $sLogFile;

        if ((true == $killFile) && (file_exists($this->_sLogFile)) && (is_writable($this->_sLogFile))) {
            unlink($this->_sLogFile);
        }

        return true;
    }

    public function kill()
    {
        if ((file_exists($this->_sLogFile)) && (is_writable($this->_sLogFile))) {
            unlink($this->_sLogFile);
        }
    }

    public function filename()
    {
        return $this->_sLogFile;
    }

    /**
     * Add a line in the $sLogFile log file.
     *
     * @param type $sLine
     *
     * @return type
     */
    public function addLog($sLine)
    {
        if (!is_writable(dirname((string) $this->_sLogFile))) {
            return;
        }
        if ('' != $this->_sLogFile) {
            if ($handle = fopen($this->_sLogFile, 'a')) {
                fwrite($handle, (string) ($sLine . "\n"));
                fclose($handle);
            }
        }
    }

    /**
     * @param type $sLogFile Name of the logfile that will be used
     * @param type $killFile Default True : kill the logfile if present when starting the run
     *
     * @return type
     */
    public static function getInstance($sLogFile = null, $killFile = false)
    {
        if (null != $sLogFile) {
            if (null === self::$instance) {
                self::$instance = new aeSecureLog($sLogFile, $killFile);
            }
        }

        return self::$instance;
    }
}

/**
 * Working with files and folders.
 */
class aeSecureFiles
{
    protected $aeSession = null;
    protected $aeLog     = null;

    protected static $instance = null;

    public function __construct()
    {
        $this->aeSession = aeSecureSession::getInstance();
    }

    public function SeeFile(?string $filename = null): ?string
    {
        $return = null;

        if (null != $filename) {
            if ((is_file($filename)) && is_readable($filename)) {
                $return = file_get_contents($filename);
            }
        }

        return $return;
    }

    /**
     * Kill physically a file.
     *
     * @return type -1 if the file has been removed successfully
     */
    public function KillFile(?string $filename = null): int
    {
        $return = 0;

        if (null == $filename) {
            return $return;
        }

        if ((is_file($filename)) && is_writable($filename)) {
            try {
                if (true === $this->aeSession->get('Debug', DEBUG)) {
                    if (null == $this->aeLog) {
                        $this->aeLog = aeSecureLog::getInstance();
                    }
                    $this->aeLog->addLog('*** Kill ' . $filename . ' ***');
                }
                unlink($filename);
                if (!is_file($filename)) {
                    $return = -1;
                }
            } catch (Exception $e) {
                $return = -999;
            }
        } else {
            $return = -50;
        }

        echo $return;
    }

    /**
     * Remove recursively folders
     * (f.i. rrmdir(__DIR__/hashes/cms/joomla/2.5.27) will kill the full tree below
     * the specified folder).
     *
     * @param bool $killroot If true, the folder himself will be removed.
     *                       rrmdir(__DIR__/hashes/cms/joomla/2.5.27, true) ==>
     *                       remove folder 2.5.27 too and not only his children
     */
    public function rrmdir(string $folder, bool $killroot = false, array $arrIgnoreFiles = ['.htaccess', 'index.html']): bool
    {
        try {
            if (!is_dir($folder) && file_exists($folder)) {
                try {
                    if (!is_writable($folder)) {
                        $bResult = @chmod($folder, octdec('755'));
                    }

                    return @unlink($folder);
                } catch (Exception $e) {
                    return false;
                }
            }

            foreach (scandir($folder) as $file) {
                if ('.' == $file || '..' == $file || in_array($file, $arrIgnoreFiles)) {
                    continue;
                }
                if (!self::rrmdir($folder . DS . $file)) {
                    if (!is_writable($folder . DS . $file)) {
                        $bResult = chmod($folder . DS . $file, octdec('755'));
                    }
                    if (!self::rrmdir($folder . DS . $file)) {
                        return false;
                    }
                }
            }

            // Remove the folder only if not read-only and empty
            if ((is_writable($folder)) && (0 === count(glob("$folder/*")))) {
                @rmdir($folder);
            }

            return true;
        } catch (Exception $ex) {
            if (true === $this->aeSession->get('Debug', DEBUG)) {
                echo '<pre>' . print_r($ex, true) . '</pre>';
            }

            return false;
        }
    }

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new aeSecureFiles();
        }

        return self::$instance;
    }

    public static function getFileMimeType(string $filename): ?string
    {
        $mime_type = null;

        if (is_file($filename)) {
            $finfo = null;

            if (class_exists('info')) {
                // return mime type
                $finfo = new finfo(FILEINFO_MIME);
            }

            if ($finfo) {
                $file_info = $finfo->file($filename);
                $mime_type = substr($file_info, 0, strpos($file_info, ';'));
            } else {
                // Requires to enable "extension=php_fileinfo.dll" on a Windows machine
                if (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                }
                if ($finfo) {
                    $mime_type = finfo_file($finfo, $filename);
                    finfo_close($finfo);
                }
            }
        }

        return $mime_type;
    }

    /**
     * Return true if the file contains text content.
     */
    public static function isTextFileContent(string $filename): string
    {
        $mimeType   = aeSecureFiles::getFileMimeType($filename);
        $isTextType = false;

        // Try to determine if it's a text file; in that case the MIME type is
        // something like text/plain or text/richtext i.e. starting with the "text/" prefix
        $isTextType = ('text/' == substr((string) $mimeType, 0, 5));

        // A few mimetype are also text like application/javascript
        if (!$isTextType) {
            $isTextType = in_array($mimeType, ['application/xml']);

            // Still not? Try to use the file's extension to determine this
            if (!$isTextType) {
                $ext        = pathinfo($filename, PATHINFO_EXTENSION);
                $isTextType = in_array($ext, ['css', 'csv', 'eot', 'html', 'htm', 'ini',
                    'js', 'json', 'php', 'sh', 'svg', 'txt', 'xml']);
            }
        }

        return $isTextType;
    }
}

/**
 * Session helper.
 */
class aeSecureSession
{
    protected static $instance = null;
    protected static $prefix   = 'aeS_';

    public function __construct($bDestroy = false)
    {
        // server should keep session data for AT LEAST 1 hour
        try {
            ini_set('session.gc_maxlifetime', 3600);
            // each client should remember their session id for EXACTLY 1 hour
            session_set_cookie_params(3600);
        } catch (\Exception $exception) {
        }

        if (!isset($_SESSION)) {
            try {
                session_start();
            } catch (Exception $e) {
                // 1.1.9 - On some hoster the path where to store session is incorrectly
                // set and this gives a fatal error
                // Handle this and use the /tmp folder in this case.
                try {
                    session_destroy();
                    session_save_path(sys_get_temp_dir());

                    try {
                        session_start();
                    } catch (Exception $e) {
                        // 1.1.12
                        // Still not? Use the current dir
                        session_destroy();
                        session_save_path(DIR);
                        session_start();
                    }
                } catch (\Exception $exception) {
                }
            }
        }

        if ($bDestroy) {
            session_destroy();
        }

        return true;
    }

    public static function getInstance($bDestroy = false): self
    {
        if (null === self::$instance) {
            self::$instance = new aeSecureSession($bDestroy);
        }

        return self::$instance;
    }

    public static function set(string $name, mixed $value): void
    {
        $_SESSION[static::$prefix . $name] = $value;
    }

    public static function get(string $name, mixed $defaultvalue): mixed
    {
        return $_SESSION[static::$prefix . $name] ?? $defaultvalue;
    }
}

/**
 * CMS functionnalities.
 */
class aeSecureCMS
{
    final public const SUPPORTED_CMS = 'aesecure_quickscan_supported_cms.json';

    /**
     * Try to determine if the site is a CMS site and in that case, get the CMS version.
     */
    public static function getInfo(string $directory): array
    {
        $CMS         = '';
        $MainVersion = '';
        $Version     = '';
        $FullVersion = '';

        // Try to derive the root folder
        $root = rtrim($directory, DS) . DS;

        // Get the list of CMS (it's a json string stored in a constant)
        // and try to find a CMS
        $file = DIR . DS . self::SUPPORTED_CMS;
        if (!is_file($file)) {
            aeSecureDownload::get($file, 'settings/');
        }

        if (!is_file($file)) {
            die(sprintf('Sorry, the file [%s] is missing', basename($file)));
        }

        $arrCMS = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

        foreach ($arrCMS as $key => $value) {
            if (method_exists('aeSecureCMS', 'is' . $key)) {
                $method = self::class . '::is' . $key; // PHP 8.2

                [$return, $CMS, $Filename, $FullVersion, $MainVersion, $Version] = call_user_func($method, $root);

                if (true === $return) {
                    break;
                }
            }
        }

        return [(string) $CMS, (string) $FullVersion, (string) $MainVersion, (string) $Version, (string) $root];
    }

    /**
     * Detect if the CMS is Cake.
     */
    private static function iscake(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'admin' . DS . 'cake' . DS . 'config' . DS . 'config.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            preg_match('/.*\\Cake\.version\'\] *= *\'(.*)\'/', $content, $arrMatches, PREG_OFFSET_CAPTURE);
            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'Cake', $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is Concrete5.
     */
    private static function isConcrete5(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'concrete' . DS . 'config' . DS . 'concrete.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            preg_match('/.*\'version\' *\=\> *\'(.*)\'/', $content, $arrMatches, PREG_OFFSET_CAPTURE);
            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'Concrete5', $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is Contao.
     */
    private static function isContao(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'system' . DS . 'config' . DS . 'constants.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            preg_match('/.*VERSION\', *\'(.*)\'/', $content, $arrMatches, PREG_OFFSET_CAPTURE);
            $MainVersion = (count($arrMatches) > 0) ? $arrMatches[1][0] : '';

            preg_match('/.*BUILD\', *\'(.*)\'/', $content, $arrMatches, PREG_OFFSET_CAPTURE);
            $FullVersion = $MainVersion . '.' . ((count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '');

            return [true, 'Contao', $filename, $FullVersion, $MainVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is Dolibarr.
     */
    private static function isDolibarr(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'htdocs' . DS . 'filefunc.inc.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            preg_match('/.*DOL_VERSION\', *\'(.*)\'/', $content, $arrMatches, PREG_OFFSET_CAPTURE);
            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'Dolibarr', $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is Drupal.
     */
    private static function isDrupal(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'modules' . DS . 'system' . DS . 'system.module';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            preg_match('/.*define\(\'VERSION\', *\'(.*)\'/', $content, $arrMatches, PREG_OFFSET_CAPTURE);
            $FullVersion = (count($arrMatches) > 0) ? $arrMatches[1][0] : '';

            return [true, 'Drupal', $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is eFront.
     */
    private static function iseFront(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'libraries' . DS . 'configuration.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            preg_match('/.*G_VERSION_NUM\', *\'(.*)\'/', $content, $arrMatches, PREG_OFFSET_CAPTURE);
            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'eFront', $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is EspoCRM.
     */
    private static function isEspoCRM(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'data' . DS . 'config.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            preg_match('/.*version\' \=\> *\'(.*)\'/', $content, $arrMatches, PREG_OFFSET_CAPTURE);
            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'EspoCRM', $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is FormaLMS.
     */
    private static function isFormaLMS(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'appCore' . DS . 'index.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            preg_match('/.*_file_version_\', *\'(.*)\'/', $content, $arrMatches, PREG_OFFSET_CAPTURE);
            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'FormaLMS', $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is Grav.
     */
    private static function isGrav(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'system' . DS . 'defines.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            preg_match('/.*define\(\'GRAV_VERSION\', *\'(.*)\'/', $content, $arrMatches, PREG_OFFSET_CAPTURE);
            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'Grav', $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is GRR.
     */
    private static function isGrr(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'include' . DS . 'misc.inc.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            $pattern = '/\\$version_grr[[:blank:]]=[[:blank:]]"(.*)"/';

            preg_match($pattern, $content, $arrMatches, PREG_OFFSET_CAPTURE);

            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'GRR', $filename, $FullVersion, $FullVersion, $FullVersion, $root];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is Joomla.
     */
    private static function isJoomla(string $root): bool|array
    {
        if ((($wpos = strpos($root, DS . 'administrator' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'bin' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'cache' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'cli' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'components' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'images' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'includes' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'language' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'layouts' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'libraries' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'logs' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'media' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'modules' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'plugins' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'templates' . DS)) > 0) ||
            (($wpos = strpos($root, DS . 'tmp' . DS)) > 0)
        ) {
            $root = substr($root, 0, $wpos);
        }

        // Now, $root is probably the website root.  Check if we can found a Joomla!® instance
        $filename = rtrim($root, DS) . DS . 'libraries' . DS . 'src' . DS . 'Version.php';

        // Now, $root is probably the website root.  Check if we can found a Joomla!® instance
        if (!file_exists($filename)) {
            $filename = rtrim($root, DS) . DS . 'libraries' . DS . 'cms' . DS . 'version' . DS . 'version.php';
        }

        if (!file_exists($filename)) {
            $filename = rtrim($root, DS) . DS . 'libraries' . DS . 'joomla' . DS . 'version.php';
        }

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            $pattern = '/MAJOR_VERSION = (\d+)/';

            if (preg_match($pattern, $content, $arrMatches, PREG_OFFSET_CAPTURE) > 0) {
                // As from Joomla 4, RELEASE, DEV_LEVEL, ... are removed.
                $arr = ['MAJOR_VERSION' => 0, 'MINOR_VERSION' => 0, 'PATCH_VERSION' => 0, 'RELDATE' => 0, 'RELTIME' => 0, 'RELTZ' => 0];
            } else {
                $arr = ['RELEASE' => 0, 'DEV_LEVEL' => 0, 'DEV_STATUS' => 0, 'RELDATE' => 0, 'RELTIME' => 0, 'RELTZ' => 0];
            }

            foreach ($arr as $key => $value) {
                // Use [[:blank:]] and not just a space character because sometimes the
                // version.php file contains something other than a space
                // this is the case for J1.5.26
                // Note : since J3.5, variables are now constants and without the preceding dollar sign so
                //     before J3.5, it was $RELEASE f.i., since 3.5, it's just RELEASE
                $pattern = '/.*\$?' . $key . "[[:blank:]]*=[[:blank:]]*'?([0-9A-Za-z\-\.]*)'?/";

                preg_match($pattern, $content, $arrMatches, PREG_OFFSET_CAPTURE);
                if (count($arrMatches) > 0) {
                    $arr[$key] = $arrMatches[1][0];
                }
            }

            if (isset($arr['MAJOR_VERSION'])) {
                // Joomla 3.8.2 or greater
                $MainVersion = $arr['MAJOR_VERSION'] . '.' . $arr['MINOR_VERSION'];
                $Version     = $arr['MAJOR_VERSION'] . '.' . $arr['MINOR_VERSION'] . '.' . $arr['PATCH_VERSION'];
                $FullVersion = $arr['MAJOR_VERSION'] . '.' . $arr['MINOR_VERSION'] . '.' . $arr['PATCH_VERSION'] . ' ' . '(' . $arr['RELDATE'] . ' ' . $arr['RELTIME'] . ' ' . $arr['RELTZ'] . ')';
            } else {
                $MainVersion = $arr['RELEASE'];
                $Version     = $arr['RELEASE'] . '.' . $arr['DEV_LEVEL'];
                $FullVersion = $arr['RELEASE'] . '.' . $arr['DEV_LEVEL'] . ' (' . $arr['DEV_STATUS'] . ') ' . '(' . $arr['RELDATE'] . ' ' . $arr['RELTIME'] . ' ' . $arr['RELTZ'] . ')';
            }

            return [true, 'Joomla', $filename, (string) $FullVersion, (string) $MainVersion, (string) $Version];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is Magento.
     */
    private static function isMagento(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'app' . DS . 'Mage.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            $arr = ['major' => 0, 'minor' => 0, 'revision' => 0, 'patch' => 0];
            foreach ($arr as $key => $value) {
                preg_match('/.*\'' . $key . '\' *=\> *\'(\\d+)\'/', $content, $arrMatches, PREG_OFFSET_CAPTURE);
                if (count($arrMatches) > 0) {
                    (string) $arr[$key] = $arrMatches[1][0];
                }
            }

            $FullVersion = $arr['major'] . '.' . $arr['minor'] . '.' . $arr['revision'] . '.' . $arr['patch'];

            return [true, 'Magento', $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is MediaWiki.
     */
    private static function isMediaWiki(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'includes' . DS . 'DefaultSettings.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            $pattern = '/.*\$wgVersion \= \'(\\d+\\.\\d+\\.\\d+)\'/';

            preg_match($pattern, $content, $arrMatches, PREG_OFFSET_CAPTURE);

            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'MediaWiki', $filename, $FullVersion, $FullVersion, $FullVersion, $root];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is phpBB.
     */
    private static function isphpBB(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'styles' . DS . 'prosilver' . DS . 'style.cfg';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            $pattern = '/.*phpbb_version \= (\\d+\\.\\d+\\.\\d+)/';

            preg_match($pattern, $content, $arrMatches, PREG_OFFSET_CAPTURE);

            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'phpBB', $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is phpList.
     */
    private static function isphpList(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'public_html' . DS . 'lists' . DS . 'admin' . DS . 'init.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            preg_match('/.*"VERSION", "(.*)"/', $content, $arrMatches, PREG_OFFSET_CAPTURE);

            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'phpList', $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the framework is phpMyAdmin.
     */
    private static function isphpmyadmin(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'libraries' . DS . 'config.class.php';
        if (!file_exists($filename)) {
            $filename = rtrim($root, DS) . DS . 'libraries' . DS . 'config.php';
        }

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            $pattern = '/.*PMA_VERSION\', *\'(\\d+\\.\\d+\\.\\d+)\'/';

            preg_match($pattern, $content, $arrMatches, PREG_OFFSET_CAPTURE);

            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'phpmyadmin', $filename, $FullVersion, $FullVersion, $FullVersion, $root];
        } else {
            return false;
        }
    }

    /**
     * Detect if the framework is PMP.
     */
    private static function isPMB(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'includes' . DS . 'config.inc.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            $pattern = '/\\$pmb_version_brut[[:blank:]]=[[:blank:]]"(\\d+\\.\\d+\\.\\d+(.*))"/';

            preg_match($pattern, $content, $arrMatches, PREG_OFFSET_CAPTURE);

            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'PMB', $filename, $FullVersion, $FullVersion, $FullVersion, $root];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is Prestashop.
     */
    private static function isPrestashop(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'config' . DS . 'settings.inc.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            $pattern = '/.*_PS_VERSION_\', *\'(.*)\'/';

            preg_match($pattern, $content, $arrMatches, PREG_OFFSET_CAPTURE);

            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'Prestashop', $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is SilverStripe.
     */
    private static function isSilverStripe(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'cms' . DS . 'silverstripe_version';

        if (file_exists($filename)) {
            $FullVersion = (string) file_get_contents($filename);

            return [true, 'silverstripe', $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is WordPress.
     */
    private static function isWordPress(string $root): bool|array
    {
        $filename = '';
        if (strpos($root, 'wp-admin') > 0) {
            $filename = rtrim(substr($filename, 0, strpos($root, 'wp-admin')), DS) . DS . 'wp-includes' . DS . 'version.php';
        } elseif (strpos($root, 'wp-content') > 0) {
            $filename = rtrim(substr($filename, 0, strpos($root, 'wp-content')), DS) . DS . 'wp-includes' . DS . 'version.php';
        } elseif (strpos($root, 'wp-includes') > 0) {
            $filename = rtrim(substr($filename, 0, strpos($root, 'wp-includes')), DS) . DS . 'wp-includes' . DS . 'version.php';
        } else {
            $filename = rtrim($root, DS) . DS . 'wp-includes' . DS . 'version.php';
            if (!file_exists($filename)) {
                $filename = rtrim(dirname($root), DS) . DS . 'wp-includes' . DS . 'version.php';
            }
        }

        if (file_exists($filename)) {
            // ---------------------------
            // ---      WordPress      ---
            // ---------------------------

            $CMS = 'Wordpress';

            $configuration = file_get_contents($filename);

            $pattern = '/.*\\$wp_version *= *\'(.*)\'/';

            preg_match($pattern, $configuration, $arrMatches, PREG_OFFSET_CAPTURE);

            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            // Get the website root folder
            $root = dirname(dirname($filename));

            return [true, $CMS, $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }

    /**
     * Detect if the CMS is x3cms.
     */
    private static function isx3cms(string $root): bool|array
    {
        $filename = rtrim($root, DS) . DS . 'INSTALL' . DS . 'index.php';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            $pattern = '/.*\'X4VERSION\', *\'(\\d+\\.\\d+\\.\\d+)\'/';

            preg_match($pattern, $content, $arrMatches, PREG_OFFSET_CAPTURE);

            $FullVersion = (count($arrMatches) > 0) ? (string) $arrMatches[1][0] : '';

            return [true, 'x3cms', $filename, $FullVersion, $FullVersion, $FullVersion];
        } else {
            return false;
        }
    }
}

/**
 * PHP, JS and HTML code related to the progress bar functionnality.
 */
class aeSecureProgressBar
{
    protected $aeSession = null;

    private $_filename       = null;
    private $_ID             = null;
    private $_CSS            = 'progress-bar';
    private int $_frequency  = 1000; // refresh the progress bar each xx seconds  (1000=one second)
    private int $_start      = 0;    // f.i. 0   (current step in the progress bar)
    private int $_end        = 0;    // f.i. 100 (number of steps)
    private int $_pct        = 0;    // calculated progression in percentage

    protected static $instance = null;

    public function __construct($ID, $Class)
    {
        $this->_ID        = $ID;
        $this->_CSS       = $Class;
        $this->_filename  = str_replace('.php', '.tmp', __FILE__);
        $this->_start     = 0;
        $this->_end       = 100;
        $this->_pct       = 0;
        // Refresh frequency
        $this->_frequency = PROGRESSBARFREQUENCY * 1000;

        $this->aeSession = aeSecureSession::getInstance();

        return true;
    }

    /**
     * Getter & Setter for the Start position of the progress bar (by default, 0).
     */
    public function getStart(): int
    {
        return $this->_start;
    }

    public function setStart(int $value = 0): void
    {
        $this->_start = $value;
    }

    /**
     * Getter & Setter for the End position of the progress bar (by default, 100).
     */
    public function getEnd(): int
    {
        return 0 == $this->_end ? 1 : $this->_end;
    }

    public function setEnd(int $value = 100): void
    {
        $this->_end = $value;
    }

    /**
     * Remove the temporary file with the progress indicator.
     */
    public function clean()
    {
        if (file_exists($this->_filename)) {
            unlink($this->_filename);
        }
    }

    /**
     * Increment the position of the progress bar percentage (write the percentage
     * in the temporary file that will be used by the ajax Progress bar).
     *
     * @return bool
     */
    public function incTaskCount()
    {
        if (true === $this->aeSession->get('Debug', DEBUG)) {
            return false;
        }

        ++$this->_start;

        if (($this->_start / $this->getEnd()) > $this->_pct) {
            $this->_pct = intval(intval($this->_start) / $this->getEnd());

            // *******************************************************************
            // *******************************************************************
            // *******************************************************************
            //
            // The session should be closed otherwise Ajax request won't be called
            // asynchronously and the progress bar won't be incremented
            // http://stackoverflow.com/questions/3506574
            session_write_close();
            //
            // *******************************************************************

            if ($handle = fopen($this->_filename, 'w+')) {
                fwrite($handle, (int)($this->_pct * 100));
                fclose($handle);
            }
        }

        return true;
    }

    /**
     * Return the current progress value; read it from a file.
     */
    public function getProgress()
    {
        if (true === $this->aeSession->get('Debug', DEBUG)) {
            return false;
        }

        header('Content-Type: application/json');
        header('Cache-Control: no-cache');

        if (file_exists($this->_filename)) {
            echo json_encode(['pct' => file_get_contents($this->_filename)], JSON_THROW_ON_ERROR);
        } else {
            echo json_encode(['pct' => '100']);
        }

        try {
            ob_end_flush();
            flush();
        } catch (Exception $e) {
        }

        die();
    }

    /**
     * Generate the HTML code for the progress bar.
     *
     * @return type
     */
    public function getHTML()
    {
        if (true === $this->aeSession->get('Debug', DEBUG)) {
            return false;
        }

        echo
            '<div id="' . $this->_ID . '" class="progress" style="display:none;">' .
                '<div class="progress progress-striped active">' .
                    '<div class="' . $this->_CSS . '" aria-valuenow="1" aria-valuemin="1" ' .
                        'aria-valuemax="100">' .
                    '</div>' .
                '</div>' .
            '</div>';
    }

    /**
     * Generate the JS code for the progress bar (initialization and show evolution
     * during the scanning).
     *
     * @param mixed $what
     */
    public function getJSFunction($what = 'function')
    {
        if (true === $this->aeSession->get('Debug', DEBUG)) {
            return false;
        }

        switch ($what) {
            case 'initialize':
                // Variables needed for the progress bar JS code

                echo 'progressFct=null;previousPct=0;';

                break;
            case 'ajax_before':
                // The long process will start; initialize the progress bar and show it

                echo
                 'previousPct=0;' .
                 '$(".' . $this->_CSS . '").attr("aria-valuenow", 0);' .
                 '$(".' . $this->_CSS . '").css("width","0%");' .
                 '$(".' . $this->_CSS . '").html($(".' . $this->_CSS . '").attr("aria-valuenow") + "%");' .
                 '$("#' . $this->_ID . '").fadeIn(300); ' .
                 'progressFct=setInterval( function () {getProgress();},' . $this->_frequency . ');';

                break;
            case 'ajax_success':
                // The long process is now finished, put the progress bar to 100% and then hide it

                echo
                  '$(".' . $this->_CSS . '").attr("aria-valuenow", 100);' .
                  '$(".' . $this->_CSS . '").css("width","100%");' .
                  '$(".' . $this->_CSS . '").html($(".' . $this->_CSS . '").attr("aria-valuenow") + "%");' .
                  'clearTimeout(progressFct);' .
                  '$("#' . $this->_ID . '").fadeOut(300);';

                break;
            case 'function':
                // The long process is running, update the progress bar

                echo
                    'function getProgress() {
                        $.ajax({
                            url:"' . FILE . '",
                            data:"task=progress",
                            type:"' . ((true === $this->aeSession->get('Debug', DEBUG)) ? 'GET' : 'POST') . '",
                            async:true,
                            timeout: 600000,  // Scanning a site can be very long
                            cache:false,
                            dataType:"json",
                            success: function(json) {
                                percentage=parseInt(json.pct);
                                if (percentage>=100) {
                                    $(".' . $this->_CSS . '").attr("aria-valuenow", 100);
                                    $(".' . $this->_CSS . '").css("width","100%");
                                    $(".' . $this->_CSS . '").html($(".' . $this->_CSS . '").attr("aria-valuenow") + "%");
                                    clearTimeout(progressFct);
                                    $("#' . $this->_ID . '").fadeOut(300);
                                } else {
                                    if (percentage>previousPct) {
                                        if ($("#gettingFiles").length) $("#gettingFiles").hide();
                                        if (percentage>100) percentage=100;
                                        $(".' . $this->_CSS . '").attr("aria-valuenow", Math.round(percentage));
                                        $(".' . $this->_CSS . '").css("width", percentage + "%");
                                        $(".' . $this->_CSS . '").html($(".' . $this->_CSS . '").attr("aria-valuenow") + "%");
                                        $("#' . $this->_ID . '").show();
                                        previousPct=percentage;
                                    }
                                }
                            } // success
                        });
                        return;
                    } // function getProgress()';

                break;
        }
    }

    /**
     * @param type $ID    ID to give to the HTML progress bar container
     * @param type $Class CSS class to give to the HTML container
     *
     * @return type
     */
    public static function getInstance($ID = 'ajaxResultPct', $Class = 'progress-bar')
    {
        if (null === self::$instance) {
            self::$instance = new aeSecureProgressBar($ID, $Class);
        }

        return self::$instance;
    }
}

/**
 * The scanner himself.
 */
class aeSecureScan
{
    // hash of files already scanned and are viruses (the file is a virus)
    final public const BLACKLIST = 'aesecure_quickscan_blacklist.json';

    // JSON for the detected CMS (f. i. aesecure_quickscan_J!3.9.0.json for a Joomla 3.9.0 version)
    final public const CMS = 'aesecure_quickscan_%s.json';

    // hash of files already scanned and where a virus was found (the file contains a virus)
    final public const EDITED = 'aesecure_quickscan_edited.json';

    // hash of files that can be considered as safe
    final public const OTHER = 'aesecure_quickscan_other.json';

    // JSON with patterns to scan for finding viruses
    final public const PATTERN   = 'aesecure_quickscan_pattern.json';

    // hash of files that can be considered as safe
    final public const WHITELIST = 'aesecure_quickscan_whitelist.json';

    // List of supported CMS
    final public const SUPPORTED_CMS = 'aesecure_quickscan_supported_cms.json';

    final public const FOLDERS = 'aesecure_quickscan_folders.json';

    protected $aeFiles    = null;
    protected $aeLanguage = null;
    protected $aeLog      = null;
    protected $aeSession  = null;
    protected $aeProgress = null;

    private $_directory      = null; // Folder to scan
    private $_start          = 0;    // When processing files by block (files #1 till #2500, #2501 till #5000, ...) start is the "from" part f.i. 2501
    private int $_end        = 0;    //    and _end will be the end part f.i. 5000
    private $_arrCMS         = null; // Used by the hash functions
    private $_arrRegex       = null; // Array with regex patterns to match

    private $_arrCMSHashes       = null; // Array with the hash of the installed CMS
    private $_arrWhiteListHashes = null; // Array with whitelisted files (files from the CMS core)
    private $_arrOtherHashes     = null; // Array with whitelisted files (files whitelisted during aeSecure DeepScan runs)
    private $_arrBlackListHashes = null; // Array with blacklisted files (the file is a virus)
    private $_arrEditedHashes    = null; // Array with hash of edited files (a virus was appended in a file)

    public function __construct()
    {
        date_default_timezone_set('Europe/Brussels');
        setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

        // Instanciate objects
        $this->aeLanguage = aeSecureLanguage::getInstance();
        $this->aeFiles    = aeSecureFiles::getInstance();
        $this->aeProgress = aeSecureProgressBar::getInstance();

        $this->aeSession = aeSecureSession::getInstance();

        // Create the logfile
        if (true === $this->aeSession->get('Debug', DEBUG)) {
            $this->aeLog = aeSecureLog::getInstance(str_replace('.php', '.files.tmp', __FILE__));
        }

        $rootFolder = DIR;
        $this->aeSession->set('folder', '');

        // By default, scan the current directory.
        // In Expert mode, allow to use a session to store the name of the folder
        // Get the folder to process
        if (true === $this->aeSession->get('Expert', EXPERT)) {
            $folder = base64_decode((string) aeSecureFct::getParam('folder', 'string', ''));
            if ('' == $folder) {
                $folder = $this->aeSession->get('folder', $rootFolder);
            }

            // Check if the user has specified a folder in the user entry form
            if (is_dir($folder)) {
                $this->aeSession->set('folder', $folder);
            }
        } else {
            $tmp = $this->aeSession->get('folder', '');
            if ('' == $tmp) {
                $this->aeSession->set('folder', $rootFolder);
            }
        }

        // When running the scan for f.i. only 1000 files and not all files present on the server,
        // the start parameter will f.i. be set to 0 while the end parameter will be set to 1000.
        // This is just like a pagination so processing the 1000 next files will be :
        // start=1000 and end=1000.
        $this->_start = aeSecureFct::getParam('start', 'integer', 0);
        $this->_end   = $this->_start + aeSecureFct::getParam('end', 'integer', 0);

        $this->_directory = $rootFolder;
        if ($this->aeSession->get('Expert', EXPERT)) {
            $this->_directory =  trim((string) $this->aeSession->get('folder', $rootFolder));
            if ('' == $this->_directory) {
                $this->_directory = $rootFolder;
            }
        }

        $this->_arrCMS = [['joomla' => 'J!'], ['wordpress' => 'WP']];

        if (!is_file($file = DIR . DS . self::PATTERN)) {
            aeSecureDownload::get($file, 'settings/');
        }

        return true;
    }

    public function directory()
    {
        return $this->_directory;
    }

    /**
     * The aesecure_quickscan_pattern.json is using constant for the disclaimer info.
     * These constants should be replaced by their translated text.
     *
     * @param type $disclaimer
     *
     * @return string
     */
    public function getDisclaimerText($disclaimer)
    {
        $return = match ($disclaimer) {
            'HIGHPROBALITY'               => $this->aeLanguage->get('HIGHPROBALITY'),
            'HIGHPROBALITYFALSEGIF'       => $this->aeLanguage->get('HIGHPROBALITYFALSEGIF'),
            'WARNINGBASE64ENCODEDPATTERN' => $this->aeLanguage->get('WARNINGBASE64ENCODEDPATTERN'),
            'HIGHPROBALITYBADSITE'        => $this->aeLanguage->get('HIGHPROBALITYBADSITE'),
            'HIGHPROBALITYBASE64KEYWORD'  => $this->aeLanguage->get('HIGHPROBALITYBASE64KEYWORD'),
            'WARNINGNOTMANDATORYAVIRUS'   => $this->aeLanguage->get('WARNINGNOTMANDATORYAVIRUS'),
            default                       => $disclaimer . ((true === $this->aeSession->get('Debug', DEBUG))
                ? ' *please add translation*'
                : ''),
        };

        return $return;
    }

    /**
     * Add a file in the whitelist.
     */
    public function WhiteList(?string $filename = null)
    {
        // Don't white list files in demo mode, simulate that everything was ok (return -1)
        if (DEMO) {
            return -1;
        }

        if (!is_file($file = DIR . DS . self::WHITELIST)) {
            aeSecureDownload::get($file, 'settings/');
        }

        if (!is_file($file)) {
            die(sprintf('Sorry, the file [%s] is missing', basename($file)));
        }

        if (file_exists($filename)) {
            $json = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

            $sha = md5_file($filename);

            if (!(isset($json[$sha]))) {
                $json[$sha] = 1;
            }

            asort($json);

            // Output the file with all hashes
            $fp = fopen($file, 'w');
            fwrite($fp, json_encode($json, JSON_THROW_ON_ERROR));
            fclose($fp);
            unset($fp);
        }

        return -1;
    }

    /**
     * Process the action (like running the scan or displaying the progress bar).
     */
    public function Process()
    {
        if (!is_file($file = DIR . DS . self::PATTERN)) {
            aeSecureDownload::get($file, 'settings/');
        }

        if (!is_file($file)) {
            die(sprintf('Sorry, the file [%s] is missing', basename($file)));
        }

        $this->_arrRegex = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

        // Process the task if any, from POST or GET depending on the debug mode state
        // Get the folder to process
        $this->_directory = DIR;
        if ($this->aeSession->get('Expert', EXPERT)) {
            $this->_directory = trim((string) $this->aeSession->get('folder', DIR));
            if ('' == $this->_directory) {
                $this->_directory = DIR;
            }
        }

        $task = aeSecureFct::getParam('task', 'string', '');

        if ('' !== $task) {
            switch ($task) {
                case 'checkwhitelist': {
                    // Detect the existence of the whitelist.json file
                    $filename = DIR . DS . self::WHITELIST;
                    echo file_exists($filename) ? 1 : 0;
                    die();

                    break;
                }

                case 'byebye': {
                    // Don't kill files in demo mode, simulate that everything was ok (return -1)
                    if (DEMO) {
                        die(-1);
                    }

                    // keepwhitelist is a variable posted by the Ajax request and will
                    // inform if the script should or not remove the user's whitelist file.
                    $bKeepWhiteList = aeSecureFct::getParam('keepwhitelist', 'boolean', true);

                    if (true !== $this->aeSession->get('Debug', DEBUG)) {
                        // Get the list of aeSecure QuickScan JSON
                        // Need to use "DIR . DS" so filenames will be absolute which is needed
                        $arrDeleteFiles = glob(DIR . DS . 'aesecure_quickscan_*.json');

                        // And don't scan this script also
                        $arrDeleteFiles[] = DIR . DS . FILE;

                        // Kill the debug log file if present
                        if (null !== $this->aeLog) {
                            $arrDeleteFiles[] = $this->aeLog->filename();
                        }

                        foreach ($arrDeleteFiles as $filename) {
                            $bDelete = true;

                            if ($filename == DIR . DS . self::WHITELIST) {
                                // If $bKeepWhiteList=true, we can't delete the file
                                $bDelete = !$bKeepWhiteList;
                            }

                            if ($bDelete) {
                                if (is_file($filename) && is_readable($filename)) {
                                    unlink($filename);
                                }
                            }
                        }
                    }

                    die('<div class="alert alert-info" role="alert">' .
                        '<strong>Success</strong> The file ' . FILE .
                        ' has been removed from the server.</div>');

                    break;
                }

                case 'doscan': {
                    die($this->doScan());

                    break;
                }

                case 'getcountfiles': {
                    die($this->getCountFiles());

                    break;
                }

                case 'killfile': {
                    // Don't kill files in demo mode, simulate that everything was ok (return -1)
                    if (DEMO) {
                        die(-1);
                    }

                    $filename = base64_decode((string) aeSecureFct::getParam('filename', 'string', ''));

                    if ('' != $filename) {
                        die($this->aeFiles->KillFile($filename));
                    }

                    break;
                }

                case 'seedebug': {
                    $filename = $this->aeLog->filename();
                    if (file_exists($filename)) {
                        $src = htmlentities((string) $this->aeFiles->SeeFile($filename));
                    } else {
                        $src = 'File ' . $filename . ' not found';
                    }
                    die('<pre>' . $src . '</pre>');

                    break;
                }

                case 'seefile': {
                    if (DEMO) {
                        // Don't return source code in DEMO mode
                        echo '<div class="alert alert-warning" role="alert">' .
                            '<strong>Demo mode</strong>&nbsp;This functionnality is not enabled ' .
                            'during the demo mode; sorry.</div>';
                        die();
                    }

                    $filename = base64_decode((string) aeSecureFct::getParam('filename', 'string', ''));
                    $src      = htmlentities((string) $this->aeFiles->SeeFile($filename));

                    // Highlight patterns in the file source code
                    foreach ($this->_arrRegex as $regex) {
                        $arrMatch = [];

                        preg_match_all('/' . $regex['pattern'] . '/im', $src, $arrMatch, PREG_OFFSET_CAPTURE);

                        // Something found? Greater than zero means; yes, the regex has been matched.
                        if (count($arrMatch[0]) > 0) {
                            $disclaimer = (key_exists('disclaimer', $regex)
                                ? $this->getDisclaimerText($regex['disclaimer'])
                                : '');

                            $patternFound = count($arrMatch[1]);

                            for ($i = 0; $i < $patternFound; $i++) {
                                // Get the found keyword (f.i. AnonGhost then, in the second loop,
                                // bash_history in our example)
                                $keyword = (isset($arrMatch[1][$i][0])
                                    ? $keyword = $arrMatch[1][$i][0]
                                    : '');

                                if (!in_array($arrMatch[0], [null, ''])) {
                                    $src = str_replace($keyword, '<span class="blink alert ' .
                                        'alert-danger text-danger highlight" role="alert" title="' .
                                        $disclaimer . '">' . $keyword . '</span>', $src);
                                }
                            }
                        }
                    }

                    $src = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr"><head>' .
                        '<meta charset="utf-8" />' .
                        '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' .
                        '<meta http-equiv="X-UA-Compatible" content="IE=edge" />' .
                        '<meta name="robots" content="noindex, nofollow" />' .
                        '<meta name="viewport" content="width=device-width, initial-scale=1" />' .
                        '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" />' .
                        '<title>aeSecure QuickScan | ' . basename($filename) . '</title>' .
                        '<link href="https://www.avonture.be/images/medias/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon"/>' .
                        '<style type="text/css">' .
                            '.highlight {border-bottom:dotted;color:red;padding:0px;font-weight:bolder;}' .
                            '.blink {animation: blink 1s steps(5, start) infinite; -webkit-animation: blink 1s steps(5, start) infinite; } @keyframes blink {to {visibility: hidden;}} @-webkit-keyframes blink {to {visibility: hidden;}}' .
                            '.footer{padding-top:50px;padding-left:10px;}' .
                            '.fanpage{color:rgb(255,158,158);}' .
                            '.cleanforyou, a.cleanforyou{color:green;text-decoration:none;}' .
                        '</style>' .
                        '</head>' .
                        '<body>' .
                        '<div class="container-fluid" role="main">' .
                        '<h1>' . $filename . '</h1><pre><code>' . $src . '</code></pre></div>' .
                        self::getHTMLFooter() .
                        '</body></html>';

                    die($src);

                    break;
                }

                case 'progress': {
                    die($this->aeProgress->getProgress());

                    break;
                }

                case 'cleansite': {
                    die($this->doCleanSite());

                    break;
                }

                case 'chgfolder': {
                    // Get the CMS if any
                    [$CMS, $CMSFullVersion, $CMSMainVersion, $CMSVersion, $SiteRoot] = aeSecureCMS::getInfo($this->_directory);

                    // reset the list of files to be sure that the scanner will process them again
                    $this->aeSession->set('arrFiles', null);
                    $this->aeSession->set('folder', null);

                    header('Content-Type: application/json');
                    header('Cache-Control: no-cache');
                    echo json_encode(
                        [
                            'CMS' => $CMS . ' ' . $CMSFullVersion,
                        ],
                        JSON_THROW_ON_ERROR
                    );
                    die('');

                    break;
                }

                case 'whitelist': {
                    $filename = base64_decode((string) aeSecureFct::getParam('filename', 'string', ''));

                    die($this->WhiteList($filename));

                    break;
                }

                default: {
                    die('<div class="alert alert-danger" role="alert"><strong>Invalid call</strong>' .
                        'Sorry, the call to ' . FILE . ' is invalid.</div>');
                }
            }
        }

        // No given task given, display the HTML page
    }

    /**
     * Download from avonture.be a json file with the hash of natives files of,
     * f.i., Joomla 3.9.0. If the download is successfull, the downloaded file will be stored
     * in the same folder than this script, name : aesecure_quickscan_CMS.json. This file will be killed
     * when the user will click on the "Kill this script" button available on the user's form.
     *
     * @param string $CMS     f.i. "Joomla"
     * @param string $version f.i. "2.5.27"
     *
     * @return array array of hashes like below.  All these hashes are native files => no scan needed
     *               array(3707) {
     *               ["a62f6525e7418dc679ad1c6c2ebe662dc67207cb"]=> int(1)
     *               ["43ae5dae1df4bec4ecb2879146518283f55eab67"]=> int(1)
     *               ["9b0661ab4d6d640ef8275995dfc2830c974af781"]=> int(1)
     *               ["6e71c8f8ba7b9318d57773c4cdb60e98ffb43eb0"]=> int(1)
     */
    public function gethashes(string $CMS, string $version): array
    {
        if (null == $CMS) {
            return [null, null];
        }

        $file = DIR . DS . self::SUPPORTED_CMS;
        if (!is_file($file)) {
            return [null, null];
        }

        $arrCMS = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

        // arrCMS is an array like this :
        //  ['joomla']=array("prefix"=>"J!","archives"=>"http://")
        //  One entry by CMS with an array with, at least, "prefix" and "archives"
        $prefix = $arrCMS[strtolower($CMS)]['prefix'];

        // Build the name of the JSON file with whitelisted hash for the CMS found on the system
        // Build a filename like c:/site/hacked/aesecure_quickscan_J!3.9.0.json
        $json = DIR . DS . sprintf(self::CMS, $prefix . $version);
        // Pascal : recherche dans repertoire hashes :
        // $json = DIR . DS . 'hashes'. DS.strtolower($CMS).DS.$prefix . $version.'.json';

        // If the file has zero byte, remove it, not normal.

        // @TODO : file_exists while raise a fatal error when there is an open_basedir
        // restriction and when aeSecure QuickScan has been stored in the website rootfolder
        // => by searching one folder above, open_basedir will block the request and will return
        // an empty page
        if ((null != $json) && file_exists($json) && (0 == filesize($json))) {
            @unlink($json);
        }

        if (!file_exists($json)) {
            // Try to download the JSON for the CMS and the version found
            // On GitHub the file with CMS hashes doesn't start with the
            // "aesecure_quickscan_" prefix
            $json = rtrim(dirname($json), DS) . DS .
                str_replace('aesecure_quickscan_', '', basename($json));

            aeSecureDownload::get($json, 'hashes/' . strtolower($CMS) . '/');

            if (file_exists($json)) {
                // rename the file from f.i. "J!3.9.0.json" to "aesecure_quickscan_J!3.9.0.json"
                // so all Quickscan files are using the "aesecure_quickscan_" prefix
                $old  = $json;
                $json = rtrim(dirname($old), DS) . DS . 'aesecure_quickscan_' . basename($old);
                rename($old, $json);
            }
        }

        // Do we have the json file with all hashes?
        // Created just now or already there from a previous run?
        $arrHashes = [];

        if (file_exists($json)) {
            $arrHashes = json_decode(file_get_contents($json), true, 512, JSON_THROW_ON_ERROR);
        }

        return [$arrHashes];
    }

    public function getHTMLFooter(): string
    {
        $aeLanguage = aeSecureLanguage::getInstance();

        return '<footer class="footer">' .
         '&copy; aeSecure 2013-' . date('Y') . ' - AVONTURE Christophe | <span style="font-style:italic;">' .
         '<a href="' . $aeLanguage->get('QUICKSCANURL') . '" target="_blank">aeSecure QuickScan v.' . VERSION . '</a>' .
         '<br/>' .
         '<span class="glyphicon glyphicon-heart fanpage" style="min-width:16px;"></span>' .
         '<a href="https://www.facebook.com/aesecure" target="_blank">Fanpage</a></span> | ' .
         '<span class="glyphicon glyphicon-screenshot cleanforyou" style="min-width:16px;"></span>' .
         '<a class="cleanforyou" href="' . $aeLanguage->get('CLEANING_URL') . '" target="_blank">' . $aeLanguage->get('CLEANFORYOU') . '</a></span>' .
        '</footer>';
    }

    public function getCountPatterns(): int
    {
        return count($this->_arrRegex);
    }

    /**
     * Clean the site by emptying the cache folders and temporary folders.
     *
     * @return type
     */
    private function doCleanSite(): void
    {
        $output = '';

        // Try to clean these folders
        $arr = [
            'administrator' . DS . 'cache',
            'aesecure' . DS . 'cache',
            'aesecure' . DS . 'tmp',
            'cache',
            'temp'
        ];

        foreach ($arr as $tmp) {
            if (is_dir($folder = rtrim((string) $this->_directory, DS) . DS . $tmp)) {
                if (!DEMO) {
                    $this->aeFiles->rrmdir(
                        $dir            = $folder,
                        $killroot       = false,
                        $arrIgnoreFiles = ['.htaccess', 'index.html']
                    );
                }
                // Don't give full path in demo mode
                if (DEMO) {
                    $folder = str_replace(DIR, '', $folder);
                }
                $output .= '<li><span class="glyphicon glyphicon-thumbs-up">&nbsp;</span>' .
                    sprintf($this->aeLanguage->get('CLEANFOLDER'), $folder) . '</li>';
            }
        }

        echo '<ul class="list-unstyled text-success">' . $output . '</ul>';
    }

    /**
     * Read the json files with hashes (whitelist, other and blacklist) and initialize arrays
     * This function is called by the GetCountFiles() and doScan() functions.
     */
    private function initializeHashes(): bool
    {
        // Try to determine the CMS used and, if found one, try to get a json file
        // with hashes of native files. If found, this is a tremendous news since
        // these files are known as safe (native ones!) so should not be scanned
        // when the file present on the website has the same hash meaning that this
        // file was never altered at all.
        [$CMS, $CMSFullVersion, $CMSMainVersion, $CMSVersion, $SiteRoot] = aeSecureCMS::getInfo($this->_directory);

        [$this->_arrCMSHashes] = $this->gethashes($CMS, $CMSVersion);

        // Get whitelist.json and other.json
        for ($i = 0; $i < 2; $i++) {
            $file = (0 == $i ? self::WHITELIST : self::OTHER);

            $arr = [];

            if (file_exists(DIR . DS . $file)) {
                $arr = json_decode(file_get_contents(DIR . DS . $file), true, 512, JSON_THROW_ON_ERROR);
            } else {
                // Try to download the file

                $json = DIR . DS . $file;

                // Download the whitelist file if possible
                if (!file_exists($json)) {
                    aeSecureDownload::get($json, 'settings/');
                }

                // If the file has zero byte, delete it, the download was not successfull
                try {
                    if (file_exists($json) && (0 == filesize($json))) {
                        unlink($json);
                    }
                } catch (Exception $ex) {
                }

                if (file_exists($json)) {
                    $arr = json_decode(file_get_contents($json), true, 512, JSON_THROW_ON_ERROR);
                }
            }

            if (0 == $i) {
                $this->_arrWhiteListHashes = $arr;
            } else {
                $this->_arrOtherHashes = $arr;
            }
        }

        // Check if there is a blacklist hash file
        if (!file_exists($file = DIR . DS . self::BLACKLIST)) {
            aeSecureDownload::get($file, 'settings/');
        }

        // If so, load the file
        if (file_exists($file)) {
            $this->_arrBlackListHashes = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        }

        // Check if there is a edited hash file
        if (!file_exists($file = DIR . DS . self::EDITED)) {
            aeSecureDownload::get($file, 'settings/');
        }

        // If so, load the file
        if (file_exists($file)) {
            $this->_arrEditedHashes = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        }

        return true;
    }

    /**
     * Scan the disk and search for each files that will be then processed by the scanner.
     * This function will initialize the arrFiles session variable.
     */
    private function getCountFiles(mixed $echo = true, mixed &$arrFiles = null): bool
    {
        try {
            clearstatcache();

            if (!get_cfg_var('safe_mode')) {
                // set_time_limit isn't used when safe_mode is active
                // No max execution time
                @ini_set('max_execution_time', '0');
                // Remove time limit; avoid 504 HTTP errors
                @ini_set('set_time_limit', '0');
            }
        } catch (Exception $e) {
        }

        // Allocate the maximum allowed memory to the script (-1 = no limit)
        @ini_set('memory_limit', (true !== $this->aeSession->get('Debug', DEBUG)) ? -1 : MEMORY_LIMIT);

        if (!is_dir($this->_directory)) {
            echo '<hr/><div class="alert alert-danger" role="alert"><strong>' .
                sprintf(DIRNOTFOUND, $this->_directory) . '</strong></div>';

            return false;
        }

        $previousFolder = $this->aeSession->get('Folder', null);

        $arrFiles        = [];
        $wNbrBlacklisted = 0;
        $wNbrEdited      = 0;
        $wNbrSkipped     = 0;
        $wNbrWhitelisted = 0;

        if ((null == $arrFiles) || (0 == count($arrFiles))) {
            // The "arrFiles" session variable is either not found or equal to NULL ==>
            // Get the list of files in the current folder and subfolders by scanning
            // files and folders on the disk
            $dir   = new RecursiveDirectoryIterator($this->_directory, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::LEAVES_ONLY);

            // Release a few memory
            unset($dir);

            $arrFiles = [];

            // Collect all files but ignore this script
            $IgnoreArchives    = $this->aeSession->get('IgnoreArchives', true);
            $IgnoreDocuments   = $this->aeSession->get('IgnoreDocuments', true);
            $IgnoreFonts       = $this->aeSession->get('IgnoreFonts', true);
            $IgnoreImages      = $this->aeSession->get('IgnoreImages', true);
            $IgnoreMedia       = $this->aeSession->get('IgnoreMedia', true);
            $IgnoreSoundMovies = $this->aeSession->get('IgnoreSoundMovies', true);
            $IgnoreText        = $this->aeSession->get('IgnoreText', true);

            // Prepare the arrays with extensions, by category
            $arrArchive     = explode(',', str_replace(' ', '', ExtArchives));
            $arrDocuments   = explode(',', str_replace(' ', '', ExtDocuments));
            $arrFonts       = explode(',', str_replace(' ', '', ExtFonts));
            $arrImages      = explode(',', str_replace(' ', '', ExtImages));
            $arrMedias      = explode(',', str_replace(' ', '', ExtMedia));
            $arrSoundMovies = explode(',', str_replace(' ', '', ExtSoundMovies));
            $arrText        = explode(',', str_replace(' ', '', ExtText));

            // Initialize arrays with hashes
            self::initializeHashes();

            // Don't scan aeSecure QuickScan files
            // Need to use "DIR . DS" so filenames will be absolute which is needed
            $arrSkipFiles = glob(DIR . DS . 'aesecure_quickscan_*.json');

            // And don't scan this script also
            $arrSkipFiles[] = DIR . DS . FILE;

            foreach ($files as $filename => $object) {
                // Don't process these files
                if (in_array($filename, $arrSkipFiles)) {
                    continue;
                }

                try {
                    // Only files
                    if (is_file($filename)) {
                        $md5 = md5_file($filename);

                        if (isset($this->_arrBlackListHashes[$md5])) {
                            // Already known as bad?
                            $arrFiles[] = $filename;
                            ++$wNbrBlacklisted;
                        } elseif (isset($this->_arrEditedHashes[$md5])) {
                            // Already known as having a virus in it?
                            $arrFiles[] = $filename;
                            ++$wNbrEdited;
                        } elseif (isset($this->_arrCMSHashes[$md5]) || isset($this->_arrWhiteListHashes[$md5]) || isset($this->_arrOtherHashes[$md5])) {
                            // if the hash of file is listed in the CMS core file,
                            // white list or other hashes, don't process it, the file is safe
                            ++$wNbrWhitelisted;
                        } else {
                            // Get file's extension
                            $ext = $object->getExtension();

                            $bAdd = true;

                            if (($IgnoreArchives) && (in_array($ext, $arrArchive))) {
                                $bAdd = false;
                            }
                            if (($IgnoreDocuments) && (in_array($ext, $arrDocuments))) {
                                $bAdd = false;
                            }
                            if (($IgnoreFonts) && (in_array($ext, $arrFonts))) {
                                $bAdd = false;
                            }
                            if (($IgnoreImages) && (in_array($ext, $arrImages))) {
                                $bAdd = false;
                            }
                            if (($IgnoreMedia) && (in_array($ext, $arrMedias))) {
                                $bAdd = false;
                            }
                            if (($IgnoreSoundMovies) && (in_array($ext, $arrSoundMovies))) {
                                $bAdd = false;
                            }
                            if (($IgnoreText) && (in_array($ext, $arrText))) {
                                $bAdd = false;
                            }

                            if (true === $bAdd) {
                                $arrFiles[] = $filename;
                            } else {
                                ++$wNbrSkipped;
                            }
                        }
                    }
                } catch (Exception $ex) {
                    if (true === $this->aeSession->get('Debug', DEBUG)) {
                        echo '<pre>' . print_r($ex, true) . '</pre>';
                    }
                }
            }

            unset($arrSkipFiles);

            // Put the array in a session variable and remember the processed folder
            $this->aeSession->set('Folder', $this->_directory);
            $this->aeSession->set('arrFiles', json_encode($arrFiles, JSON_THROW_ON_ERROR));
        } else {
            // The user is running the script once more for the same folder
            // ==> don't scan the disk again, just user the session variable to speed up the process

            $arrFiles = json_decode((string) $arrFiles, null, 512, JSON_THROW_ON_ERROR);
        }

        if (null == $arrFiles) {
            $arrFiles = json_decode((string) $this->aeSession->get('arrFiles', null), null, 512, JSON_THROW_ON_ERROR);
        }

        unset($this->arrOtherHashes, $this->arrWhiteListHashes, $this->arrBlackListHashes, $this->arrCMSHashes);

        if (true == $echo) {
            try {
                header('Content-Type: application/json');
                header('Cache-Control: no-cache');
            } catch (\Exception $exception) {
            }

            echo json_encode(
                [
                    'count'       => count($arrFiles),
                    'whitelisted' => $wNbrWhitelisted,
                    'blacklisted' => $wNbrBlacklisted,
                    'edited'      => $wNbrEdited,
                    'skipped'     => $wNbrSkipped,
                ],
                JSON_THROW_ON_ERROR
            );

            // Prevent a warning; flush only if there is something to flush
            try {
                while (ob_get_level() > 0) {
                    ob_end_flush();
                }

                flush();
            } catch (\Exception $e) {
            }

            die();
        } else {
            return true;
        }
    }

    /**
     * Start the scan.
     *
     * @global type $sLogFile
     * @global type $sProgressFile
     */
    private function doScan(): string|bool
    {
        $aeLanguage = aeSecureLanguage::getInstance();

        try {
            if (!get_cfg_var('safe_mode')) {
                // set_time_limit isn't used when safe_mode is active
                // No max execution time
                @ini_set('max_execution_time', '0');
                // Remove time limit; avoid 504 HTTP errors
                @ini_set('set_time_limit', '0');
            }
        } catch (Exception $e) {
        }

        // Allocate the maximum allowed memory to the script (-1 = no limit)
        @ini_set('memory_limit', (true !== $this->aeSession->get('Debug', DEBUG)) ? -1 : MEMORY_LIMIT);

        $wFile          = 0;
        $wCount         = 0;
        $wFound         = 0;
        $wProcessedFile = 0;
        $wSkipSize      = 0;
        $wSkipChmod     = 0;
        $wSkipHashes    = 0;
        $wUnreadable    = 0;

        if (!is_dir($this->_directory)) {
            echo '<hr/><div class="alert alert-danger" role="alert"><strong>' .
                sprintf(DIRNOTFOUND, $this->_directory) . '</strong></div>';

            return false;
        }

        $previousFolder = $this->aeSession->get('Folder', null);

        // If the folder isn't the same, clear the arrFiles session variable
        if ((null == $previousFolder) || ($previousFolder != $this->_directory)) {
            $this->aeSession->set('arrFiles', null);
        }

        $arrFiles = $this->aeSession->get('arrFiles', null);
        if (null == $arrFiles) {
            $this->getCountFiles($echo = false, $arrFiles);
        }

        $arrFiles = json_decode((string) $this->aeSession->get('arrFiles', null), null, 512, JSON_THROW_ON_ERROR);

        $wCount   = count($arrFiles);

        // $this->_end can be 0 => process all files
        if ((0 == $this->_end) || ($this->_end > $wCount)) {
            $this->_end = $wCount;
        }

        $bFound = false;
        $output = '';

        // Initialize arrays with hashes
        self::initializeHashes();

        // Start the scan, process all files
        if (($wCount = count($arrFiles)) > 0) {
            if (true === $this->aeSession->get('Debug', DEBUG)) {
                // Reset the log file
                $this->aeLog->kill();
                $this->aeLog->addLog('#' . $wCount . " files to process\n");
            }

            $output = '<ol>';

            $this->aeProgress->setStart(0);
            $this->aeProgress->setEnd($this->_end - $this->_start);

            for ($wFile = $this->_start; $wFile < $this->_end; $wFile++) {
                $filename = $arrFiles[$wFile];

                // Skip aesecure_quickscan to avoid a ton of false positive.
                // aesecure_quickscan.php is supposed to be just download,
                // installed on the website and thus supposed to be clean.
                // Once aeSecure QuickScan has finished his job, the script
                // is supposed to be deleted so, yes, to avoid a ton of false
                // positive, don't scan this script.
                if (FILE == (basename((string) $filename))) {
                    continue;
                }

                if (!file_exists($filename)) {
                    continue;
                }

                ++$wProcessedFile;

                // Consider the file safe
                $bInfected = false;

                $this->aeProgress->incTaskCount();

                $output_line = '';

                if (is_readable($filename)) {
                    // Empty file
                    if (0 == filesize($filename)) {
                        if (true === $this->aeSession->get('Debug', DEBUG)) {
                            $this->aeLog->addLog('Scanning #' . ($wFile + 1) . '. ' . $filename .
                                '   SKIP;   Filesize=0');
                        }

                        continue;
                    }

                    if (filesize($filename) <= MAX_SIZE) {
                        $md5 = md5_file($filename);

                        // Check if the file was never altered.  If yes, great news,
                        // the processing of that file can be avoided => faster
                        // Be sure that the hash isn't in the blacklist; for security.
                        if (!isset($this->_arrBlackListHashes[$md5])) {
                            if (isset($this->_arrCMSHashes[$md5])) {
                                if (FULLDEBUG && !aeSecureFct::isAjaxRequest()) {
                                    echo sprintf(
                                        '%s is an original CMS file; not altered thus safe<br/>',
                                        $filename
                                    );
                                }

                                ++$wSkipHashes;
                                if (true === $this->aeSession->get('Debug', DEBUG)) {
                                    $this->aeLog->addLog('Scanning #' . ($wFile + 1) . '. ' .
                                        $filename . '   SKIP;   Original CMS file');
                                }

                                continue;
                            }

                            // Check if the file is a white listed one or found in
                            // the Other hashes (also white listed)
                            if (isset($this->_arrWhiteListHashes[$md5]) || isset($this->_arrOtherHashes[$md5])) {
                                ++$wSkipHashes;

                                if (FULLDEBUG && !aeSecureFct::isAjaxRequest()) {
                                    echo sprintf(
                                        '%s is whitelisted, healthy file<br/>',
                                        $filename
                                    );
                                }

                                if (true === $this->aeSession->get('Debug', DEBUG)) {
                                    $this->aeLog->addLog('Scanning #' . ($wFile + 1) . '. ' . $filename .
                                        '   SKIP;   Hash whitelisted' .
                                        (isset($this->_arrOtherHashes[$md5])
                                        ? ' in the other.json file'
                                        : ''));
                                }

                                continue;
                            }
                        }

                        $OutputTemplate =
                            '<li>' .
                                '<span class="newline">' .
                                    '<span class="filename">' . (DEMO ? str_replace(DIR, '', (string) $filename) : $filename) . '</span>' .  // filename
                                    '<span class="filesize">(' . aeSecureFct::human_filesize(filesize($filename)) . ')</span>' . // filesize
                                    '<span class="filedate">(' . $aeLanguage->get('LASTMOD') . ' ' . date('F d Y H:i:s.', filemtime($filename)) . ')</span>' . // filedate
                                '</span>' .
                                '<span class="md5" title="MD5" data-toggle="popover" data-content="' . $aeLanguage->get('MD5') . '">' . $md5 . '</span>' .
                                '$FOUND$' .
                            '</li>';

                        // Check if the file is a black listed one
                        if (isset($this->_arrBlackListHashes[$md5])) {
                            // The file being scanned is blacklisted
                            $bInfected = true;
                            $bFound    = true;

                            $FOUND =
                                '<span class="label label-danger blink">' . $aeLanguage->get('DANGER') . '</span>&nbsp;' . $aeLanguage->get('BLACKLISTED') .
                                '<span class="newline"><pre>' . trim(str_replace('<', '&lt;', file_get_contents($filename))) . '</pre>' .
                                '</span>';

                            $output_line = str_replace('$FOUND$', $FOUND, $OutputTemplate);

                            if (FULLDEBUG && !aeSecureFct::isAjaxRequest()) {
                                echo sprintf(
                                    '%s IS BLACKLISTED, VIRUS FOUND<br/>',
                                    $filename
                                );
                            }

                            if (true === $this->aeSession->get('Debug', DEBUG)) {
                                $this->aeLog->addLog('Scanning #' . ($wFile + 1) . '. ' . $filename .
                                    '. This file is in the blacklist');
                            }
                        }

                        // Check if the file contains a virus
                        if (isset($this->_arrEditedHashes[$md5])) {
                            // The file being scanned contains a virus
                            $bInfected = true;
                            $bFound    = true;

                            $FOUND =
                                '<span class="label label-danger blink">' . $aeLanguage->get('DANGER') . '</span>&nbsp;' . $aeLanguage->get('EDITED') .
                                '<span class="newline"><pre style="white-space: pre-wrap;">' . trim(str_replace('<', '&lt;', file_get_contents($filename))) . '</pre>' .
                                '</span>';

                            $output_line = str_replace('$FOUND$', $FOUND, $OutputTemplate);

                            if (FULLDEBUG && !aeSecureFct::isAjaxRequest()) {
                                echo sprintf(
                                    '%s CONTAINS A VIRUS<br/>',
                                    $filename
                                );
                            }

                            if (true === $this->aeSession->get('Debug', DEBUG)) {
                                $this->aeLog->addLog('Scanning #' . ($wFile + 1) . '. ' .
                                    $filename . '. This file is in the edited list i.e. ' .
                                    'contains a known virus');
                            }
                        }

                        if (false == $bInfected) {
                            $content = '';

                            try {
                                $content = file_get_contents($filename);
                            } catch (Exception $ex) {
                                ++$wUnreadable;

                                if (true === $this->aeSession->get('Debug', DEBUG)) {
                                    $this->aeLog->addLog('Scanning #' . ($wFile + 1) . '. ' .
                                        $filename . '   SKIP;   Unreadable content');
                                }

                                $bFound      = true;
                                $output_line = '<li>' .
                                    '<span class="label label-warning">' . $aeLanguage->get('WARNING') . '</span>&nbsp;' .
                                    '<span class="filename">' . (DEMO ? str_replace(DIR, '', (string) $filename) : $filename) . '</span>' .
                                    ($aeLanguage->get('SHOWMD5') ? '<span class="md5" title="MD5" data-toggle="popover" data-content="' . $aeLanguage->get('MD5') . '">' . md5_file($filename) . '</span>' : '') .
                                    '<span class="disclaimer text-info newline">' . $aeLanguage->get('UNREADABLE') . '</span>' .
                                    '</li>';
                            }

                            if ('' != $content) {
                                $FOUND = '';

                                // Template on how the file's informations should be reported
                                // $FOUND$ will be replaced by every occurences found in the
                                // file (severall occurences are indeed possible)

                                if (true === $this->aeSession->get('Debug', DEBUG)) {
                                    $this->aeLog->addLog('Scanning #' . ($wFile + 1) . '. ' .
                                        $filename . '   Processing... (filesize=' .
                                        aeSecureFct::human_filesize(filesize($filename)) . ')');
                                }

                                // Process every regex for the processing file
                                foreach ($this->_arrRegex as $regex) {
                                    if (FULLDEBUG === true) {
                                        $this->aeLog->addLog('   scan pattern ' . $regex['pattern']);
                                    }

                                    $arrMatch = [];

                                    try {
                                        preg_match_all('/' . $regex['pattern'] . '/im', $content, $arrMatch, PREG_OFFSET_CAPTURE);
                                    } catch (Exception $ex) {
                                        if (true === $this->aeSession->get('Debug', DEBUG)) {
                                            echo '<h5>' . __LINE__ . '  EXCEPTION ENCOUNTERED = ' . $ex->getMessage() . '</h5>';
                                        }
                                    }

                                    // Something found?  Greater than zero means; yes, the regex
                                    // has been matched.
                                    if (is_array($arrMatch) && (count($arrMatch) > 0)) {
                                        if (count($arrMatch[0]) > 0) {
                                            // Something has been found in that file.
                                            $bInfected = true;

                                            // The regex is always composed of three things
                                            //          1.     2.      3.
                                            //        (.*)(VIRUS_CODE)(.*)
                                            //
                                            // (1) Something before followed by the virus code
                                            // (2) followed by something
                                            // (3) so the regex match f.i. the entire line
                                            // and not only the keyword (2)
                                            //
                                            // When the regex is something of severall codes like
                                            //     1.              2.          3.
                                            //    (.*)(AnonGhost|bash_history)(.*)
                                            //
                                            // the $arrMatch[2] position will return the number
                                            // of matches so if these two words are found in the file
                                            // $patternFound will be set to two and the code below
                                            // will process these two matches
                                            $patternFound = count($arrMatch[1]);

                                            // Process every matches
                                            for ($i = 0; $i < $patternFound; $i++) {
                                                // Get the found keyword (f.i. AnonGhost then, in the
                                                // second loop, bash_history in our example)
                                                $keyword = (isset($arrMatch[1][$i][0])
                                                ? $keyword = $arrMatch[1][$i][0]
                                                : '');

                                                // Get the full line where the keyword was found
                                                $code = $arrMatch[0][$i][0];

                                                // And get the position in the file (start position of
                                                // the keyword)
                                                $position = $arrMatch[1][$i][1];

                                                // When outputting the result, get the context i.e. a
                                                // specific number of characters before the found pattern
                                                // and the same number of characters after so QuickScan
                                                // can display the portion of code where this suspicious
                                                // pattern is found, making easier to read and determine
                                                // the dangerosity of the code
                                                $sContext = '';

                                                try {
                                                    $wStart   = ($position > CONTEXT_NBRCHARS ? $position - CONTEXT_NBRCHARS : 0);
                                                    $wEnd     = ($position + strlen($keyword) + CONTEXT_NBRCHARS) - $wStart;
                                                    $sContext = substr($content, $wStart, $wEnd);
                                                } catch (Exception $e) {
                                                    if (true === $this->aeSession->get('Debug', DEBUG)) {
                                                        echo '<h5>' . __LINE__ . '  EXCEPTION ENCOUNTERED = ' . $e->getMessage() . '</h5>';
                                                    }
                                                }

                                                if (!in_array($arrMatch[0], [null, ''])) {
                                                    $bFound = true;

                                                    if (FULLDEBUG && !aeSecureFct::isAjaxRequest()) {
                                                        echo sprintf(
                                                            '%s contains [%s] risk [%s], so needs to be ' .
                                                            'analyzed<br/>',
                                                            $filename,
                                                            $regex['risk'],
                                                            $keyword
                                                        );
                                                    }

                                                    $disclaimer = (key_exists('disclaimer', $regex) ? $this->getDisclaimerText($regex['disclaimer']) : '');

                                                    $FOUND .=
                                                        '<span class="label label-' . $regex['risk'] . '">' . ('danger' == $regex['risk'] ? $this->aeLanguage->get('DANGER') : ('warning' == $regex['risk'] ? $this->aeLanguage->get('WARNING') : $this->aeLanguage->get('INFO'))) . '</span>&nbsp;' . $disclaimer .
                                                        '<span class="regex newline" title="regex = ' . $regex['pattern'] . '">' . $this->aeLanguage->get('PATTERN') . ' : <strong>' . $keyword . '</strong></span>' .
                                                        '<span class="position newline">' . sprintf($this->aeLanguage->get('FOUNDPOSITION'), $position) . '</strong></span>' .
                                                        '<span class="regexresult"><pre>' . str_replace(
                                                            $keyword,
                                                            '<strong class="double_underline" style="color:red;" data-html="true" ' .
                                                            'data-toggle="popover" data-content="<span class=\'text-' . $regex['risk'] . ' ' . $regex['risk'] . '\'>' .
                                                            $disclaimer . '</span>">' . $keyword . '</strong>',
                                                            trim(str_replace('<', '&lt;', $sContext))
                                                        ) . '</pre>' .
                                                        '</span>';
                                                }
                                            }
                                        }
                                    }

                                    unset($arrMatch);
                                }

                                if ($bInfected) {
                                    $output_line = str_replace('$FOUND$', $FOUND, $OutputTemplate);
                                }

                                unset($content);
                            }
                        }

                        if ($bInfected) {
                            ++$wFound;
                        }

                        unset($content);
                    } else {
                        ++$wSkipSize;

                        if (true === $this->aeSession->get('Debug', DEBUG)) {
                            $this->aeLog->addLog('Scanning #' . ($wFile + 1) . '. ' .
                                $filename . '   SKIP;   Too big.');
                        }

                        // The filename wasn't whitelisted, show it.
                        $bFound      = true;
                        $output_line = '<li>' .
                            '<span class="label label-warning">' . $this->aeLanguage->get('WARNING') . '</span>&nbsp;' .
                            '<span class="filename">' . (DEMO ? str_replace(DIR, '', (string) $filename) : $filename) . '</span>' .
                            (SHOWMD5 ? '<span class="md5" title="MD5" data-toggle="popover" data-content="' . $this->aeLanguage->get('MD5') . '">' . md5_file($filename) . '</span>' : '') .
                            '<span class="disclaimer text-info newline">' . sprintf($this->aeLanguage->get('TOOBIG'), aeSecureFct::human_filesize(filesize($filename), 0)) . '</span>' .
                            '</li>';
                    }
                } else {
                    ++$wSkipChmod;

                    if (true === $this->aeSession->get('Debug', DEBUG)) {
                        $this->aeLog->addLog('Scanning #' . ($wFile + 1) . '. ' . $filename . '   SKIP;   Chmod too restrictive.');
                    }

                    $bFound      = true;
                    $output_line = '<li>' .
                        '<span class="label label-danger">' . $this->aeLanguage->get('DANGER') . '</span>&nbsp;' .
                        '<span class="filename">' . (DEMO ? str_replace(DIR, '', (string) $filename) : $filename) . '</span>' .
                        (SHOWMD5 ? '<span class="md5" title="MD5" data-toggle="popover" data-content="' . MD5 . '">' . md5_file($filename) . '</span>' : '') .
                        '<span class="disclaimer text-info newline">' . $this->aeLanguage->get('BADCHMOD') . '</span>' .
                        '</li>';
                }

                if ('' != $output_line) {
                    // Add action buttons :
                    //
                    //    1. See the file (only if text file)
                    //    2. Kill the file (only if expert mode enabled)
                    //
                    $isTextType = aeSecureFiles::isTextFileContent($filename);

                    if ($isTextType) {
                        // The "See the source file" button is only displayed for files that are detected with text content and
                        // thus not for images or archive files f.i.
                        $buttonSee = '<button type="button" class="seefile btn btn-success btn-xs" data-toggle="popover" data-html="true" data-old-caption="<span class=\'glyphicon glyphicon-eye-open\'></span>" data-caption="<span class=\'glyphicon glyphicon-eye-open\'>&nbsp;</span>' . $this->aeLanguage->get('SEE_FILE') . '" data-content="' . $this->aeLanguage->get('SEE_FILE_HINT') . '" data-filename="' . base64_encode((string) $filename) . '"><span class="glyphicon glyphicon-eye-open"></span></button>';
                    } else {
                        $buttonSee = '<span style="display:inline-block;min-width:26px;"></span>';
                    }

                    // Add to white list button
                    $buttonWhitelist = '<button type="button" class="whitelist btn ' .
                        'btn-primary btn-xs" data-toggle="popover" data-html="true" ' .
                        'data-old-caption="<span class=\'glyphicon glyphicon-heart\'></span>" ' .
                        'data-caption="<span class=\'glyphicon glyphicon-heart\'>&nbsp;</span>' .
                        $this->aeLanguage->get('WHITE_LIST') . '" data-content="' .
                        $this->aeLanguage->get('WHITE_LIST_HINT') . '" data-filename="' .
                        base64_encode((string) $filename) . '"><span class="glyphicon glyphicon-heart">' .
                        '</span></button>';

                    if (true === $this->aeSession->get('Expert', EXPERT)) {
                        // Kill file button
                        $buttonDelete = '<button type="button" class="killfile btn ' .
                            'btn-danger btn-xs" data-toggle="popover" data-html="true" ' .
                            'data-old-caption="<span class=\'glyphicon glyphicon-trash\'></span>" ' .
                            'data-caption="<span class=\'glyphicon glyphicon-trash\'>&nbsp;</span>' .
                            $this->aeLanguage->get('KILL_FILE') . '" data-content="' .
                            $this->aeLanguage->get('KILL_FILE_HINT') . '" data-filename="' .
                            base64_encode((string) $filename) . '"><span class="glyphicon glyphicon-trash">' .
                            '</span></button>';
                    } else {
                        $buttonDelete = '';
                    }

                    // Add a dismissal div so the user can hide this result
                    $output .= '<div class="alert-dismissible fade in" role="alert">' .
                        '<button type="button" class="close hidefile" data-dismiss="alert" ' .
                        'aria-label="Close" data-toggle="popover" data-content="' .
                        $this->aeLanguage->get('HIDERESULT') . '"><span aria-hidden="true">×</span>' .
                        '</button>' . $output_line . '<span class="newline">' . $buttonSee .
                        ' ' . $buttonWhitelist . ' ' . $buttonDelete .
                        '</span></div>';
                }
            }

            // Release the array
            unset($arrFiles);
        }

        unset($this->arrOtherHashes, $this->arrWhiteListHashes, $this->arrBlackListHashes, $this->arrCMSHashes);

        if (true === $this->aeSession->get('Debug', DEBUG)) {
            $this->aeLog->addLog("\nEND OF SCAN");
        }

        // The scan is now finished
        //
        // --------------------------------------------

        $output .= '</ol>';

        if (false == $bFound) {
            // Nothing found, congrats!
            $output = '<div class="alert alert-success" role="alert"><strong>' .
                $aeLanguage->get('SUCCESS') . '</strong> ' . $aeLanguage->get('NOTHINGFOUND') .
                '</div>' . $output;
        } else {
            // Something has been found
            $output =
             '<div class="stats">' . sprintf($aeLanguage->get('FILESFOUND'), $wProcessedFile, $wFound, str_replace('"', '\'', (string) $aeLanguage->get('POTENTIALLY')), $wSkipSize, aeSecureFct::human_filesize(MAX_SIZE, 0), $wSkipChmod) . '</div>' .
             '<div class="text-info">' . sprintf($aeLanguage->get('VIRUSFOUND'), $aeLanguage->get('HOME'), $aeLanguage->get('HOME')) . '</span><hr style="height:20px;"/></div>' . $output;
        }

        // Add script for popovers
        $output .= '<script>' .
            '$(\'[data-toggle="popover"]\').popover({trigger:\'hover\',placement:\'top\',html:true});' .
            'initButtons();' .
            '</script>';

        $this->aeProgress->clean();

        return $output;
    }
}

// --------------------------
// PHP ENTRY POINT
// --------------------------

$showInfo = '';

aeSecureDebug::setDebugMode(DEBUG);

$aeSession = aeSecureSession::getInstance();
$aeSession->set('Debug', DEBUG);

$lang = str_replace('_', '-', (string) aeSecureFct::getParam('lang', 'string', '', 5));

$aeLanguage = aeSecureLanguage::getInstance($lang);

// SaveSession is the action behind the "Apply" button of the Advanced form
if ('SaveSession' === aeSecureFct::getParam('formTask', 'string', '', strlen('SaveSession'))) {
    $aeSession->set('Expert', (!DEMO ? aeSecureFct::getParam('chkExpert', 'boolean', EXPERT) : false));
    $aeSession->set('Debug', (!DEMO ? aeSecureFct::getParam('chkDebug', 'boolean', DEBUG) : false));

    if (true !== $aeSession->get('Expert', EXPERT)) {
        unset($aeSession);
        $aeSession = aeSecureSession::getInstance(true);
    }

    $aeSession->set('IgnoreArchives', true == aeSecureFct::getParam('chkIgnoreArchives', 'boolean', true) ? 1 : 0);
    $aeSession->set('IgnoreDocuments', true == aeSecureFct::getParam('chkIgnoreDocuments', 'boolean', true) ? 1 : 0);
    $aeSession->set('IgnoreFonts', true == aeSecureFct::getParam('chkIgnoreFonts', 'boolean', true) ? 1 : 0);
    $aeSession->set('IgnoreImages', true == aeSecureFct::getParam('chkIgnoreImages', 'boolean', true) ? 1 : 0);
    $aeSession->set('IgnoreMedia', true == aeSecureFct::getParam('chkIgnoreMedia', 'boolean', true) ? 1 : 0);
    $aeSession->set('IgnoreSoundMovies', true == aeSecureFct::getParam('chkIgnoreSoundMovies', 'boolean', true) ? 1 : 0);
    $aeSession->set('IgnoreText', true == aeSecureFct::getParam('chkIgnoreText', 'boolean', true) ? 1 : 0);

    // Max. number of files by cycle
    // Be sure to have only positive numbers.
    // zero is allowed : no limitation
    $aeSession->set('MaxFilesByCycle', abs(aeSecureFct::getParam('nbFiles', 'integer', MAXFILESBYCYCLE)));
}

$aeScan = new aeSecureScan();

$script = $_SERVER['SCRIPT_FILENAME'];

$aeScan->Process();

$aeLog      = aeSecureLog::getInstance();
$aeProgress = aeSecureProgressBar::getInstance();

// Try to obtain a few informations about the site
[$CMS, $CMSFullVersion, $CMSMainVersion, $CMSVersion, $SiteRoot] = aeSecureCMS::getInfo($aeScan->directory());

// Is it a website made with a supported CMS? (Joomla, WordPress, Drupal, CakePHP,
//  PrestaShop, Magento, ...)
if ('' != $CMS) {
    // Yes so try to retrieve the hash of that CMS and for the installed version
    [$arrCMSHashes] = $aeScan->gethashes($CMS, $CMSVersion);

    $usingHashes = '<strong style="color:%s;height:48px;line-height:48px;vertical-align:center;">' .
        '<span class="glyphicon glyphicon-thumbs-%s"></span>&nbsp;%s</strong>';

    if (null != $arrCMSHashes) {
        // Hashes found
        if (true === $aeSession->get('Expert', EXPERT)) {
            $usingHashes = sprintf($usingHashes, 'yellow', 'up', $aeLanguage->get('USINGHASHESSHORT'));
        }

        $showInfo = '<div class="alert alert-success">' .
            '<strong><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;' .
            $aeLanguage->get('USINGHASHES') . '</strong>&nbsp;' .
            sprintf($aeLanguage->get('USINGHASHESINFO'), $CMS, $CMSVersion) . '</div>';
    } else {
        // No hashes found for that version of the CMS
        if (true === $aeSession->get('Expert', EXPERT)) {
            $usingHashes = sprintf(
                $usingHashes,
                'red',
                'down',
                $aeLanguage->get('NOTUSINGHASHESSHORT')
            );
        }

        $showInfo = '<div class="alert alert-warning">' .
            sprintf($aeLanguage->get('SORRYNOHASHES'), $CMSVersion, $CMS) .
            '</div>';
    }

    // If a CMS has been detected, display the CMS used + version as navbar
    $siteInfos = '<nav class="navbar navbar-inverse navbar-fixed-top"><div class="container-fluid">' .
        '<div class="navbar-header"><span class="navbar-brand">' . $CMS . ' ' . $CMSFullVersion . '</span></div>' .
        '<div class="navbar-right">' . $usingHashes . '</div>' .
        '</div></nav><br/><br/><br/>';
} else {
    $siteInfos = '';
}

// Get the GitHub corner
$github = '';
if (is_file($cat = __DIR__ . DIRECTORY_SEPARATOR . 'octocat.tmpl')) {
    $github = str_replace('%REPO%', REPO, file_get_contents($cat));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head>
        <meta charset="utf-8" />
        <meta name="robots" content="noindex, nofollow" />
        <meta name="author" content="Christophe Avonture" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8;" />
        <meta property="og:title" content="<?php echo $aeLanguage->get('PAGETITLE');?>" />
        <meta property="og:description" content="<?php echo $aeLanguage->get('DESCRIPTION');?>" />
        <meta property="og:image" content="aesecure.png" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title><?php echo $aeLanguage->get('PAGETITLE');?> | AVONTURE Christophe - www.avonture.be</title>

        <link href="favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon"/>
        <?php
            echo aeSecureFct::addStylesheet('libs/bootstrap/css/bootstrap.min.css', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css');
        echo aeSecureFct::addStylesheet('libs/tablesorter/css/theme.ice.min.css', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.24.5/css/theme.ice.min.css');
        echo aeSecureFct::addStylesheet('libs/alertify/css/alertify.core.css', 'https://cdnjs.cloudflare.com/ajax/libs/alertify.js/0.3.11/alertify.core.css');
        echo aeSecureFct::addStylesheet('libs/alertify/css/alertify.bootstrap.css', 'https://cdnjs.cloudflare.com/ajax/libs/alertify.js/0.3.11/alertify.bootstrap.css');
        ?>
        <style type="text/css">
            #DebugMode{background-color:red;color:yellow;padding:5px;margin:10px;right:0px;}
            #DemoMode{background-color:orange;color:white;padding:5px;margin:10px;}
            #result li{padding-top:10px;}
            #result{overflow:auto;padding-top:20px;margin:5px;}
            #result button.close{top:10px;color:red;}
            #ALERTINFO{min-height:90px;}
            .ignoredext{font-size:x-small;}
            .bottomright{position:fixed;right:0;bottom:0;}
            span.regexresult .popover{min-width:350px;max-width:500px;}
            span.regexresult .popover-content{word-wrap: break-word;}
            .danger{font-weight:bold;}
            .danger:before {content:"<?php echo $aeLanguage->get('DANGER');?>";background-color:#d9534f;color:#fff;font-weight:bold;padding:.2em .6em .3em;text-align:center;border-radius:.25em;margin-right:0.5em;}
            .warning:before {content:"<?php echo $aeLanguage->get('WARNING');?>";background-color:#f0ad4e;color:#fff;font-weight:bold;padding:.2em .6em .3em;text-align:center;border-radius:.25em;margin-right:0.5em;}
            .md5{padding-left:10px;color:gray;float:right;}
            .disclaimer{font-style:italic;}
            .double_underline{text-decoration:underline;border-bottom:1px solid #000;}
            .filename{color:red;white-space:nowrap;padding-bottom:10px;display:inline-block;}
            .filesize{padding-left:5px;color:red;white-space:nowrap;padding-bottom:10px;display:inline-block;font-style:italic;font-size:x-small;}
            .filedate{padding-left:5px;color:red;white-space:nowrap;padding-bottom:10px;display:inline-block;font-style:italic;font-size:x-small;}
            .newline{display:block;}
            .seefile{margin-right:25px;}
            .whitelist{margin-right:25px;}
            .killfile{margin-left:100px;}
            .btnscan{margin:5px;min-width:140px;}
            .footer{padding-top:50px;padding-left:10px;}
            .stats{margin-bottom:25px;border:1px solid green;padding:5px;color:green;}
            .underline{text-decoration:underline;}
            .fanpage{color:rgb(255,158,158);}
            .cleanforyou, a.cleanforyou{color:green;text-decoration:none;}
            .blink {animation: blink 1s steps(5, start) infinite; -webkit-animation: blink 1s steps(5, start) infinite; } @keyframes blink {to {visibility: hidden;}} @-webkit-keyframes blink {to {visibility: hidden;}}
            .border{border:2px dotted #C9CBFF;padding:5px;margin-bottom:10px;}
            #frmAdvanced{color:#31708f;}
            /* OffCanvasMenuEffects from http://tympanus.net/Development/OffCanvasMenuEffects/ */
            .container,.content-wrap{overflow:hidden;height:100%}
            /* Menu Button */
            .menu-button{position:fixed;z-index:1000;margin:1em;padding:0;width:2.5em;height:2.25em;border:none;text-indent:2.5em;font-size:1.5em;color:transparent;background:0 0}
            .menu-button::before{position:absolute;top:.5em;right:.5em;bottom:.5em;left:.5em;background:linear-gradient(#2e6da4 20%,transparent 20%,transparent 40%,#2e6da4 40%,#337ab7 60%,transparent 60%,transparent 80%,#2e6da4 80%);content:''}
            .menu-button:hover{opacity:.6}
            .close-button{width:1em;height:1em;position:absolute;right:1em;top:1em;overflow:hidden;text-indent:1em;font-size:.75em;border:none;background:0 0;color:transparent}
            .close-button::after,.close-button::before{content:'';position:absolute;width:3px;height:100%;top:0;left:50%;background:#bdc3c7}
            .close-button::before{-webkit-transform:rotate(45deg);transform:rotate(45deg)}
            .close-button::after{-webkit-transform:rotate(-45deg);transform:rotate(-45deg)}
            .menu-wrap{position:absolute;top:0px;z-index:1001;width:300px;height:100%;background:#d9edf7;padding:2.5em 1.5em 0;font-size:1.15em;-webkit-transform:translate3d(-320px,0,0);transform:translate3d(-320px,0,0);-webkit-transition:-webkit-transform .4s;transition:transform .4s;-webkit-transition-timing-function:cubic-bezier(.7,0,.3,1);transition-timing-function:cubic-bezier(.7,0,.3,1)}
            /* Shown menu */
            .show-menu .menu-wrap{-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0);-webkit-transition:-webkit-transform .8s;transition:transform .8s;-webkit-transition-timing-function:cubic-bezier(.7,0,.3,1);transition-timing-function:cubic-bezier(.7,0,.3,1)}
            .show-menu .icon-list,.show-menu .icon-list a{-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0);-webkit-transition:-webkit-transform .8s;transition:transform .8s;-webkit-transition-timing-function:cubic-bezier(.7,0,.3,1);transition-timing-function:cubic-bezier(.7,0,.3,1)}
            .show-menu .icon-list a{-webkit-transition-duration:.9s;transition-duration:.9s}
        </style>
    </head>
    <body>

        <?php echo $github; ?>

        <div class="container-full">

            <?php
            if (DEMO) {
                echo '<span id="DemoMode" class="blink img-rounded bottomright">' .
                'Demo Mode Enabled</span>';
            }

            if (DEBUG || true === $aeSession::get('Debug', DEBUG)) {
                echo '<span id="DebugMode" class="bottomright blink img-rounded" ' .
                'style="cursor:pointer;">Debug Mode Enabled</span>';
            }
            ?>

            <!-- Advanced menu -->
            <div class="menu-wrap">
                <div class="menu">
                    <form id="frmAdvanced" name="frmAdvanced" method="POST">

                        <div class="border">
                            <?php echo $aeLanguage->get('SELECT_LANGUAGE');?>
                            &nbsp;:

                            <div>
                                <select class="form-control" id="lang" name="lang">
                                    <?php
    // Retrieve the list of JSON files that match
    // aesecure_quickscan_*.json
    // f.i. aesecure_quickscan
                                    $script  = str_replace('.php', '', basename(__FILE__));

// f.i. aesecure_quickscan_lang_*.json
                                    $pattern = str_replace('.php', '_lang_*.json', basename(__FILE__));

                                    $arr = glob($pattern);

                                    if (count($arr) > 0) {
                                        foreach ($arr as $filename) {
                                            $lang = str_replace(
                                                '.json',
                                                '',
                                                str_replace($script . '_lang_', '', (string) $filename)
                                            );
                                            echo '<option value="' . $lang . '"' .
                                                ($aeLanguage->getlang() == $lang
                                                ? ' selected="selected"'
                                                : '') . '>' . $lang . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="border">
                            <?php echo $aeLanguage->get('ADVANCED_OPTIONS');?>
                            &nbsp;:

                            <div class="checkbox">

                                <?php if (!DEMO) { ?>
                                    <label>
                                        <input type="checkbox" id="chkExpert" name="chkExpert" title=""
                                            <?php
                                            if (true === $aeSession::get('Expert', EXPERT)) {
                                                echo 'checked="checked" ';
                                            }
                                            ?>
                                        >
                                        <?php echo $aeLanguage->get('EXPERT_MODE');?>
                                    </label>
                                    <br/>
                                <?php }?>
                                <label>
                                    <input type="checkbox" id="chkDebug" name="chkDebug" title=""
                                        <?php
                                        if (true === $aeSession::get('Debug', DEBUG)) {
                                            echo 'checked="checked" ';
                                        }
                                        ?>
                                    >
                                    <?php echo $aeLanguage->get('DEBUG_MODE');?>
                                </label>
                                <br/>
                                <br/>

                                <?php
                                    $select = '';
                                $wMax                                       = $aeSession::get('MaxFilesByCycle', MAXFILESBYCYCLE);

                                $arr    = [
                                '250'  => 250,
                                '500'  => 500,
                                '1000' => 1000,
                                '1250' => 1250,
                                '1500' => 1500,
                                '2500' => 2500,
                                '5000' => 5000,
                                '0'    => $aeLanguage->get('ALL')
                                ];

                                foreach ($arr as $key => $value) {
                                    $select .= '<option value="' . $key . '" ' .
                                         (($wMax == $key) ? 'selected="SELECTED"' : '') . '>' .
                                         $value . '</option>';
                                }

                                $select = sprintf(
                                    $aeLanguage->get('EXPERT_MAXFILES'),
                                    '<select id="nbrFilesCycle" name="nbrFilesCycle">' .
                                    $select . '</select>'
                                );

                                echo '<label>' . $select . '</label>';
                                ?>

                            </div>
                        </div>

                        <div class="border">
                            <?php echo $aeLanguage->get('IGNORE_TITLE');?>
                            &nbsp;:

                            <div class="checkbox">
                                <label title="
                                    <?php
                                    echo sprintf($aeLanguage->get('IGNORE_EXTENSIONS'), ExtArchives);
                                    ?>"
                                >
                                <input type="checkbox" id="chkIgnoreArchives" name="chkIgnoreArchives"
                                    <?php
                                    if (1 === $aeSession::get('IgnoreArchives', 1)) {
                                        echo 'checked="checked" ';
                                    }
                                    ?>
                                >
                                <?php
                                echo $aeLanguage->get('IGNORE_ARCHIVE') .
                                '<br/><span class="ignoredext">' . ExtArchives . '</span>';
                                ?>

                                </label>

                                <br/>

                                <label title="
                                    <?php
                                    echo sprintf($aeLanguage->get('IGNORE_EXTENSIONS'), ExtDocuments);
                                    ?>"
                                >
                                <input type="checkbox" id="chkIgnoreDocuments" name="chkIgnoreDocuments"
                                    <?php
                                    if (1 === $aeSession::get('IgnoreDocuments', 1)) {
                                        echo 'checked="checked" ';
                                    }
                                    ?>
                                >
                                <?php
                                echo $aeLanguage->get('IGNORE_DOCUMENTS') . '<br/>' .
                                '<span class="ignoredext">' . ExtDocuments . '</span>';
                                ?>

                                </label>

                                <br/>

                                <label title="
                                    <?php
                                    echo sprintf($aeLanguage->get('IGNORE_EXTENSIONS'), ExtFonts);
                                    ?>"
                                >
                                <input type="checkbox" id="chkIgnoreFonts" name="chkIgnoreFonts"
                                    <?php
                                    if (1 === $aeSession::get('IgnoreFonts', 1)) {
                                        echo 'checked="checked" ';
                                    }
                                    ?>
                                >
                                <?php
                                echo $aeLanguage->get('IGNORE_FONT') . '<br/>' .
                                '<span class="ignoredext">' . ExtFonts . '</span>';
                                ?>

                                </label>

                                <br/>

                                <label title="
                                    <?php
                                    echo sprintf($aeLanguage->get('IGNORE_EXTENSIONS'), ExtImages);
                                    ?>"
                                >
                                <input type="checkbox" id="chkIgnoreImages" name="chkIgnoreImages"
                                    <?php
                                    if (1 === $aeSession::get('IgnoreImages', 1)) {
                                        echo 'checked="checked" ';
                                    }
                                    ?>
                                >
                                <?php echo $aeLanguage->get('IGNORE_IMAGES') . '<br/>' .
                                '<span class="ignoredext">' . ExtImages . '</span>';
                                ?>

                                </label>

                                <br/>

                                <label title="
                                    <?php
                                    echo sprintf($aeLanguage->get('IGNORE_EXTENSIONS'), ExtMedia);
                                    ?>"
                                >
                                <input type="checkbox" id="chkIgnoreMedia" name="chkIgnoreMedia"
                                    <?php
                                    if (1 === $aeSession::get('IgnoreMedia', 1)) {
                                        echo 'checked="checked" ';
                                    }
                                    ?>
                                >
                                <?php
                                echo $aeLanguage->get('IGNORE_MEDIA') . '<br/>' .
                                '<span class="ignoredext">' . ExtMedia . '</span>';
                                ?>

                                </label>

                                <br/>

                                <label title="
                                    <?php
                                    echo sprintf($aeLanguage->get('IGNORE_EXTENSIONS'), ExtSoundMovies);
                                    ?>
                                ">
                                <input type="checkbox" id="chkIgnoreSoundMovies"
                                    name="chkIgnoreSoundMovies"
                                    <?php
                                    if (1 === $aeSession::get('IgnoreSoundMovies', 1)) {
                                        echo 'checked="checked" ';
                                    }
                                    ?>
                                >
                                <?php
                                echo $aeLanguage->get('IGNORE_MOVIES') . '<br/>' .
                                '<span class="ignoredext">' . ExtSoundMovies . '</span>';
                                ?>

                                </label>

                                <br/>

                                <label title="
                                    <?php
                                    echo sprintf($aeLanguage->get('IGNORE_EXTENSIONS'), ExtText);
                                    ?>"
                                >
                                <input type="checkbox" id="chkIgnoreText" name="chkIgnoreText"
                                    <?php
                                    if (1 === $aeSession::get('IgnoreText', 1)) {
                                        echo 'checked="checked" ';
                                    }
                                    ?>
                                >
                                <?php echo $aeLanguage->get('IGNORE_TEXTES') . '<br/>' .
                                '<span class="ignoredext">' . ExtText . '</span>';
                                ?>

                            </div>
                        </div>

                        <button type="button" id="btnSubmit" class="btn btn-primary">
                            <?php echo $aeLanguage->get('APPLY');?>
                        </button>

                    </form>
                </div>
                <button class="close-button" id="close-button"></button>
            </div> <!-- Advanced menu -->

            <div class="">

                <div class="col-md-2" >
                    <button class="menu-button" id="open-button"></button>
                </div>

                <div class="col-md-9">

                    <?php

                        // Houston, we've a serious problem; no signatures to scan =>
                        // there was an error in the json PATTERN
                    if (0 == $aeScan->getCountPatterns()) {
                        die();
                    }

                    ?>

                    <?php echo $siteInfos;?>

                    <div style="margin-top:10px;">

                        <div class="alert alert-info fade in" role="alert" id="ALERTINFO">
                            <a href="<?php echo $aeLanguage->get('HOME');?>" target="_blank">
                                <img style="float:left;margin-right:10px;" title="
                                    <?php echo $aeLanguage->get('HOMETITLE'); ?>"
                                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAA8CAYAAAAgwDn8AAAABGdBTUEAALGPC/xhBQAAAAlwSFlzAAAOwQAADsEBuJFr7QAAABh0RVh0U29mdHdhcmUAcGFpbnQubmV0IDQuMC41ZYUyZQAAFENJREFUaEPVWglYU3e2p+10melrbetSWxStYlUgJCEQwhpZsrNKIosgypINsPKsU7tM7UxHx67a9jlttXtnXqe209a6sLmhglpxQUFUVBRZRfYkgELOO+d6g4mA2qrf++Z83/lC/vd/7/2d/Zx/cPr/pCbO6KQmjzGXGt3HlDZxxk1hl/9zqJEzpqyJMxYaOWOtTW5jEtnl/xxq4o5f0cR9sq+JM6a6wX20D7v82+iU6xOPNnmMW9zgMea/m93G/he7fFfJND9F2RYre+kib8y09U5O97HLv42a3ccamzzG9jd6jB1o8BitByene9hLd43MRv1bZoNBwH69PcJgepX8kWH8+24LYNFqXczZunzr4uSH2aXbI8oCDZzRBc0+zxxv16jS7qYAy5Ytu7fHYFjeY9SnsUt3hhD0vSZj6nhzlmFLh9Hoyi7fcTLrdM6mbMP3zUbj3Yk1U5ZeZjEavoe79AJLtkFjydKn3lU37c7WLzQbdbkAcEdfAhrNfQj+u65s7QzQau9nl+88taSlPWIy6tabs/RRtyoEoG8TKGtu7u+tS9IeseamP2HO1Tmb9XpRt1GXaMnSvdylz9yEz+xBN23BLHTenG1Y2a3Xj7sr1rAY5k9Ed/rOZDTy2CWGCGRXTs7YXr1+umWhMdBi1M1BUIvM2foV+LkW7/kReR9q+gICvIx/W4nr5sbDUXk4NKYkAboRw3gdLFmGst6sjGfZx98y3bB2oDZ/17h48cO9OYZIS7ZuIwZdujlL97bFqC9A7Z02ZevrEWwLcicCtSCQfhsoe8br0LJgHuyfFQhbvblQJPCEw9JQQEvY7yEBPyD3Yl/vQHncGapCvoc3+5Whn6ZPf2Szp9sq9qsjdRsMnghqLfJFAsZo6Tdwy4IUOKqQwDZvHuzw9YKyMDHUJmoYoYbu1zfWouuxEBwon+fuj/zOxwLBYMwU8jyUBXyPoQL0ZGc/g4CPIlvtX0AaG/7FI3ONJhZKAkUIPBi2C/kM7xQJGEHaM9Mc9tL7zBkZE1gYDvSz4Nkx+Vz3U4WcmWH0fROH83gBz31/Ad89gdlgT+iPr9pr/FJaKpSj35L59wT6wvFIBaPFtowFNxUIkwCciFLC3gQ1NGzZBH2trdBdXQ1Vb/4NSiUhzLPt9/dk6WaxMBxoi6vrg/k8t2/QCr1FfE4+ftYX8DwaigRuLuyWa4Ruc8T+oQTy9Oxo2Ira25eSCAdzDFCMPrxT5M1o8kLSnBEFaZo3F3aFzwJLfT0mMgBTTQ1c6eqCgcuXoXrNB3AgNJgR0ra/J9uYycIYQgW8mYqjinATPXOf2N+az3VbV2bnUoOEwVljD6I5NRn2oAWaCvLA2t8PYLUyAOp/+gF2oGuciokY3Hs9n4qNhNMf/g9YBwagYdNGKEGXKpaFgaWuDq50dsIucQC0ps8f3N+dpX+DhTGErOmYkrP0TbTv0oLUhjyvGU+xlxwJNxXbHkh8UBYKF/69HgBBtJUdgPI/LoambUVgvXIFTn+0Bg6Qhu3223OFSgaNKHi/xQL70+bBtiARYOBB/cYNjEVK8PpFDHTbfpNB/xMLYwiVYepGYb/v1Gf2YP0wsMtDqSfb8IXtgeQau6Qh0HXyBPS1t0EzAi/CINwe7Ad9bW3QfvgQFIv9HVKiPZ+Oi2b8naxW8/lnsAsB75uXBH2XWqCnuQl2oUAUS7b9nQb9UbFYM2L7gv7/1laBZ+mmQM7j7NJQsmQbl9keyAiAFug+Xc0A3pecwGhwGwYzBWRHxTEUIAA6demDIOyZ8v+u0CC03C+MBS11F+Ayuk5/Tw8c+9NLcATdyT5+zmRm9noHKdc+6SkZtsUu5Lmvz+e7by7w9By5BTcZ9Sn2IPajizRvLWK02FKyG448nwsNmzcx8VD77TewNyRoxCAmJiuQq1z493fQdaIK2g6WQcWrL8M+vK9Tl+G4V6cFv9DogUl+su+f9gkbzUIapDye2wFMn/9Y7+b2ALs0lHqM2jAENFhVKcvsi4+D7lMnGb+lgCRhCEwpgqtHq9iDGI6pdThG7oPWoJghoTq0Q612KDOz30uqhmlBkVcm+yk+mSQWP0SYqE/CbiAA68qlkzGqr27YBG5PmS/rMOp6bQ+lNHc8Ug670dzVa96HxvwtcHLV20x6pCxj0z59EpNWuwyZmEKTmAxTGSEfLIL0LPu0ac94va80I+MlL0XCMY4kDoWI6Hfxlb3iJBb/rk2rHYV7qLjSvs5e7GRZuI70uCB8lCIkau85NOV1D4cGrAEnolVwTCmDk9ERjOZpndIsWakKC9aZuBjM0QHM36VBfnBWHQPlKHjHdVV3CCMoTN/LSLPeynh/L0V8nXvYbJgSqOpy8ZOpOrOyRmOr0UZ7TVnYIeQYxCxke9LcN8FHstpLHGk9nOHomzYmwDa2rRHoY0oJY43z8WpGIHIPsgTtwzbaip9XsAHsQw2asdK3IzfhWgW219sQ/BqTXs+v0Ghsfn2PMCJeypfP6Z4ZEgOu/oqSb7z58WdmR3Wi9Trwvk0dBsPQLPS0j4Q3yV/RRDe9mZBSi93nZXvwIzGBJLfAzwFsp7sQUAMygtMX4PVPce5dgYlhSU+OPg21rOg2Gt3hudTHalJTHxqp+3SV5zzorUp8zyd8dv9C30DrJp5HD2a/f56Ji/OkWYPd5kguQulq16AIK/pfT7AqIYqmMdIWao0Cup+0iKB6SYv4dxOa8hiuFyKo91GA+R3ZOmEnzgetBsPErpwFY9te0I6imbdxcfLDhxHwL6/op1e+kBZ8enFmQtWSzL/XPq8tbHtO92r3woVPrkdB1msce/tFQaFeH/iKTBu8PCHX279T4RXMYS8NJWeheIKLSN5B2ufKNMWCcM0omq76snVcc7Yu15Sle5HmgR6DIcRiTJtE0xq1vmUrtaMqnjeOP74049nKpZnhtYv1Cc3PZRoaF2n/3JKjXd2Uq/u5IVd3+lKOrr0rW9+DjFZysJ4VXexgSYDPu4V89xU/CwR/oIxT6MURFXA9SjbwPVtSg6UDM4Ijr7iIZEsQ6r1XEV9H6D7LpwaqgCNRA0+ekMouDxI9lPjQEt20o0szF556PvNfzYt0xR05+qPINV05+kbkLpyj+9Ai/chWe6A3Y8pU6CIDWKTiC7gzkwu4bvXYrB14398/SKCIr3QLjYVnAhTbp/tHPcJCukZPCOWPThDJKp8VR4GndE6dSK0e1se6sw3JCLB1OAC3y+ex3aYKn8dzr8JW+RLyt9QqizGFCpSJq9Ctrahg8ySBdGj6fNo7jD/ZX3GJpOTK41ewy4NEvXi5JCQYY8IBPAUv9TGUQmsRAE1fJqNj+rVnCnTKTDTIdBsc9zXPTwZ0G8Aqay7gu+Xatwo+kUkRXNkc8/RZ0TDJV76UXb5GzgKJeoq/st89fPZlT7kmkF0epC08d8PeYH8KYuZl9NmKwGv0GXD+nTfhUmE+tBXvhLrP1sGZl1+AcwuzoA731GfpoA6Bns1cAFVYiQ9jT0WTGTFVYnsBaLChgSmf73GYApp9NUN+kuRxXsr4izNRwZNF8hJ2+RpNFEr/4hoUCR7hcRe8VUlDfmRAc/6wy98HNbcA6ubOgWosYPXYRvdcqMX24hSc/eRjOImCNOZtgZ6GeuhraYFe/KTrDGPv34n9TyW2I2ewsFGFvr57bcNn06iZz/P4in2tPd3jrUzcQYVtsr+yYxJX/Bi7fpUmCKXfMv4vUR/hydVj2WWnIxLPhw+pYwxH5ifXHc/NgXMIsnXHdujB6cp09gxUf7AatmJXWkCmRy4UeGKb7c+0zKUJGqZtLkHAe2ZHoUANUI3zAFXmuqR4B/DE6Fp9pcF+Ozby3Lns6x0IFbuS2gsmDrBesctXyUUo2z1dHA1cqWaXICLiD+yy067AwLHVr758uO9iM9O41f/8Exxf8TqUoZ8X44RGgCnwboXNKPAxdKMj6Eb1c4c2f+iWnd2ZaSHsq4eQIDIp3VOqQQEieif7yWTs8lWaKJIeZwSQqQsp6tllp608N7ej6fPPWtANdmMPVIg+ymh7GIA3485jR6EaGzzydVssOQiQjW1GjiGSffUQEirnqlDBgIW2z0Uo17DLV8lFJD01AyOcK1fnObEBhJnn0QKue+GeWQFWS+15dImrae5mvB2ns8rlf4Yzn6yFPdgb2dZb9++HCnShCqV0xOkN240Rj/TRhcJZAS5PEkmT2OWrNEEoqZjO1AB1kUCgvf9bkej3mI8/w5RmKRZ5F7bvLRk4iFqzBzock0tV//0DHDMPQv2GH6EEfd927VLJHqhG4NefBdlzhy79nd2BgY8PJ8SgC6EALj7SuezyVZroKy2+WsQ0e9xwHs3jumVh5jFjJfxkX6DQ7cK6j3pPrn7HwX2KfL0YbZNbUZCeePsN2JukgXNffwnN27fC2U/XwgEcM237W0tL4HzO8MBtXBM/21LI5+ygSnz9cYm3KuFF6hKmYAw4+4Yr2eWrNFEo+ce04EgMYvWxtX7CRMzFXflc9+IiwZRRpI2qJYv2Ne/YBoU+PAYMudMlBEQT2gFMf0yAo8a7jlfCLnSRytdfgwvff8scndgCvePQQai7iQANyYlXixnfw4LvX1FhNzZ6qRI+wTQPUwKUPRN95Y61aoKv5BUPFGClOrm7PDayvSRQdHYLBjBdo4bubLb+s+7KCuYwi8A05G2G48tfh5ZdxXDirTdw2G+FxoJ8aNi4AfYmaqBq5XKo+fIzMJ+rYSxFVjKdOAHNNxGApjc6+KV3YE9k2uLpnsC4E8A9fGXCCTeqA36KrvE8+WCqZ2i8UKJZHZdo7UI/pwzRoc84WjorMKCA55ZaFhr8RVtmmrlj21YEu5J5OB1onXj7TeaIhWrBxeIdWBfOMicUhxYaoearL+DMuo+gRBPD7Kdg7sYs1DZM9rFnajVsAjDMdS8qxXj0VSVO4yvi2ynRUMZkYV8jZy+J74EFae22B7VmzB/Y6edlLuB5tJYGiY516zPb6l77U3/H0XLmYIp8nk4YziHQoy+9ANuC/aAEWwM6pRt8uR3TuVA7Cttx3QnEcLzdl99guw/j0EKnb97KpBTMQL3k5hN9Za+xsK+RC0f1eK4y7mQHWQAf0qbN2LxT6BVU5M2Zgk3VOJMxg3c8VvXX9l/295dhJingc6BIyL+lQkY+TWdCje+9O6SBu57R+m3bfLir7e9fL/AMFKgSPif/fwb7NawBjGsPoacE4WskIZGQotJAsFzzArvM0I5Jkx4q4nNyjy/J7Sc/LxJ6OYC8EZO1LOfPwfl0x1Noe0bgA8g4+elymXnA7v7VIlE6Tz6nltxnsp98jyu2/iwsR5ogCnV2Fob3YakGbJoOe0pix9E6BRH64h8pre7EtNlWshvKly5xADkyc5ggb/jn19h2zwf6icls1FtwLG0zGw2nEPwPOEP/tSfLsOByltbbmpPzYJ7XzCD7Z6SHyA9Q+sQC1j9RKHsJIY38O5qzt+TDSSK5FfvuK+5h6nnU1ubx3VOwoPXRw8gdyo3aga6qSocqOxLvlIRAR/kRqNKlb7Nk6+ZZcgz+CHIqnfEMV6wAh/wqlSxqHyqKfo76ISgQAmRqK425qP3GCSLpjX+3fspLKnD2kVyk0XJmSGzTcwGhCjTpGRsgtMKVrUL+mpp332m+uGvn4EnzcLwV22+qHc0bfmw4HBfpzr5iWAJMk13ZugCMv42YieopVlr1WnhRnYwjbhxpH6b7Sl9mt9+AMOdTLODwbKXKHCVWtNKJgA0UVuiSIp8Zow+oY1UXN2+8RACpKyVXsQdPBe8UpteuY+UDRzSxr18/oNgTaZ18H2PAPBgTyJsXpIOPfA5Qi+MWoDwgHMn3r6dROCw4e4eXYsEADwycxQGzaEoiYP1bBG5MBdyBHWt5cvz8+q+/NHWUHwbqk5hOFfdt9fPGNmIddJYfsVYZtP+7Ty684YsR8DyMg24beOIdaRkQokqAGeg6U/2VpqAgZTRuvfXfkCfwwnywYWrCsg3u+JBXAsRQ4Cs4UGs37C9zcrq3LFoRU7v2w1rzuXNWOi8tQ9PXYZEzna4eqMpduGV/2NDTZXui0zUM6EYbcNL8Ya0WIiOTgOZzdOXLokDV33DrrYMn0qDJAwIU81xF0stTMB74oTHwl9kJLa0G3Xv0LzLsNkaI/RIxD8Guu/D5pybTyRPQUlTQX6lP/3yfWDzsr432hFkpg1KoDXwBaj4iMpEB7xqoGuAGKNdFCK4NWL+KljktuzcgQJI+VSQxTwlQgQc+dNHsuQMntZmHmo3pDtmgQuP2wEGVVIC8br88fGlZhOCWXmrO0i3HtNrQrNea/jUvDYIU8Zg8YrBhU13hBCjyY8JibmjBm5LGTfNAUIBMN10k7cAqCFRMYlXxsCph3i+r4lMns9t+M53LzHzqeXXKoujIxNM8zPV0bIJDey8dcaql6ifYbbdH5E5ykVw83VdycjLWiGlBkcAJjbV6SuIqubL4+TNQSzQEsdtvTpjp/KOiHuErEryxwuZ5SjRmdBkrncnS0ebTPuGLXV3lD7K77xxJsYhwfKUfTvWVtZM1qLFCc1s9wuJOeUjiPsZmK5MnTxR7S+Mn0lxNgtuYGx39GAH2UibM4cs1K7jSObuxul4mX5+GafIZf8UANml5430kwfiqXxewv4bIpcKCI/x8xJElLr7SXhKEKTJo+pmhMX0eYbM7sem66KWMv+AblXzOLyqlxkeVVIuDUiMCbvcIn21xC4u1zpgVg/dFUnN25dngyFqfyLmpw5553i3SarX3B6lSZAh87bQgVcVUDLrJ/gr6NYURiKxDhdDG9J3W6fqUQKUVv5+fGRK90UeVkBmTlXV7gXo7RO7hF5s8blpwzEwsfs+h/36Nrch5Z6HkMs7ZVnQLRgBPiboXtf+Lb0TSKmHMXClXpXaWJC++M/+p6OTk9H895/Dti65t0gAAAABJRU5ErkJggg=="/>
                            </a>
                            <?php
                            echo sprintf(
                                $aeLanguage->get('ALERTINFO'),
                                $aeLanguage->get('HOME'),
                                aeSecureFct::human_filesize(MAX_SIZE, 0)
                            );
                            ?>
                        </div>

                        <?php

                        if (!$aeLanguage->ready()) {
                            echo '<div class="alert alert-warning fade in" >';
                            echo '<div class="text-danger">Error. No translation file found. ' .
                                'Please download the <a href="https://github.com/cavo789/aesecure_quickscan" ' .
                                'target="_blank">aeSecure QuickScan</a> archive again and take ' .
                                'the json file that match your preferred language ' .
                                '(for instance aesecure_quickscan_en-GB.json). ' .
                                'You can find this file in the archive and you need to save ' .
                                'it in the same folder of aesecure_quickscan.php, then refresh ' .
                                'this page.</div>';
                            echo '</div>';
                        } else {
                            if (true !== $aeSession::get('Expert', EXPERT)) {
                                echo '<div class="alert alert-warning alert-dismissible fade in" role="alert">' .
                                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>';
                                echo $aeLanguage->get('ALERTWARNING');
                                echo '</div>';
                            }

                            echo '<div class="row">';

                            if (true === $aeSession::get('Expert', EXPERT)) {
                                echo '<div class="col-md-12" style="padding-bottom:16px;">' . $aeLanguage->get('SCANFOLDER') . ' <input type="text" name="folder" id="folder" value="' . $aeScan->directory() . '" size="80" /></div>';
                            }

                            echo '<button type="button" id="cleansite" class="btn btn-primary" ' .
                                'data-toggle="popover" data-placement="top" data-html="true" ' .
                                ' data-content="' . $aeLanguage->get('BTNCLEANHINT') . '" ' .
                                'data-old-caption="1. ' . $aeLanguage->get('BTNCLEAN') . '">1. ' .
                                $aeLanguage->get('BTNCLEAN') . '</button>&nbsp;';

                            echo '<button type="button" id="getcountfiles" disabled="disabled" ' .
                                'class="btn btn-primary" data-toggle="popover" ' .
                                'data-placement="bottom" data-html="true" ' .
                                'data-content="<span class=\'text-info\'>' .
                                $aeLanguage->get('BTNGETLISTHINT') . '</span>" ' .
                                'data-old-caption="2. ' . $aeLanguage->get('BTNGETLIST') .
                                '">2. ' . $aeLanguage->get('BTNGETLIST') . '</button>&nbsp;';
                            echo '<button type="button" id="startscan" data-start="0" ' .
                                'data-end="0" disabled="disabled" class="btn btn-primary" ' .
                                'data-toggle="popover" data-placement="bottom" data-html="true" ' .
                                'data-content="<span class=\'text-info\'>' .
                                $aeLanguage->get('BTNSCANHINT') . '</p>" data-old-caption="3. ' .
                                $aeLanguage->get('BTNSCAN') . '">3. ' .
                                $aeLanguage->get('BTNSCAN') . '</button>&nbsp;';

                            $killnr = 4;

                            echo '<button type="button" id="destroy" class="btn btn-warning" ' .
                                'data-toggle="popover" data-placement="bottom" data-html="true" ' .
                                'data-content="<strong class=\'text-danger\'>' .
                                $aeLanguage->get('BTNKILLMEHINT') . '</strong>">' .
                                '<span class="glyphicon glyphicon-trash">&nbsp;</span>' .
                                $killnr . '. ' . $aeLanguage->get('BTNKILLME') . '</button>';

                            echo '<div id="resultGetCountFiles" style="display:none;padding-top:25px;" class="text-info">' .
                            '<div id="resultGetCountFilesNumber" style="padding-bottom:15px;"></div>' .
                            '<div id="resultGetCountFilesButtons"></div>' .
                            '</div>';
                            echo '<div id="result">' . $showInfo . '</div>';
                            echo '</div>';

                            $aeProgress->getHTML();
                        }
                        ?>
                    </div>

                    <?php echo $aeScan->getHTMLFooter(); ?>

                </div>
            </div>
        </div>

        <?php
            echo aeSecureFct::addJavascript(
                'libs/jquery/js/jquery.min.js',
                '//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js'
            );

            echo aeSecureFct::addJavascript(
                'libs/bootstrap/js/bootstrap.min.js',
                '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js'
            );

            echo aeSecureFct::addJavascript(
                'libs/tablesorter/js/jquery.tablesorter.combined.js',
                'https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.24.5/js/jquery.tablesorter.combined.js',
                true
            );

            echo aeSecureFct::addJavascript(
                'libs/alertify/js/alertify.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/alertify.js/0.3.11/alertify.min.js',
                true
            );
            ?>

        <script defer="defer">
            $(document).ready(function() {

                var $body   = $(document.body);
                var navHeight = $('.navbar').outerHeight(true) + 10;

                // For esthetic purpose; scroll back to top-left before
                // displaying the Expert menu
                $('#open-button').bind('click', function() { window.scrollTo(0,0); });

                // Set the TOP position of the advanced menu button and area
                // depending on the top horizontal navigation bar
                $top = $("nav").height();

                if($top!==null) {
                    $("#open-button").css({ top: $top +'px' });
                    $(".menu-wrap").css({ top: $top +'px' });
                }

                try {
                    // OffCanvasMenuEffects from http://tympanus.net/Development/OffCanvasMenuEffects/
                    !function(s){"use strict";function e(s){return new RegExp("(^|\\s+)"+s+"(\\s+|$)")}function n(s,e){var n=a(s,e)?c:t;n(s,e)}var a,t,c;"classList"in document.documentElement?(a=function(s,e){return s.classList.contains(e)},t=function(s,e){s.classList.add(e)},c=function(s,e){s.classList.remove(e)}):(a=function(s,n){return e(n).test(s.className)},t=function(s,e){a(s,e)||(s.className=s.className+" "+e)},c=function(s,n){s.className=s.className.replace(e(n)," ")});var i={hasClass:a,addClass:t,removeClass:c,toggleClass:n,has:a,add:t,remove:c,toggle:n};"function"==typeof define&&define.amd?define(i):s.classie=i}(window);
                    !function(){function e(){n()}function n(){d.addEventListener("click",t),u&&u.addEventListener("click",t),o.addEventListener("click",function(e){var n=e.target;i&&n!==d&&t()})}function t(){i?classie.remove(c,"show-menu"):classie.add(c,"show-menu"),i=!i}var c=document.body,o=document.querySelector(".content-wrap"),d=document.getElementById("open-button"),u=document.getElementById("close-button"),i=!1;e()}();
                } catch (e) {
                }

            });
        </script>

        <script defer="defer">

            var $maxFilesByCycle=parseInt($('#nbrFilesCycle').val());
            var $debug=<?php echo true === $aeSession::get('Debug', DEBUG) ? 'true' : 'false'; ?>;
            var $demo=<?php echo DEMO ? 'true' : 'false'; ?>;
            var $rootfolder='<?php echo str_replace('\\', '\\\\', (string) $aeScan->directory()); ?>';

            /**
             * Format a number in javascript; display 15.202 (with thousand separator) instead 15202
             * @param {type} x
             * @returns {unresolved}
             */
            function numberWithCommas(x) {
                return x.toString().replace(
                    /\B(?=(\d{3})+(?!\d))/g,
                    "<?php echo $aeLanguage->get('THOUSANDSEPARATOR'); ?>"
                );
            }

            $('[data-toggle="popover"]').popover({trigger:'hover',html:true});

            <?php $aeProgress->getJSFunction('initialize'); ?>

            // Submit button of the advanced form
            $('#btnSubmit').click(function(e) {

                e.stopImmediatePropagation();

                var $data = '';

                $data += "lang=" + $('#lang').val() + "&";

                // Prepare a string with all checkboxes and their status (on or off).
                $('input[type=checkbox]').each(function () {
                    $data+=this.id + "=" + (this.checked ? "on" : "off") + "&";
                });

                if($demo==true) {
                    alert('Expert and Debug modes aren\'t allowed in demo mode. ' +
                        'These options won\'t be accessible at this time');
                }

                $data += "formTask=SaveSession&nbFiles=" + parseInt($('#nbrFilesCycle').val());

                // Post the data and reload the page to take the new settings into account
                $.post("<?php echo FILE; ?>", $data).done(function( data ) {
                    var $url = window.location.href;
                    $url = removeURLParameter($url,'lang') + 'lang=' + $('#lang').val();
                    window.location.href=$url;
                });

            });

            // Start the scan process
            $('#cleansite').click(function (e) {
                e.stopImmediatePropagation();
                var btn=this;
                $.ajax({
                    beforeSend: function() {
                        if(!$debug) $('#cleansite').prop("disabled", true);
                        $('.popover').popover('hide');
                        $('#result').empty();
                        $('#resultGetCountFilesNumber').empty();
                        <?php $aeProgress->getJSFunction('ajax_before'); ?>
                    },
                    async:true,
                    type:($debug?'GET':'POST'),
                    url: "<?php echo FILE; ?>",
                    data:"task=cleansite&folder="+(btoa($('#folder').val())),
                    success: function (data) {
                        <?php $aeProgress->getJSFunction('ajax_success'); ?>
                        $('#cleansite').html("1. <?php echo $aeLanguage->get('BTNCLEANDONE');?>");
                        $('#result').html(data);
                        $('#getcountfiles').prop("disabled", false);
                        // To remember that we've already click on this button
                        $(btn).addClass('btn-success');

                    }
                });
            });

            // Get the number of files that will be analyzed during the scan
            $('#getcountfiles').click(function (e) {
                e.stopImmediatePropagation();
                var $data = new Object;
                $data.task = "getcountfiles"
                $data.folder=btoa($('#folder').val());

                // Define the "toString()" method to get a string representation of the object
                Object.prototype.toString = function dogToString() {
                    var ret = 'task=' + this.task + '&folder=' + this.folder;
                    return ret;
                }

                var btn=this;

                $.ajax({
                    beforeSend: function() {
                        if(!$debug) $('#getcountfiles').prop("disabled", true);
                        $('.popover').popover('hide');
                        $('#getcountfiles').html("2. <?php echo $aeLanguage->get('RUNNING');?>");
                        $('#result').empty();
                        $('#resultGetCountFilesNumber').empty();
                        $('#result').html('<div class="blink" id="gettingFiles"><?php echo str_replace("'", "\'", (string) $aeLanguage->get('GETTINGFILES'));?></div>');
                    },
                    async:true,
                    cache:false,
                    type:($debug?'GET':'POST'),
                    url: "<?php echo FILE; ?>",
                    data:$data,
                    dataType:"json",
                    success: function (json) {
                        var $msg="<?php echo str_replace('"', '\"', (string) $aeLanguage->get('GETCOUNTFILESDONE'));?>".replace("%s",numberWithCommas(json.count));
                        $msg=$msg.replace("%s",numberWithCommas(json.blacklisted));
                        $msg=$msg.replace("%s",numberWithCommas(json.edited));
                        $msg=$msg.replace("%s",numberWithCommas(json.whitelisted));
                        $msg=$msg.replace("%s",numberWithCommas(json.skipped));

                        var $tmp="<?php echo $aeLanguage->get('FILES');?>".replace("%s",numberWithCommas(json.count));

                        $('#startscan').html("3. <?php echo $aeLanguage->get('SCANFILES');?>".replace("%s",numberWithCommas(json.count)));

                        if(json.count<=$maxFilesByCycle) {

                            $('button[id^=startscan_]').hide();

                        } else {

                            // There are for instance 5.560 files and we process 1.000 files at a time
                            // We need then dynamically generate six buttons
                            //  1.  Files 1 -> 999
                            //  2.  Files 1.000 -> 1.999
                            //  3.  Files 2.000 -> 2.999
                            //  4.  Files 3.000 -> 3.999
                            //  5.  Files 4.000 -> 4.999
                            //  6.  Files 5.000 -> 5.560

                            var $wBtn=0;
                            var $start=0;
                            var $end=0;

                            while ($start<json.count) {
                                $wBtn+=1;
                                $end=$start+$maxFilesByCycle;

                                if ($end>json.count) $end=json.count;

                                btn='<button type="button" id="startscan_'+$wBtn+'" data-start="'+$start+'" data-end="'+$maxFilesByCycle+'" disabled="disabled" class="btn btnscan btn-primary" data-toggle="popover" data-placement="bottom" data-html="true" data-content="" data-old-caption="'+numberWithCommas($start+1)+' -> '+numberWithCommas($end)+'">'+
                                numberWithCommas($start+1)+' -> '+numberWithCommas($end)+'</button>&nbsp;';

                                $('#resultGetCountFilesButtons').append(btn);

                                $start+=$maxFilesByCycle;
                            }

                        } // if(json.count<=$maxFilesByCycle)

                        // Call initButtons to set the onClick event for these buttons
                        initButtons();

                        $('#result').empty();
                        $('#getcountfiles').html("2. "+$tmp);
                        $('#resultGetCountFilesNumber').html($msg);
                        $('#resultGetCountFiles').show();
                        $('button[id^=startscan]').prop("disabled", false);

                        // No viruses immediatly detected after the count files?
                        // Ok, great, remove the warning
                        if(json.blacklisted==0) {
                            $('#virusalreadyfound').remove();
                        }

                        // No files having virus in it immediatly detected after the count files?
                        // Ok, great, remove the warning
                        if(json.edited==0) {
                            $('#virusaddedfound').remove();
                        }

                        // To remember that we've already click on this button
                        $(btn).addClass('btn-success');

                    },
                    error: function(Request, textStatus, errorThrown) {
                        // Restore the caption of the button and put it in red
                        $(btn).text($(btn).attr('data-old-caption'));
                        $(btn).removeClass('btn-warning').addClass('btn-danger');

                        // Re-enable the button so the user can restart the scan once he made some changes (like f.i. modifying the
                        // number of files to scan in one pass
                        $(btn).prop("disabled", false);

                        // Display an error message to inform the user about the problem
                        var $msg = '<div class="bg-danger text-danger img-rounded" style="margin-top:25px;padding:10px;">';
                        $msg = $msg + '<strong>An error has occured :</strong><br/>';
                        $msg = $msg + 'Internal status: '+textStatus+'<br/>';
                        $msg = $msg + 'HTTP Status: '+Request.status+' ('+Request.statusText+')<br/>';
                        $msg = $msg + 'XHR ReadyState: ' + Request.readyState + '<br/>';
                        $msg = $msg + 'Raw server response:<br/>'+Request.responseText+'<br/>';

                        if($debug) {
                            $url='<?php echo FILE; ?>?'+$data.toString();
                            $msg = $msg + 'URL that has returned the error : <a target="_blank" href="'+$url+'">'+$url+'</a><br/><br/>';
                        }

                        $msg = $msg + '<?php echo str_replace("'", "\'", sprintf($aeLanguage->get('QUICKSCANFAQ'), $aeLanguage->get('QUICKSCANURL'))); ?>';
                        $msg = $msg + '</div>';

                        $('#result').html($msg);
                    }
                });


            });

            // Destroy, delete this script on the server
            $('#destroy').click(function (e) {
                e.stopImmediatePropagation();

                var $bExists=0;
                var $keepWhiteList=1;

                // Check if a whitelist file exists; the checkwhitelist task will return 1 or 0
                $.ajax({
                    async:false,
                    type:($debug?'GET':'POST'),
                    url: "<?php echo FILE; ?>",
                    data:"task=checkwhitelist",
                    success: function (data) {
                        $bExists=(data==1?true:false);
                    }
                });

                // If there is a whitelist file, ask if we need
                if ($bExists) var $keepWhiteList=confirm("<?php echo $aeLanguage->get('JS_KEEPWHITELIST');?>");

                // Now, start the request for the deletion of this script
                $.ajax({
                    beforeSend: function() {
                        $('#cleansite').prop("disabled", true);
                        $('#getcountfiles').prop("disabled", true);
                        $('#startscan').prop("disabled", true);
                        $('#destroy').prop("disabled", true);
                        $('.popover').popover('hide');
                        $('#result').empty();
                    },
                    async:true,
                    type:($debug?'GET':'POST'),
                    url: "<?php echo FILE; ?>",
                    data:"task=byebye&keepwhitelist="+$keepWhiteList,
                    success: function (data) {
                        $('#cleansite').html("<?php echo $aeLanguage->get('BTNKILLMEDONE');?>");
                        $('#getcountfiles').html("<?php echo $aeLanguage->get('BTNKILLMEDONE');?>");
                        $('#startscan').html("<?php echo $aeLanguage->get('BTNKILLMEDONE');?>");
                        $('#destroy').html("<?php echo $aeLanguage->get('BTNKILLMEDONE');?>");
                        $('#result').html(data);
                    }
                });
            });

            // Change the folder to scan
            $('#folder').change(function (e) {
                e.stopImmediatePropagation();

                $.ajax({
                    beforeSend: function() {
                        $('#folder').prop("disabled", true);
                        $('#result').empty();
                    },
                    async:true,
                    type:($debug?'GET':'POST'),
                    url: "<?php echo FILE; ?>",
                    data:"task=chgfolder&folder="+btoa($('#folder').val()),
                    success: function (data) {
                        $('#folder').prop("disabled", false);
                        $('#cleansite').text($('#cleansite').attr('data-old-caption'));
                        $('#cleansite').prop("disabled", false);
                        $('#getcountfiles').text($('#getcountfiles').attr('data-old-caption'));
                        $('#getcountfiles').prop("disabled", false);
                        $('#resultGetCountFiles').hide();
                        $('#result').html(data);
                        // By changing of folder, the returned value, if there, is the
                        // name of the CMS in that folder
                        $('.navbar-brand').html(data.CMS);
                    }
                });
            });

            if($debug){
                $('#DebugMode').click(function(e) {
                    e.stopImmediatePropagation();
                    $.ajax({
                        async:true,
                        type:($debug?'GET':'POST'),
                        url: "<?php echo FILE; ?>",
                        data:"task=seedebug",
                        datatype:"html",
                        success: function (data) {
                            var w = window.open("", "_blank");
                            if(w!=undefined) { var $w = $(w.document.body); $w.html(data); }
                        }
                    });
                });
            }

            function initButtons() {

                // See file handler
                $('.seefile').click(function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    var $filename=$(this).attr('data-filename');
                    var $button=$(this);
                    $.ajax({
                        async:true,
                        type:($debug?'GET':'POST'),
                        url: "<?php echo FILE; ?>",
                        data:"task=seefile&filename="+$filename,
                        datatype:"html",
                        success: function (data) {
                            var w = window.open("", "_blank");
                            if(w!=undefined) { var $w = $(w.document.body); $w.html(data); }
                        }
                    });
                });

                // Hide the file
                $('.hidefile').click(function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    $(this).parent().fadeOut('slow');
                });

                // Add to the white list handler
                $('.whitelist').click(function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    // Add to the white list file handler
                    var $filename=$(this).attr('data-filename');
                    var $button=$(this);
                    $.ajax({
                        async:true,
                        type:($debug?'GET':'POST'),
                        url: "<?php echo FILE; ?>",
                        data:"task=whitelist&filename="+$filename,
                        success: function (data) {
                            $button.parent().parent().fadeOut(500);
                        }
                    });
                });

                // Trigger click on the StartScan buttons
                $("[id^=startscan]").click(function(e) {

                    e.stopImmediatePropagation();

                    // Check if the startscan button has data-start and/or data-end attributes.  If yes, use it to limit the scan action
                    // For instance data-start=100 data-end=50  ==> process files from the file number 100 and process 50 files max.
                    var $start=0;
                    var $end=0;
                    if ($(this).attr('data-start')) $start=$(this).attr('data-start');
                    if ($(this).attr('data-end')) $end=$(this).attr('data-end');

                    var $data = new Object;
                    $data.task = "doscan"
                    $data.folder=btoa($('#folder').val());
                    $data.start=$start;
                    $data.end=$end;

                    // Define the "toString()" method to get a string representation of the object
                    Object.prototype.toString = function dogToString() {
                        var ret = 'task=' + this.task + '&folder=' + this.folder + '&start=' + this.start + '&end=' + this.end;
                        return ret;
                    }

                    var btn=this;

                    $.ajax({
                        beforeSend: function() {
                            $("[id^=startscan_]").each(function() {
                                $(this).removeClass('btn-warning');
                            });

                            $(btn).addClass('btn-warning');
                            $(btn).prop("disabled", true);
                            $('.popover').popover('hide');

                            <?php $aeProgress->getJSFunction('ajax_before'); ?>

                            $(btn).html("3. <?php echo $aeLanguage->get('RUNNING');?>");
                            $('#result').empty();
                        },
                        async:true,
                        cache:false,
                        type:($debug?'GET':'POST'),
                        url: "<?php echo FILE; ?>",
                        data:$data,
                        success: function (data) {
                            <?php $aeProgress->getJSFunction('ajax_success'); ?>
                            $('#result').html(data);
                            // To remember that we've already click on this button
                            $(btn).removeClass('btn-warning');
                            // To remember that we've already click on this button
                            $(btn).addClass('btn-success');
                            $(btn).prop("disabled", false);
                            $(btn).text($(btn).attr('data-old-caption'));
                        },
                        error: function(Request, textStatus, errorThrown) {
                            // Hide the progress bar
                            <?php $aeProgress->getJSFunction('ajax_success'); ?>

                            // Restore the caption of the button and put it in red
                            $(btn).text($(btn).attr('data-old-caption'));
                            $(btn).removeClass('btn-warning').addClass('btn-danger');

                            // Re-enable the button so the user can restart the scan once he made some changes (like f.i. modifying the
                            // number of files to scan in one pass
                            $(btn).prop("disabled", false);

                            // Display an error message to inform the user about the problem
                            var $msg = '<div class="bg-danger text-danger img-rounded" style="margin-top:25px;padding:10px;">';
                            $msg = $msg + '<strong>An error has occured :</strong><br/>';
                            $msg = $msg + 'Internal status: '+textStatus+'<br/>';
                            $msg = $msg + 'HTTP Status: '+Request.status+' ('+Request.statusText+')<br/>';
                            $msg = $msg + 'XHR ReadyState: ' + Request.readyState + '<br/>';
                            $msg = $msg + 'Raw server response:<br/>'+Request.responseText+'<br/>';

                            if ($debug) {
                                $url='<?php echo FILE; ?>?'+$data.toString();
                                $msg = $msg + 'URL that has returned the error : <a target="_blank" href="'+$url+'">'+$url+'</a><br/><br/>';
                            }

                            $msg = $msg + '<?php echo str_replace("'", "\'", (string) $aeLanguage->get('QUICKSCANFAQ')); ?>';
                            $msg = $msg + '</div>';

                            $('#result').html($msg);
                        }
                    });
                });

                <?php if (true === $aeSession::get('Expert', EXPERT)) {?>
                    $('.killfile').mouseover(function(e) {
                        $(this).html($(this).attr("data-caption"));
                    });

                    $('.killfile').mouseleave(function(e) {
                        $(this).html($(this).attr("data-old-caption"));
                    });

                    $('.killfile').click(function(e) {
                        e.stopImmediatePropagation();
                        // Kill file handler
                        var $filename=$(this).attr('data-filename');
                        var $button=$(this);
                        var $confirm="<?php echo $aeLanguage->get('JS_CONFIRMKILL'); ?>".replace("%s",atob($filename));
                        if (confirm($confirm)) {
                            $.ajax({
                                async:true,
                                type:($debug?'GET':'POST'),
                                url: "<?php echo FILE; ?>",
                                data:"task=killfile&filename="+$filename,
                                success: function (data) {
                                    if(data==-1) {
                                        // Delete successfull when returned value is -1
                                        $button.parent().parent().fadeOut(500);
                                        $button.parent().html("<?php echo str_replace('"', '\"', (string) $aeLanguage->get('JS_UNLINKSUCCESS')); ?>");
                                    } else {
                                        if(data==-50) {
                                            $button.parent().html("<?php echo str_replace('"', '\"', (string) $aeLanguage->get('JS_FILENOTFOUND')); ?>");
                                        } else {
                                            $button.parent().html("<?php echo str_replace('"', '\"', (string) $aeLanguage->get('JS_UNLINKERROR')); ?>");
                                        }
                                    }
                                }
                            });
                        }
                    });

                <?php }?>

            }

            function removeURLParameter(url, parameter) {
                var rtn = url.split("?")[0], param, params_arr = [], queryString = (url.indexOf("?") !== -1) ? url.split("?")[1] : "";

                if (queryString !== "") {
                    params_arr = queryString.split("&");
                    for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                        param = params_arr[i].split("=")[0];
                        if (param === parameter) params_arr.splice(i, 1);
                    }
                    rtn = rtn + "?" + params_arr.join("&");
                } else {
                    rtn += "?";
                }

                return rtn;
            }

            <?php $aeProgress->getJSFunction('function'); ?>

        </script>
    </body>
</html>
