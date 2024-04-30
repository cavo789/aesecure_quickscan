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

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\Registry\Registry;
use TZ_Portfolio_Plus\Image\TppImageWaterMark;

jimport('joomla.filesytem.file');
JLoader::register('TZ_Portfolio_PlusFrontHelper', JPATH_SITE
    .'/components/com_tz_portfolio_plus/helpers/tz_portfolio_plus.php');

class PlgTZ_Portfolio_PlusMediaTypeModelImage extends TZ_Portfolio_PlusPluginModelAdmin{

    public function save($data){

        $app    = JFactory::getApplication();
        $input  = $app -> input;

        $_data  = array('id' => ($data -> id), 'asset_id' => ($data -> asset_id),'media' => '{}');
        $alias  = '';
        if(is_array($data) && isset($data['alias'])){
            $alias  = $data['alias'];
        }elseif(is_object($data) && isset($data -> alias)){
            $alias  = $data -> alias;
        }

        $params     = $this -> getState('params');

        if($mainCategory = TZ_Portfolio_PlusHelperCategories::getMainCategoryByArticleId($data -> id)) {
            $mainCategory = $mainCategory[0];
            $categoryParams = new Registry();
            $categoryParams -> loadString($mainCategory -> params);
            $watermarkOptions   = new Registry($categoryParams -> get('mt_image_watermark_admin_options', array()));
            $params -> merge($watermarkOptions);
        }

        // Get some params
        $mime_types     = $params -> get('image_mime_type','image/jpeg,image/gif,image/png,image/bmp');
        $mime_types     = explode(',',$mime_types);
        $file_types     = $params -> get('image_file_type','bmp,gif,jpg,jpeg,png');
        $file_types     = explode(',',$file_types);
        $file_sizes     = $params -> get('image_file_size',10);
        $file_sizes     = $file_sizes * 1024 * 1024;

        // Get and Process data
        $image_data = $input -> get('jform', null, 'array');
        if(isset($image_data['media'])) {
            if(isset($image_data['media'][$this->getName()])) {
                $image_data = $image_data['media']['image'];
            }
        }

        $media  = null;
        if($data -> media && !empty($data -> media)) {
            $media  = new JRegistry;
            $media -> loadString($data -> media);
            $media  = $media -> get('image');
        }

        // Set data when save as copy article
        if($input -> getCmd('task') == 'save2copy' && $input -> getInt('id')){
            if((isset($image_data['url_remove']) && $image_data['url_remove'])){
                $image_data['url_remove']   = null;
                $image_data['url']          = '';
            }
            if((isset($image_data['url_detail_remove']) && $image_data['url_detail_remove'])){
                $image_data['url_detail_remove'] = '';
                $image_data['url_detail']        = '';
            }
            if(!isset($image_data['url_server'])
                || (isset($image_data['url_server']) && empty($image_data['url_server']))){
                if(isset($image_data['url']) && $image_data['url']) {
                    $ext        = File::getExt($image_data['url']);
                    $path_copy  = str_replace('.'.$ext,'_o.'.$ext, $image_data['url']);
                    if(File::exists(JPATH_ROOT.DIRECTORY_SEPARATOR.$path_copy)) {
                        $image_data['url_server']   = $path_copy;
                        $image_data['url']          = '';
                    }
                }
            }
            if(!isset($image_data['url_detail_server'])
                || (isset($image_data['url_detail_server']) && empty($image_data['url_detail_server']))){
                if(isset($image_data['url_detail']) && $image_data['url_detail']) {
                    $ext        = File::getExt($image_data['url_detail']);
                    $path_copy  = str_replace('.'.$ext,'_o.'.$ext, $image_data['url_detail']);
                    if(File::exists(JPATH_ROOT.DIRECTORY_SEPARATOR.$path_copy)) {
                        $image_data['url_detail_server']   = $path_copy;
                        $image_data['url_detail']          = '';
                    }
                }
            }
        }

        // Remove image and image hover with resized
        if($image_size = $params -> get('image_size', array())){

            $image_size = $this -> prepareImageSize($image_size);

            if(is_array($image_size) && count($image_size)){
                foreach($image_size as $_size){
                    $size           = json_decode($_size);

                    // Delete old image files
                    if((isset($image_data['url_remove']) && $image_data['url_remove'])
                        && $media && isset($media -> url) && !empty($media -> url)){

                        $image_url  = TZ_Portfolio_PlusFrontHelper::getImageURLBySize($media -> url,
                            $size ->image_name_prefix);

                        if(File::exists(JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . $image_url))) {
                            File::delete(JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . $image_url));
                        }
                    }elseif(isset($image_data['url']) && empty($image_data['url']) && !empty($alias)){
                        // Remove all old images of this article if it has images
                        $murl       = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_BASE.'/'.$alias;
                        $old_files  = glob(JPATH_ROOT.DIRECTORY_SEPARATOR.$murl.'_'.$size ->image_name_prefix.'.*');
                        if(!empty($old_files)) {
                            array_map('Joomla\CMS\Filesystem\File::delete', $old_files);
                        }
                    }

                    // Delete old image hover files
                    if((isset($image_data['url_detail_remove']) && $image_data['url_detail_remove'])
                        && $media && isset($media -> url_detail) && !empty($media -> url_detail)){

                        $image_url  = TZ_Portfolio_PlusFrontHelper::getImageURLBySize($media -> url_detail,
                            $size ->image_name_prefix);

                        if(File::exists(JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . $image_url))) {
                            File::delete(JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . $image_url));
                        }
                    }elseif(isset($image_data['url_detail']) && empty($image_data['url_detail']) && !empty($alias)){
                        // Remove all old images of this article if it has images
                        $murl       = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_BASE.'/'.$alias;
                        $old_files  = glob(JPATH_ROOT.DIRECTORY_SEPARATOR.$murl.'-h_'.$size ->image_name_prefix.'.*');
                        if(!empty($old_files)) {
                            array_map('Joomla\CMS\Filesystem\File::delete', $old_files);
                        }
                    }
                }
            }
        }

        // Remove Image file when tick to remove file box
        if(isset($image_data['url_remove']) && $image_data['url_remove']){
            // Before upload image to file must delete original file
            if($media && isset($media -> url) && !empty($media -> url)){

                $image_url  = TZ_Portfolio_PlusFrontHelper::getImageURLBySize($media -> url, 'o');

                if(File::delete(JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . $image_url))){
                    $image_data['url']    = '';
                    unset($image_data['url_remove']);
                }
            }
        }else{
            if(isset($image_data['url']) && empty($image_data['url']) && !empty($alias)){
                $old_files  = glob(JPATH_ROOT.DIRECTORY_SEPARATOR.$murl.'_o.*');
                if(!empty($old_files)) {
                    array_map('Joomla\CMS\Filesystem\File::delete', $old_files);
                }
            }
            unset($image_data['url']);
        }

        // Remove Image detail file when tick to remove file box
        if(isset($image_data['url_detail_remove']) && $image_data['url_detail_remove']){
            // Before upload image to file must delete original file
            if($media && isset($media -> url_detail) && !empty($media -> url_detail)){

                $image_url  = TZ_Portfolio_PlusFrontHelper::getImageURLBySize($media -> url_detail, 'o');

                if(File::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,
                        $image_url))){
                    $image_data['url_detail']    = '';
                    unset($image_data['url_detail_remove']);
                }
            }
        }else{
            if(isset($image_data['url_detail']) && empty($image_data['url_detail']) && !empty($alias)){
                $old_files  = glob(JPATH_ROOT.DIRECTORY_SEPARATOR.$murl.'h_o.*');
                if(!empty($old_files)) {
                    array_map('Joomla\CMS\Filesystem\File::delete', $old_files);
                }
            }
            unset($image_data['url_detail']);
        }

        $images         = array();
        $images_hover   = array();
        $imageObj       = new JImage();

        // Upload image or image hover
        if($files = $input -> files -> get('jform', array(), 'array')) {

            if(isset($files['media']) && isset($files['media']['image'])){
                $files  = $files['media']['image'];

                // Get image from form
                if(isset($files['url_client']['name']) && !empty($files['url_client']['name'])) {
                    $images = $files['url_client'];
                }

                // Get image hover data from form
                if(isset($files['url_detail_client']['name']) && !empty($files['url_detail_client']['name'])) {
                    $images_hover    = $files['url_detail_client'];
                }
            }
        }

        $path               = '';
        $path_hover         = '';

        jimport('joomla.filesystem.file');

        $imageType              = null;
        $imageMimeType          = null;
        $imageSize              = null;
        $image_hoverType        = null;
        $image_hoverMimeType    = null;
        $image_hoverSize        = null;

        // Create dir if not exists
        if(!is_dir(COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT)){
            Folder::create(COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT);
        }

        // Create original image with new name (upload from client)
        if(count($images) && !empty($images['tmp_name'])) {

            // Get image file type
            $imageType  = File::getExt($images['name']);
            $imageType  = strtolower($imageType);

            // Get image's mime type
            $imageMimeType  = $images['type'];

            // Get image's size
            $imageSize  = $images['size'];

            $path   = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT.DIRECTORY_SEPARATOR;
            $path  .=  $data -> alias . '-' . $data -> id . '_o';
            $path  .= '.' . File::getExt($images['name']);

            if($input -> getCmd('task') == 'save2copy' && $input -> getInt('id')){
                $image_data['url_server']   = null;
            }
        }elseif(isset($image_data['url_server'])
            && !empty($image_data['url_server'])){ // Create original image with new name (upload from server)

            $url_server = $image_data['url_server'];
            if(strpos($url_server, '#') != false) {
                list($url_server, $other) = explode('#', $url_server);
            }

            // Get image file type
            $imageType  = File::getExt($url_server);
            $imageType  = strtolower($imageType);


            // Get image's mime type
            $imageObj -> loadFile(JPATH_ROOT . DIRECTORY_SEPARATOR
                . $url_server);
            $imageProperty  = $imageObj->getImageFileProperties($imageObj->getPath());
            $imageMimeType  = $imageProperty -> mime;

            // Get image's size
            $imageSize  = $imageProperty -> filesize;

            $path   = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT.DIRECTORY_SEPARATOR;
            $path  .=  $data -> alias . '-' . $data -> id . '_o';
            $path  .= '.' . File::getExt($url_server);
        }

        // Create original image hover with new name (upload from client)
        if(count($images_hover) && !empty($images_hover['tmp_name'])) {

            // Get image hover file type
            $image_hoverType  = File::getExt($images_hover['name']);
            $image_hoverType  = strtolower($image_hoverType);

            // Get image hover's mime type
            $image_hoverMimeType    = $images_hover['type'];

            // Get image's size
            $image_hoverSize    = $images_hover['size'];

            $path_hover     = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT.DIRECTORY_SEPARATOR;
            $path_hover    .= $data -> alias . '-' . $data -> id . '-h_o';
            $path_hover    .= '.' . File::getExt($images_hover['name']);

            if($input -> getCmd('task') == 'save2copy' && $input -> getInt('id')){
                $image_data['url_detail_server']   = null;
            }
        }elseif(isset($image_data['url_detail_server'])
            && !empty($image_data['url_detail_server'])){ // Create original image with new name (upload from server)

            $url_detail_server = $image_data['url_detail_server'];
            if(strpos($url_detail_server, '#') != false) {
                list($url_detail_server, $other) = explode('#', $url_detail_server);
            }

            // Get image hover file type
            $image_hoverType  = File::getExt($url_detail_server);
            $image_hoverType  = strtolower($image_hoverType);

            // Get image hover's mime type
            $imageObj -> loadFile(JPATH_ROOT . DIRECTORY_SEPARATOR
                . $url_detail_server);

            $image_hoverProperty    = $imageObj->getImageFileProperties($imageObj->getPath());
            $image_hoverMimeType    = $image_hoverProperty -> mime;

            // Get image hover's size
            $image_hoverSize  = $image_hoverProperty -> filesize;

            $path_hover     = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT.DIRECTORY_SEPARATOR;
            $path_hover    .=  $data -> alias . '-' . $data -> id . '-h_o';
            $path_hover    .= '.' . File::getExt($url_detail_server);
        }

        // Upload original image
        if($path && !empty($path)){

            //-- Check image information --//
            // Check MIME Type
            if (!in_array($imageMimeType, $mime_types)) {
                $app->enqueueMessage(JText::_('PLG_MEDIATYPE_IMAGE_ERROR_WARNINVALID_MIME'), 'notice');
                return false;
            }

            // Check file type
            if (!in_array($imageType, $file_types)) {
                $app->enqueueMessage(JText::_('PLG_MEDIATYPE_IMAGE_ERROR_WARNFILETYPE'), 'notice');
                return false;
            }

            // Check file size
            if ($imageSize > $file_sizes) {
                $app->enqueueMessage(JText::_('PLG_MEDIATYPE_IMAGE_ERROR_WARNFILETOOLARGE'), 'notice');
                return false;
            }
            //-- End check image information --//

            // Before upload image to file must delete original file
            if($media && isset($media -> url) && !empty($media -> url)){

                $image_url  = TZ_Portfolio_PlusFrontHelper::getImageURLBySize($media -> url, 'o');

                $imgPath  = JPath::clean(JPATH_ROOT.DIRECTORY_SEPARATOR.'/'.$image_url);

                if(File::exists($imgPath)) {
                    File::delete($imgPath);
                }
            }

            if(isset($images['tmp_name']) && !empty($images['tmp_name'])
                && !File::upload($images['tmp_name'],$path)){
                $path       = '';
            }elseif(isset($url_server) && !empty($url_server)
                && !File::copy(JPATH_ROOT.DIRECTORY_SEPARATOR.$url_server,$path)){
                $path       = '';
            }
        }

        // Upload original image hover
        if($path_hover && !empty($path_hover)){

            //-- Check image information --//
            // Check MIME Type
            if (!in_array($image_hoverMimeType, $mime_types)) {
                $app->enqueueMessage(JText::_('PLG_MEDIATYPE_IMAGE_ERROR_WARNINVALID_MIME'), 'notice');
                return false;
            }

            // Check file type
            if (!in_array($image_hoverType, $file_types)) {
                $app->enqueueMessage(JText::_('PLG_MEDIATYPE_IMAGE_ERROR_WARNFILETYPE'), 'notice');
                return false;
            }

            // Check file size
            if ($image_hoverSize > $file_sizes) {
                $app->enqueueMessage(JText::_('PLG_MEDIATYPE_IMAGE_ERROR_WARNFILETOOLARGE'), 'notice');
                return false;
            }
            //-- End check image information --//

            // Before upload image hover file to file must delete original file
            if($media && isset($media -> url_detail) && !empty($media -> url_detail)){
                $image_url  = $media -> url_detail;
                $image_url  = str_replace('.'.File::getExt($image_url),'_o'
                    .'.'.File::getExt($image_url),$image_url);

                $imgDetailPath  = JPath::clean(JPATH_ROOT.DIRECTORY_SEPARATOR.'/'.$image_url);
                if(File::exists($imgDetailPath)) {
                    File::delete($imgDetailPath);
                }
            }

            if(isset($images_hover['tmp_name']) && !empty($images_hover['tmp_name'])
                && !File::upload($images_hover['tmp_name'],$path_hover)){
                $path_hover = '';
            }elseif(isset($url_detail_server) && !empty($url_detail_server)
                && !File::copy(JPATH_ROOT.DIRECTORY_SEPARATOR.$url_detail_server,$path_hover)){
                $path_hover = '';
            }
        }

        // Upload image and image hover with resize
        if($image_size = $params -> get('image_size')){
            $image_size = $this -> prepareImageSize($image_size);

            $image              = null;
            $image_hover        = null;

            if(is_array($image_size) && count($image_size)){
                foreach($image_size as $_size){
                    $size       = json_decode($_size);

                    // Upload image with resize
                    if($path) {
                        // Create new ratio from new with of image size param
                        $imageObj -> loadFile($path);
                        $imgProperties  = $imageObj->getImageFileProperties($imageObj -> getPath());
                        $newH           = ($imgProperties -> height * $size -> width) / ($imgProperties -> width);
                        $newImage       = $imageObj->resize($size -> width, $newH);

                        $newPath = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT . DIRECTORY_SEPARATOR
                            . $data->alias . '-' . $data->id . '_' . $size->image_name_prefix
                            . '.' . File::getExt($path);

                        // Before generate image to file must delete old files
                        if($media && isset($media -> url) && !empty($media -> url)){
                            $image_url  = $media -> url;
                            $image_url  = str_replace('.'.File::getExt($image_url),'_'.$size ->image_name_prefix
                                .'.'.File::getExt($image_url),$image_url);

                            $imgPath  = JPath::clean(JPATH_ROOT.DIRECTORY_SEPARATOR.'/'.$image_url);
                            if(File::exists($imgPath)) {
                                File::delete($imgPath);
                            }
                        }

                        // Generate image to file
                        $newImage->toFile($newPath, $imgProperties->type);

                        // Add watermark for each image size
                        $this -> watermark($newPath, $size ->image_name_prefix);
                    }
//                    elseif(isset($media -> url) && $media -> url){
//                        // Add watermark for each image size
//                        $this -> watermark(TZ_Portfolio_PlusFrontHelper::getImageURLBySize(JPATH_ROOT
//                            .DIRECTORY_SEPARATOR.$media -> url,
//                            $size ->image_name_prefix), $size ->image_name_prefix, $fontSize, $coordinates);
//                    }

                    // Upload image hover with resize
                    if($path_hover) {
                        // Create new ratio from new with of image size param
                        $imageObj -> loadFile($path_hover);
                        $imgHoverProperties = $imageObj->getImageFileProperties($imageObj -> getPath());
                        $newH               = ($imgHoverProperties -> height * $size -> width) / ($imgHoverProperties -> width);
                        $newHImage          = $imageObj->resize($size -> width, $newH);
                        $newHPath           = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT . DIRECTORY_SEPARATOR
                            . $data->alias . '-' . $data->id . '-h_' . $size -> image_name_prefix
                            . '.' . File::getExt($path_hover);

                        // Before generate image hover to file must delete old files
                        if($media && isset($media -> url_detail) && !empty($media -> url_detail)){
                            $image_url_detail    = $media -> url_detail;
                            $image_url_detail    = str_replace('.'.File::getExt($image_url_detail),'_'.$size ->image_name_prefix
                                .'.'.File::getExt($image_url_detail),$image_url_detail);


                            $imgDetailPath  = JPath::clean(JPATH_ROOT.DIRECTORY_SEPARATOR.'/'.$image_url_detail);
                            if(File::exists($imgDetailPath)) {
                                File::delete($imgDetailPath);
                            }
                        }

                        // Generate image to file
                        $newHImage->toFile($newHPath, $imgHoverProperties->type);

                        // Add watermark for each image size
                        if($params -> get('mt_image_watermark_img_detail', 0)) {
                            $this->watermark($newHPath, $size->image_name_prefix);
                        }
                    }
//                    elseif(isset($media -> url_detail) && $media -> url_detail
//                        && $params -> get('mt_image_watermark_img_detail', 0)){
//                        // Add watermark for each image size
//                        $this -> watermark(TZ_Portfolio_PlusFrontHelper::getImageURLBySize(JPATH_ROOT
//                            .DIRECTORY_SEPARATOR.$media -> url_detail,
//                            $size ->image_name_prefix), $size ->image_name_prefix, $fontSize, $coordinates);
//                    }
                }
            }
        }

        if($path && !empty($path)){
            $this -> watermark($path, 'o');
            $image_data['url']   = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_BASE.'/'
                .$data -> alias . '-' . $data -> id. '.' . File::getExt($path);
        }
//        elseif(isset($media -> url) && $media -> url){
//            $this -> watermark(JPATH_ROOT.DIRECTORY_SEPARATOR
//                .TZ_Portfolio_PlusFrontHelper::getImageURLBySize($media -> url, 'o'), 'o');
//        }

        if($path_hover && !empty($path_hover)){
            if($params -> get('mt_image_watermark_img_detail', 0)) {
                $this->watermark($path_hover, 'o');
            }
            $image_data['url_detail']   = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_BASE.'/'
                .$data -> alias . '-' . $data -> id. '-h.' . File::getExt($path_hover);
        }
//        elseif(isset($media -> url_detail) && $media -> url_detail
//            && $params -> get('mt_image_watermark_img_detail', 0)){
//            $this -> watermark(JPATH_ROOT.DIRECTORY_SEPARATOR
//                .TZ_Portfolio_PlusFrontHelper::getImageURLBySize($media -> url_detail, 'o'), 'o');
//        }

        unset($image_data['url_server']);
        unset($image_data['url_detail_server']);

        $this -> __save($data,$image_data);
//        }
    }

    public function delete(&$article){
        if($article){
            if(is_object($article)){
                if($article -> media && !empty($article -> media)) {
                    $media  = new JRegistry;
                    $media -> loadString($article -> media);

                    $media  = $media -> get('image');
                    $params = $this -> getState('params');

                    if($media){
                        if(isset($media -> url) && !empty($media -> url)){
                            // Delete original image
                            $image_url  = str_replace('.'.File::getExt($media->url),
                                '_o.'.File::getExt($media->url),$media->url);
                            File::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,
                                    $image_url));
                        }

                        if(isset($media -> url_detail) && !empty($media -> url_detail)){
                            // Delete original image hover
                            $image_url  = str_replace('.'.File::getExt($media->url_detail),
                                '_o.'.File::getExt($media->url_detail),$media->url_detail);
                            File::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,
                                    $image_url));
                        }

                        // Delete image with some size
                        if($image_size = $params -> get('image_size', array())){

                            $image_size = $this -> prepareImageSize($image_size);

                            if(is_array($image_size) && count($image_size)){
                                foreach($image_size as $_size){
                                    $size           = json_decode($_size);

                                    // Delete image
                                    if(isset($media -> url) && !empty($media -> url)) {
                                        // Create file name and execute delete image
                                        $image_url = str_replace('.' . File::getExt($media->url), '_' . $size->image_name_prefix
                                            . '.' . File::getExt($media->url), $media->url);
                                        File::delete(JPATH_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR,
                                                $image_url));
                                    }

                                    // Delete image hover
                                    if(isset($media -> url_detail) && !empty($media -> url_detail)) {
                                        // Create file name and execute delete image
                                        $image_url = str_replace('.' . File::getExt($media->url_detail), '_' . $size->image_name_prefix
                                            . '.' . File::getExt($media->url_detail), $media->url_detail);
                                        File::delete(JPATH_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR,
                                                $image_url));
                                    }
                                }
                            }
                        }
                    }
                }

            }
        }
    }

    protected function watermark($file, $imgType = '', $fontSize = 0, $cordinates = ''){

        $params     = $this -> getState('params');

        if(!$params -> get('mt_image_watermark', 0) || !$file){
            return false;
        }

        if(!$params -> get('mt_image_wtm_original_image', 0) && $imgType == 'o'){
            return false;
        }

        tzportfolioplusimport('image.watermark');

//        if (is_resource($file) && (get_resource_type($file) != 'gd')) {
//            $mainLayer = TppImageWaterMark::initFromResourceVar($file);
//        }else{
        $mainLayer = TppImageWaterMark::initFromPath($file);
//        }


        $stype      = $params -> get('mt_image_watermark_stype', 'text');
        $text       = $params -> get('mt_image_watermark_text');
        $fontPath   = JPATH_ROOT.'/'.$params -> get('mt_image_watermark_fontpath',
                'administrator/components/com_tz_portfolio_plus/fonts/arial.ttf');
        $_fontSize  = $fontSize?$fontSize:$params -> get('mt_image_watermark_fontsize', 14);
        $textColor  = $params -> get('mt_image_watermark_color', '#fff');
        $textColor  = str_replace('#', '', $textColor);
        $image      = $params -> get('mt_image_watermark_image');
        $bgColor    = $params -> get('mt_image_watermark_bgcolor');
        $bgColor    = $bgColor?str_replace('#', '', $bgColor):null;
        $rotate     = $params -> get('mt_image_watermark_rotate', 0);
        $opacity    = $params -> get('mt_image_watermark_opacity');
        $filter     = $params -> get('mt_image_watermark_filter', -1);
        $flip       = $params -> get('mt_image_watermark_flip', 0);
        $position   = $params -> get('mt_image_watermark_position', TppImageWaterMark::POSITION_TOP_LEFT);

        $_coordinates= $cordinates?$cordinates:$params -> get('mt_image_watermark_coordinates', '10,10');

        list($positionX, $positionY)    = explode(',', $_coordinates,2);

        switch($stype){
            default:
            case 'text':
                // This is the text layer
                if($text){
                    $layer  = TppImageWaterMark::initTextLayer($text, $fontPath, $_fontSize, $textColor, 0, $bgColor);
                }
                break;
            case 'image':
                if($image) {
                    $layer = TppImageWaterMark::initFromPath(JPATH_ROOT . '/' . $image);
//                    $layer -> rotate($rotate);
                }
                break;
        }

        if(isset($layer) && $layer){
            if($rotate){
                $layer -> rotate($rotate);
            }
            if($opacity != null){
                $layer -> opacity($opacity);
            }
            if($filter > -1){
                $layer -> applyFilter($filter);
            }

            if(is_string($flip)){
                $layer -> flip($flip);
            }elseif(is_array($flip)){
                foreach($flip as $_flip){
                    $layer -> flip($_flip);
                }
            }

            if($params -> get('mt_image_watermark_resize', 1)) {
                $rwPer   = $params -> get('mt_image_watermark_resize_wpercent', 30);
                $rhPer   = $params -> get('mt_image_watermark_resize_hpercent', 0);
                $nW     = null;
                $nH     = null;
                if($rwPer) {
                    $mW = $mainLayer -> getWidth();
                    $nW = $mW * $rwPer / 100;
                }
                if($rhPer) {
                    $mH = $mainLayer -> getHeight();
                    $nH = $mH * $rhPer / 100;
                }
                if($nW || $nH) {
                    $layer->resizeInPixel($nW, $nH, true,0, 0, $position);
                }
            }

            $mainLayer -> addLayerOnTop($layer,(int)$positionX, (int)$positionY, $position );

            $mainLayer -> save(COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT,basename($file), true, null, 100);
        }
    }
}