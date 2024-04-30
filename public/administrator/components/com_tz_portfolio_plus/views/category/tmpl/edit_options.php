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

// No direct access.
defined('_JEXEC') or die;
echo JHtml::_('bootstrap.startAccordion', 'categoryOptions', array('active' => 'collapse0', 'parent' => true));
?>
	<?php echo JHtml::_('bootstrap.addSlide', 'categoryOptions', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'collapse0'); ?>

	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('created_user_id'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('created_user_id'); ?>
		</div>
	</div>
	<?php if (intval($this->item->created_time)) : ?>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('created_time'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('created_time'); ?>
			</div>
		</div>
	<?php endif; ?>
	<?php if ($this->item->modified_user_id) : ?>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('modified_user_id'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('modified_user_id'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('modified_time'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('modified_time'); ?>
			</div>
		</div>
	<?php endif; ?>
	<?php echo JHtml::_('bootstrap.endSlide');?>

<?php
    $form       = $this -> form;
	$fieldSets  = $form->getFieldsets('params');

    $attribsFieldSet = $form->getFieldsets('attribs');

    $fieldSets = array_merge($fieldSets, $attribsFieldSet);
	$i = 1;

	foreach ($fieldSets as $name => $fieldSet) :
		$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_TZ_PORTFOLIO_PLUS_'.$name.'_FIELDSET_LABEL';

		$fields	= $form->getFieldset($name);
		if($name == 'basic' && $fields && count($fields) <= 1 ){
			continue;
		}
		echo JHtml::_('bootstrap.addSlide', 'categoryOptions', JText::_($label), 'collapse' . $i++);
		if (isset($fieldSet->description) && trim($fieldSet->description)) :
			echo '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
		endif;
			?>
			<?php foreach ($fields as $field){
				echo $field -> renderField();
			} ?>
			<?php
			echo JHtml::_('bootstrap.endSlide');
		endforeach;
echo JHtml::_('bootstrap.endAccordion');
?>