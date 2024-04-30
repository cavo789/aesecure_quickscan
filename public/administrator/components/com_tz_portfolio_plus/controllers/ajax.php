<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;

jimport('joomla.application.component.controllerform');

/**
 * The Category Controller.
 */
class TZ_Portfolio_PlusControllerAjax extends JControllerForm
{
    /**
     * The extension for which the categories apply.
     *
     * @var    string
     * @since  1.6
     */
    protected $extension	= 'com_tz_portfolio_plus';

    public function gallery_upload() {
        header('Content-Type: application/json');
        try {
            $japp   = Factory::getApplication();
            $input  = $japp -> input;

            if(!$japp->isClient('administrator')){
                throw new RuntimeException('You are not authorized!');
            }

            // Get the uploaded file information.
            // Do not change the filter type 'raw'. We need this to let files containing PHP code to upload. See JInputFiles::get.
            $userfile   =   $input->files->get('file', null, 'raw');
            $folder     =   $input->get('folder','');
            if (
                !isset($userfile['error']) ||
                is_array($userfile['error'])
            ) {
                throw new RuntimeException('Invalid parameters.');
            }

            switch ($userfile['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('No file sent.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('Exceeded filesize limit.');
                default:
                    throw new RuntimeException('Unknown errors.');
            }

            // Build the appropriate paths.
            jimport('joomla.filesystem.file');
            jimport('joomla.filesystem.folder');
            $filename           =   \JApplicationHelper::stringURLSafe(File::stripExt($userfile['name'])).'.'.File::getExt($userfile['name']);

            $config             =   Factory::getConfig();
            $tmp_dest           =   $config->get('tmp_path') . '/' .$folder . '/' . $filename;
            $tmp_resize_folder  =   $config->get('tmp_path') . '/' .$folder . '/resize';
            $tmp_src            =   $userfile['tmp_name'];
            if (!File::upload($tmp_src, $tmp_dest)) {
                throw new RuntimeException('Failed to move uploaded file.');
            }

            // Resize image
            if (Folder::create($tmp_resize_folder)) {
                $addon      =   TZ_Portfolio_PlusPluginHelper::getPlugin('content','gallery');
                $params     =   new JRegistry($addon->params);
                if ($params && $image_size = $params->get('gallery_size')) {
                    if($image_size && !is_array($image_size) && preg_match_all('/(\{.*?\})/',$image_size,$match)) {
                        $image_size = $match[1];
                    }
                    $gallery   =   new JImage();

                    $gallery -> destroy();
                    $gallery -> loadFile($tmp_dest);

                    foreach ($image_size as $_size) {
                        $size = json_decode($_size);

                        $newPath = $tmp_resize_folder . DIRECTORY_SEPARATOR
                            . File::stripExt($filename)
                            . '_' . $size->image_name_prefix . '.' . File::getExt($filename);

                        // Create new ratio from new with of image size param
                        $imageProperties   = $gallery->getImageFileProperties($tmp_dest);
                        $newH              = ($imageProperties->height * $size->width) / ($imageProperties->width);
                        $newImage          = $gallery->resize($size->width, $newH);

                        // Before upload image to file must delete original file
                        if (File::exists($newPath)) {
                            // Execute delete image
                            File::delete($newPath);
                        }

                        // Generate image to file
                        if (!$newImage->toFile($newPath, $imageProperties->type)) {
                            throw new RuntimeException('Failed to resize image!');
                        }

                    }
                } else {
                    throw new RuntimeException('Failed to read Addon parameter.');
                }
            } else {
                throw new RuntimeException('Failed to create resize folder!.');
            }

            // All good, send the response
            echo json_encode([
                'status' => 'ok',
                'name'  => $filename
            ]);

        } catch (RuntimeException $e) {
            // Something went wrong, send the err message as JSON
            http_response_code(400);

            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        die();
    }
}
