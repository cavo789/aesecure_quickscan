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

$params = $this -> item -> params;
if($params -> get('show_author',1)) {
    ?>

    <?php if (!empty($this->item->author)){ ?>
        <div class="tpAuthor" itemprop="author" itemscope itemtype="http://schema.org/Person">
            <i class="tp tp-user"></i>
            <span itemprop="name">
            <?php $author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author; ?>
            <?php if ($params->get('link_author', 1)){ ?>
                <?php
                $target = '';
                if (isset($tmpl) AND !empty($tmpl)) {
                    $target = ' target="_blank"';
                }
                $needle = 'index.php?option=com_tz_portfolio_plus&view=users&id=' . $this->item->created_by;
                $item = JMenu::getInstance('site')->getItems('link', $needle, true);
                if (!$userItemid = '&Itemid=' . $this->FindUserItemId($this->item->created_by)) {
                    $userItemid = null;
                }
                $cntlink = $needle . $userItemid;
                ?>
                <?php echo JHtml::_('link', JRoute::_($cntlink), $author, $target . ' itemprop="url" rel="author"'); ?>
            <?php }else{ ?>
                <?php echo $author; ?>
            <?php } ?>
            </span>
        </div>
    <?php }
} ?>