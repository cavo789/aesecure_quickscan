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

//no direct access
defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

JFormHelper::loadFieldClass('checkboxes');

class JFormFieldCategoriesAssignment extends JFormFieldCheckboxes
{
    protected $type = 'CategoriesAssignment';
    protected $layout = 'form.field.categoriesassignment';

    protected function getOptions()
    {
        $options    = array();
        $db         = TZ_Portfolio_PlusDatabase::getDbo();
        $query      = $db -> getQuery(true);

        $query -> select('c.title AS text, c.id AS value,c.template_id, c.level');
        $query -> from('#__tz_portfolio_plus_categories AS c');
        $query -> where('extension = "com_tz_portfolio_plus"');
        $query -> order('c.lft');

        $db -> setQuery($query);
        if($rows = $db -> loadObjectList()){
            foreach($rows as $option){
                $tmp = JHtml::_('select.option', (string) $option -> value, trim($option -> text), 'value', 'text');

                $checked    = false;
                $app    = Factory::getApplication();
                $input  = $app -> input;
                $curTemplateId  = null;

                if(!isset($this -> element['template_id'])){
                    if($input -> get('option') == 'com_tz_portfolio_plus' && $input -> get('view') == 'template_style'){
                        $curTemplateId  = $input -> get('id');
                    }
                }else{
                    $curTemplateId  = $this -> element['template_id'];
                }

                if(isset($option -> template_id) && $option -> template_id && !empty($option -> template_id)){
                    if($option -> template_id == $curTemplateId){
                        $checked    = true;
                    }
                }

                $checked = ($checked == 'true' || $checked == 'checked' || $checked == '1');

                // Set some option attributes.
                $tmp->checked = $checked;

                $tmp -> level   = $option -> level;

                // Add the option object to the result set.
                $options[] = $tmp;
            }
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
?>