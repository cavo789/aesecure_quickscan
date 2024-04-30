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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

$app    = JFactory::getApplication();

// Create shortcuts to some parameters.
$item       = $this -> item;
$params		= $item->params;
$images     = json_decode($item->images);
$urls       = json_decode($item->urls);
$canEdit	= $item->params->get('access-edit');
$user		= JFactory::getUser();
$doc        = JFactory::getDocument();

$bootstrap4 = ($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4);

$bootstrapClass = '';
if($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4){
    $bootstrapClass = 'tpp-bootstrap ';
}elseif($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 3){
    $bootstrapClass = 'tzpp_bootstrap3 ';
}
?>

<div class="<?php echo $bootstrapClass;?>TzItemPage tpp-item-page item-page<?php
echo $this->pageclass_sfx?>"  itemscope itemtype="http://schema.org/Article">
    <div class="tpp-item-page__inner">
        <meta itemprop="inLanguage" content="<?php
        echo ($item->language === '*') ? JFactory::getConfig()->get('language') : $item->language; ?>" />
        <?php if ($this->params->get('show_page_heading', 1)) : ?>
            <h1 class="TzHeadingTitle">
            <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
        <?php endif; ?>

        <?php
        if($this -> generateLayout && !empty($this -> generateLayout)) {
            echo $this->generateLayout;
        }else{
            echo $this -> loadTemplate('media');
        ?>
            <?php echo $item -> event -> beforeDisplayAdditionInfo; ?>

            <?php if($created_date = $this -> loadTemplate('created_date')):?>
            <div class="muted">
                <?php echo $created_date;?>
            </div>
            <?php endif;?>
            <?php if($category = $this -> loadTemplate('category')):?>
            <div class="muted">
            <?php echo $category;?>
            </div>
            <?php endif;?>
            <?php if($hits = $this -> loadTemplate('hits')):?>
            <div class="muted">
            <?php echo $hits;?>
            </div>
            <?php endif;?>
            <?php if($published_date = $this -> loadTemplate('published_date')):?>
            <div class="muted">
            <?php echo $published_date;?>
            </div>
            <?php endif;?>
            <?php if($modified_date = $this -> loadTemplate('modified_date')):?>
            <div class="muted">
            <?php echo $modified_date;?>
            </div>
            <?php endif;?>

            <?php echo $item -> event -> afterDisplayAdditionInfo; ?>

            <?php if(($title = $this -> loadTemplate('title')) || ($icons = $this -> loadTemplate('icons'))):?>
            <div class="">
                <?php echo $this -> loadTemplate('icons');?>
                <?php echo $title;?>
            </div>
            <?php endif;?>
            <?php if($introtext = $this -> loadTemplate('introtext')):?>
            <div class="tpp-item-introtext">
                <?php echo $introtext;?>
            </div>
            <?php endif;?>
            <?php if($fulltext = $this -> loadTemplate('fulltext')):?>
            <div class="tpp-item-fulltext">
                <?php echo $fulltext;?>
            </div>
            <?php endif;?>
            <?php if($extrafields = $this -> loadTemplate('extrafields')):?>
                <?php echo $extrafields;?>
            <?php endif;?>
            <?php if($tag = $this -> loadTemplate('tags')):?>
            <div class="tpp-item-tags">
                <?php echo $tag;?>
            </div>
            <?php endif;?>
            <?php if($author_info = $this -> loadTemplate('author')):?>
            <div class="">
                <?php echo $author_info;?>
            </div>
            <?php endif;?>

            <?php
            //Call event onContentAfterDisplayArticleView on plugin
            echo $item->event->contentDisplayArticleView;
            ?>

            <?php if($related = $this -> loadTemplate('related')):?>
                <div class="">
                    <?php echo $related;?>
                </div>
            <?php endif;?>

        <?php }?>

        <?php

        //Call event onContentAfterDisplay on plugin
//        echo $item->event->afterDisplayContent;
        ?>
    </div>
</div>
