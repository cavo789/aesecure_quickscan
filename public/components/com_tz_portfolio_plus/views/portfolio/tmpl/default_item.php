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

defined('_JEXEC') or die();

use Joomla\Utilities\ArrayHelper;

$doc    = JFactory::getDocument();

if($this -> items):
    $tpParams   = TZ_Portfolio_PlusTemplate::getTemplate(true) -> params;
?>
    <?php foreach($this -> items as $i => $item):
        $this -> item   = $item;
        $params         = $item -> params;
        $bootstrap4     = $params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4;

        if($params -> get('tz_column_width',230))
            $tzItemClass    = ' tz_item';
        else
            $tzItemClass    = null;

        if($item -> featured == 1)
            $tzItemFeatureClass    = ' tz_feature_item';
        else
            $tzItemFeatureClass    = null;

        $class  = '';
        if($params -> get('tz_filter_type','tags') == 'tags'){
            if($item -> tags && count($item -> tags)){
                $alias  = ArrayHelper::getColumn($item -> tags, 'alias');
                $class  = implode(' ', $alias);
            }
        }
        elseif($params -> get('tz_filter_type','tags') == 'categories'){
	        $class  = $item -> cat_alias;
	        if(isset($item -> second_categories) && $item -> second_categories &&  count($item -> second_categories)) {
		        foreach($item -> second_categories as $category){
			        $class  .= ' '.$category -> alias.'_'.$category -> id;
		        }
	        }
        }
        elseif($params -> get('tz_filter_type','tags') == 'letters'){
            $class  = mb_strtolower(mb_substr(trim($item -> title),0,1));
        }
    ?>
<div id="tzelement<?php echo $item -> id;?>"
     data-date="<?php echo strtotime($item -> created); ?>"
     data-title="<?php echo $this->escape($item -> title); ?>"
     data-hits="<?php echo (int) $item -> hits; ?>"
     data-portfolio-item-id="<?php echo $item -> id; ?>"
     class="element bg-transparent <?php echo $class.$tzItemClass.$tzItemFeatureClass;?>"
     itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">

    <div class="card rounded-0 tpp-portfolio__inner m-2">
        <?php
        // Display media from plugin of group tz_portfolio_plus_mediatype
        echo $this -> loadTemplate('media');
        ?>

        <?php
        if(!isset($item -> mediatypes) || (isset($item -> mediatypes) && !in_array($item -> type,$item -> mediatypes))){
            // Start Description and some info
            ?>
            <div class="card-body tpp-portfolio__description">
                <?php
                // Begin Icon print, Email or Edit
                if ($params->get('show_cat_print_icon', 0) || $params->get('show_cat_email_icon', 0)
                    || $params -> get('access-edit')) : ?>
                    <div class="tpp-item-icon">
                        <div class="btn-group pull-right float-right" role="presentation">
                            <a class="btn btn-default btn-outline-secondary btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="tps tp-cog"></i><?php if(!$bootstrap4){ ?> <span class="caret"></span><?php }?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <?php if ($params->get('show_cat_print_icon', 0)) : ?>
                                    <li class="print-icon"> <?php echo JHtml::_('icon.print_popup', $item, $params); ?> </li>
                                <?php endif; ?>
                                <?php if ($params->get('show_cat_email_icon', 0)) : ?>
                                    <li class="email-icon"> <?php echo JHtml::_('icon.email', $item, $params); ?> </li>
                                <?php endif; ?>

                                <?php if ($params -> get('access-edit')) : ?>
                                    <li class="edit-icon"> <?php echo JHtml::_('icon.edit', $item, $params); ?> </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif;
                // End Icon print, Email or Edit
                ?>

                <?php if($params -> get('show_cat_title',1)): ?>
                    <h2 class="tpp-item-title name" itemprop="name">
                        <?php if($params->get('cat_link_titles',1)) : ?>
                            <a href="<?php echo $item ->link; ?>"  itemprop="url">
                                <?php echo $this->escape($item -> title); ?>
                            </a>
                        <?php else : ?>
                            <?php echo $this->escape($item -> title); ?>
                        <?php endif; ?>
                    </h2>
                <?php endif;?>

                <?php
                if(!$params -> get('show_cat_intro',1)) {
                    //Call event onContentAfterTitle on plugin
                    echo $item->event->afterDisplayTitle;
                }
                ?>

                <?php
                //Show vote
                echo $item -> event -> contentDisplayVote;
                ?>

                <?php
                //Call event onContentBeforeDisplay on plugin
                echo $item -> event -> beforeDisplayContent;
                ?>

                <?php  if ($params->get('show_cat_intro',1) AND !empty($item -> introtext)) :?>
                    <div class="tpp-item-introtext" itemprop="description">
                        <?php echo $item -> introtext;?>
                    </div>
                <?php endif; ?>

                <?php
                //-- Start display some information --//
                if ($params->get('show_cat_author',0) or $params->get('show_cat_category',0)
                    or $params->get('show_cat_create_date',0) or $params->get('show_cat_modify_date',0)
                    or $params->get('show_cat_publish_date',0) or $params->get('show_cat_parent_category',0)
                    or $params->get('show_cat_hits',0) or $params->get('show_cat_tags',0)
                    or !empty($item -> event -> beforeDisplayAdditionInfo)
                    or !empty($item -> event -> afterDisplayAdditionInfo)) :
                    ?>
                    <div class="tpp-portfolio__info muted text-muted mb-3">

                        <?php echo $item -> event -> beforeDisplayAdditionInfo;?>

                        <?php if ($params->get('show_cat_category',0)) : ?>
                            <div class="tpp-item-category">
                                <?php $title = $this->escape($item->category_title);
                                $url = '<a href="' . $item -> category_link
                                    . '" itemprop="genre">' . $title . '</a>';
                                $lang_text  = 'COM_TZ_PORTFOLIO_PLUS_CATEGORY';
                                ?>

                                <?php if(isset($item -> second_categories) && $item -> second_categories
                                    && count($item -> second_categories)){
                                    $lang_text  = 'COM_TZ_PORTFOLIO_PLUS_CATEGORIES';
                                    foreach($item -> second_categories as $j => $scategory){
                                        if($j <= count($item -> second_categories)) {
                                            $title  .= ', ';
                                            $url    .= ', ';
                                        }
                                        $url    .= '<a href="' . $scategory -> link
                                            . '" itemprop="genre">' . $scategory -> title . '</a>';
                                        $title  .= $this->escape($scategory -> title);
                                    }
                                }?>

                                <?php if ($params->get('cat_link_category',1)) : ?>
                                    <?php echo JText::sprintf($lang_text, $url); ?>
                                <?php else : ?>
                                    <?php echo JText::sprintf($lang_text, '<span itemprop="genre">' . $title . '</span>'); ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($params->get('show_cat_parent_category', 0) && $item->parent_id != 1) : ?>
                            <div class="tpp-item-parent-category">
                                <?php $title = $this->escape($item->parent_title);
                                $url = '<a href="' . JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($item->parent_id)) . '" itemprop="genre">' . $title . '</a>'; ?>
                                <?php if ($params->get('cat_link_parent_category', 1)) : ?>
                                    <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_PARENT', $url); ?>
                                <?php else : ?>
                                    <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_PARENT', '<span itemprop="genre">' . $title . '</span>'); ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php
                        if ($params->get('show_cat_tags', 0)) :
                            echo $this -> loadTemplate('tags');
                        endif;
                        ?>

                        <?php if ($params->get('show_cat_create_date',0)) : ?>
                            <div class="tpp-item-created" itemprop="dateCreated">
                                <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_CREATED_DATE_ON', JHtml::_('date', $item->created, $tpParams -> get('date_format', 'l, d F Y H:i'))); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($params->get('show_cat_modify_date', 0)) : ?>
                            <div class="tpp-item-modified" itemprop="dateModified">
                                <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_LAST_UPDATED', JHtml::_('date', $item->modified, $tpParams -> get('date_format', 'l, d F Y H:i'))); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($params->get('show_cat_publish_date',0)) : ?>
                            <div class="tpp-item-published" itemprop="datePublished">
                                <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_PUBLISHED_DATE_ON', JHtml::_('date', $item->publish_up, $tpParams -> get('date_format', 'l, d F Y H:i'))); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($params->get('show_cat_author', 0) && !empty($item->author )) : ?>
                            <div class="tpp-item-created-by" itemprop="author" itemscope itemtype="http://schema.org/Person">
                                <?php $author =  $item->author; ?>
                                <?php $author = ($item->created_by_alias ? $item->created_by_alias : $author);?>
                                <?php $author = '<span itemprop="name">' . $author . '</span>'; ?>

                                <?php if ($params->get('cat_link_author', 1)):?>
                                    <?php 	echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_WRITTEN_BY' ,
                                        JHtml::_('link', $item -> author_link, $author, array('itemprop' => 'url'))); ?>
                                <?php else :?>
                                    <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_WRITTEN_BY', $author); ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($params->get('show_cat_hits', 0)) : ?>
                            <div class="tpp-item-hit">
                                <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_ARTICLE_HITS', $item->hits); ?>
                                <meta itemprop="interactionCount" content="UserPageVisits:<?php echo $item->hits; ?>" />
                            </div>
                        <?php endif; ?>

                        <?php echo $item -> event -> afterDisplayAdditionInfo; ?>

                    </div>
                <?php
                endif;
                //-- End display some information --//
                ?>

                <?php echo $item -> event -> contentDisplayListView; ?>

                <?php echo $this -> loadTemplate('extrafields');?>

                <?php if($params -> get('show_cat_readmore',1)):?>
                    <a class="btn btn-primary tpp-item-readmore" href="<?php echo $item ->link; ?>">
                        <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_READ_MORE'); ?>
                    </a>
                <?php endif;?>

                <?php
                //Call event onContentAfterDisplay on plugin
                echo $item->event->afterDisplayContent;
                ?>

            </div>
            <?php
            // End Description and some info
        }?>
    </div>
</div>

    <?php endforeach;?>
<?php endif;?>
