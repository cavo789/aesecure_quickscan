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

if($feeds = $this -> feedBlog) {
    ?>
    <div class="tp-widget tp-feed-blog">
        <h4 class="title"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LATEST_FROM_OUR_BLOG'); ?></h4>
        <ul class="inside">
            <?php for ($i = 0, $max = min(count($feeds), 5); $i < $max; $i++) {
                $feed   = $feeds[$i];
                $uri  = $feed->uri || !$feed->isPermaLink ? trim($feed->uri) : trim($feed->guid);
                $uri  = !$uri || stripos($uri, 'http') !== 0 ? $rssurl : $uri;
            ?>
                <li><a href="<?php echo htmlspecialchars($uri, ENT_COMPAT, 'UTF-8'); ?>" target="_blank"><?php echo trim($feed -> title); ?></a>
                    <span class="rss-date">
                    <?php echo JHtml::_('date', $feed->publishedDate, JText::_('DATE_FORMAT_LC3')); ?>
                    </span></li>
            <?php } ?>
        </ul>
    </div>
    <?php
}