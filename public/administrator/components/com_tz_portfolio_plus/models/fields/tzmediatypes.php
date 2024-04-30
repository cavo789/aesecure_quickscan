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

use Joomla\CMS\Factory;

JFormHelper::loadFieldClass('list');

class JFormFieldTZMediaTypes extends JFormFieldList
{

    protected $type     = 'TZMediaTypes';

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
        $_plugin_group  = $element['plugin_group']?$element['plugin_group']:'mediatype';

        if($plugins = TZ_Portfolio_PlusPluginHelper::getPlugin($_plugin_group)){
            $lang   = Factory::getApplication() -> getLanguage();
            foreach($plugins as $plugin){
                $std    = new stdClass();
                $std -> value   = $plugin -> name;

                TZ_Portfolio_PlusPluginHelper::loadLanguage($plugin -> name, $plugin -> type);
                if($lang -> hasKey('PLG_'.$plugin -> type.'_'.$plugin -> name.'_TITLE')) {
                    $std -> text    = JText::_('PLG_'.$plugin -> type.'_'.$plugin -> name.'_TITLE');
                }else{
                    $std -> text    = $plugin -> name;
                }
                $options[]  = $std;
            }
        }

        return array_merge(parent::getOptions(),$options);
    }
}