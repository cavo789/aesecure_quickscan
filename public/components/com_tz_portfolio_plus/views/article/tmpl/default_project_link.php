<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

if ($this->item->params ->get('project_link')) :
    ?>
    <div class="tpPortfolioLink"><a href="<?php echo $this->item->params ->get('project_link'); ?>" title="<?php
        echo $this->item->params ->get('project_link_title');
        ?>" target="_blank" itemprop="url" class="btn btn-default btn-secondary btn-block btn-large btn-lg"><?php
            echo $this->item->params ->get('project_link_title'); ?></a></div>
<?php endif; ?>