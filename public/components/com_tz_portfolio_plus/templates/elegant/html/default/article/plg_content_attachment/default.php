<?php
/*------------------------------------------------------------------------

# Attachment Addon

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2016 tzportfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum

# Family website: http://www.templaza.com

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

if($attachs = $this -> attachments):
    $this -> document -> addStyleSheet(TZ_Portfolio_PlusUri::root(true).'/addons/content/attachment/css/style.css', array('version' => 'auto'));
    $params = $this -> params;
?>
    <div class="tpAttachment">

        <div class="tpAttachment-title">
            <h4 class="reset-heading"><?php echo JText::_('TPL_ELEGANT_ATTACHMENT_TITLE'); ?></h4>
        </div>

        <ul class="list-striped attachments">
            <?php foreach ($attachs as $attach) {
                $item   = $attach -> value;
                ?>
                <li><a href="<?php echo $attach -> link;?>"
                       title="<?php echo $item -> title_attrib;?>"><?php
                        if($params -> get('attachment_show_icon', 1) && isset($item -> icon) && $item -> icon){
                            echo $item -> icon.' ';
                        }
                        echo $item -> title;
                        ?></a>
                    <?php if($params -> get('attachment_show_hit', 1)){?>
                        <span class="hit"><?php echo ($item -> hits < 2)?JText::sprintf('PLG_CONTENT_ATTACHMENT_DOWNLOAD_1', $item -> hits)
                                :JText::sprintf('PLG_CONTENT_ATTACHMENT_DOWNLOAD_N', $item -> hits)?></span>
                    <?php }?>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php endif;