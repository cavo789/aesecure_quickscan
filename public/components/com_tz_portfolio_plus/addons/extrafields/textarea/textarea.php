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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Editor\Editor;

class TZ_Portfolio_PlusExtraFieldTextArea extends TZ_Portfolio_PlusExtraField
{
    public function getInput($fieldValue = null, $group = null)
    {

        if(!$this -> isPublished()){
            return "";
        }

        $value = !is_null($fieldValue) ? $fieldValue : $this -> value;

        $editor = $this->getEditor();
        if ($editor)
        {
            $editorHtml = $this->getEditorHtml($editor, $value);
        }
        else
        {
            $editorHtml = $this->getTextArea($value, $this->getInputClass());
        }

        $this->setVariable('value', $value);
        $this->setVariable('editorHtml', $editorHtml);

        return $this -> loadTmplFile('input', __CLASS__);
    }

    public function getInputClass()
    {
        $class = parent::getInputClass();

        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE && strpos($class, 'form-control') == false){
            $class  .= ' form-control';
        }

        return $class;
    }

    public function getTextArea($value, $class = '')
    {
        $placeholder = $this->params->get("placeholder", "") ? "placeholder=\"" . htmlspecialchars($this->params->get("placeholder", ""), ENT_COMPAT, 'UTF-8') . "\"" : "";
        $width       = $this->params->get('width');
        $height      = $this->params->get('height');
        $cols        = $this->params->get('cols', 50);
        $rows        = $this->params->get('rows', 5);
        $html        = '<textarea id="' . $this->getId() . '" name="' . $this->getName() . '" class="' . $class . '"
							style="width: ' . $width . 'px; height: ' . $height . 'px;"
						    cols="' . $cols . '" rows="' . $rows . '" ' . $placeholder . ' >' . $value . '</textarea>';

        return $html;
    }

    protected function getEditor()
    {
        $app    = JFactory::getApplication();
        $config = \JFactory::getConfig();
        $editor = '';
        if ($app->isClient('administrator'))
        {
            if ($this->params->get('use_editor_back_end', 0))
            {
                $editor = $this->params->get('backend_editor', '');
                if($editor == '')
                {
//                    $editor = $app->get('editor', 'tinymce');
                    $editor = $config -> get('editor');
                }
            }
        }
        else
        {
            if ($this->params->get('use_editor_front_end', 0))
            {
                $editor = $this->params->get('frontend_editor', '');
                if($editor == '')
                {
//                    $editor = $app->get('editor', 'tinymce');
                    $editor = $config -> get('editor');
                }

                if ($editor && $editor != 'none')
                {
                    $groupsCanUseFrontendEditor = (array) $this->params->get('groups_can_use_frontend_editor', array());
                    $user                       = JFactory::getUser();
                    $userGroups                 = $user->getAuthorisedViewLevels();
                    if (!count(array_intersect($userGroups, $groupsCanUseFrontendEditor)) > 0)
                    {
                        $editor = '';
                    }
                }
            }
        }

        return $editor;
    }

    protected function getEditorHtml($selectedEditor, $value)
    {
        $html         = '';
        $class        = get_class();
        $called_class = get_called_class();

        $buttons = ($class != $called_class) ? array('pagebreak') : array('pagebreak', 'readmore');

        if(!JPluginHelper::isEnabled('editors', $selectedEditor)){
            $selectedEditor = 'none';
        }

        $editor = Editor::getInstance($selectedEditor);

        $html = $editor->display($this->getName(), htmlspecialchars($value, ENT_COMPAT, 'UTF-8')
            , $this->params->get('width', '90%'), $this->params->get('height', 200)
            , $this->params->get('cols', 50), $this->params->get('rows', 5)
            , $buttons, $this->getId());

        return $html;
    }

    public function getInputDefault($group = null){
        $this -> group  = $group?$group:$this -> group;

        $fieldValues    = $this -> getFieldValues();

        if ($this->getAttribute("type", "", "input") == "")
        {
            $this->setAttribute("type", "text", "input");
        }
        $this->setVariable('value', $fieldValues);

        if($html = $this -> loadTmplFile('input_default', __CLASS__)){
            return $html;
        }
    }

    public function getSearchInput($defaultValue = '')
    {

        if ($this->getAttribute("type", "", "sear") == "")
        {
            $this->setAttribute("type", "textarea", "search");
        }

        if ($this->params->get("placeholder", ""))
        {
            $placeholder = htmlspecialchars($this->params->get("placeholder", ""), ENT_COMPAT, 'UTF-8');
            $this->setAttribute("placeholder", $placeholder, "search");
        }
		
        return parent::getSearchInput($defaultValue);
    }

    public function prepareFieldValue($value = '')
    {
        $result = parent::prepareFieldValue($value);

        $filter = $this -> params -> get('filter');
        $filter = !empty($filter)?$filter:$this -> plugin_params -> get('filter', 'none');

        if($filter == 'none'){
            $result = strip_tags($result);
        }
        return $result;
    }

}