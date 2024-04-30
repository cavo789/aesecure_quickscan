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
$class = ' class="first"';
JHtml::_('bootstrap.tooltip');

if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0){
?>
<?php foreach($this->items[$this->parent->id] as $id => $item){
	if ($this->params->get('show_empty_categories_cat', 0) || $item->numitems || count($item->getChildren())) {
        if (!isset($this->items[$this->parent->id][$id + 1]))
        {
            $class = ' class="last"';
        }
?>
    <div class="cat-item cat-grid"<?php echo $item -> level > 1?' data-cat-filter="category-'.$this->parent->id.'"':''; ?>>
        <?php if ($this->params->get('show_description_image',0) && $item->images) { ?>
            <div class="img">
                <img src="<?php echo $item->images; ?>" alt="<?php echo htmlspecialchars($item->title); ?>" />
            </div>
        <?php } ?>

        <div class="cat-item-content">
            <?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) {
                ?>
                <a href="#category-<?php echo $item->id;?>" class="cat-child-btn float-right">
                    <i class="tps tp-chevron-left cat-caret-left"></i><i class="tps tp-chevron-down cat-caret-down"></i>
                </a>
            <?php }?>
            <h2 class="title"><a href="<?php echo JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($item->id));
            ?>"><?php echo $this->escape($item->title); ?></a>
                <?php if ($this->params->get('show_cat_num_articles_cat', 1) == 1){?>
                    <span class="badge badge-info hasTooltip cat-badge" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_NUM_ITEMS'); ?>">
                <?php echo $item->numitems; ?>
                </span>
                <?php } ?>
            </h2>

            <?php if ($this->params->get('show_subcat_desc_cat', 1) && $item->description) {?>
                <div class="category-desc">
                    <?php echo JHtml::_('content.prepare', $item->description, '', 'com_tz_portfolio_plus.categories'); ?>
                </div>
            <?php } ?>
        </div>
    </div>


    <?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1){ ?>
        <?php
        $this->items[$item->id] = $item->getChildren();
        $this->parent = $item;
        $this->maxLevelcat--;
        echo $this->loadTemplate('items');
        $this->parent = $item->getParent();
        $this->maxLevelcat++;
        ?>
    <?php }
	} ?>
<?php }
} ?>
