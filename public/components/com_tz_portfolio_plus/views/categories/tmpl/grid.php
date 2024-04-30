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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

$doc    = JFactory::getDocument();
$doc -> addScriptDeclaration('(function($){
    $(document).ready(function(){
        $(".tpp-categories-grid-page .cat-child-btn").off("click").on("click", function(e){
            e.preventDefault();
            var btn = $(this),
                main = btn.closest(".tpp-categories-grid-page"),
                parent = btn.closest(".cat-grid"),
                filter = btn.attr("href").replace(/^\#/gi, ""),
                items = parent.parent().find(".cat-grid.cat-faded"),
                items_filter = parent.siblings(":not([data-cat-filter=\""+filter+"\"])");

            if(typeof main.data("cat-on-click") !== typeof undefined && main.data("cat-on-click") !== parent.index()){
                items.removeClass("cat-faded");
                parent.parent().find(".cat-child-btn.cat-active").removeClass("cat-active");
                console.log(items.find(".cat-child-btn"));
            }

            items_filter.toggleClass("cat-faded");
            btn.toggleClass("cat-active");

//            items_filter.toggleClass(function(){
////                if($(this).hasClass("cat-faded")){
////                    btn.removeClass("cat-active");
////                }else{
////                    btn.removeClass("cat-active");                
////                }
//                btn.toggleClass("cat-active");
//                return "cat-faded";
//            });
            main.data("cat-on-click", parent.index());
        });
    });
})(jQuery);');

$bootstrap4 = ($this->params -> get('enable_bootstrap',1) && $this->params -> get('bootstrapversion', 4) == 4);

$bootstrapClass = '';
if($this->params -> get('enable_bootstrap',1) && $this->params -> get('bootstrapversion', 4) == 4){
    $bootstrapClass = 'tpp-bootstrap ';
}elseif($this->params -> get('enable_bootstrap',1) && $this->params -> get('bootstrapversion', 4) == 3){
    $bootstrapClass = 'tzpp_bootstrap3 ';
}
?>
<div class="<?php echo $bootstrapClass;?>tpp-categories-grid-page <?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>
<?php if ($this->params->get('show_base_description')) : ?>
	<?php 	//If there is a description in the menu parameters use that; ?>
		<?php if($this->params->get('categories_description')) : ?>
			<?php echo  JHtml::_('content.prepare', $this->params->get('categories_description'), '', 'com_tz_portfolio_plus.categories'); ?>
		<?php  else: ?>
			<?php //Otherwise get one from the database if it exists. ?>
			<?php  if ($this->parent->description) : ?>
				<div class="category-desc">
					<?php  echo JHtml::_('content.prepare', $this->parent->description, '', 'com_tz_portfolio_plus.categories'); ?>
				</div>
			<?php  endif; ?>
		<?php  endif; ?>
	<?php endif; ?>

    <div class="cat-items cat-grids">
        <?php
        echo $this->loadTemplate('items');
        ?>
    </div>

</div>

