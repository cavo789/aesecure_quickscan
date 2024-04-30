<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

defined('JPATH_BASE') or die;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\HTML\HTMLHelper;

jimport('joomla.html.html');
jimport('joomla.form.formfield');


class JFormFieldPhocaMapsRadio extends FormField
{

	protected $type = 'PhocaMapsRadio';

	protected function getInput()
	{
		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="radio '.(string) $this->element['class'].'"' : ' class="radio"';

		// Start the radio field output.
		$html[] = '<fieldset id="'.$this->id.'"'.$class.'>';

		$design	= ( (string)$this->element['typedesign'] ? $this->element['typedesign'] : 0 );
		if ($design == 1) {
			$options = $this->getOptionsDesign1();
		}
		if ($design == 2) {
			$options = $this->getOptionsDesign2();
		}

		//$output .= '<input class="text_area" type="radio" name="'.$this->name.'" id="'.$this->id.'_id" value="'.(string) $option['value'].'" '.$checked.' />';



		// Build the radio field output.

		foreach ($options as $i => $option) {

			// Initialize some option attributes.
			$checked	= ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$class		= !empty($option->class) ? ' class="'.$option->class.'"' : '';
			$disabled	= !empty($option->disable) ? ' disabled="disabled"' : '';

			// Initialize some JavaScript option attributes.
			$onclick	= !empty($option->onclick) ? ' onclick="'.$option->onclick.'"' : '';

			$html[] = '<div class="form-check"><input class="form-check-input" type="radio" id="'.$this->id.$i.'" name="'.$this->name.'"' .
					' value="'.htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8').'"'
					.$checked.$class.$onclick.$disabled.'/>';

			$html[] = '<label class="form-check-label" for="'.$this->id.$i.'">'. Text::_($option->text). ' '.$option->img.'</label>';

			$html[] = '</div>';



			/*if ($design == 1) {
				$html[] = '<label for="'.$this->id.$i.'"'.$class.' style="width:auto">'. Text::_($option->text). '</label>'
						. '<div style="position:relative;float:left;width:30px;margin-left:5px">'.$option->img.'</div><div style="clear:both"></div>';

			}
			if ($design == 2) {
				if($option->imgnr % 3 == 0) {
					$cssPart 	= '';
					$htmlPart 	= '<div style="clear:both"></div>';
				} else {
					$cssPart 	='margin-right:10px;';
					$htmlPart 	= '<div style="clear:both"></div>';
				}

				$html[] = '<label for="'.$this->id.$i.'" '.$class.' style="width:auto">'. Text::_($option->text). '</label>'
						. '<div style="position:relative;float:left;margin:0px;padding:0px;margin-left:5em;margin-top: -2em;'.$cssPart.'">'.$option->img.'</div></div>'. $htmlPart;
			}*/
		}

		// End the radio field output.
		$html[] = '</fieldset>';

		return implode($html);
	}


	protected function getOptionsDesign1()
	{
		$options = array();

		foreach ($this->element->children() as $option) {

			$tmp = new CMSObject();
			if ($option->getName() != 'option') {
				continue;
			}

			$tmp->value = (string) $option['value'];
			$tmp->text	= trim((string) $option);

			switch((int)$option['value']) {

				case 1:	$optName = 'grey';		break;
				case 2:	$optName = 'grey';		break;//$optName = 'greywb';the same but other padding
				case 3:	$optName = 'greyrc';	break;
				case 4:	$optName = 'black';		break;
				default:
				case 0:	$optName = 'none';		break;
			}

			if ((int)$option['value'] == 0) {
				$tmp->img = '';
			} else {
				$tmp->img = HTMLHelper::_('image', 'components/com_phocamaps/assets/images/box-'.$optName.'-tl.png', '', array('style' => 'margin:0;padding:0'));
			}

			//$tmp->class = (string) $option['class'];
			//$tmp->onclick = (string) $option['onclick'];
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}

	protected function getOptionsDesign2()
	{
		$options = array();

		$i = 1;
		foreach ($this->element->children() as $option) {

			$tmp = new CMSObject();
			if ($option->getName() != 'option') {
				continue;
			}

			$tmp->value = (string) $option['value'];
			$tmp->text	= trim((string) $option);

			switch((int)$option['value']) {
				case 1:	$optName = 'igrey';		break;
				case 2:	$optName = 'iyellow';	break;
				case 3:	$optName = 'ihome';		break;
				case 4:	$optName = 'igreen';	break;
				case 5:	$optName = 'istar';		break;
				case 6:	$optName = 'iinfoh';	break;
				case 7:	$optName = 'iinfoi';	break;
				case 8:	$optName = 'iinfop';	break;
				case 9:	$optName = 'iinfoph';	break;
				case 10:$optName = 'iinfoz';	break;
				default:
				case 0:	$optName = 'default';	break;
			}

			if ((int)$option['value'] == 0) {
				$tmp->img 	= '';
				$tmp->imgnr	= 0;
			} else {
				$tmp->img = HTMLHelper::_('image', 'media/com_phocamaps/images/'.$optName.'/image.png', '', array('style' => 'margin:0;padding:0'));
				$tmp->imgnr =  $i;
				$i++;
			}

			//$tmp->class = (string) $option['class'];
			//$tmp->onclick = (string) $option['onclick'];
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
