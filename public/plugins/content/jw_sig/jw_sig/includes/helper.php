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

class SimpleImageGalleryHelper
{
    public $srcimgfolder;
    public $thb_width;
    public $thb_height;
    public $smartResize;
    public $jpg_quality;
    public $cache_expire_time;
    public $gal_id;
    public $format;

    public function renderGallery()
    {
        // Initialize
        $srcimgfolder = $this->srcimgfolder;
        $thb_width = $this->thb_width;
        $thb_height = $this->thb_height;
        $smartResize = $this->smartResize;
        $jpg_quality = $this->jpg_quality;
        $cache_expire_time = $this->cache_expire_time;
        $gal_id = $this->gal_id;
        $format = $this->format;

        // API
        jimport('joomla.filesystem.folder');

        // Path assignment
        $sitePath = JPATH_SITE.'/';
        if ($format == 'feed') {
            $siteUrl = JURI::root(true).'';
        } else {
            $siteUrl = JURI::root(true).'/';
        }

        // Internal parameters
        $prefix = "jw_sig_cache_";

        // Set the cache folder
        $cacheFolderPath = JPATH_SITE.'/cache/jw_sig';
        if (file_exists($cacheFolderPath) && is_dir($cacheFolderPath)) {
            // all OK
        } else {
            mkdir($cacheFolderPath);
        }

        // Check if the source folder exists and read it
        $srcFolder = JFolder::files($sitePath.$srcimgfolder);

        // Proceed if the folder is OK or fail silently
        if (!$srcFolder) {
            return;
        }

        // Loop through the source folder for images
        $fileTypes = array('gif', 'jpg', 'jpeg', 'png', 'webp');

        // Create an array of file types
        $found = array();

        // Create an array for matching files
        foreach ($srcFolder as $srcImage) {
            $fileInfo = pathinfo($srcImage);
            if (array_key_exists('extension', $fileInfo) && in_array(strtolower($fileInfo['extension']), $fileTypes)) {
                $found[] = $srcImage;
            }
        }

        // Bail out if there are no images found
        if (count($found) == 0) {
            return;
        }

        // Sort array
        sort($found);

        // Initiate array to hold gallery
        $gallery = array();

        // Loop through the image file list
        foreach ($found as $key => $filename) {

            // Determine thumb image filename
            if (strtolower(substr($filename, -4, 4)) == 'jpeg' || strtolower(substr($filename, -4, 4)) == 'webp') {
                $thumbfilename = substr($filename, 0, -4).'jpg';
            } elseif (strtolower(substr($filename, -3, 3)) == 'gif' || strtolower(substr($filename, -3, 3)) == 'jpg' || strtolower(substr($filename, -3, 3)) == 'png') {
                $thumbfilename = substr($filename, 0, -3).'jpg';
            }

            // Object to hold each image elements
            $gallery[$key] = new JObject;

            // Assign source image and path to a variable
            $original = $sitePath.$srcimgfolder.'/'.$filename;

            // Check if thumb image exists already
            $thumbimage = $cacheFolderPath.'/'.$prefix.$gal_id.'_'.strtolower($this->cleanThumbName($thumbfilename));

            if (file_exists($thumbimage) && is_readable($thumbimage) && (filemtime($thumbimage) + $cache_expire_time) > time()) {
                // Do nothing
            } else {
                // Otherwise create the thumb image

                // Begin by getting the details of the original
                list($width, $height, $type) = getimagesize($original);

                // Create an image resource for the original
                switch ($type) {
                    case 1:
                        // GIF
                        $source = imagecreatefromgif($original);
                        break;
                    case 2:
                        // JPEG
                        $source = imagecreatefromjpeg($original);
                        break;
                    case 3:
                        // PNG
                        $source = imagecreatefrompng($original);
                        break;
                    case 18:
                        // WEBP
                        if (version_compare(PHP_VERSION, '7.1.0', 'ge')) {
                            $source = imagecreatefromwebp($original);
                        } else {
                            $source = null;
                        }
                        break;
                    default:
                        $source = null;
                }

                // Bail out if the image resource is not OK
                if (!$source) {
                    if (version_compare(JVERSION, '4', 'ge')) {
                        $app = JFactory::getApplication();
                        $app->enqueueMessage(JText::_('JW_PLG_SIG_ERROR_SRC_IMGS'), 'notice');
                    } else {
                        JError::raiseNotice('', JText::_('JW_PLG_SIG_ERROR_SRC_IMGS'));
                    }
                    return;
                }

                // Calculate thumbnails
                $thumbnail = $this->thumbDimCalc($width, $height, $thb_width, $thb_height, $smartResize);

                $thumb_width = $thumbnail['width'];
                $thumb_height = $thumbnail['height'];

                // Create an image resource for the thumbnail
                $thumb = imagecreatetruecolor($thumb_width, $thumb_height);

                // Create the resized copy
                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);

                // Convert and save all thumbs to .jpg
                $success = imagejpeg($thumb, $thumbimage, $jpg_quality);

                // Bail out if there is a problem in the GD conversion
                if (!$success) {
                    return;
                }

                // Remove the image resources from memory
                imagedestroy($source);
                imagedestroy($thumb);
            }

            // Assemble the image elements
            $gallery[$key]->filename = $filename;
            $gallery[$key]->sourceImageFilePath = $siteUrl.$srcimgfolder.'/'.$this->replaceWhiteSpace($filename);
            $gallery[$key]->thumbImageFilePath = $siteUrl.'cache/jw_sig/'.$prefix.$gal_id.'_'.strtolower($this->cleanThumbName($thumbfilename));
            $gallery[$key]->width = $thb_width;
            $gallery[$key]->height = $thb_height;
        }

        return $gallery;
    }



    /* ------------------ Helper Functions ------------------ */

    // Calculate thumbnail dimensions
    private function thumbDimCalc($width, $height, $thb_width, $thb_height, $smartResize)
    {
        if ($smartResize) {
            // thumb ratio bigger that container ratio
            if ($width / $height > $thb_width / $thb_height) {
                // wide containers
                if ($thb_width >= $thb_height) {
                    // wide thumbs
                    if ($width > $height) {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                    // high thumbs
                    else {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                    // high containers
                } else {
                    // wide thumbs
                    if ($width > $height) {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                    // high thumbs
                    else {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                }
            } else {
                // wide containers
                if ($thb_width >= $thb_height) {
                    // wide thumbs
                    if ($width > $height) {
                        $thumb_width = $thb_width;
                        $thumb_height = $thb_width * $height / $width;
                    }
                    // high thumbs
                    else {
                        $thumb_width = $thb_width;
                        $thumb_height = $thb_width * $height / $width;
                    }
                    // high containers
                } else {
                    // wide thumbs
                    if ($width > $height) {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                    // high thumbs
                    else {
                        $thumb_width = $thb_width;
                        $thumb_height = $thb_width * $height / $width;
                    }
                }
            }
        } else {
            if ($width > $height) {
                $thumb_width = $thb_width;
                $thumb_height = $thb_width * $height / $width;
            } elseif ($width < $height) {
                $thumb_width = $thb_height * $width / $height;
                $thumb_height = $thb_height;
            } else {
                $thumb_width = $thb_width;
                $thumb_height = $thb_height;
            }
        }

        $thumbnail = array();
        $thumbnail['width'] = round($thumb_width);
        $thumbnail['height'] = round($thumb_height);

        return $thumbnail;
    }

    // Replace white space
    private function replaceWhiteSpace($text_to_parse)
    {
        $source_html = array(" ");
        $replacement_html = array("%20");
        return str_replace($source_html, $replacement_html, $text_to_parse);
    }

    // Cleanup thumbnail filenames
    private function cleanThumbName($text_to_parse)
    {
        $source_html = array(' ', ',');
        $replacement_html = array('_', '_');
        return str_replace($source_html, $replacement_html, $text_to_parse);
    }

    // Path overrides
    public function getTemplatePath($pluginName, $file, $tmpl)
    {
        $app = JFactory::getApplication();
        $template = $app->getTemplate();

        $p = new stdClass;

        if (file_exists(JPATH_SITE.'/templates/'.$template.'/html/'.$pluginName.'/'.$tmpl.'/'.$file)) {
            $p->file = JPATH_SITE.'/templates/'.$template.'/html/'.$pluginName.'/'.$tmpl.'/'.$file;
            $p->http = JURI::root(true)."/templates/".$template."/html/{$pluginName}/{$tmpl}/{$file}";
        } else {
            if (version_compare(JVERSION, '2.5.0', 'ge')) {
                // Joomla 2.5+
                $p->file = JPATH_SITE.'/plugins/content/'.$pluginName.'/'.$pluginName.'/tmpl/'.$tmpl.'/'.$file;
                $p->http = JURI::root(true)."/plugins/content/{$pluginName}/{$pluginName}/tmpl/{$tmpl}/{$file}";
            } else {
                // Joomla 1.5
                $p->file = JPATH_SITE.'/plugins/content/'.$pluginName.'/tmpl/'.$tmpl.'/'.$file;
                $p->http = JURI::root(true)."/plugins/content/{$pluginName}/tmpl/{$tmpl}/{$file}";
            }
        }
        return $p;
    }
}
