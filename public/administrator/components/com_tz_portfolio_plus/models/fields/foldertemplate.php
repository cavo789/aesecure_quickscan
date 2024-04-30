<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2011-2017 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldFolderTemplate extends JFormFieldList
{
    protected $type = 'FolderTemplate';

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $setup  = parent::setup($element, $value, $group);

        $layout = $this -> layout;

        if($this -> multiple && $layout != 'joomla.form.field.list-fancy-select') {
            JHtml::_('formbehavior.chosen', '#' . $this->id);
        }

        return $setup;
    }

    public function getOptions(){
        $options = array();

        if($opts = TZ_Portfolio_PlusHelperTemplates::getTemplateOptions()){
            $options    = array_merge($options, $opts);
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}