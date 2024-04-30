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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
$doc    = JFactory::getDocument();
$app    = JFactory::getApplication('site');
$input  = $app -> input;
$params = &$this -> params;

$doc -> addScriptDeclaration('
(function($, window){
    $(document).ready(function(){           
        jQuery("#portfolio").tzPortfolioPlusIsotope({
            "rtl": '.(JFactory::getLanguage() -> isRtl()?'true':'false').',
            "mainElementSelector": "#tpPortfolio",
            "params": '.$this -> params .'
        });
    });
})(jQuery, window);
');
?>
<?php if($this -> items):?>

    <?php
    $params     = &$this -> params;
    $bootstrap4 = ($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4);

    $bootstrapClass = '';
    if($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4){
        $bootstrapClass = 'tpp-bootstrap ';
    }elseif($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 3){
        $bootstrapClass = 'tzpp_bootstrap3 ';
    }
?>
<div id="tpPortfolio" class="<?php echo $bootstrapClass;?>tplElegant<?php echo $this->pageclass_sfx;?>">
    <?php if ($params->get('show_page_heading', 1)) : ?>
        <h1 class="page-heading">
            <?php echo $this->escape($params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>

    <?php
    // Display category about when the portfolio has filter category by category id
    echo $this -> loadTemplate('category_about');
    ?>

    <?php
    // Display tag about when the portfolio has filter tag by tag id
    echo $this -> loadTemplate('tag_about');
    ?>

    <?php
    // Display author about when the portfolio has filter user by user id
    echo $this -> loadTemplate('author_about');
    ?>

    <?php if($params -> get('use_filter_first_letter',0)):?>
        <div class="TzLetters text-center mb-3">
            <?php echo $this -> loadTemplate('letters');?>
        </div>
    <?php endif;?>

    <div id="tz_options" class="clearfix">
        <?php
        $tpstyleParams  = TZ_Portfolio_PlusTemplate::getTemplate(true) -> params;
        $dropdownStyle  = $tpstyleParams -> get('filter_style') == 'dropdown';
        if($params -> get('tz_show_filter',1)):
            ?>
            <div class="option-combo<?php echo $dropdownStyle?' dropdown':''; ?>">

                <?php if($dropdownStyle){ ?>
                <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="btn btn-primary btn-sm dropdown-toggle"><?php
                    echo JText::_('TP_STYLE_ELEGANT_FILTER'); ?><?php if(!$bootstrap4){ ?> <span class="tp tp-angle-down option-icon"></span><?php } ?></button>
                <?php } ?>
                <div id="filter" class="option-set text-center clearfix<?php echo $dropdownStyle?' dropdown-menu':''; ?>" data-option-key="filter">
                    <a href="#show-all" data-option-value="*" class="btn btn-primary active"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SHOW_ALL');?></a>
                    <?php if($params -> get('tz_filter_type','tags') == 'tags'):?>
                        <?php echo $this -> loadTemplate('filter_tags');?>
                    <?php endif;?>
                    <?php if($params -> get('tz_filter_type','tags') == 'categories'):?>
                        <?php echo $this -> loadTemplate('filter_categories');?>
                    <?php endif;?>

                    <?php echo (!$dropdownStyle && $this -> filterSubCategory)?implode("\n", $this -> filterSubCategory):''; ?>
                </div>
            </div>
        <?php endif;?>

        <?php if($params -> get('show_sort',0) AND $sortfields = $params -> get('sort_fields',array('date','hits','title'))):
            $sort   = $params -> get('orderby_sec','rdate');
            ?>
            <div class="option-combo<?php echo $dropdownStyle?' dropdown':''; ?>">
                <?php if($dropdownStyle){ ?>
                    <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="btn btn-primary btn-sm dropdown-toggle"><?php
                        echo JText::_('TP_STYLE_ELEGANT_SORT'); ?><?php if(!$bootstrap4){ ?> <span class="tp tp-angle-down option-icon"></span><?php } ?></button>
                <?php }else{ ?>
                <div class="filter-title"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SORT')?></div>
                <?php } ?>

                <div id="sort" class="option-set clearfix<?php echo $dropdownStyle?' dropdown-menu':''; ?>" data-option-key="sortBy">
                    <?php
                    foreach($sortfields as $sortfield):
                        switch($sortfield):
                            case 'title':
                                ?>
                                <a class="btn btn-primary btn-sm<?php
                                echo ($sort == 'alpha' || $sort == 'ralpha')?' active':''?>"
                                   href="#title" data-option-value="name"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_TITLE');?></a>
                                <?php
                                break;
                            case 'date':
                                ?>
                                <a class="btn btn-primary btn-sm<?php
                                echo ($sort == 'date' || $sort == 'rdate')?' active':''?>"
                                   href="#date" data-option-value="date"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DATE');?></a>
                                <?php
                                break;
                            case 'hits':
                                ?>
                                <a class="btn btn-primary btn-sm<?php
                                echo ($sort == 'hits' || $sort == 'rhits')?' active':''?>"
                                   href="#hits" data-option-value="hits"><?php echo JText::_('JGLOBAL_HITS');?></a>
                                <?php
                                break;
                        endswitch;
                    endforeach;
                    ?>
                </div>
            </div>
        <?php endif;?>

        <?php if($params -> get('show_layout',0)):?>
            <div class="option-combo<?php echo $dropdownStyle?' dropdown':''; ?>">
                <?php if($dropdownStyle){ ?>
                    <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="btn btn-primary btn-sm dropdown-toggle"><?php
                        echo JText::_('TP_STYLE_ELEGANT_LAYOUT'); ?><?php if(!$bootstrap4){ ?> <span class="tp tp-angle-down option-icon"></span><?php } ?></button>
                <?php }else{ ?>
                        <div class="filter-title"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LAYOUT');?></div>
                <?php }?>
                <div id="layouts" class="option-set clearfix<?php echo $dropdownStyle?' dropdown-menu':''; ?>" data-option-key="layoutMode">
                    <?php
                    if(count($params -> get('layout_type',array('masonry','fitRows','straightDown')))>0):
                        foreach($params -> get('layout_type',array('masonry','fitRows','straightDown')) as $i => $param):
                            ?>
                            <a class="btn btn-primary btn-sm<?php
                            echo ($i == 0)?' active':'';?>" href="#<?php echo $param?>" data-option-value="<?php echo $param?>">
                                <?php echo $param?>
                            </a>
                        <?php endforeach;?>
                    <?php endif;?>
                </div>
            </div>
        <?php endif;?>

        <?php if($params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'default'):?>
            <?php if($params -> get('show_limit_box',1)):?>
                <div class="TzShow">
                    <span class="title"><?php echo strtoupper(JText::_('JSHOW'));?></span>
                    <form name="adminForm" method="post" id="TzShowItems"
                          action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=portfolio&Itemid='.$this -> Itemid);?>">
                        <?php echo $this -> pagination -> getLimitBox();?>
                    </form>
                </div>
            <?php endif;?>
        <?php endif;?>
    </div>

    <div id="portfolio" class="super-list variable-sizes clearfix"
         itemscope itemtype="http://schema.org/Blog">
        <?php echo $this -> loadTemplate('item');?>
    </div>

    <?php if($params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'default'):?>
        <?php if (($params->def('show_pagination', 1) == 1  || ($params->get('show_pagination', 1) == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
            <div class="pagination align-items-center">
                <?php  if ($params->def('show_pagination_results', 1)) : ?>
                    <p class="counter mr-2 mb-0">
                        <?php echo $this->pagination->getPagesCounter(); ?>
                    </p>
                <?php endif; ?>

                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        <?php endif;?>
    <?php endif;?>

    <?php if($params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'ajaxButton'
        || $params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'ajaxInfiScroll'):?>
        <?php echo $this -> loadTemplate('infinite_scroll');?>
    <?php endif;?>

</div>
<?php
endif;