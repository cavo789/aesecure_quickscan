<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 tzportfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum

# Family website: http://www.templaza.com

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$user   = Factory::getUser();

$doc    = Factory::getApplication() -> getDocument();
$doc -> addScript(TZ_Portfolio_PlusUri::base(true, true).'/js/script.min.js',
    array('version' => 'auto', 'relative' => true));

$xml	= simplexml_load_file(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/tz_portfolio_plus.xml');
ob_start();
$date   = Factory::getDate();
?>
    <script type="text/html" id="tmpl-tpPortfolio-footer">
        <div class="tpFooter muted">
            <?php echo JHtml::_('tzbootstrap.addrow');?>
            <div class="span5 col-md-5"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_COPYRIGHT_FOOTER', $date ->year); ?></div>
            <?php if ($user->authorise('core.manage', 'com_installer')) { ?>
                <div class="span7 col-md-7">
                    <ul class="tpLinks inline unstyled list-unstyled">
                        <li class="list-inline-item"><a href="<?php echo $xml -> guideUrl; ?>" target="_blank"><i class="tps tp-book"></i> <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_GUIDE'); ?></a></li>
                        <li class="list-inline-item"><a href="<?php echo $xml -> forumUrl; ?>" target="_blank"><i class="tps tp-comment"></i> <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FORUM'); ?></a></li>
                        <li class="list-inline-item"><a href="<?php echo $xml -> transifexUrl; ?>" target="_blank"><span class="tps tp-language"></span> <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FIND_HELP_TRANSLATE'); ?></a></li>
                        <li class="list-inline-item"><a href="<?php echo $xml -> jedUrl; ?>" target="_blank"><span class="tpb tp-joomla"></span> <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_RATE_ON_JED'); ?></a></li>
                    </ul>
                </div>
            <?php } ?>
            <?php echo JHtml::_('tzbootstrap.endrow');?>
        </div>
    </script>
<?php
$script = ob_get_contents();
ob_end_clean();
$doc -> addCustomTag($script);
