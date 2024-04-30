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

//JFactory::getLanguage()->load('com_content');

// Create shortcuts to some parameters.
$item       = $this -> item;
$params		= $item->params;
$images     = json_decode($item->images);
$urls       = json_decode($item->urls);
$canEdit	= $item->params->get('access-edit');
$user		= JFactory::getUser();
$doc        = JFactory::getDocument();

$layoutbuidler  =   $this -> generateLayout && !empty($this -> generateLayout) ? true : false;
$bootstrap4 = ($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4);

$bootstrapClass = '';
if($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4){
    $bootstrapClass = 'tpp-bootstrap ';
}elseif($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 3){
    $bootstrapClass = 'tzpp_bootstrap3 ';
}
?>

<div class="<?php echo $bootstrapClass;?>tpItemPage<?php echo $this->pageclass_sfx?>"  itemscope itemtype="http://schema.org/Article">
    <meta itemprop="inLanguage" content="<?php echo ($item->language === '*') ? JFactory::getConfig()->get('language') : $item->language; ?>" />
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
        <h2 class="tpHeadingTitle">
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h2>
    <?php endif; ?>
    <?php
    if($this -> generateLayout && !empty($this -> generateLayout)) {
        echo $this->generateLayout;
    }else{
        ?>
        <?php if($icons = $this -> loadTemplate('icons')):?>
            <?php echo $this -> loadTemplate('icons');?>
        <?php endif;?>
        <div class="tpHead">
            <?php if($title = $this -> loadTemplate('title')):?>
                <?php echo $title;?>
            <?php endif;?>
            <div class="tpMeta muted">
                <?php echo $item -> event -> beforeDisplayAdditionInfo; ?>
                <?php
                if($item -> featured == 1) {
                    ?>
                    <div class="tp-post-featured"><i class="tp tp-star"></i> <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FEATURED'); ?></div>
                    <?php
                }
                ?>
                <?php if($published_date = $this -> loadTemplate('published_date')):?>
                    <?php echo $published_date;?>
                <?php endif;?>
                <?php if($author_info = $this -> loadTemplate('author')):?>
                    <?php echo $author_info;?>
                <?php endif;?>
                <?php if($category = $this -> loadTemplate('category')):?>
                    <?php echo $category;?>
                <?php endif;?>
                <?php if($hits = $this -> loadTemplate('hits')):?>
                    <?php echo $hits;?>
                <?php endif;?>
                <?php if($modified_date = $this -> loadTemplate('modified_date')):?>
                    <?php echo $modified_date;?>
                <?php endif;?>
                <?php echo $item -> event -> afterDisplayAdditionInfo; ?>
            </div>
        </div>
        <div class="tpBody type-standard clearfix">
            <div class="tpArticle clearfix" itemprop="articleBody" data-blog-content>
                <?php
                if (trim($this->item->params ->get('mt_image_show_image_article',1))) :
                    echo $this -> loadTemplate('media');
                endif;
                ?>
                <?php if($introtext = $this -> loadTemplate('introtext')):?>
                    <?php echo $introtext;?>
                <?php endif;?>
                <?php if($fulltext = $this -> loadTemplate('fulltext')):?>
                    <?php echo $fulltext;?>
                <?php endif;?>
                <?php if($extrafields = $this -> loadTemplate('extrafields')):?>
                    <?php echo $extrafields;?>
                <?php endif;?>
                <?php if (trim($this->item->params ->get('project_link', ''))) : ?>
                    <div class="tpPortfolioLink"><a href="<?php echo $this->item->params ->get('project_link'); ?>" title="<?php echo $this->item->params ->get('project_link_title'); ?>" target="_blank" itemprop="url"><?php echo $this->item->params ->get('project_link_title'); ?></a></div>
                <?php endif; ?>
                <?php
                $plugins = array('hikashop_checkout','attachment','vote','social');
                $dispatcher = TZ_Portfolio_PlusPluginHelper::getDispatcher();
                $html = '';
                foreach ($plugins as $plugin) {

                    if ($plugin_obj = TZ_Portfolio_PlusPluginHelper::getPlugin('content', $plugin)) {
                        $className = 'PlgTZ_Portfolio_PlusContent' . ucfirst($plugin);

                        if (!class_exists($className)) {
                            TZ_Portfolio_PlusPluginHelper::importPlugin('content', $plugin);
                        }
                        if (class_exists($className)) {
                            $registry = new JRegistry($plugin_obj->params);

                            $plgClass = new $className($dispatcher, array('type' => ($plugin_obj->type)
                            , 'name' => ($plugin_obj->name), 'params' => $registry));

                            if (method_exists($plgClass, 'onContentDisplayArticleView')) {
                                $html .= $plgClass->onContentDisplayArticleView('com_tz_portfolio_plus.'
                                    . $this->getName(), $this->item, $this->item->params
                                    , $this->state->get('list.offset'), '');
                            }
                        }
                        if (is_array($html)) {
                            $html .= implode("\n", $html);
                        }
                    }
                }
                echo $html;
                ?>
                <?php if($tag = $this -> loadTemplate('tags')):?>
                    <?php echo $tag;?>
                <?php endif;?>
            </div>

        </div>
        <?php
        $plugins = array('music','charity','googlemap','navigation');
        $dispatcher = TZ_Portfolio_PlusPluginHelper::getDispatcher();
        $html = '';
        foreach ($plugins as $plugin) {

            if ($plugin_obj = TZ_Portfolio_PlusPluginHelper::getPlugin('content', $plugin)) {
                $className = 'PlgTZ_Portfolio_PlusContent' . ucfirst($plugin);

                if (!class_exists($className)) {
                    TZ_Portfolio_PlusPluginHelper::importPlugin('content', $plugin);
                }
                if (class_exists($className)) {
                    $registry = new JRegistry($plugin_obj->params);

                    $plgClass = new $className($dispatcher, array('type' => ($plugin_obj->type)
                    , 'name' => ($plugin_obj->name), 'params' => $registry));

                    if (method_exists($plgClass, 'onContentDisplayArticleView')) {
                        $html .= $plgClass->onContentDisplayArticleView('com_tz_portfolio_plus.'
                            . $this->getName(), $this->item, $this->item->params
                            , $this->state->get('list.offset'), '');
                    }
                }
                if (is_array($html)) {
                    $html .= implode("\n", $html);
                }
            }
        }
        if ($html) {
            echo '<div class="tpAddons">'.$html.'</div>';
        }
        if($about_author = $this -> loadTemplate('author_about')):
            echo $about_author;
        endif;


        //Call event onContentAfterDisplayArticleView on plugin
        echo $item->event->contentDisplayArticleView;

        if($related = $this -> loadTemplate('related')):
            echo $related;
        endif;
        $plugins = array('comment');
        $dispatcher = TZ_Portfolio_PlusPluginHelper::getDispatcher();
        $html = '';
        foreach ($plugins as $plugin) {

            if ($plugin_obj = TZ_Portfolio_PlusPluginHelper::getPlugin('content', $plugin)) {
                $className = 'PlgTZ_Portfolio_PlusContent' . ucfirst($plugin);

                if (!class_exists($className)) {
                    TZ_Portfolio_PlusPluginHelper::importPlugin('content', $plugin);
                }
                if (class_exists($className)) {
                    $registry = new JRegistry($plugin_obj->params);

                    $plgClass = new $className($dispatcher, array('type' => ($plugin_obj->type)
                    , 'name' => ($plugin_obj->name), 'params' => $registry));

                    if (method_exists($plgClass, 'onContentDisplayArticleView')) {
                        $html .= $plgClass->onContentDisplayArticleView('com_tz_portfolio_plus.'
                            . $this->getName(), $this->item, $this->item->params
                            , $this->state->get('list.offset'), '');
                    }
                }
                if (is_array($html)) {
                    $html .= implode("\n", $html);
                }
            }
        }
        echo $html;
    }?>

    <?php

    //Call event onContentAfterDisplay on plugin
    //        echo $item->event->afterDisplayContent;
    ?>
</div>
