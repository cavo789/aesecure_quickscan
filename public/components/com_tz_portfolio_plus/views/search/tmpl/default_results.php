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

//no direct access
defined('_JEXEC') or die();

if($this -> items):
    $params     = &$this -> params;
?>
    <div class="search-results"
         itemscope itemtype="http://schema.org/Blog">

        <div class="TzItemsRow row">
        <?php
        $col        = $params -> get('article_columns', 1);
        $cols       = TZ_Portfolio_PlusContentHelper::getBootstrapColumns($col);
        $colCounter = 0;
        
        foreach($this -> items as $i => $item) {
            $this->item = $item;
        ?>
            <div class="<?php echo ($cols && isset($cols[$colCounter]))?'col-md-'.$cols[$colCounter]:'col-md-12'; ?>">
                <div class="TzItem mb-4 card rounded-0" itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
                <?php echo $this->loadTemplate('item'); ?>
                </div>
            </div>
        <?php
            $colCounter++;
            if($i % $col == 0){
                $colCounter = 0;
            }
        }
        ?>
        </div>
    </div>

    <?php if (($params->def('show_pagination', 1) == 1
        || ($params->get('show_pagination', 1) == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
        <div class="tpp-pagination pagination align-items-center w-100">
            <?php  if ($params->def('show_pagination_results', 1)) : ?>
                <p class="counter mr-2 mb-0">
                <?php echo $this->pagination->getPagesCounter(); ?>
            </p>
            <?php endif; ?>

            <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
    <?php endif;?>
<?php
endif;