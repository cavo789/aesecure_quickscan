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
use Joomla\CMS\HTML\HTMLHelper;
jimport('joomla.form.formfield');

class JFormFieldPhocaTextWarning extends FormField
{
	protected $type 		= 'PhocaTextWarning';

	protected function getInput() {
	
		
		// Initialize some field attributes.
		$warning	= ( (string)$this->element['warningtext'] ? $this->element['warningtext'] : '' );
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$maxLength	= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		// Initialize JavaScript field attributes.
		$onchange	= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		
		$value 		= htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
		
		
		$html ='<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.$value.'"'
			   .$class.$size.$disabled.$readonly.$onchange.$maxLength.'/>';
			   
		if ($warning != '') {
			//$html .= '<div style="margin-left:10px;">'.JHtml::_('image', 'administrator/components/com_phocamaps/assets/images/icon-16-warning.png', '' ) . '</div><div>' . JText::_($warning).'</div>';
			
			$html .='<div style="position:relative;float:left;width:auto;margin-left:10px">'.HTMLHelper::_('image', 'administrator/components/com_phocamaps/assets/images/icon-16-warning.png', '',array('style' => 'margin:0;padding:0;margin-right:5px;') ).' '.Text::_($warning).'</div><div style="clear:both"></div>';
		}
		
		
		return $html;
	}
	
	protected function getLabel() {
		echo '<div class="clearfix"></div>';
		return parent::getLabel();
		echo '<div class="clearfix"></div>';
	}

}
?>