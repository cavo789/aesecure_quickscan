<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2015 templaza.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

if($this -> item):
    $params = $this -> params;
    if($params -> get('show_cat_comment_count', 1) && isset($this -> item -> commentCount)):
?>
<div class="TzPortfolioCommentCount" itemprop="comment" itemscope itemtype="http://schema.org/Comment">
    <i class="tp tp-comment-o" aria-hidden="true"></i>
    <span itemprop="commentCount"><?php echo $this -> item -> commentCount;?></span>
</div>
<?php endif;
endif;
