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

jimport('joomla.filesytem.file');

class PlgTZ_Portfolio_PlusMediaTypeImage extends TZ_Portfolio_PlusPlugin
{
    protected $autoloadLanguage = true;

    // Display html for views in front-end.
    public function onContentDisplayMediaType($context, &$article, $params, $page = 0, $layout = null){
        if($article){
            if($media = $article -> media){
                $image  = null;
                $image_properties = null;
                if(isset($media -> image)){
                    $image  = clone($media -> image);
                    if(isset($image -> url) && $image -> url) {
                        if ($size = $params->get('mt_image_size', 'o')) {
                            if (isset($image->url) && !empty($image->url)) {
                                $image_url_ext = File::getExt($image->url);
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url);
                                if (file_exists(JPATH_BASE.'/'.$image_url)) {
                                    $image_properties =   getimagesize(JPATH_BASE.'/'.$image_url);
                                }
                                $image->url = JURI::base(true) .'/'. $image_url;
                            }

                            if (isset($image->url_detail) && !empty($image->url_detail)) {
                                $image_url_ext = File::getExt($image->url_detail);
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url_detail);
                                $image->url_detail = JURI::base(true) . '/' . $image_url;
                            }
                        }
                    }
                }
                $this -> setVariable('image', $image);
                $this -> setVariable('image_properties', $image_properties);
            }
            $this -> setVariable('item', $article);

            return parent::onContentDisplayMediaType($context, $article, $params, $page, $layout);
        }
    }
}