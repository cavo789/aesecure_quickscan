<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
$r = $this->r;

JFactory::getDocument()->addScriptDeclaration(

'Joomla.submitbutton = function(task) {
	if (task == "'. $this->t['task'].'.cancel" || document.formvalidator.isValid(document.getElementById("adminForm"))) {
		Joomla.submitform(task, document.getElementById("adminForm"));
	} else {
        Joomla.renderMessages({"error": ["'. Text::_('JGLOBAL_VALIDATION_FORM_FAILED', true).'"]});
	}
}'

);

echo $r->startForm($this->t['o'], $this->t['task'], $this->item->id, 'adminForm', 'adminForm');
// First Column
echo '<div class="span12 form-horizontal">';
$tabs = array (
'general' 		=> Text::_($this->t['l'].'_GENERAL_OPTIONS'),
'publishing' 	=> Text::_($this->t['l'].'_PUBLISHING_OPTIONS')
);
echo $r->navigation($tabs);

$formArray = array ('title', 'alias');
echo $r->groupHeader($this->form, $formArray, '');

echo $r->startTabs();

echo $r->startTab('general', $tabs['general'], 'active');


if ($this->item->url != '') {
	echo '<div class="ph-admin-additional-box">';
	echo '<img src="'.$this->item->url.'" alt="" />';
	echo '</div>';
}


$formArray = array ('url', 'object', 'objectshape', 'lang','ordering');
echo $r->group($this->form, $formArray);
$formArray = array('description');
echo $r->group($this->form, $formArray, 1);
echo $r->endTab();

echo $r->startTab('publishing', $tabs['publishing']);
foreach($this->form->getFieldset('publish') as $field) {
	echo '<div class="control-group">';
	if (!$field->hidden) {
		echo '<div class="control-label">'.$field->label.'</div>';
	}
	echo '<div class="controls">';
	echo $field->input;
	echo '</div></div>';
}
echo $r->endTab();

echo '</div>';//end tab content
echo '</div>';//end span10

echo $r->formInputs();
echo $r->endForm();
?>
