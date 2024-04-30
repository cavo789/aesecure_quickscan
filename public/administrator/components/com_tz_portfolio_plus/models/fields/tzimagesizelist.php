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

JFormHelper::loadFieldClass('list');

class JFormFieldTZImageSizeList extends JFormFieldList
{

    protected $type     = 'TZImageSizeList';

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $setup  = parent::setup($element, $value, $group);

        $layout = $this -> layout;

        if($this -> multiple && $layout != 'joomla.form.field.list-fancy-select') {
            JHtml::_('formbehavior.chosen', '#' . $this->id);
        }

        return $setup;
    }

    protected function getOptions(){
        $element        = $this -> element;
        $options        = array();
        $_plugin        = $element['addon']?$element['addon']:null;
        $_plugin_group  = $element['addon_group']?$element['addon_group']:'mediatype';
        $param_filter   = $element['param_name']?$element['param_name']:null;
        if($_plugin && $param_filter) {
            if ($plugin = TZ_Portfolio_PlusPluginHelper::getPlugin($_plugin_group, $_plugin, false)) {
                if(!empty($plugin -> params)) {
                    $plg_params = new JRegistry;
                    $plg_params -> loadString($plugin->params);
                    if($image_size = $plg_params -> get($param_filter)){
                        if(!is_array($image_size) && preg_match_all('/(\{.*?\})/',$image_size,$match)) {
                            $image_size = $match[1];
                        }

                        foreach($image_size as $i => $size){
                            $_size  = json_decode($size);
                            $options[$i]            = new stdClass();
                            $options[$i] -> text    = $_size -> {$element['param_text']};
                            $options[$i] -> value   = $_size -> {$element['param_value']};
                        }
                    }
                }
            }
        }
        return array_merge(parent::getOptions(),$options);
    }
}