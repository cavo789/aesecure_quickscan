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

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\File;

jimport('joomla.filesystem.file');

class PlgTZ_Portfolio_PlusMediaTypeImageViewArticle extends JViewLegacy{

    protected $item     = null;
    protected $params   = null;
    protected $image    = null;
    protected $state    = null;
    protected $head     = false;
    protected $image_properties     = false;

    public function display($tpl = null){
        $state          = $this -> get('State');
        $params         = $state -> get('params');
        $this -> state  = $state;
        $this -> params = $params;
        $item           = $this -> get('Item');
        $this -> image  = null;

        if($item){
            if($media = $item -> media){
                if(isset($media -> image)){
                    $doc    = JFactory::getDocument();
                    $doc -> addStyleSheet(TZ_Portfolio_PlusUri::root(true).'/css/jquery.fancybox.min.css', array('version' => 'auto'));
                    $doc -> addScript(TZ_Portfolio_PlusUri::root(true).'/js/jquery.fancybox.min.js', array('version' => 'auto'));
                    $doc -> addScript(TZ_Portfolio_PlusUri::root(true).'/addons/mediatype/image/js/lightbox.min.js');

                    $lightboxopt    =   $params->get('image_lightbox_option',['zoom', 'slideShow', 'fullScreen', 'thumbs', 'close']);
                    $lightboxopts   = array(
                        'buttons'   => $lightboxopt
                    );
                    $doc -> addScriptDeclaration('jQuery(function($){
                        $.image_addon_lightbox('.json_encode($lightboxopts).');
                      });');

                    $image  = clone($media -> image);

                    if(isset($image -> url) && $image -> url) {
                        if ($size = $params->get('mt_image_related_size', 'o')) {
                            if (isset($image->url) && !empty($image->url)) {
                                $image_url_ext = File::getExt($image->url);
                                if ($params->get('mt_show_original_gif',1) && $image_url_ext == 'gif') {
                                    $size = 'o';
                                }
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url);

                                $image->related_url = JURI::base( true ) . '/' . $image_url;
                            }
                        }

                        if ($size = $params->get('mt_image_size', 'o')) {
                            if (isset($image->url) && !empty($image->url)) {
                                $image_url_ext = File::getExt($image->url);
                                if ($params->get('mt_show_original_gif',1) && $image_url_ext == 'gif') {
                                    $size = 'o';
                                }
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url);
                                if ($params->get('mt_image_uikit',0) && file_exists(JPATH_BASE.'/'.$image_url)) {
                                    $this->image_properties =   getimagesize(JPATH_BASE.'/'.$image_url);
                                }
                                $image->url = JURI::base( true ) . '/' . $image_url;

                                if($this -> getLayout() != 'related') {
                                    JFactory::getDocument()->addCustomTag('<meta property="og:image" content="' . $image->url . '"/>');
                                    if ($author = $item->author_info) {
                                        JFactory::getDocument()->setMetaData('twitter:image', $image->url);
                                    }
                                }
                            }

                            if (isset($image->url_detail) && !empty($image->url_detail)) {
                                $image_url_ext = File::getExt($image->url_detail);
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url_detail);
                                if ($params->get('mt_image_uikit',0) && file_exists(JPATH_BASE.'/'.$image_url)) {
                                    $this->image_properties =   getimagesize(JPATH_BASE.'/'.$image_url);
                                }
                                $image->url_detail = JURI::base( true ) . '/' . $image_url;
                            }
                        }

                        $this -> image  = $image;
                    }

                }
            }
            $this -> item   = $item;
        }

        parent::display($tpl);
    }
}