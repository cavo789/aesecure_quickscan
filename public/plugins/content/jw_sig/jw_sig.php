<?php
/**
 * @version      4.2
 * @package      Simple Image Gallery (plugin)
 * @author       JoomlaWorks - https://www.joomlaworks.net
 * @copyright    Copyright (c) 2006 - 2022 JoomlaWorks Ltd. All rights reserved.
 * @license      GNU/GPL license: https://www.gnu.org/licenses/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
if (version_compare(JVERSION, '2.5.0', 'ge')) {
    jimport('joomla.html.parameter');
}

class plgContentJw_sig extends JPlugin
{

    // JoomlaWorks reference parameters
    public $plg_name             = "jw_sig";
    public $plg_tag              = "gallery";
    public $plg_version          = "4.2";
    public $plg_copyrights_start = "\n\n<!-- JoomlaWorks \"Simple Image Gallery\" Plugin (v4.2) starts here -->\n";
    public $plg_copyrights_end   = "\n<!-- JoomlaWorks \"Simple Image Gallery\" Plugin (v4.2) ends here -->\n\n";

    public function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);

        // Define the DS constant (b/c)
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
    }

    // Joomla 1.5
    public function onPrepareContent(&$row, &$params, $page = 0)
    {
        $this->renderSimpleImageGallery($row, $params, $page = 0);
    }

    // Joomla 2.5+
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        $this->renderSimpleImageGallery($row, $params, $page = 0);
    }

    // The main function
    public function renderSimpleImageGallery(&$row, &$params, $page = 0)
    {
        // API
        jimport('joomla.filesystem.file');
        $app = JFactory::getApplication();
        $document = JFactory::getDocument();

        if (version_compare(JVERSION, '4', 'ge')) {
            if ($app->isClient('administrator')) {
                return;
            }
            $jinput = $app->input;
            $tmpl = $jinput->getCmd('tmpl');
            $print = $jinput->getCmd('print');
            $format = $jinput->getCmd('format');
        } else {
            if ($app->isAdmin()) {
                return;
            }
            $tmpl = JRequest::getCmd('tmpl');
            $print = JRequest::getCmd('print');
            $format = JRequest::getCmd('format');
        }

        // Assign paths
        $sitePath = JPATH_SITE;
        $siteUrl  = JURI::root(true);

        if (version_compare(JVERSION, '2.5.0', 'ge')) {
            $pluginLivePath = $siteUrl.'/plugins/content/'.$this->plg_name.'/'.$this->plg_name;
            $defaultImagePath = 'images';
        } else {
            $pluginLivePath = $siteUrl.'/plugins/content/'.$this->plg_name;
            $defaultImagePath = 'images/stories';
        }

        // Check if plugin is enabled
        if (JPluginHelper::isEnabled('content', $this->plg_name) == false) {
            return;
        }

        // Bail out if the page format is not what we want
        $allowedFormats = array('', 'html', 'feed', 'json');
        if (!in_array($format, $allowedFormats)) {
            return;
        }

        // Simple performance check to determine whether plugin should process further
        if (strpos($row->text, $this->plg_tag) === false) {
            return;
        }

        // expression to search for
        $regex = "#{".$this->plg_tag."}(.*?){/".$this->plg_tag."}#is";

        // Find all instances of the plugin and put them in $matches
        preg_match_all($regex, $row->text, $matches);

        // Number of plugins
        $count = count($matches[0]);

        // Plugin only processes if there are any instances of the plugin in the text
        if (!$count) {
            return;
        }

        // Load the plugin language file the proper way
        JPlugin::loadLanguage('plg_content_'.$this->plg_name, JPATH_ADMINISTRATOR);

        // Check for basic requirements
        if (!extension_loaded('gd') && !function_exists('gd_info')) {
            if (version_compare(JVERSION, '4', 'ge')) {
                $app->enqueueMessage(JText::_('JW_PLG_SIG_NOTICE_01'), 'notice');
            } else {
                JError::raiseNotice('', JText::_('JW_PLG_SIG_NOTICE_01'));
            }
            return;
        }
        if (!is_writable($sitePath.'/cache')) {
            if (version_compare(JVERSION, '4', 'ge')) {
                $app->enqueueMessage(JText::_('JW_PLG_SIG_NOTICE_02'), 'notice');
            } else {
                JError::raiseNotice('', JText::_('JW_PLG_SIG_NOTICE_02'));
            }
            return;
        }

        // Check if Simple Image Gallery Pro is present and mute
        if (JPluginHelper::isEnabled('content', 'jw_sigpro') == true) {
            return;
        }

        // Check if Simple Image Gallery Free (old) is present and show a warning
        if (JPluginHelper::isEnabled('content', 'jw_simpleImageGallery') == true) {
            if (version_compare(JVERSION, '4', 'ge')) {
                $app->enqueueMessage(JText::_('JW_PLG_SIG_NOTICE_OLD_SIG'), 'notice');
            } else {
                JError::raiseNotice('', JText::_('JW_PLG_SIG_NOTICE_OLD_SIG'));
            }
            return;
        }

        // ----------------------------------- Get plugin parameters -----------------------------------

        // Get plugin info
        $plugin = JPluginHelper::getPlugin('content', $this->plg_name);

        // Control external parameters and set variable for controlling plugin layout within modules
        if (!$params) {
            $params = class_exists('JParameter') ? new JParameter(null) : new JRegistry(null);
        }
        if (is_string($params)) {
            $params = class_exists('JParameter') ? new JParameter($params) : new JRegistry($params);
        }
        $parsedInModule = $params->get('parsedInModule');

        $pluginParams = class_exists('JParameter') ? new JParameter($plugin->params) : new JRegistry($plugin->params);

        $galleries_rootfolder = ($params->get('galleries_rootfolder')) ? $params->get('galleries_rootfolder') : $pluginParams->get('galleries_rootfolder', $defaultImagePath);
        $popup_engine = 'jquery_fancybox';
        $jQueryHandling = $pluginParams->get('jQueryHandling', '1.12.4');
        $thb_template = 'Classic';
        $thb_width = (!is_null($params->get('thb_width', null))) ? $params->get('thb_width') : $pluginParams->get('thb_width', 200);
        $thb_height = (!is_null($params->get('thb_height', null))) ? $params->get('thb_height') : $pluginParams->get('thb_height', 160);
        $smartResize = 1;
        $jpg_quality = $pluginParams->get('jpg_quality', 80);
        $showcaptions = 0;
        $cache_expire_time = $pluginParams->get('cache_expire_time', 3600) * 60; // Cache expiration time in minutes
        // Advanced
        $memoryLimit = (int)$pluginParams->get('memoryLimit');
        if ($memoryLimit) {
            ini_set("memory_limit", $memoryLimit."M");
        }

        // Cleanups
        // Remove first and last slash if they exist
        if (substr($galleries_rootfolder, 0, 1) == '/') {
            $galleries_rootfolder = substr($galleries_rootfolder, 1);
        }
        if (substr($galleries_rootfolder, -1, 1) == '/') {
            $galleries_rootfolder = substr($galleries_rootfolder, 0, -1);
        }

        // Includes
        require_once dirname(__FILE__).'/'.$this->plg_name.'/includes/helper.php';

        // Other assignments
        $transparent = $pluginLivePath.'/includes/images/transparent.gif';

        // When used with K2 extra fields
        if (!isset($row->title)) {
            $row->title = '';
        }

        // Variable cleanups for K2
        if ($format == 'raw') {
            $this->plg_copyrights_start = '';
            $this->plg_copyrights_end = '';
        }

        // ----------------------------------- Prepare the output -----------------------------------

        // Process plugin tags
        if (preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER) > 0) {

            // start the replace loop
            foreach ($matches[0] as $key => $match) {
                $tagcontent = preg_replace("/{.+?}/", "", $match);
                $tagcontent = str_replace(array('"','\'','`'), array('&quot;','&apos;','&#x60;'), $tagcontent); // Address potential XSS attacks
                $tagcontent = trim(strip_tags($tagcontent));

                if (strpos($tagcontent, ':')!==false) {
                    $tagparams        = explode(':', $tagcontent);
                    $galleryFolder    = $tagparams[0];
                } else {
                    $galleryFolder    = $tagcontent;
                }

                // HTML & CSS assignments
                $srcimgfolder = $galleries_rootfolder.'/'.$galleryFolder;
                $gal_id = substr(md5($key.$srcimgfolder), 1, 10);

                // Render the gallery
                $SIGHelper = new SimpleImageGalleryHelper();

                $SIGHelper->srcimgfolder = $srcimgfolder;
                $SIGHelper->thb_width = $thb_width;
                $SIGHelper->thb_height = $thb_height;
                $SIGHelper->smartResize = $smartResize;
                $SIGHelper->jpg_quality = $jpg_quality;
                $SIGHelper->cache_expire_time = $cache_expire_time;
                $SIGHelper->gal_id = $gal_id;
                $SIGHelper->format = $format;

                $gallery = $SIGHelper->renderGallery();

                if (!$gallery) {
                    if (version_compare(JVERSION, '4', 'ge')) {
                        $app->enqueueMessage(JText::_('JW_PLG_SIG_NOTICE_03'), 'notice');
                    } else {
                        JError::raiseNotice('', JText::_('JW_PLG_SIG_NOTICE_03'));
                    }
                    continue;
                }

                // CSS & JS includes: Append head includes, but not when we're outputing raw content (like in K2)
                if ($format == '' || $format == 'html') {

                    // Initialize variables
                    $relName = '';
                    $extraClass = '';
                    $extraWrapperClass = '';
                    $legacyHeadIncludes = '';
                    $customLinkAttributes = '';

                    $popupPath = "{$pluginLivePath}/includes/js/{$popup_engine}";
                    $popupRequire = dirname(__FILE__).'/'.$this->plg_name.'/includes/js/'.$popup_engine.'/popup.php';

                    if (file_exists($popupRequire) && is_readable($popupRequire)) {
                        require $popupRequire;
                    }

                    if (version_compare(JVERSION, '4', 'ge')) {
                        // Do nothing
                    } elseif (version_compare(JVERSION, '2.5.0', 'ge')) {
                        JHtml::_('behavior.framework');
                    } else {
                        JHTML::_('behavior.mootools');
                    }

                    if (count($stylesheets)) {
                        foreach ($stylesheets as $stylesheet) {
                            if (substr($stylesheet, 0, 4) == 'http' || substr($stylesheet, 0, 2) == '//') {
                                $document->addStyleSheet($stylesheet);
                            } else {
                                $document->addStyleSheet($popupPath.'/'.$stylesheet);
                            }
                        }
                    }
                    if (count($stylesheetDeclarations)) {
                        foreach ($stylesheetDeclarations as $stylesheetDeclaration) {
                            $document->addStyleDeclaration($stylesheetDeclaration);
                        }
                    }

                    if (strpos($popup_engine, 'jquery_') !== false && $jQueryHandling != 0) {
                        if (version_compare(JVERSION, '3.0', 'ge')) {
                            JHtml::_('jquery.framework');
                        } else {
                            $document->addScript('https://cdn.jsdelivr.net/npm/jquery@'.$jQueryHandling.'/dist/jquery.min.js');
                        }
                    }

                    if (count($scripts)) {
                        foreach ($scripts as $script) {
                            if (substr($script, 0, 4) == 'http' || substr($script, 0, 2) == '//') {
                                $document->addScript($script);
                            } else {
                                $document->addScript($popupPath.'/'.$script);
                            }
                        }
                    }
                    if (count($scriptDeclarations)) {
                        foreach ($scriptDeclarations as $scriptDeclaration) {
                            $document->addScriptDeclaration($scriptDeclaration);
                        }
                    }

                    if ($legacyHeadIncludes) {
                        $document->addCustomTag($this->plg_copyrights_start.$legacyHeadIncludes.$this->plg_copyrights_end);
                    }

                    if ($extraClass) {
                        $extraClass = ' '.$extraClass;
                    }

                    if ($extraWrapperClass) {
                        $extraWrapperClass = ' '.$extraWrapperClass;
                    }

                    if ($customLinkAttributes) {
                        $customLinkAttributes = ' '.$customLinkAttributes;
                    }

                    $pluginCSS = $SIGHelper->getTemplatePath($this->plg_name, 'css/template.css', $thb_template);
                    $pluginCSS = $pluginCSS->http;
                    $document->addStyleSheet($pluginCSS.'?v='.$this->plg_version);
                }

                // Print output
                $isPrintPage = ($tmpl == "component" && $print !== false) ? true : false;

                // Fetch the template
                ob_start();
                $templatePath = $SIGHelper->getTemplatePath($this->plg_name, 'default.php', $thb_template);
                $templatePath = $templatePath->file;
                include $templatePath;
                $getTemplate = $this->plg_copyrights_start.ob_get_contents().$this->plg_copyrights_end;
                ob_end_clean();

                // Output
                $plg_html = $getTemplate;

                // Do the replace
                $row->text = preg_replace("#{".$this->plg_tag."}".preg_quote($tagcontent)."{/".$this->plg_tag."}#s", $plg_html, $row->text);
            }
        }
    }
}
