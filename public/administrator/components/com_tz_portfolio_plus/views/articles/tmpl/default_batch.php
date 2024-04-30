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

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

$published = $this->state->get('filter.published');
?>
<div class="modal hide fade" id="collapseModal">
	<div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal" data-bs-dismiss="modal">x</button>
		<h3><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ARTICLES_BATCH_OPTIONS');?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ARTICLES_BATCH_TIP'); ?></p>
		<div class="control-group">
			<div class="controls">
                <?php echo LayoutHelper::render('joomla.html.batch.access', array()); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
                <?php echo LayoutHelper::render('joomla.html.batch.language', array()); ?>
			</div>
		</div>
		<?php if ($published >= 0) : ?>
		<div class="control-group">
			<div class="controls">
				<?php echo JHtml::_('tzbatch.item', 'com_tz_portfolio_plus');?>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('batch-category-id').value='';document.id('batch-access').value='';document.id('batch-language-id').value=''" data-dismiss="modal" data-bs-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('article.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>
