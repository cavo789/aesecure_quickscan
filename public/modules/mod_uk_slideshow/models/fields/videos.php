<?php

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('filelist');

class JFormFieldVideos extends JFormFieldFileList
{
    protected $type = 'ImageList';
    protected function getOptions()
    {
        // Define the vide file type filter. Edit as needed.
        $this->filter = '\.mov$|\.mpg$|\.mp4$|\.ogv$|\.webm$|\.mts$|\.avi$|\.wmv$';
        return parent::getOptions();
    }
}