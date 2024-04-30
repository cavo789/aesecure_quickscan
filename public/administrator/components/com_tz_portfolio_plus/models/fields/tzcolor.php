<?php
/*------------------------------------------------------------------------

# TZ Extension

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

class JFormFieldTZColor extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  11.3
     */
    protected $type = 'TZColor';

    /**
     * The control.
     *
     * @var    mixed
     * @since  3.2
     */
    protected $control = 'hue';

    /**
     * The position.
     *
     * @var    mixed
     * @since  3.2
     */
    protected $position = 'right';


    protected static $initialised = false;

    /**
     * The colors.
     *
     * @var    mixed
     * @since  3.2
     */
    protected $colors;

    /**
     * The split.
     *
     * @var    integer
     * @since  3.2
     */
    protected $split = 3;

    /**
     * Method to get certain otherwise inaccessible properties from the form field object.
     *
     * @param   string  $name  The property name for which to the the value.
     *
     * @return  mixed  The property value or null.
     *
     * @since   3.2
     */
    public function __get($name)
    {
        switch ($name)
        {
            case 'control':
            case 'exclude':
            case 'colors':
            case 'split':
                return $this->$name;
        }

        return parent::__get($name);
    }

    /**
     * Method to set certain otherwise inaccessible properties of the form field object.
     *
     * @param   string  $name   The property name for which to the the value.
     * @param   mixed   $value  The value of the property.
     *
     * @return  void
     *
     * @since   3.2
     */
    public function __set($name, $value)
    {
        switch ($name)
        {
            case 'split':
                $value = (int) $value;
            case 'control':
            case 'exclude':
            case 'colors':
                $this->$name = (string) $value;
                break;

            default:
                parent::__set($name, $value);
        }
    }

    /**
     * Method to attach a JForm object to the field.
     *
     * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
     * @param   mixed             $value    The form field value to validate.
     * @param   string            $group    The field name group control value. This acts as as an array container for the field.
     *                                      For example if the field has name="foo" and the group value is set to "bar" then the
     *                                      full field name would end up being "bar[foo]".
     *
     * @return  boolean  True on success.
     *
     * @see     JFormField::setup()
     * @since   3.2
     */
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        if ($return)
        {
            $this->control  = isset($this->element['control']) ? (string) $this->element['control'] : 'hue';
            $this->position = isset($this->element['position']) ? (string) $this->element['position'] : 'right';
            $this->colors   = (string) $this->element['colors'];
            $this->split    = isset($this->element['split']) ? (int) $this->element['split'] : 3;
        }

        return $return;
    }

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   11.3
     */
    protected function getInput()
    {
        $color = strtolower($this->value);

        if (!$color || in_array($color, array('none', 'transparent')))
        {
            $color = 'rgba(0,0,0,0)';
        }
        elseif ($color['0'] != '#')
        {
            $color = '#' . $color;
        }

        // Add Script
        if (!self::$initialised)
        {
            Factory::getApplication() -> getDocument() -> addStyleSheet(JUri::root().'/administrator/components/com_tz_portfolio_plus/css/spectrum.min.css', array('version' => 'auto'));
            Factory::getApplication() -> getDocument() -> addScript(JUri::root().'/administrator/components/com_tz_portfolio_plus/js/spectrum.min.js', array('version' => 'auto'));

            self::$initialised = true;
        }
        $script = array();
        $script[]   = 'jQuery(document).ready(function(){
                jQuery("#'.$this -> id.'[data-extrafield-type=color]").spectrum({
                flat:false,
                color: "'.$color.'",
                showInput:true,
                allowEmpty:true,
                preferredFormat: "rgb",
                showButtons:true,
                showAlpha:true,
                showPalette:true,
                clickoutFiresChange:true,
                cancelText:"cancel",
                chooseText:"Choose",
                showPaletteOnly: true,
                togglePaletteOnly: true,
                togglePaletteMoreText: "more",
                togglePaletteLessText: "less",
                palette : [
                    ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
                    ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
                    ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
                    ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
                    ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
                    ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
                    ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
                    ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
                ],
                show: function(color) {
                    if(color && color._a == 1){
                        color.toHex();
                    }
                },
                change: function(color){
                    var currentcolor = \'\';
                    if(color){
                        currentcolor = color.toRgbString();
                        if(currentcolor == \'rgba(0, 0, 0, 0)\'){
                            currentcolor    = \'\';
                        }
                    }
                    jQuery(this).parent().find(\'>[type=hidden][data-name=\'+jQuery(this).attr(\'data-extrafield-name\')+\']\').val(currentcolor);
                }
            });
        });';

        // Add the script to the document head.
        Factory::getApplication() -> getDocument()->addScriptDeclaration(implode("\n", $script));

        // Translate placeholder text
        $hint = $this->translateHint ? JText::_($this->hint) : $this->hint;

        // Control value can be: hue (default), saturation, brightness, wheel or simple
        $control = $this->control;

        // Position of the panel can be: right (default), left, top or bottom
        $position = ' data-position="' . $this->position . '"';

        $onchange  = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';
        $class     = $this->class;
        $required  = $this->required ? ' required aria-required="true"' : '';
        $disabled  = $this->disabled ? ' disabled' : '';
        $autofocus = $this->autofocus ? ' autofocus' : '';

            $readonly     = $this->readonly ? ' readonly' : '';
            $autocomplete = !$this->autocomplete ? ' autocomplete="off"' : '';

        return '<input type="text" name="' . $this->name . '"  id="' . $this->id . '"' . ' value="'
        . htmlspecialchars($color, ENT_COMPAT, 'UTF-8') . '" data-extrafield-type="color"' . $hint . $class . $position . $control
        . $readonly . $disabled . $required . $onchange . $autocomplete . $autofocus . '/>';
    }
}