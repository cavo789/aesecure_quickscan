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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldPhocaHead extends FormField
{
	protected $type = 'PhocaHead';
	protected function getLabel() { return '';}
	
	protected function getInput() {
	
		HTMLHelper::stylesheet( 'media/com_phocamaps/css/administrator/phocamapsoptions.css' );
		echo '<div style="clear:both;"></div>';
		$phocaImage	= ( (string)$this->element['phocaimage'] ? $this->element['phocaimage'] : '' );
		$image 		= '';
		
		if ($phocaImage != ''){
			$image 	= HTMLHelper::_('image', 'media/com_phocamaps/images/administrator/'. $phocaImage, '' );
		}
		
		if ($this->element['default']) {
			if ($image != '') {
				return '<div class="ph-options-head">'
				.'<div>'. $image.' <strong>'. Text::_($this->element['default']) . '</strong></div>'
				.'</div>';
			} else {
				return '<div class="ph-options-head">'
				.'<strong>'. Text::_($this->element['default']) . '</strong>'
				.'</div>';
			}
		} else {
			return parent::getLabel();
		}
		echo '<div style="clear:both;"></div>';
	}
}
?>