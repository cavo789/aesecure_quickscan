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

class PlgTZ_Portfolio_PlusMediaTypeImageViewDate extends JViewLegacy
{

    protected $item = null;
    protected $params = null;
    protected $image = null;

    public function display($tpl = null)
    {
        $state          = $this -> get('State');
        $params         = $state -> get('params');
        $this -> params = $params;
        $item           = $this -> item;

        if(!$item){
            $item = $this -> get('Item');
        }


        if($item){
            if($media = $item -> media){
                if(isset($media -> image)){
                    $image  = clone($media -> image);

                    if($params -> get('mt_image_show_feed_image',1)){
                        $title = $this->escape($item->title);
                        $title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

                        $link = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item -> slug, $item -> catid, true, -1));

                        if($size = $params -> get('mt_image_feed_size','o')){
                            if(isset($image -> url) && !empty($image -> url)) {
                                $image_url_ext          = File::getExt($image->url);
                                $image_url              = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url);
                                $image -> url = JURI::root().$image_url;
                                echo '<a href="'.$link.'"><img src="'.$image -> url.'" alt="'.$title.'"/></a>';
                            }
                        }
                    }
                }
            }
        }
    }
}