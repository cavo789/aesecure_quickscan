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
    if ($item->type == 'link' || $item->type == 'quote') {
        ?>
        <div class="tpLink">
            <?php echo $this -> loadTemplate('item_media'); ?>
        </div>
    <?php
    }
?>
    <?php
    if(!isset($item -> mediatypes) || (isset($item -> mediatypes) && !in_array($item -> type,$item -> mediatypes))){
        $bootstrap4 = ($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4);
    // Start Description and some info
    ?>
<div class="tpHead">
    <?php
    // Begin Icon print, Email or Edit
    if ($params->get('show_search_print_icon', 0) || $params->get('show_search_email_icon', 0)
        || $params -> get('access-edit')) { ?>
        <div class="TzIcon">
            <div class="dropdown pull-right" role="presentation">
                <a class="btn btn-default btn-outline-secondary btn-sm"
                   data-target="#" data-toggle="dropdown"<?php echo $params->get('enable_bootstrap',1) ? ' href="#"' :''; ?>>
                    <i class="tps tp-cog"></i> <span class="tps tp-angle-down"></span>
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
        <h3 class="tpTitle reset-heading" itemprop="name">
            <?php if($params->get('search_link_titles',1)) { ?>
                <a<?php if($params -> get('tz_use_lightbox', 1)){echo ' class="fancybox fancybox.iframe"';}?>
                    href="<?php echo $item ->link; ?>"  itemprop="url">
                    <?php echo $this->escape($item -> title); ?>
                </a>
            <?php }else { ?>
                <?php echo $this->escape($item -> title); ?>
            <?php } ?>
        </h3>
    <?php }?>
    <?php
    if(!$params -> get('show_search_intro',1)) {
        //Call event onContentAfterTitle on plugin
        echo $item->event->afterDisplayTitle;
    }
    ?>
    <?php
    //-- Start display some information --//
    if ($params->get('show_search_author',0) or $params->get('show_search_category',0)
        or $params->get('show_search_create_date',0) or $params->get('show_search_modify_date',0)
        or $params->get('show_search_publish_date',0) or $params->get('show_search_parent_category',0)
        or $params->get('show_search_hits',0) or $params->get('show_search_tags',0)
        or !empty($item -> event -> beforeDisplayAdditionInfo)
        or !empty($item -> event -> afterDisplayAdditionInfo)) :
        ?>
        <div class="muted tpMeta">

            <?php echo $item -> event -> beforeDisplayAdditionInfo;?>

            <?php if ($params->get('show_search_category',0)){ ?>
                <div class="TZcategory-name">
                    <?php $title = $this->escape($item->category_title);
                    $url = '<a href="' . $item -> category_link
                        . '" itemprop="genre">' . $title . '</a>';
                    ?>

                    <?php if(isset($item -> second_categories) && $item -> second_categories
                        && count($item -> second_categories)){
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
                    <i class="tp tp-folder-open"></i>
                    <?php if ($params->get('search_link_category',1)){ ?>
                        <?php echo $url; ?>
                    <?php }else{ ?>
                        <?php echo '<span itemprop="genre">' . $title . '</span>'; ?>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if ($params->get('show_search_parent_category', 0) && $item->parent_id != 1){ ?>
                <div class="TzParentCategoryName">
                    <?php $title = $this->escape($item->parent_title);
                    $url = '<a href="' . JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($item->parent_id)) . '" itemprop="genre">' . $title . '</a>'; ?>
                    <?php if ($params->get('search_link_parent_category', 1)){ ?>
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

            <?php if ($params->get('show_search_create_date', 1)) : ?>
                <div class="date"><i class="tp tp-clock-o"></i>
                    <time itemprop="datePublished" datetime="<?php echo JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC')); ?>"><?php echo JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC')); ?></time>
                </div>
            <?php endif; ?>

            <?php if ($params->get('show_search_modify_date',0)) : ?>
                <div class="TzBlogModified">
                    <i class="tp tp-pencil-square-o"></i>
                    <time itemprop="dateModified" datetime="<?php echo JHtml::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC')); ?>"><?php echo JHtml::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC')); ?></time>
                </div>
            <?php endif; ?>

            <?php if ($params->get('show_search_publish_date',0)) : ?>
                <div class="TzBlogPublished">
                    <i class="tp tp-calendar"></i>
                    <time itemprop="datePublished" datetime="<?php echo JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC')); ?>"><?php echo JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC')); ?></time>
                </div>
            <?php endif; ?>

            <?php if ($params->get('show_search_author',1) && !empty($this->item->author )) : ?>
                <div class="TzBlogCreatedby" itemprop="author" itemscope itemtype="http://schema.org/Person">
                    <i class="tp tp-pencil"></i>
                    <?php $author =  $this->item->author; ?>
                    <?php $author = ($this->item->created_by_alias ? $this->item->created_by_alias : $author);?>
                    <?php $author = '<span itemprop="name">' . $author . '</span>'; ?>

                    <?php if ($params->get('search_link_author', 1)):?>
                        <?php echo JHtml::_('link', $this -> item -> author_link, $author, array('itemprop' => 'url')); ?>
                    <?php else :?>
                        <?php echo $author; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($params->get('show_search_hits', 1)) : ?>
                <div class="TzBlogHits">
                    <i class="tp tp-eye"></i>
                    <?php echo $this -> item ->hits; ?>
                    <meta itemprop="interactionCount" content="UserPageVisits:<?php echo $this -> item->hits; ?>" />
                </div>
            <?php endif; ?>

            <?php echo $item -> event -> afterDisplayAdditionInfo; ?>

        </div>
    <?php
    endif;
    //-- End display some information --//
    ?>
</div>
<div class="tpBody">
    <?php
    // Display media from plugin of group tz_portfolio_plus_mediatype
        if ($params->get('show_search_item_media', 1)) echo $this -> loadTemplate('item_media');
    ?>
    <?php
    //Call event onContentBeforeDisplay on plugin
    echo $item -> event -> beforeDisplayContent;
    ?>
    <?php  if ($params->get('show_search_intro',1) AND !empty($item -> introtext)){?>
        <div class="tpDescription" itemprop="description">
            <?php echo $item -> introtext;?>
        </div>
    <?php } ?>
    <?php echo $item -> event -> contentDisplayListView; ?>
    <?php echo $this -> loadTemplate('item_extrafields');?>
    <?php
    //Show vote
    echo $item -> event -> contentDisplayVote;
    ?>
    <?php if($params -> get('show_search_readmore',1)){?>
        <a class="btn btn-default btn-outline-secondary TzPortfolioReadmore<?php if($params -> get('tz_use_lightbox', 1)){
            echo ' fancybox fancybox.iframe';}?>" href="<?php echo $item ->link; ?>">
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
