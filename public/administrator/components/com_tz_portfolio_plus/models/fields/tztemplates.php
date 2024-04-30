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

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

JFormHelper::loadFieldClass('list');
JLoader::import('com_tz_portfolio_plus.includes.defines', JPATH_ADMINISTRATOR);

class JFormFieldTZTemplates extends JFormFieldList
{
    protected $type = 'TZTemplates';
    protected $module_layout;
    protected $module;

    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        if ($return)
        {
            $module = (string) $this->element['module'];

            if(!empty($module)){
                if($module == 'true' || $module = 1){
                    if($this->form instanceof JForm)
                    {
                        $module = $this->form->getValue('module');
                    }
                }
                $this -> module = $module;
            }
        }

        $lang   = Factory::getApplication() -> getLanguage();
        $lang -> load('com_tz_portfolio_plus', JPATH_ADMINISTRATOR);

        return $return;
    }

    protected function getOptions()
    {
        $options = array();

        $db     = TZ_Portfolio_PlusDatabase::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from('#__tz_portfolio_plus_templates');
        $query -> where('NOT template =""');
        $db -> setQuery($query);
        if($items = $db -> loadObjectList()){
            foreach($items as $i => $item){
                $options[$i] = new stdClass();
                $options[$i] -> text    = $item -> title;
                $options[$i] -> value   = $item -> id;
            }
        }

        return array_merge(parent::getOptions(), $options);
    }
}