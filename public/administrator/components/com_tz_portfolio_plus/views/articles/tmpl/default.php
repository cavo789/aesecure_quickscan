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
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Button\FeaturedButton;
use Joomla\Component\Content\Administrator\Helper\ContentHelper;

$j4Compare  = COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

if(!$j4Compare) {
    JHtml::_('behavior.multiselect');
    JHtml::_('bootstrap.tooltip');
    JHtml::_('dropdown.init');

    JHtml::_('formbehavior.chosen', '.multipleMediaType', null,
        array('placeholder_text_multiple' => JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_SELECT_MEDIA_TYPE')));
    JHtml::_('formbehavior.chosen', '.multipleAuthors', null,
        array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_AUTHOR')));
    JHtml::_('formbehavior.chosen', '.multipleAccessLevels', null,
        array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_ACCESS')));
    JHtml::_('formbehavior.chosen', '.multipleCategories', null,
        array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_CATEGORY')));
    JHtml::_('formbehavior.chosen', '#filter_category_id_sec', null,
        array('placeholder_text_multiple' => JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_SELECT_SECONDARY_CATEGORY')));

    JHtml::_('formbehavior.chosen', 'select');
}else{
    JHtml::_('formbehavior.chosen', 'select[multiple]:not(.choices__input)');
}

$user		    = Factory::getUser();
$userId		    = $user->get('id');
$listOrder	    = $this->escape($this->state->get('list.ordering'));
$listDirn	    = $this->escape($this->state->get('list.direction'));
$canOrder	    = $user->authorise('core.edit.state', 'com_tz_portfolio_plus.article');
$archived	    = $this->state->get('filter.published') == 2 ? true : false;
$trashed	    = $this->state->get('filter.published') == -2 ? true : false;
$saveOrder	    = $listOrder == 'a.ordering';
$savePriority   = $listOrder == 'a.priority';

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

$this -> document -> addScript(TZ_Portfolio_PlusUri::root(true).'/js/core.min.js', array('version' => 'auto'));
$this -> document -> addScriptDeclaration('(function($, TZ_Portfolio_Plus){
        "use strict";
        TZ_Portfolio_Plus.dialogAjax(["'.$this -> getName().'.approve", "'.$this -> getName().'.reject"]);
    })(jQuery, window.TZ_Portfolio_Plus);');
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
                        <?php if($j4Compare){ ?>
                        <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', 'JFEATURED', 'a.featured', $listDirn, $listOrder); ?>
                        </th>
                        <?php } ?>
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
                        <th width="8%" class="nowrap hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
                        </th>
                        <th width="5%" class="nowrap text-center hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
                        </th>
                        <th width="1%" class="nowrap center text-center hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', 'COM_TZ_PORTFOLIO_PLUS_PRIORITY', 'a.priority', $listDirn, $listOrder); ?>
                            <?php
                            if($savePriority) {
                                echo JHTML::_('grid.order', $this->items, 'filesave.png', 'articles.savepriority');
                            }
                            ?>
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
                        $canApprove     = TZ_Portfolio_PlusHelperACL::allowApprove($item);
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
                            <?php if($j4Compare){ ?>
                            <td class="center text-center">
                                <?php
                                $workflow_featured = false;
                                $options = [
                                    'task_prefix' => 'articles.',
                                    'disabled' => $workflow_featured || !$canChange,
                                    'id' => 'featured-' . $item->id
                                ];

                                echo (new FeaturedButton)
                                    ->render((int) $item->featured, $i, $options);
                                ?>
                            </td>
                            <?php } ?>
                            <td class="center text-center">
                                <?php
                                $filterPublished    = $this -> state -> get('filter.published');

                                if(!$j4Compare){
                                ?>
                                <div class="btn-group">
                                    <?php
                                    }
                                    if($canApprove && ($item -> state == 3 || $item -> state == 4) ){
                                        echo JHtml::_('tppgrid.approve', $i, $this->getName() . '.', $canChange, 'cb');
                                        echo JHtml::_('tppgrid.reject', $i, $this->getName() . '.', $canChange, 'cb');
                                    }elseif($item -> state != 4){
                                        if($item -> state == -3 || $item -> state == 3){
                                            echo JHtml::_('jgrid.action', $i, 'trash',
                                                $this -> getName().'.', 'JTOOLBAR_TRASH', 'JTOOLBAR_TRASH', '', true, 'trash', $canChange);
                                        }else{
                                            echo JHtml::_('tppgrid.status', $item->state, $i, $item -> status,
                                                $this -> getName().'.', $canChange, 'cb', $item->publish_up, $item->publish_down);
                                            if(!$j4Compare) {
                                                echo JHtml::_('tzcontentadmin.featured', $item->featured, $i, $canChange);
                                            }
                                        }
                                    }
                                    // Create dropdown items and render the dropdown list.
                                    if (!$j4Compare && $canChange &&
                                        ($canApprove || (!$canApprove && $item -> state != 3 && $item -> state != 4)))
                                    {
                                        if($item -> state == 3) {
                                            JHtml::_('actionsdropdown.trash', 'cb' . $i,  $this -> getName());
                                        }else {
                                            JHtml::_('actionsdropdown.' . ((int)$item->state === -2 ? 'un' : '')
                                                . 'trash', 'cb' . $i,  $this -> getName());
                                        }
                                        if($item -> state != -3) {
                                            echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
                                        }
                                    }

                                    if(!$j4Compare){
                                    ?>
                                </div>
                                <?php } ?>
                            </td>
                            <td class="has-context">
                                <?php if ($item->checked_out) : ?>
                                    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'articles.', $canCheckin); ?>
                                <?php endif; ?>
                                <?php
                                if(($canApprove && ($canEdit || $canEditOwn || $item -> state == 3 || $item -> state == 4)) ||
                                    (!$canApprove && ($canEditOwn || $item -> state == 3 || $item -> state == -3) && $item -> state != 4)){
                                    ?>
                                    <a href="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&task=article.edit&id='.$item->id);?>">
                                        <?php echo $this->escape($item->title); ?></a>
                                <?php }else{ ?>
                                    <?php echo $this->escape($item->title); ?>
                                <?php } ?>
                                <?php if(isset($item -> rejected_id) && $item -> rejected_id && in_array($item -> state, array(-3,3,4))){ ?>
                                    <span class="label label-danger label-important"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_REJECTED'); ?></span>
                                <?php } ?>
                                <?php
                                if($filterPublished === '*'){?>
                                    <?php if($item -> state == -3){ ?>
                                        <span class="label"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DRAFT'); ?></span>
                                    <?php } ?>
                                    <?php if($item -> state == 3){ ?>
                                        <span class="label label-warning"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_PENDING'); ?>...</span>
                                    <?php } ?>
                                    <?php if($item -> state == 4){ ?>
                                        <span class="label label-info"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_UNDER_REVIEW'); ?></span>
                                    <?php } ?>
                                <?php } ?>
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
                                <?php if(isset($item -> rejected_id) && $item -> rejected_id){ ?>
                                    <div class="tpp-reject__message">
                                        <strong><u><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_REASON'); ?></u></strong>: <?php echo $item -> rejected_message; ?>
                                    </div>
                                <?php } ?>
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
                            <td class="nowrap hidden-phone order" style="text-align: right;">
                                <?php if ($savePriority){ ?>
                                    <div class="btn-group">
                                        <?php echo $this -> pagination -> orderUpIcon($i, true, 'articles.priorityup', 'Move Up');?>
                                        <?php if($orderDown = $this -> pagination -> orderDownIcon($i, $this -> pagination -> pagesTotal, true, 'articles.prioritydown')){
                                            echo $orderDown;
                                        }?>
                                    </div>
                                <?php }
                                ?>
                                <input type="text" name="priority[]" class="width-auto text-center" min="0"<?php
                                echo $savePriority ?  '' : ' disabled="disabled"';
                                ?> style="margin-bottom: 0;" size="1" step="1" value="<?php
                                echo (int) $item -> priority; ?>"/>
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

            <?php //Load the batch processing form. ?>
            <?php echo $this->loadTemplate('batch'); ?>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    <?php echo JHtml::_('tzbootstrap.endcontainer');?>
<?php echo JHtml::_('tzbootstrap.endrow');?>
</form>
