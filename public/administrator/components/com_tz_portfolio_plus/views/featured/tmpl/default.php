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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');

$j4Compare  = COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE;
if(!$j4Compare) {
    JHtml::_('dropdown.init');
    JHtml::_('formbehavior.chosen', 'select');
}else{
    JHtml::_('formbehavior.chosen', 'select[multiple]');
}

JHtml::_('formbehavior.chosen', '.multipleMediaType', null,
    array('placeholder_text_multiple' => JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_SELECT_MEDIA_TYPE')));
JHtml::_('formbehavior.chosen', '.multipleAuthors', null,
    array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_AUTHOR')));
JHtml::_('formbehavior.chosen', '.multipleAccessLevels', null,
    array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_ACCESS')));
JHtml::_('formbehavior.chosen', '.multipleCategories', null,
    array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_CATEGORY')));

$user		= Factory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_tz_portfolio_plus.article');
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_tz_portfolio_plus&task=articles.saveOrderAjax&tmpl=component';
    if($j4Compare){
        JHtml::_('draggablelist.draggable');
    }else {
        JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
    }
}

$assoc		= JLanguageAssociations::isEnabled();
?>

<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=articles');?>" method="post" name="adminForm" id="adminForm">

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
            <table class="table table-striped" id="articleList">
                <thead>
                <tr>
                    <th width="1%" class="nowrap center text-center hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                    </th>
                    <th width="1%" class="hidden-phone">
                        <?php echo JHtml::_('grid.checkall'); ?>
                    </th>
                    <th width="1%" style="min-width:55px" class="nowrap center text-center">
                        <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                    </th>
                    <th>
                        <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                    </th>
                    <th width="6%" class="nowrap">
                        <?php echo JHtml::_('searchtools.sort', 'COM_TZ_PORTFOLIO_PLUS_TYPE_OF_MEDIA', 'groupname', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', 'COM_TZ_PORTFOLIO_PLUS_GROUP', 'groupname', $listDirn, $listOrder); ?>
                    </th>
                    <th width="6%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
                    </th>

                    <?php if ($assoc) : ?>
                        <th width="5%" class="nowrap hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', 'COM_TZ_PORTFOLIO_PLUS_HEADING_ASSOCIATION', 'association', $listDirn, $listOrder); ?>
                        </th>
                    <?php endif;?>

                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', 'JAUTHOR', 'a.created_by', $listDirn, $listOrder); ?>
                    </th>
                    <th width="5%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="13"><?php echo $this->pagination->getListFooter(); ?></td>
                </tr>
                </tfoot>
                <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl;
                ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
                <?php
                if($this -> items):
                    foreach ($this->items as $i => $item) :
                        $item->max_ordering = 0; //??
                        $ordering	    = ($listOrder == 'a.ordering');
                        $canCreate	    = $user->authorise('core.create',	  'com_tz_portfolio_plus.category.'.$item->catid);
                        $canEdit	    = $user->authorise('core.edit',		  'com_tz_portfolio_plus.article.'.$item->id);
                        $canCheckin	    = $user->authorise('core.manage',	  'com_checkin')
                            || $item->checked_out == $userId || $item->checked_out == 0;
                        $canEditOwn	    = $user->authorise('core.edit.own', 'com_tz_portfolio_plus.article.'.$item->id)
                            && $item->created_by == $userId;
                        $canChange	    = ($user->authorise('core.edit.state', 'com_tz_portfolio_plus.article.'.$item->id)
                                ||($user->authorise('core.edit.state.own', 'com_tz_portfolio_plus.article.'
                                        .$item->id)
                                    && $item->created_by == $userId)) && $canCheckin;
                        ?>
                        <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item -> catid;
                        ?>" data-draggable-group="<?php echo $item->catid; ?>">
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
                                <div class="btn-group">
                                    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'articles.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
                                    <?php echo JHtml::_('tzcontentadmin.featured', $item->featured, $i, $canChange); ?>
                                    <?php // Create dropdown items and render the dropdown list.
                                    if (!$j4Compare && $canChange)
                                    {
                                        JHtml::_('actionsdropdown.' . ((int) $item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'articles');
                                        echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
                                    }
                                    ?>
                                </div>
                            </td>
                            <td class="nowrap has-context">
                                <?php if ($item->checked_out) : ?>
                                    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'articles.', $canCheckin); ?>
                                <?php endif; ?>
                                <?php if ($canEdit || $canEditOwn) : ?>
                                    <?php
                                    $editIcon   = '';
                                    if($j4Compare) {
                                        $editIcon = $item->checked_out ? '' : '<span class="tps tp-pencil-square mr-2" aria-hidden="true"></span>';
                                    }
                                    ?>
                                    <a href="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&task=article.edit&id='.$item->id);?>">
                                        <?php echo $editIcon.$this->escape($item->title); ?></a>
                                <?php else : ?>
                                    <?php echo $this->escape($item->title); ?>
                                <?php endif; ?>
                                <div class="small">
                                    <div class="clearfix">
                                        <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                    </div>
                                    <div class="clearfix">
                                        <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_MAIN_CATEGORY') . ": " ?>
                                        <a href="index.php?option=com_tz_portfolio_plus&task=category.edit&id=<?php echo $item -> catid;?>"><?php echo $this->escape($item->category_title); ?></a>
                                    </div>
                                    <?php if(isset($item -> categories) && $item -> categories && count($item -> categories)):?>
                                        <div class="clearfix">
                                            <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SECONDARY_CATEGORY') . ": " ?>
                                            <?php foreach($item -> categories as $i => $category):?>
                                                <a href="index.php?option=com_tz_portfolio_plus&task=category.edit&id=<?php echo $category -> id;?>"><?php echo $this->escape($category->title); ?></a>
                                                <?php
                                                if($i < count($item -> categories) - 1){
                                                    echo ',';
                                                }
                                                ?>
                                            <?php endforeach;?>
                                        </div>
                                    <?php endif;?>
                                </div>
                            </td>
                            <td class="small hidden-phone">
                                <?php echo $item -> type;?>
                            </td>
                            <td class="small hidden-phone">
                                <a href="index.php?option=com_tz_portfolio_plus&task=group.edit&id=<?php echo $item -> groupid?>">
                                    <?php echo $item -> groupname;?>
                                </a>
                            </td>
                            <td class="small hidden-phone">
                                <?php echo $this->escape($item->access_level); ?>
                            </td>
                            <?php if ($assoc) : ?>
                                <td class="hidden-phone">
                                    <?php if ($item->association) : ?>
                                        <?php echo JHtml::_('tzcontentadmin.association', $item->id); ?>
                                    <?php endif; ?>
                                </td>
                            <?php endif;?>
                            <td class="small hidden-phone">
                                <?php echo $this->escape($item->author_name); ?>
                            </td>
                            <td class="small hidden-phone">
                                <?php if ($item->language=='*'):?>
                                    <?php echo JText::alt('JALL', 'language'); ?>
                                <?php else:?>
                                    <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
                                <?php endif;?>
                            </td>
                            <td class="small nowrap hidden-phone">
                                <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
                            </td>
                            <td class="center text-center hidden-phone">
                                <?php echo (int) $item->hits; ?>
                            </td>
                            <td class="center text-center">
                                <?php echo (int) $item->id; ?>
                            </td>
                        </tr>
                    <?php endforeach;
                endif;
                ?>
                </tbody>
            </table>
        <?php } ?>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
    <?php echo JHtml::_('tzbootstrap.endcontainer');?>
    <?php echo JHtml::_('tzbootstrap.endrow');?>
</form>
