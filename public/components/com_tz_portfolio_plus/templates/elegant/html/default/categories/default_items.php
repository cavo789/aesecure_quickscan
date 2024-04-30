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
$class = ' first';
JHtml::_('bootstrap.tooltip');

if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0) :
?>
<?php foreach($this->items[$this->parent->id] as $id => $item) : ?>
	<?php
	if ($this->params->get('show_empty_categories_cat', 0) || $item->numitems || count($item->getChildren())) :
	if (!isset($this->items[$this->parent->id][$id + 1]))
	{
		$class = ' last';
	}
	?>
	<div class="tpCategoryRow<?php echo $class; ?>">
	<?php $class = ''; ?>
        <div class="tpCategory">
            <?php if ($this->params->get('show_description_image',0) && $item->images) : ?>
            <div class="tpMedia">
                <img src="<?php echo $item->images; ?>" alt="<?php echo htmlspecialchars($item->title); ?>" />
            </div>
            <?php endif; ?>
            <div class="tpInfo">
                <h3 class="item-title reset-heading"><a href="<?php echo JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($item->id));?>">
                        <?php echo $this->escape($item->title); ?></a>
                    <?php if ($this->params->get('show_cat_num_articles_cat', 1) == 1) :?>
                        <span class="badge badge-info hasTooltip" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_NUM_ITEMS'); ?>">
					<?php echo $item->numitems; ?>
				</span>
                    <?php endif; ?>
                    <?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) : ?>
                        <a href="#category-<?php echo $item->id;?>" data-toggle="collapse" class="pull-right collapsed"><i class="tp tp-caret-up"></i></a>
                    <?php endif;?>
                </h3>

                <?php if ($this->params->get('show_subcat_desc_cat', 1)) :?>
                    <?php if ($item->description) : ?>
                        <div class="category-desc">
                            <?php echo JHtml::_('content.prepare', $item->description, '', 'com_tz_portfolio_plus.categories'); ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

		<?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) :?>
			<div class="collapse fade" id="category-<?php echo $item->id;?>">
			<?php
			$this->items[$item->id] = $item->getChildren();
			$this->parent = $item;
			$this->maxLevelcat--;
			echo $this->loadTemplate('items');
			$this->parent = $item->getParent();
			$this->maxLevelcat++;
			?>
			</div>
		<?php
		endif; ?>

	</div>
	<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
