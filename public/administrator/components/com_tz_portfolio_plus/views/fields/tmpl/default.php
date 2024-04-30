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

JHtml::_('bootstrap.tooltip');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', '.multipleFieldTypes', null, array('placeholder_text_multiple' => JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_SELECT_TYPE')));
JHtml::_('formbehavior.chosen', '.multipleFieldGroups', null, array('placeholder_text_multiple' => JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_SELECT_GROUP')));

$j4Compare  = COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE;
if(!$j4Compare) {
    JHtml::_('formbehavior.chosen', 'select');
}
else{
    JHtml::_('formbehavior.chosen', 'select[multiple]');
}
$user		= TZ_Portfolio_PlusUser::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$saveOrder	= ($listOrder == 'f.ordering' || $listOrder == 'ordering');

if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_tz_portfolio_plus&task=fields.saveOrderAjax&tmpl=component';
    if($j4Compare){
        JHtml::_('draggablelist.draggable');
    }else {
        JHtml::_('sortablelist.sortable', 'extraFieldList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
    }
}
?>

<form id="adminForm" name="adminForm" method="post" action="index.php?option=com_tz_portfolio_plus&view=fields">

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
            <table class="table table-striped" id="extraFieldList">
                <thead>
                <tr>
                    <th width="1%" class="nowrap center text-center hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', '', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                    </th>
                    <th width="1%">
                        <?php echo JHtml::_('grid.checkall'); ?>
                    </th>
                    <th width="1%" class="nowrap center text-center">
                        <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'f.published', $listDirn, $listOrder); ?>
                    </th>
                    <th><?php echo JHTML::_('searchtools.sort','JGLOBAL_TITLE','f.title'
                            ,$listDirn,$listOrder);?></th>
                    <th width="18%"><?php echo JHTML::_('searchtools.sort','COM_TZ_PORTFOLIO_PLUS_GROUP','groupname'
                            ,$listDirn,$listOrder);?></th>
                    <th width="7%"><?php echo JHTML::_('searchtools.sort','COM_TZ_PORTFOLIO_PLUS_TYPE','f.type'
                            ,$listDirn,$listOrder);?></th>
                    <th width="5%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'f.access', $listDirn, $listOrder); ?>
                    </th>
                    <th width="5%" class="nowrap center text-center"><?php echo JHTML::_('searchtools.sort','COM_TZ_PORTFOLIO_PLUS_LIST_VIEW_LABEL','f.list_view'
                            ,$listDirn,$listOrder);?></th>
                    <th width="5%" class="nowrap center text-center"><?php echo JHTML::_('searchtools.sort','COM_TZ_PORTFOLIO_PLUS_DETAILS_VIEW_LABEL','f.detail_view'
                            ,$listDirn,$listOrder);?></th>
                    <th width="5%" class="nowrap center text-center"><?php echo JHTML::_('searchtools.sort','COM_TZ_PORTFOLIO_PLUS_ADVANCED_SEARCH_LABEL','f.advanced_search'
                            ,$listDirn,$listOrder);?></th>
                    <th nowrap="nowrap" width="1%">
                        <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'f.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="11">
                        <?php echo $this -> pagination -> getListFooter();?>
                    </td>
                </tr>
                </tfoot>

                <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl;
                ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
                <?php
                    foreach($this -> items as $i => $item){
                        $canEdit    = $user->authorise('core.edit',		  'com_tz_portfolio_plus.field.'.$item->id)
                            && (count($user -> getAuthorisedFieldGroups('core.edit', $item -> groupid)) > 0);
                        $canEditOwn = $user->authorise('core.edit.own', 'com_tz_portfolio_plus.field.'.$item->id)
                            && $item->created_by == $userId && (count($user -> getAuthorisedFieldGroups('core.edit.own', $item -> groupid)) > 0);
                        $canCheckin = $user->authorise('core.manage',     'com_checkin')
                            || $item->checked_out == $userId || $item->checked_out == 0;
                        $canChange  = ($user->authorise('core.edit.state', 'com_tz_portfolio_plus.field.'.$item->id)
                                ||($user->authorise('core.edit.state.own', 'com_tz_portfolio_plus.field.'
                                        .$item->id)
                                    && $item->created_by == $userId)) && $canCheckin;
                        ?>
                        <tr class="row<?php echo ($i%2==1)?'1':$i;?>"<?php
                        echo ($group = $this -> state -> get('filter.group'))?'sortable-group-id="'.$group
                            .'"':'';?> data-draggable-group="<?php echo $group?$group:0; ?>">
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
                            <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
                        </td>
                        <td class="center text-center">
                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="center text-center">
                            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'fields.', $canChange, 'cb'); ?>
                        </td>
                        <td class="nowrap has-context">
                            <?php if ($item -> checked_out){ ?>
                                <?php echo JHtml::_('jgrid.checkedout', $i, $item -> editor, $item -> checked_out_time, 'fields.', $canCheckin); ?>
                            <?php } ?>
                            <?php if($canEdit || $canEditOwn){?>
                            <a href="index.php?option=com_tz_portfolio_plus&task=field.edit&id=<?php echo $item -> id;?>">
                                <?php echo $this -> escape($item -> title);?>
                            </a>
                            <?php }else{ ?>
                                <?php echo $this -> escape($item -> title);?>
                            <?php } ?>
                        </td>
                        <td class="small hidden-phone">
                            <?php echo $item -> groupname;?>
                        </td>

                        <td class="small hidden-phone"><?php echo $item -> type;?></td>
                        <td class="small hidden-phone">
                            <?php echo $this->escape($item->access_level); ?>
                        </td>
                        <td class="center text-center"><?php
                            $active_class   = ($item -> list_view)?'publish':'unpublish';
                            echo JHtml::_('jgrid.action', $i, ($item -> list_view == 1?'unlistview':'listview'), 'fields.', true, '', '', false, $active_class,$active_class, $canChange); ?></td>
                        <td class="center text-center"><?php
                            $dactive_class   = ($item -> detail_view)?'publish':'unpublish';
                            echo JHtml::_('jgrid.action', $i, ($item -> detail_view == 1?'undetailview':'detailview'), 'fields.', true, '', '', false, $dactive_class,$dactive_class, $canChange); ?></td>
                        <td class="center text-center"><?php
                            $advactive_class   = ($item -> advanced_search)?'publish':'unpublish';
                            echo JHtml::_('jgrid.action', $i, ($item -> advanced_search == 1?'unadvsearch':'advsearch'), 'fields.', true, '', '', false, $advactive_class,$advactive_class, $canChange); ?></td>
                        <td class="center text-center"><?php echo $item -> id;?></td>
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