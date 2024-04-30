<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2017 tzportfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum

# Family website: http://www.templaza.com

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$user		= Factory::getUser();
?>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_tz_portfolio_plus&view=acls">

<?php echo JHtml::_('tzbootstrap.addrow');?>
    <?php if(!empty($this -> sidebar)){?>
        <div id="j-sidebar-container" class="span2 col-md-2">
            <?php echo $this -> sidebar; ?>
        </div>
    <?php } ?>

    <?php echo JHtml::_('tzbootstrap.startcontainer', '10', !empty($this -> sidebar));?>

        <div class="tpContainer">
            <table class="table table-striped" id="groups">
                <thead>
                <tr>
                    <th width="1%" class="hidden-phone"></th>
                    <th class="nowrap">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SECTION');?>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php if($items = $this -> items){
                    foreach($items as $i => $item) {
                ?>
                    <tr>
                        <td><?php echo JHtml::_('grid.id', $i, $item -> section, false, 'section'); ?></td>
                        <td>
                            <?php if ($user -> authorise('core.edit', 'com_tz_portfolio_plus')){ ?>
                                <a href="index.php?option=com_tz_portfolio_plus&task=acl.edit&section=<?php echo $item -> section;?>">
                                    <?php echo $this->escape($item -> title);?>
                                </a>
                            <?php }else{ ?>
                                <?php echo $this->escape($item->title); ?>
                            <?php } ?>
                        </td>
                    </tr>
                <?php
                    }
                } ?>
                </tbody>

            </table>
        </div>
    <?php echo JHtml::_('tzbootstrap.endcontainer');?>
<?php echo JHtml::_('tzbootstrap.endrow');?>

    <input type="hidden" value="" name="task">
    <input type="hidden" value="0" name="boxchecked">
    <?php echo JHTML::_('form.token');?>
</form>
