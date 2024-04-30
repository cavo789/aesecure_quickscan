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

class TZ_Portfolio_PlusExtraFieldText extends TZ_Portfolio_PlusExtraField{

    public function getInput($fieldValue = null, $group = null){

        if(!$this -> isPublished()){
            return "";
        }

        $this->setAttribute("class", $this->getInputClass(), "input");

        if ((int) $this->params->get("size"))
        {
            $this->setAttribute("size", (int) $this->params->get("size"), "input");
        }

        if ($this->params->get("placeholder", ""))
        {
            $placeholder = htmlspecialchars($this->params->get("placeholder", ""), ENT_COMPAT, 'UTF-8');
            $this->setAttribute("placeholder", $placeholder, "input");
        }

        $values          = !is_null($fieldValue) ? $fieldValue : (string) $this -> value;

        $this -> setAttribute('value', $values, 'input');

        return parent::getInput($fieldValue);
    }

    public function getInputClass()
    {
        $class = array();

        if ($this->isRequired())
        {
            $class[] = 'required';
        }

        if ($this->getRegex())
        {
            $class[] = 'validate-' . $this->getId();
            $this->JSValidate();
        }

        if ($this->params->get('auto_suggest', 0))
        {
            $class[] = 'autosuggest';
        }

        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE && !in_array('form-control', $class)){
            $class[]    = 'form-control';
        }

        if ($class)
        {
            return implode(' ', $class);
        }
        else
        {
            return "";
        }
    }

    protected function getRegex()
    {
        $regex = $this->params->get('regex', 'none');

        if($regex == 'none'){
            $regex  = '';
        }

        if ($regex == "custom")
        {
            $regex = trim($this->params->get('custom_regex', ''));
        }

        if (!$regex)
        {
            $regex = $this->regex;
        }

        return $regex;
    }


    protected function JSValidate()
    {
        $regex = $this->getRegex();

        if (!$regex)
        {
            return false;
        }
        $invalid_message = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_EXTRAFIELDS_FIELD_VALUE_IS_INVALID', $this->getTitle());


        $invalid_message = htmlspecialchars($invalid_message, ENT_COMPAT, 'UTF-8');
        $validate_id     = $this->getId();
        $document        = JFactory::getDocument();

        $script = "jQuery(document).ready(function ($) {
			$('#" . $this->getId() . "-lbl').data(\"invalid_message\",\"" . $invalid_message . "\" );
			document.formvalidator.setHandler('" . $validate_id . "',
				function (value) {
					if(value=='') {
						return true;
					}
					var regex = " . $regex . ";
					return regex.test(value);
				});
			});";

        $document->addScriptDeclaration($script);

        return true;
    }

    public function getSearchInput($defaultValue = '')
    {
        if ($this->params->get("placeholder", ""))
        {
            $placeholder = htmlspecialchars($this->params->get("placeholder", ""), ENT_COMPAT, 'UTF-8');
            $this->setAttribute("placeholder", $placeholder, "search");
        }

        return parent::getSearchInput($defaultValue);
    }

    public function onSearch(&$query, &$where, $search, $forceModifyQuery = false)
    {
        if ($search === '' || empty($search))
        {
            return '';
        }

        $storeId = md5(__METHOD__ . "::" . $this->id);
        if (!isset(self::$cache[$storeId]) || $forceModifyQuery)
        {
            $query -> join('LEFT', '#__tz_portfolio_plus_field_content_map AS field_values_'.$this -> id
                . ' ON (c.id = field_values_' . $this -> id . '.contentid AND field_values_' . $this -> id
                . '.fieldsid = ' . $this -> id . ')');

            self::$cache[$storeId] = true;
        }

        if (is_string($search))
        {

            if ($this->params->get("is_numeric", 0))
            {
                $search = (int) $search;

                $where[] = "(CONVERT(" . $this->fieldvalue_column . ", DECIMAL(" . $this->params->get("digits_in_total", 11) . "," . $this->params->get("digits_after_decimal", 2) . ") ) = $search )";
            }

            else
            {
                $db = JFactory::getDbo();

                $where[] = $this->fieldvalue_column . " LIKE '%" . $db->escape($search, true) . "%'";
            }
        }

        elseif (is_array($search))
        {

            if ($this->params->get("is_numeric", 0))
            {
                if ($search['from'] !== "" && $search['to'] !== "")
                {
                    $from = (int) $search['from'];
                    $to   = (int) $search['to'];
                    if ($from > $to)
                    {
                        $this->swap($from, $to);
                    }

                    $where[] = "(CONVERT(" . $this->fieldvalue_column . ", DECIMAL(" . $this->params->get("digits_in_total", 11) . "," . $this->params->get("digits_after_decimal", 2) . ") ) BETWEEN $from AND $to )";
                }
                elseif ($search['from'] !== "")
                {
                    $from = (int) $search['from'];

                    $where[] = "(CONVERT(" . $this->fieldvalue_column . ", DECIMAL(" . $this->params->get("digits_in_total", 11) . "," . $this->params->get("digits_after_decimal", 2) . ") ) >= $from )";
                }
                elseif ($search['to'] !== "")
                {
                    $to = (int) $search['to'];

                    $where[] = "(CONVERT(" . $this->fieldvalue_column . ", DECIMAL(" . $this->params->get("digits_in_total", 11) . "," . $this->params->get("digits_after_decimal", 2) . ") ) <= $to )";
                }
            }

            else
            {
                $db     = JFactory::getDbo();
                $_where = array();
                foreach ($search AS $value)
                {
                    if ($value !== "")
                    {

                        $_where[] = "( " . $this->fieldvalue_column . " = " . $db->quote($value) .
                            " OR " . $this->fieldvalue_column . " LIKE '" . $db->escape($value, true) . "|%'" .
                            " OR " . $this->fieldvalue_column . " LIKE '%|" . $db->escape($value, true) . "|%'" .
                            " OR " . $this->fieldvalue_column . " LIKE '%|" . $db->escape($value, true) . "' )";
                    }
                }

                if (!empty($_where))
                {

                    $search_operator = " " . $this->params->get("search_operator", "OR") . " ";
                    $where[]         = "(" . implode($search_operator, $_where) . ")";
                }
            }
        }
    }
}