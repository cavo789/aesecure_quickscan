<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

$options = array(
	JHtml::_('select.option', 'c', JText::_('JLIB_HTML_BATCH_COPY')),
	JHtml::_('select.option', 'm', JText::_('JLIB_HTML_BATCH_MOVE'))
);
$published = $this->state->get('filter.published');
$extension = $this->escape($this->state->get('filter.extension'));

$j4compare  = COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE;
?>

<div class="container-fluid">

    <?php echo JHtml::_('tzbootstrap.addrow');?>
		<div class="<?php echo $j4compare?'form-group col-md-6':'control-group span6'; ?>">
			<div class="controls">
                <?php echo LayoutHelper::render('joomla.html.batch.language', array()); ?>
			</div>
		</div>
		<div class="<?php echo $j4compare?'form-group col-md-6':'control-group span6'; ?>">
			<div class="controls">
                <?php echo LayoutHelper::render('joomla.html.batch.access', array()); ?>
			</div>
		</div>
    <?php echo JHtml::_('tzbootstrap.endrow');?>
    <?php echo JHtml::_('tzbootstrap.addrow');?>
		<?php if ($published >= 0) : ?>
        <div class="span12 col-md-12">
            <div class="<?php echo $j4compare?'form-group':'control-group'; ?>">
                <label id="batch-choose-action-lbl" for="batch-category-id" class="control-label">
                    <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_BATCH_CATEGORY_LABEL'); ?>
                </label>
                <div id="batch-choose-action" class="combo controls">
                    <select name="batch[category_id]" class="inputbox" id="batch-category-id">
                        <option value=""><?php echo JText::_('JSELECT') ?></option>
                        <?php echo JHtml::_('select.options', JHtml::_('tzcategory.categories', $extension, array('filter.published' => $published)));?>
                    </select>
                </div>
            </div>
            <div id="batch-copy-move" class="<?php echo $j4compare?'form-group':'control-group'; ?> radio">
                <?php echo JText::_('JLIB_HTML_BATCH_MOVE_QUESTION'); ?>
                <?php echo JHtml::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
            </div>
        </div>
		<?php endif; ?>
    <?php echo JHtml::_('tzbootstrap.endrow');?>
</div>

