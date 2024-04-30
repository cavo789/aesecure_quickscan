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

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

//JHtml::_('bootstrap.tooltip');
JHtml::_('bootstrap.tooltip');
JHtml::_('dropdown.init');

$j4Compare  = COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE;
if(!$j4Compare) {
    JHtml::_('formbehavior.chosen', 'select');
}else{
    JHtml::_('formbehavior.chosen', 'select[multiple]');
}

$user		= Factory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'g.ordering';

if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_tz_portfolio_plus&task=groups.saveOrderAjax&tmpl=component';
    if($j4Compare){
        JHtml::_('draggablelist.draggable');
    }else {
        JHtml::_('sortablelist.sortable', 'groups', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
    }
}

?>

<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_tz_portfolio_plus&view=groups">

<?php echo JHtml::_('tzbootstrap.addrow');?>
    <?php if(!empty($this -> sidebar)){?>
    <div id="j-sidebar-container" class="span2 col-md-2">
        <?php echo $this -> sidebar; ?>
    </div>
    <?php } ?>

    <?php echo JHtml::_('tzbootstrap.startcontainer', '10', !empty($this -> sidebar));?>
        <div class="tpContainer">
            <?php
            // Search tools bar
            echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
            ?>

            <?php if (empty($this->items)){ ?>
                <div class="alert alert-warning alert-no-items">
                    <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                </div>
            <?php }else{ ?>
            <table class="table table-striped" id="groups">
                <thead>
                <tr>
                    <th width="1%" class="nowrap center text-center hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', '', 'g.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                    </th>
                    <th width="1%" class="hidden-phone">
                        <?php echo JHtml::_('grid.checkall'); ?>
                    </th>
                    <th width="1%" class="nowrap center text-center">
                        <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'g.published', $listDirn, $listOrder); ?>
                    </th>
                    <th class="title">
                        <?php echo JHTML::_('searchtools.sort','JGLOBAL_TITLE','g.name',$listDirn,$listOrder);?>
                    </th>
                    <th style="width: 25%;" class="nowrap">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_ASSIGNMENT');?>
                    </th>
                    <th style="width: 1%;" class="nowrap center text-center">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_TOTAL_FIELDS');?>
                    </th>
                    <th width="5%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'g.access', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" nowrap="nowrap">
                        <?php echo JHTML::_('searchtools.sort','JGRID_HEADING_ID','g.id',$listDirn,$listOrder);?>
                    </th>
                </tr>
                </thead>

                <tfoot>
                <tr>
                    <td colspan="8">
                        <?php echo $this -> pagination -> getListFooter();?>
                    </td>
                </tr>
                </tfoot>

                <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl;
                ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
                <?php
                    foreach ($this->items as $i => $item) {

                        $canEdit = $user->authorise('core.edit', 'com_tz_portfolio_plus.group.' . $item->id);
                        $canEditOwn = $user->authorise('core.edit.own', 'com_tz_portfolio_plus.group.' . $item->id)
                            && $item->created_by == $userId;
                        $canCheckin = $user->authorise('core.manage',     'com_checkin') ||
                            $item->checked_out == $userId || $item->checked_out == 0;
                        $canChange = ($user->authorise('core.edit.state', 'com_tz_portfolio_plus.group.' . $item->id) ||
                            ($user->authorise('core.edit.state.own', 'com_tz_portfolio_plus.group.' . $item->id)
                                && $item->created_by == $userId)) && $canCheckin;
                        ?>
                    <tr class="row<?php echo ($i % 2 == 1) ? '1' : $i; ?>" data-draggable-group="0">
                        <td class="order nowrap center text-center hidden-phone">
                            <?php
                            $iconClass = '';
                            if (!$canChange)
                            {
                                $iconClass = ' inactive';
                            }
                            elseif (!$saveOrder)
                            {
                                $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED');
                            }
                            ?>
                            <span class="sortable-handler<?php echo $iconClass ?>">
								<span class="icon-menu" aria-hidden="true"></span>
							</span>
                            <input type="text" style="display:none" name="order[]" size="5"
                                   value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
                        </td>
                        <td>
                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="center text-center">
                            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'groups.', $canChange, 'cb'); ?>
                        </td>
                        <td class="has-context">
                            <?php if ($item -> checked_out){ ?>
                                <?php echo JHtml::_('jgrid.checkedout', $i, $item -> editor, $item -> checked_out_time, 'groups.', $canCheckin); ?>
                            <?php } ?>
                            <?php if ($canEdit || $canEditOwn) { ?>
                                <a href="index.php?option=com_tz_portfolio_plus&task=group.edit&id=<?php echo $item->id; ?>">
                                    <?php echo $this->escape($item->name); ?>
                                </a>
                            <?php } else { ?>
                                <?php echo $this->escape($item->name); ?>
                            <?php } ?>
                        </td>
                        <td><?php
                            if (isset($item->categories) && $item->categories) {
                                foreach ($item->categories as $j => $cat) {
                                    ?>
                                    <a href="index.php?option=com_tz_portfolio_plus&task=category.edit&id=<?php echo $cat->id; ?>">
                                        <?php echo $cat->title; ?>
                                    </a>
                                    <?php if ($j < count($item->categories) - 1) { ?>
                                        <span>,</span>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </td>
                        <td class="center text-center">
                            <span class="badge badge-info"><?php echo $item->total; ?></span>
                        </td>
                        <td class="small hidden-phone">
                            <?php echo $this->escape($item->access_level); ?>
                        </td>
                        <td align="center text-center"><?php echo $item->id; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php }?>

            <input type="hidden" value="" name="task">
            <input type="hidden" value="0" name="boxchecked">
            <input type="hidden" name="return" value="<?php echo base64_encode(JUri::getInstance() -> toString())?>">
            <?php echo JHTML::_('form.token');?>
        </div>
    <?php echo JHtml::_('tzbootstrap.endcontainer');?>
<?php echo JHtml::_('tzbootstrap.endrow');?>
</form>
