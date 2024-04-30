<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

abstract class AngieHelperSelect
{
	public static $formatOptions = ['format.depth' => 0, 'format.eol' => "\n", 'format.indent' => "\t"];

	/**
	 * Default values for options. Organized by option group.
	 *
	 * @var     array
	 */
	static protected $optionDefaults = [
		'option' => [
			'option.attr'        => null, 'option.disable' => 'disable', 'option.id' => null, 'option.key' => 'value',
			'option.key.toHtml'  => true, 'option.label' => null, 'option.label.toHtml' => true,
			'option.text'        => 'text',
			'option.text.toHtml' => true,
		],
	];

	/**
	 * Create an object that represents an option in an option list.
	 *
	 * @param   string   $value    The value of the option
	 * @param   string   $text     The text for the option
	 * @param   mixed    $optKey   If a string, the returned object property name for
	 *                             the value. If an array, options. Valid options are:
	 *                             attr: String|array. Additional attributes for this option.
	 *                             Defaults to none.
	 *                             disable: Boolean. If set, this option is disabled.
	 *                             label: String. The value for the option label.
	 *                             option.attr: The property in each option array to use for
	 *                             additional selection attributes. Defaults to none.
	 *                             option.disable: The property that will hold the disabled state.
	 *                             Defaults to "disable".
	 *                             option.key: The property that will hold the selection value.
	 *                             Defaults to "value".
	 *                             option.label: The property in each option array to use as the
	 *                             selection label attribute. If a "label" option is provided, defaults to
	 *                             "label", if no label is given, defaults to null (none).
	 *                             option.text: The property that will hold the the displayed text.
	 *                             Defaults to "text". If set to null, the option array is assumed to be a
	 *                             list of displayable scalars.
	 * @param   string   $optText  The property that will hold the the displayed text. This
	 *                             parameter is ignored if an options array is passed.
	 * @param   boolean  $disable  Not used.
	 *
	 * @return  object
	 */
	public static function option($value, $text = '', $optKey = 'value', $optText = 'text', $disable = false)
	{
		$options = [
			'attr'         => null, 'disable' => false, 'option.attr' => null, 'option.disable' => 'disable',
			'option.key'   => 'value',
			'option.label' => null, 'option.text' => 'text',
		];
		if (is_array($optKey))
		{
			// Merge in caller's options
			$options = array_merge($options, $optKey);
		}
		else
		{
			// Get options from the parameters
			$options['option.key']  = $optKey;
			$options['option.text'] = $optText;
			$options['disable']     = $disable;
		}
		$obj                            = new stdClass();
		$obj->{$options['option.key']}  = $value;
		$obj->{$options['option.text']} = trim($text) ? $text : $value;

		/*
		 * If a label is provided, save it. If no label is provided and there is
		 * a label name, initialise to an empty string.
		 */
		$hasProperty = $options['option.label'] !== null;
		if (isset($options['label']))
		{
			$labelProperty       = $hasProperty ? $options['option.label'] : 'label';
			$obj->$labelProperty = $options['label'];
		}
		elseif ($hasProperty)
		{
			$obj->{$options['option.label']} = '';
		}

		// Set attributes only if there is a property and a value
		if ($options['attr'] !== null)
		{
			$obj->{$options['option.attr']} = $options['attr'];
		}

		// Set disable only if it has a property and a value
		if ($options['disable'] !== null)
		{
			$obj->{$options['option.disable']} = $options['disable'];
		}

		return $obj;
	}

	/**
	 * Generates the option tags for an HTML select list (with no select tag
	 * surrounding the options).
	 *
	 * @param   array    $arr         An array of objects, arrays, or values.
	 * @param   mixed    $optKey      If a string, this is the name of the object variable for
	 *                                the option value. If null, the index of the array of objects is used. If
	 *                                an array, this is a set of options, as key/value pairs. Valid options are:
	 *                                -Format options
	 *                                -groups: Boolean. If set, looks for keys with the value
	 *                                "&lt;optgroup>" and synthesizes groups from them. Deprecated. Defaults
	 *                                true for backwards compatibility.
	 *                                -list.select: either the value of one selected option or an array
	 *                                of selected options. Default: none.
	 *                                -list.translate: Boolean. If set, text and labels are translated via
	 *                                AText::_(). Default is false.
	 *                                -option.id: The property in each option array to use as the
	 *                                selection id attribute. Defaults to none.
	 *                                -option.key: The property in each option array to use as the
	 *                                selection value. Defaults to "value". If set to null, the index of the
	 *                                option array is used.
	 *                                -option.label: The property in each option array to use as the
	 *                                selection label attribute. Defaults to null (none).
	 *                                -option.text: The property in each option array to use as the
	 *                                displayed text. Defaults to "text". If set to null, the option array is
	 *                                assumed to be a list of displayable scalars.
	 *                                -option.attr: The property in each option array to use for
	 *                                additional selection attributes. Defaults to none.
	 *                                -option.disable: The property that will hold the disabled state.
	 *                                Defaults to "disable".
	 *                                -option.key: The property that will hold the selection value.
	 *                                Defaults to "value".
	 *                                -option.text: The property that will hold the the displayed text.
	 *                                Defaults to "text". If set to null, the option array is assumed to be a
	 *                                list of displayable scalars.
	 * @param   string   $optText     The name of the object variable for the option text.
	 * @param   mixed    $selected    The key that is selected (accepts an array or a string)
	 * @param   boolean  $translate   Translate the option values.
	 *
	 * @return  string  HTML for the select list
	 */
	public static function options($arr, $optKey = 'value', $optText = 'text', $selected = null, $translate = false)
	{
		$options = array_merge(
			self::$formatOptions,
			self::$optionDefaults['option'],
			['format.depth' => 0, 'groups' => true, 'list.select' => null, 'list.translate' => false]
		);

		if (is_array($optKey))
		{
			// Set default options and overwrite with anything passed in
			$options = array_merge($options, $optKey);
		}
		else
		{
			// Get options from the parameters
			$options['option.key']     = $optKey;
			$options['option.text']    = $optText;
			$options['list.select']    = $selected;
			$options['list.translate'] = $translate;
		}

		$html       = '';
		$baseIndent = str_repeat($options['format.indent'], $options['format.depth']);

		foreach ($arr as $elementKey => &$element)
		{
			$attr  = '';
			$extra = '';
			$label = '';
			$id    = '';
			if (is_array($element))
			{
				$key  = $options['option.key'] === null ? $elementKey : $element[$options['option.key']];
				$text = $element[$options['option.text']];
				if (isset($element[$options['option.attr']]))
				{
					$attr = $element[$options['option.attr']];
				}
				if (isset($element[$options['option.id']]))
				{
					$id = $element[$options['option.id']];
				}
				if (isset($element[$options['option.label']]))
				{
					$label = $element[$options['option.label']];
				}
				if (isset($element[$options['option.disable']]) && $element[$options['option.disable']])
				{
					$extra .= ' disabled="disabled"';
				}
			}
			elseif (is_object($element))
			{
				$key  = $options['option.key'] === null ? $elementKey : $element->{$options['option.key']};
				$text = $element->{$options['option.text']};
				if (isset($element->{$options['option.attr']}))
				{
					$attr = $element->{$options['option.attr']};
				}
				if (isset($element->{$options['option.id']}))
				{
					$id = $element->{$options['option.id']};
				}
				if (isset($element->{$options['option.label']}))
				{
					$label = $element->{$options['option.label']};
				}
				if (isset($element->{$options['option.disable']}) && $element->{$options['option.disable']})
				{
					$extra .= ' disabled="disabled"';
				}
			}
			else
			{
				// This is a simple associative array
				$key  = $elementKey;
				$text = $element;
			}

			$key = (string) $key;
			if ($options['groups'] && $key == '<OPTGROUP>')
			{
				$html       .= $baseIndent . '<optgroup label="' . ($options['list.translate'] ? AText::_($text) : $text) . '">' . $options['format.eol'];
				$baseIndent = str_repeat($options['format.indent'], ++$options['format.depth']);
			}
			elseif ($options['groups'] && $key == '</OPTGROUP>')
			{
				$baseIndent = str_repeat($options['format.indent'], --$options['format.depth']);
				$html       .= $baseIndent . '</optgroup>' . $options['format.eol'];
			}
			else
			{
				// If no string after hyphen - take hyphen out
				$splitText = explode(' - ', $text, 2);
				$text      = $splitText[0];
				if (isset($splitText[1]))
				{
					$text .= ' - ' . $splitText[1];
				}

				if ($options['list.translate'] && !empty($label))
				{
					$label = AText::_($label);
				}
				if ($options['option.label.toHtml'])
				{
					$label = htmlentities($label);
				}
				if (is_array($attr))
				{
					$attr = self::toString($attr);
				}
				else
				{
					$attr = trim($attr);
				}
				$extra = ($id ? ' id="' . $id . '"' : '') . ($label ? ' label="' . $label . '"' : '') . ($attr ? ' ' . $attr : '') . $extra;
				if (is_array($options['list.select']))
				{
					foreach ($options['list.select'] as $val)
					{
						$key2 = is_object($val) ? $val->{$options['option.key']} : $val;
						if ($key == $key2)
						{
							$extra .= ' selected="selected"';
							break;
						}
					}
				}
				elseif ((string) $key == (string) $options['list.select'])
				{
					$extra .= ' selected="selected"';
				}

				if ($options['list.translate'])
				{
					$text = AText::_($text);
				}

				// Generate the option, encoding as required
				$html .= $baseIndent . '<option value="' . ($options['option.key.toHtml'] ? htmlspecialchars($key, ENT_COMPAT, 'UTF-8') : $key) . '"'
					. $extra . '>';
				$html .= $options['option.text.toHtml'] ? htmlentities(html_entity_decode($text, ENT_COMPAT, 'UTF-8'), ENT_COMPAT, 'UTF-8') : $text;
				$html .= '</option>' . $options['format.eol'];
			}
		}

		return $html;
	}

	/**
	 * Generates an HTML selection list.
	 *
	 * @param   array    $data       An array of objects, arrays, or scalars.
	 * @param   string   $name       The value of the HTML name attribute.
	 * @param   mixed    $attribs    Additional HTML attributes for the <select> tag. This
	 *                               can be an array of attributes, or an array of options. Treated as options
	 *                               if it is the last argument passed. Valid options are:
	 *                               Format options, see $formatOptions.
	 *                               Selection options.
	 *                               list.attr, string|array: Additional attributes for the select
	 *                               element.
	 *                               id, string: Value to use as the select element id attribute.
	 *                               Defaults to the same as the name.
	 *                               list.select, string|array: Identifies one or more option elements
	 *                               to be selected, based on the option key values.
	 * @param   string   $optKey     The name of the object variable for the option value. If
	 *                               set to null, the index of the value array is used.
	 * @param   string   $optText    The name of the object variable for the option text.
	 * @param   mixed    $selected   The key that is selected (accepts an array or a string).
	 * @param   mixed    $idtag      Value of the field id or null by default
	 * @param   boolean  $translate  True to translate
	 *
	 * @return  string  HTML for the select list.
	 */
	public static function genericlist(
		$data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false,
		$translate = false
	)
	{
		// Set default options
		$options = array_merge(self::$formatOptions, ['format.depth' => 0, 'id' => false]);
		if (is_array($attribs) && func_num_args() == 3)
		{
			// Assume we have an options array
			$options = array_merge($options, $attribs);
		}
		else
		{
			// Get options from the parameters
			$options['id']             = $idtag;
			$options['list.attr']      = $attribs;
			$options['list.translate'] = $translate;
			$options['option.key']     = $optKey;
			$options['option.text']    = $optText;
			$options['list.select']    = $selected;
		}
		$attribs = '';
		if (isset($options['list.attr']))
		{
			if (is_array($options['list.attr']))
			{
				$attribs = self::toString($options['list.attr']);
			}
			else
			{
				$attribs = $options['list.attr'];
			}
			if ($attribs != '')
			{
				$attribs = ' ' . $attribs;
			}
		}

		$id = $options['id'] !== false ? $options['id'] : $name;
		$id = str_replace(['[', ']'], '', $id);

		$baseIndent = str_repeat($options['format.indent'], $options['format.depth']++);
		$html       = $baseIndent . '<select' . ($id !== '' ? ' id="' . $id . '"' : '') . ' name="' . $name . '"' . $attribs . '>' . $options['format.eol']
			. self::options($data, $options) . $baseIndent . '</select>' . $options['format.eol'];

		return $html;
	}

	/**
	 * Utility function to map an array to a string.
	 *
	 * @param   array    $array         The array to map.
	 * @param   string   $inner_glue    The glue (optional, defaults to '=') between the key and the value.
	 * @param   string   $outer_glue    The glue (optional, defaults to ' ') between array elements.
	 * @param   boolean  $keepOuterKey  True if final key should be kept.
	 *
	 * @return  string   The string mapped from the given array
	 */
	public static function toString($array = null, $inner_glue = '=', $outer_glue = ' ', $keepOuterKey = false)
	{
		$output = [];

		if (is_array($array))
		{
			foreach ($array as $key => $item)
			{
				if (is_array($item))
				{
					if ($keepOuterKey)
					{
						$output[] = $key;
					}
					// This is value is an array, go and do it again!
					$output[] = self::toString($item, $inner_glue, $outer_glue, $keepOuterKey);
				}
				else
				{
					$output[] = $key . $inner_glue . '"' . $item . '"';
				}
			}
		}

		return implode($outer_glue, $output);
	}

	public static function getAvailableConnectors($technology = null)
	{
		$connectors = ADatabaseDriver::getConnectors($technology);

		if (defined('ANGIE_DBDRIVER_ALLOWED') && is_array(ANGIE_DBDRIVER_ALLOWED))
		{
			$connectors = array_intersect($connectors, ANGIE_DBDRIVER_ALLOWED);
		}

		return $connectors;
	}

	public static function dbtype($selected = 'mysqli', $technology = null)
	{
		return self::genericlist(
			array_map(
				function ($connector) {
					return self::option($connector, AText::_('DATABASE_LBL_TYPE_' . $connector));
				},
				self::getAvailableConnectors($technology)
			),
			'dbtype', null, 'value', 'text', $selected
		);
	}

	public static function superusers($selected = null, $name = 'superuserid', $id = 'superuserid')
	{
		$options = [];

		$params = AModel::getAnInstance('Setup', 'AngieModel')->getStateVariables();
		if (isset($params))
		{
			$superusers = $params->superusers;
			foreach ($superusers as $sa)
			{
				$options[] = self::option($sa->id, $sa->username);
			}
		}

		return self::genericlist($options, $name, ['onchange' => 'setupSuperUserChange()'], 'value', 'text', $selected, $id);
	}

	public static function forceSSL($selected = '0')
	{
		$options[] = self::option(0, AText::_('SETUP_LABEL_FORCESSL_NONE'));
		$options[] = self::option(1, AText::_('SETUP_LABEL_FORCESSL_ADMINONLY'));
		$options[] = self::option(2, AText::_('SETUP_LABEL_FORCESSL_ENTIRESITE'));

		return self::genericlist($options, 'force_ssl', ['class' => 'input-medium'], 'value', 'text', $selected);
	}


}
