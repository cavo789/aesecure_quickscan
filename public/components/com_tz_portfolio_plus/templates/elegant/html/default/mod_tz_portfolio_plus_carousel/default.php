<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Carousel Module

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 tzportfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum

# Family website: http://www.templaza.com

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;
$tzTemplate = TZ_Portfolio_PlusTemplate::getTemplateById($params -> get('template_id'));
if (!$tzTemplate) $tzTemplate = TZ_Portfolio_PlusTemplate::getTemplate(true);
$tplParams = $tzTemplate->params;
if($list){
	$doc = JFactory::getDocument();
//	$doc -> addStyleSheet('components/com_tz_portfolio_plus/css/all.min.css',
//		array('version' => 'auto'));
?>
<div id="module__<?php echo $module -> id;?>" class="tplElegant tpp-module-carousel tpp-module__carousel<?php echo $moduleclass_sfx;?>">
    <div class="owl-carousel owl-theme element">
        <?php foreach($list as $i => $item){
            // Get article's extrafields
            $extraFields    = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraFields($item, null,
                false, array('filter.list_view' => true, 'filter.group' => $item -> params -> get('order_fieldgroup', 'rdate')));
            $item -> extrafields    = $extraFields;
            ?>
            <div class="TzInner card">
                <?php
                if(isset($item->event->onContentDisplayMediaType)){
                    ?>
                    <div class="TzArticleMedia">
                        <?php echo $item->event->onContentDisplayMediaType;?>
                    </div>
                    <?php
                }
                if(!isset($item -> mediatypes) || (isset($item -> mediatypes) && !in_array($item -> type,$item -> mediatypes))){
                    ?>
                    <div class="card-body">
                        <div class="TzPortfolioDescription">
                            <div class="header-box">
			                    <?php
			                    if ($item -> params -> get('show_title', 1)) {
				                    echo '<h3 class="TzPortfolioTitle"><a href="' . $item->link . '">' . $item->title . '</a></h3>';
			                    }

			                    //Call event onContentBeforeDisplay on plugin
			                    if(isset($item -> event -> beforeDisplayContent)) {
				                    echo $item->event->beforeDisplayContent;
			                    }

			                    if($item -> params -> get('show_author', 1) or $item -> params->get('show_created_date', 1)
				                    or $item -> params->get('show_hit', 1) or $item -> params->get('show_tag', 1)
				                    or $item -> params->get('show_category', 1)
				                    or !empty($item -> event -> beforeDisplayAdditionInfo)
				                    or !empty($item -> event -> afterDisplayAdditionInfo)) {
				                    ?>
                                    <div class="muted tpMeta">
					                    <?php
					                    if (isset($item->event->beforeDisplayAdditionInfo)) {
						                    echo $item->event->beforeDisplayAdditionInfo;
					                    }
					                    if($item -> params -> get('show_category_main', 1) || $item -> params -> get('show_category_sec', 1)){ ?>
                                            <div class="TZcategory-name">
                                                <span class="tp tp-folder-open"></span>
							                    <?php if($item -> params -> get('show_category_main', 1)){ ?>
                                                    <a href="<?php echo $item -> category_link; ?>"><?php echo $item -> category_title;
								                    ?></a><?php
							                    }
							                    if($item -> params -> get('show_category_sec', 1) && $item -> second_categories
								                    && count($item -> second_categories)){
								                    foreach($item -> second_categories as $secCategory){
									                    ?><span class="tpp-module__carousel-separator">,</span>
                                                        <a href="<?php echo $secCategory -> link; ?>"><?php echo $secCategory -> title; ?></a>
								                    <?php }
							                    } ?>
                                            </div>
					                    <?php }
					                    if($item -> params -> get('show_author', 1)){ ?>
                                            <div class="TzPortfolioCreatedby">
                                                <span class="tps tp-user"></span>
                                                <a href="<?php echo $item -> authorLink;?>"><?php echo $item -> author;?></a>
                                            </div>
					                    <?php }
					                    if ($item -> params->get('show_created_date', 1)) {
						                    ?>
                                            <div class="TzPortfolioDate" itemprop="dateCreated">
                                                <i class="tpr tp-clock"></i>
							                    <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC')); ?>
                                            </div>
						                    <?php
					                    }
					                    if ($item -> params->get('show_hit', 1)) {
						                    ?>
                                            <div class="TzPortfolioHits">
                                                <i class="tp tp-eye"></i>
							                    <?php echo $item->hits; ?>
                                                <meta itemprop="interactionCount" content="UserPageVisits:<?php echo $item->hits; ?>" />
                                            </div>
						                    <?php
					                    }
					                    if ($item -> params->get('show_tag', 1)) {
						                    if (isset($tags[$item->content_id])) {
							                    echo '<div class="tz_tag"><i class="tp tp-tag" aria-hidden="true"></i> ';
							                    foreach ($tags[$item->content_id] as $t => $tag) {
								                    echo '<a href="' . $tag->link . '">' . $tag->title . '</a>';
								                    if ($t != count($tags[$item->content_id]) - 1) {
									                    echo ', ';
								                    }
							                    }
							                    echo '</div>';
						                    }
					                    }

					                    if(isset($item -> event -> afterDisplayAdditionInfo)){
						                    echo $item -> event -> afterDisplayAdditionInfo;
					                    }
					                    ?>
                                    </div>
				                    <?php
			                    }
			                    ?>
                            </div>
		                    <?php
		                    if ($item -> params->get('show_introtext', 1)) {
			                    ?>
                                <div class="TzPortfolioIntrotext" itemprop="description"><?php echo $item->introtext;?></div>
		                    <?php }
		                    if(isset($item -> extrafields) && !empty($item -> extrafields)):
			                    ?>
                                <ul class="tz-extrafields">
				                    <?php foreach($item -> extrafields as $field):?>
                                        <li class="tz_extrafield-item">
						                    <?php if($field -> hasTitle()):?>
                                                <div class="tz_extrafield-label"><?php echo $field -> getTitle();?></div>
						                    <?php endif;?>
                                            <div class="tz_extrafield-value pull-left">
							                    <?php echo $field -> getListing();?>
                                            </div>
                                        </li>
				                    <?php endforeach;?>
                                </ul>
		                    <?php
		                    endif;
		                    if(isset($item -> event -> contentDisplayListView)) {
			                    echo $item->event->contentDisplayListView;
		                    }
		                    if($item -> params -> get('show_readmore',1)){
			                    ?>
                                <a href="<?php echo $item->link?>"
                                   class="btn btn-primary"><?php echo $item -> params -> get('readmore_text','Read More');?></a>
		                    <?php }
		                    ?>
                        </div>
                    </div>
                <?php }
                ?>

            </div>
        <?php } ?>
    </div>
    <?php if($params -> get('show_view_all', 0)){?>
        <div class="tpp-portfolio__action text-center">
            <a href="<?php echo $params -> get('view_all_link');?>"<?php echo ($target = $params -> get('view_all_target'))?' target="'
                .$target.'"':'';?> class="btn btn-primary btn-view-all"><?php
                echo $params -> get('view_all_text', 'View All Portfolios');?></a>
        </div>
    <?php } ?>
</div>
<?php
}