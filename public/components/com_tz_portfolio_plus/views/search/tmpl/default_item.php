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

$doc    = JFactory::getDocument();

if($item = $this -> item):
    $params         = $this -> item -> params;
?>

    <?php
    if(!isset($item -> mediatypes) || (isset($item -> mediatypes) && !in_array($item -> type,$item -> mediatypes))){
    // Start Description and some info
    ?>
        <div class="card-header bg-transparent">
            <?php
            // Begin Icon print, Email or Edit
            if ($params->get('show_search_print_icon', 0) || $params->get('show_search_email_icon', 0)
                    || $params -> get('access-edit')) { ?>
            <div class="TzIcon">
                <div class="btn-group dropdown pull-right" role="presentation">
                    <a class="btn btn-default btn-secondary btn-sm dropdown-toggle"
                       data-target="#" data-toggle="dropdown" href="#">
                        <i class="tps tp-cog"></i><?php if($params -> get('bootstrapversion', 4) != 4){ ?> <span class="caret"></span><?php }?>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if ($params->get('show_search_print_icon', 0)) : ?>
                            <li class="print-icon"> <?php echo JHtml::_('icon.print_popup', $item, $params); ?> </li>
                        <?php endif; ?>
                        <?php if ($params->get('show_search_email_icon', 0)) : ?>
                            <li class="email-icon"> <?php echo JHtml::_('icon.email', $item, $params); ?> </li>
                        <?php endif; ?>

                        <?php if ($params -> get('access-edit')) : ?>
                            <li class="edit-icon"> <?php echo JHtml::_('icon.edit', $item, $params); ?> </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <?php }
            // End Icon print, Email or Edit
            ?>

            <?php if($params -> get('show_search_title',1)){ ?>
            <h3 class="title" itemprop="name">
                <?php if($params->get('cat_link_titles',1)) { ?>
                    <a href="<?php echo $item ->link; ?>"  itemprop="url">
                        <?php echo $this->escape($item -> title); ?>
                    </a>
                <?php }else { ?>
                    <?php echo $this->escape($item -> title); ?>
                <?php } ?>
            </h3>
            <?php }?>

            <?php
            //-- Start display some information --//
            if ($params->get('show_search_author',0) or $params->get('show_search_category',0)
                or $params->get('show_search_create_date',0) or $params->get('show_search_modify_date',0)
                or $params->get('show_search_publish_date',0) or $params->get('show_search_parent_category',0)
                or $params->get('show_search_hits',0) or $params->get('show_search_tags',0)
                or !empty($item -> event -> beforeDisplayAdditionInfo)
                or !empty($item -> event -> afterDisplayAdditionInfo)) :
                ?>
                <div class="muted TzArticle-info">

                    <?php echo $item -> event -> beforeDisplayAdditionInfo;?>

                    <?php if ($params->get('show_search_category',0)){ ?>
                        <div class="TZcategory-name d-inline-block">
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

                            <?php if ($params->get('cat_link_category',1)){ ?>
                                <?php echo JText::sprintf($lang_text, $url); ?>
                            <?php }else{ ?>
                                <?php echo JText::sprintf($lang_text, '<span itemprop="genre">' . $title . '</span>'); ?>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php if ($params->get('show_search_parent_category', 0) && $item->parent_id != 1){ ?>
                        <div class="TzParentCategoryName d-inline-block">
                            <?php $title = $this->escape($item->parent_title);
                            $url = '<a href="' . JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($item->parent_id)) . '" itemprop="genre">' . $title . '</a>'; ?>
                            <?php if ($params->get('cat_link_parent_category', 1)){ ?>
                                <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_PARENT', $url); ?>
                            <?php }else{ ?>
                                <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_PARENT', '<span itemprop="genre">' . $title . '</span>'); ?>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php
                    if ($params->get('show_search_tags', 0)) {
                        echo $this->loadTemplate('item_tags');
                    }
                    ?>

                    <?php if ($params->get('show_search_create_date',0)){ ?>
                        <div class="TzPortfolioDate d-inline-block" itemprop="dateCreated">
                            <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_CREATED_DATE_ON', JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC2'))); ?>
                        </div>
                    <?php } ?>

                    <?php if ($params->get('show_search_modify_date', 0)) { ?>
                        <div class="TzPortfolioModified d-inline-block" itemprop="dateModified">
                            <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_LAST_UPDATED', JHtml::_('date', $item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
                        </div>
                    <?php } ?>

                    <?php if ($params->get('show_search_publish_date',0)){ ?>
                        <div class="published d-inline-block" itemprop="datePublished">
                            <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_PUBLISHED_DATE_ON', JHtml::_('date', $item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
                        </div>
                    <?php } ?>

                    <?php if ($params->get('show_search_author', 0) && !empty($item->author )){ ?>
                        <div class="TzPortfolioCreatedby d-inline-block" itemprop="author" itemscope itemtype="http://schema.org/Person">
                            <?php $author =  $item->author; ?>
                            <?php $author = ($item->created_by_alias ? $item->created_by_alias : $author);?>
                            <?php $author = '<span itemprop="name">' . $author . '</span>'; ?>

                            <?php if ($params->get('cat_link_author', 1)){?>
                                <?php 	echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_WRITTEN_BY' ,
                                    JHtml::_('link', $item -> author_link, $author, array('itemprop' => 'url'))); ?>
                            <?php }else{?>
                                <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_WRITTEN_BY', $author); ?>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php if ($params->get('show_search_hits', 0)){ ?>
                        <div class="TzPortfolioHits d-inline-block">
                            <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_ARTICLE_HITS', $item->hits); ?>
                            <meta itemprop="interactionCount" content="UserPageVisits:<?php echo $item->hits; ?>" />
                        </div>
                    <?php } ?>

                    <?php echo $item -> event -> afterDisplayAdditionInfo; ?>

                </div>
            <?php
            endif;
            //-- End display some information --//
            ?>
        </div>
        <div class="card-body">
            <?php
            if(!$params -> get('show_search_intro',1)) {
                //Call event onContentAfterTitle on plugin
                echo $item->event->afterDisplayTitle;
            }
            ?>

            <?php
            // Display media from plugin of group tz_portfolio_plus_mediatype
            echo $this -> loadTemplate('item_media');
            ?>

            <?php
            //Show vote
            echo $item -> event -> contentDisplayVote;
            ?>

            <?php
             //Call event onContentBeforeDisplay on plugin
            echo $item -> event -> beforeDisplayContent;
            ?>

            <?php  if ($params->get('show_search_intro',1) AND !empty($item -> introtext)){?>
            <div class="TzPortfolioIntrotext" itemprop="description">
                <?php echo $item -> introtext;?>
            </div>
            <?php } ?>

            <?php echo $item -> event -> contentDisplayListView; ?>

            <?php echo $this -> loadTemplate('item_extrafields');?>

            <?php if($params -> get('show_search_readmore',1)){?>
            <a class="btn btn-default btn-secondary TzPortfolioReadmore" href="<?php echo $item ->link; ?>">
                <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_READ_MORE'); ?>
            </a>
            <?php }?>

            <?php
            //Call event onContentAfterDisplay on plugin
            echo $item->event->afterDisplayContent;
            ?>
        </div>
    <?php
    // End Description and some info
    }?>
<?php endif;?>
