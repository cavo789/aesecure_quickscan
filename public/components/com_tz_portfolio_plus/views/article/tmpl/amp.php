<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2019 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

$lang       = JFactory::getLanguage();

$item       = $this -> item;
$params		= $item->params;
?>
<!doctype html>
<html amp lang="<?php echo $lang -> getTag(); ?>" <?php echo $lang -> isRtl() ? 'dir="rtl"' : ''; ?>>
<head>
    <meta charset="utf-8">
    <title><?php echo $this->escape($this->params->get('page_heading')); ?></title>

    <link rel="canonical" href="<?php echo $url; ?>" />
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Heebo" rel="stylesheet">

    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <script async custom-element="amp-bind" src="https://cdn.ampproject.org/v0/amp-bind-0.1.js"></script>
</head>
<body>
    <?php

    if($this -> generateLayout && !empty($this -> generateLayout)) {
        echo $this->generateLayout;
    }else {
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
    <?php
    }
    ?>
</body>
</html>
