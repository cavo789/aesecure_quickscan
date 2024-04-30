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

use Joomla\CMS\Factory;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');

$j4Compare  = COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE;
if(!$j4Compare) {
    JHtml::_('formbehavior.chosen', 'select');
}else{
    JHtml::_('formbehavior.chosen', 'select[multiple]');
}

$user		= Factory::getUser();
$userId		= $user->get('id');
$extension	= $this->escape($this->state->get('filter.extension'));
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$ordering 	= ($listOrder == 'a.lft');
$saveOrder 	= ($listOrder == 'a.lft' && strtolower($listDirn) == 'asc');

$j4Compare  = COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE;
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_tz_portfolio_plus&task=categories.saveOrderAjax&tmpl=component';

	if($j4Compare){
        JHtml::_('draggablelist.draggable');
    }else {
        JHtml::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
    }
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=categories');?>" method="post" name="adminForm" id="adminForm">

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

            <table class="table table-striped" id="categoryList">
                <thead>
                    <tr>
                        <th width="1%" class="nowrap center text-center hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', '', 'a.lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
                        <th width="1%" class="hidden-phone">
                            <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                        </th>
                        <th width="5%" class="nowrap center text-center">
                            <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                        </th>
                        <th>
                            <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                        </th>
                        <th class="center text-center hidden-phone">
                            <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_INHERITS_PARAMETERS_FROM'); ?>
                        </th>
                        <th width="20%">
                            <?php echo JHtml::_('searchtools.sort', 'COM_TZ_PORTFOLIO_PLUS_GROUP', 'groupname', $listDirn, $listOrder);?>
                        </th>
                        <th width="10%" class="nowrap hidden-phone">
                            <?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'a.title', $listDirn, $listOrder); ?>
                        </th>
                        <th width="5%" class="nowrap hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
                        </th>
                        <th width="1%" class="nowrap hidden-phone">
                            <?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                        </th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="15">
                            <?php echo $this->pagination->getListFooter(); ?>
                        </td>
                    </tr>
                </tfoot>

                <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl;
                ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
                    <?php
                    $originalOrders = array();
                    foreach ($this->items as $i => $item) :
                        $canEdit    = $user->authorise('core.edit',       $extension . '.category.' . $item->id);
                        $canCheckin = $user->authorise('core.admin',      'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                        $canEditOwn = $user->authorise('core.edit.own',   $extension . '.category.' . $item->id) && $item->created_user_id == $userId;
                        $canChange  = ($user->authorise('core.edit.state', $extension . '.category.' . $item->id) ||
                                ($user->authorise('core.edit.state.own', $extension . '.category.' . $item->id)
                                    && $item->created_user_id == $userId)) && $canCheckin;

                        // Get the parents of item for sorting
                        if ($item->level > 1)
                        {
                            $parentsStr = "";
                            $_currentParentId = $item->parent_id;
                            $parentsStr = " ".$_currentParentId;
                            for ($j = 0; $j < $item->level; $j++)
                            {
                                foreach ($this->ordering as $k => $v)
                                {
                                    $v = implode("-", $v);
                                    $v = "-".$v."-";
                                    if (strpos($v, "-" . $_currentParentId . "-") !== false)
                                    {
                                        $parentsStr .= " ".$k;
                                        $_currentParentId = $k;
                                        break;
                                    }
                                }
                            }
                        }
                        else
                        {
                            $parentsStr = "";
                        }
                        ?>
                        <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->parent_id;
                        ?>" data-draggable-group="<?php echo $item->parent_id; ?>" item-id="<?php echo $item->id
                        ?>" parents="<?php echo $parentsStr?>" level="<?php echo $item->level?>">
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
                                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->lft; ?>" />
                            </td>
                            <td class="center text-center hidden-phone">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="center text-center">
                                <?php if(!$j4Compare){?>
                                <div class="btn-group">
                                <?php } ?>
                                    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'categories.', $canChange);?>
                                    <?php
                                    if (!$j4Compare && $canChange)
                                    {
                                        // Create dropdown items
                                        JHtml::_('actionsdropdown.' . ((int) $item->published === 2 ? 'un' : '') . 'archive', 'cb' . $i, 'categories');
                                        JHtml::_('actionsdropdown.' . ((int) $item->published === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'categories');

                                        // Render dropdown list
                                        echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
                                    }
                                    ?>
                                <?php if(!$j4Compare){?>
                                </div>
                                <?php } ?>
                            </td>
                            <td>
                                <?php echo JLayoutHelper::render('joomla.html.treeprefix', array('level' => $item->level)); ?>
                                <?php if ($item->checked_out) : ?>
                                    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'categories.', $canCheckin); ?>
                                <?php endif; ?>
                                <?php if ($canEdit || $canEditOwn) : ?>
                                    <a href="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&task=category.edit&id='.$item->id.'&extension='.$extension);?>">
                                        <?php echo $this->escape($item->title); ?></a>
                                <?php else : ?>
                                    <?php echo $this->escape($item->title); ?>
                                <?php endif; ?>
                                <span class="small" title="<?php echo $this->escape($item->path);?>">
                                    <?php if (empty($item->note)) : ?>
                                        <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                                    <?php else : ?>
                                        <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note));?>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="center text-center"><?php echo $item -> inheritFrom;?></td>
                            <td class="small hidden-phone">
                                <a href="index.php?option=com_tz_portfolio_plus&task=group.edit&id=<?php echo $item -> groupid;?>"><?php echo $item -> groupname;?></a>
                            </td>
                            <td class="small hidden-phone">
                                <?php echo $this->escape($item->access_level); ?>
                            </td>
                            <td class="small hidden-phone nowrap">
                            <?php if ($item->language=='*'):?>
                                <?php echo JText::alt('JALL', 'language'); ?>
                            <?php else:?>
                                <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
                            <?php endif;?>
                            </td>
                            <td class="center text-center">
                                <span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt);?>">
                                    <?php echo (int) $item->id; ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php //Load the batch processing form. ?>
                <?php echo JHtml::_(
                    'bootstrap.renderModal',
                    'collapseModal',
                    array(
                        'title'  => JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_BATCH_OPTIONS'),
                        'footer' => $this->loadTemplate('batch_footer'),
                    ),
                    $this->loadTemplate('batch_body')
                ); ?>
            <?php }?>

            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="original_order_values" value="<?php echo is_array($originalOrders)?implode(',', $originalOrders):''; ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    <?php echo JHtml::_('tzbootstrap.endcontainer');?>
<?php echo JHtml::_('tzbootstrap.endrow');?>
</form>
