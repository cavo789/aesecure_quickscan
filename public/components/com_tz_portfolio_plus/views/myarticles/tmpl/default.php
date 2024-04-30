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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.multiselect');

$params     = $this -> params;

$j4Compare  = COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE;

JHtml::_('formbehavior.chosen', '.multipleMediaType', null,
    array('placeholder_text_multiple' => JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_SELECT_MEDIA_TYPE')));
JHtml::_('formbehavior.chosen', '.multipleCategories', null,
    array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_CATEGORY')));
JHtml::_('formbehavior.chosen', 'select');

$user		    = JFactory::getUser();
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
	$saveOrderingUrl = 'index.php?option=com_tz_portfolio_plus&task='.$this -> getName().'.saveOrderAjax&tmpl=component';
    if($j4Compare){
        JHtml::_('draggablelist.draggable');
    }else {
        JHtml::_('sortablelist.sortable', 'myArticleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
    }
}

$assoc		= JLanguageAssociations::isEnabled();

$this -> document -> addScriptDeclaration('(function($, TZ_Portfolio_Plus){
        "use strict";
        TZ_Portfolio_Plus.dialogAjax(["'.$this -> getName().'.approve", "'.$this -> getName().'.reject", "'
    .$this -> getName().'.delete"]);
    })(jQuery, window.TZ_Portfolio_Plus);');

$menu   = JFactory::getApplication() -> getMenu();
$active = $menu -> getActive();
$url    = 'index.php?option=com_tz_portfolio_plus&view='.$this -> getName()
    .((isset($active -> id) && $active -> id)?'&Itemid='.$active -> id:'');

$bootstrap4 = ($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4);

$bootstrapClass = '';
if($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4){
    $bootstrapClass = 'tpp-bootstrap ';
}elseif($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 3){
    $bootstrapClass = 'tzpp_bootstrap3 ';
}
?>

<div class="<?php echo $bootstrapClass;?>tpp-myarticles-page <?php echo $this->pageclass_sfx;?>">
    <?php if ($params->get('show_page_heading', 1)) : ?>
        <h1 class="page-heading">
            <?php echo $this->escape($params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>

    <form action="<?php echo JRoute::_($url);
    ?>" method="post" name="adminForm" id="adminForm">

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
                <table class="table table-striped" id="myArticleList">
                    <thead>
                        <tr>
                            <th width="1%" class="hidden-phone">
                                <?php echo JHtml::_('grid.checkall'); ?>
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

                            <?php if ($assoc) : ?>
                            <th width="5%" class="nowrap hidden-phone">
                                <?php echo JHtml::_('searchtools.sort', 'COM_TZ_PORTFOLIO_PLUS_HEADING_ASSOCIATION', 'association', $listDirn, $listOrder); ?>
                            </th>
                            <?php endif;?>

                            <th width="10%" class="nowrap hidden-phone">
                                <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                            </th>
                            <th width="8%" class="nowrap hidden-phone">
                                <?php echo JHtml::_('searchtools.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
                            </th>
                            <th width="5%" class="nowrap text-center hidden-phone">
                                <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
                            </th>
                            <th width="1%" class="nowrap center text-center hidden-phone">
                                <?php echo JHtml::_('searchtools.sort', 'COM_TZ_PORTFOLIO_PLUS_PRIORITY', 'a.priority', $listDirn, $listOrder); ?>
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

                            $filterPublished= $this -> state -> get('filter.published');

                            $canCreate	    = $user->authorise('core.create',	  'com_tz_portfolio_plus.category.'.$item->catid);
                            $canEdit	    = $user->authorise('core.edit',		  'com_tz_portfolio_plus.article.'.$item->id);
                            $canCheckin	    = $user->authorise('core.manage',	  'com_checkin')
                                                || $item->checked_out == $userId || $item->checked_out == 0;
                            $canEditOwn	    = $user->authorise('core.edit.own', 'com_tz_portfolio_plus.article.'.$item->id)
                                                && $item->created_by == $userId;
                            $canDelete      = $filterPublished == -2 && ($user->authorise('core.delete', 'com_tz_portfolio_plus.article.'.$item->id)
                                    ||($user->authorise('core.delete.own', 'com_tz_portfolio_plus.article.'
                                            .$item->id)
                                        && $item->created_by == $userId)) && $canCheckin;
                            $canChange	    = ($user->authorise('core.edit.state', 'com_tz_portfolio_plus.article.'.$item->id)
                                                ||($user->authorise('core.edit.state.own', 'com_tz_portfolio_plus.article.'
                                                .$item->id)
                                                && $item->created_by == $userId)) && $canCheckin;
                            $canApprove     = TZ_Portfolio_PlusHelperACL::allowApprove($item);
                            ?>
                            <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item -> catid;
                            ?>" data-dragable-group="<?php echo $item->catid; ?>">
                                <td class="center text-center">
                                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                </td>
                                <td class="has-context">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo JHtml::_('tz_portfolio_plus.checkedout', $i, $item->editor, $item->checked_out_time, 'myarticles.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php
                                    if(($canApprove && ($canEdit || $canEditOwn || $item -> state == 3 || $item -> state == 4)) ||
                                        (!$canApprove && ($canEditOwn || $item -> state == 3 || $item -> state == -3) && $item -> state != 4)){
                                        ?>
                                        <?php
                                        $editIcon   = '';
                                        if($j4Compare){
                                            $editIcon = $item->checked_out ? '' : '<span class="fa fa-pencil-square mr-2" aria-hidden="true"></span>';
                                        }
                                        ?>
                                        <a class="title" href="<?php
                                        echo JRoute::_('index.php?option=com_tz_portfolio_plus&task=article.edit&a_id='
                                            .$item->id.((isset($active -> id) && $active -> id)?'&Itemid='.$active -> id:'')
                                            .'&return='.base64_encode(JRoute::_($url)));?>">
                                            <?php echo $editIcon.$this->escape($item->title); ?></a>
                                    <?php }else{ ?>
                                        <?php echo $this->escape($item->title); ?>
                                    <?php } ?>
                                    <?php if(isset($item -> rejected_id) && $item -> rejected_id && in_array($item -> state, array(-3,3,4))){ ?>
                                        <span class="label label-danger label-important"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_REJECTED'); ?></span>
                                    <?php } ?>
                                    <?php
                                    if($filterPublished === '*'){?>
                                        <?php if($item -> state == 3){ ?>
                                            <span class="label label-warning"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_PENDING'); ?>...</span>
                                        <?php } ?>
                                    <?php } ?>
                                    <div class="small meta">
                                        <div class="clearfix">
                                            <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_MAIN_CATEGORY') . ": " ?>
                                            <a href="<?php echo JRoute::_(
                                                'index.php?option=com_tz_portfolio_plus&view=myarticles&catid='
                                                .$item -> catid);?>"><?php echo $this->escape($item->category_title); ?></a>
                                        </div>
                                        <?php if(isset($item -> categories) && $item -> categories && count($item -> categories)):?>
                                        <div class="clearfix">
                                            <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SECONDARY_CATEGORY') . ": " ?>
                                            <?php foreach($item -> categories as $i => $category):?>
                                                <a href="<?php echo JRoute::_(
                                                        'index.php?option=com_tz_portfolio_plus&view=myarticles&catid='
                                                        .$category -> id);?>"><?php echo $this->escape($category->title); ?></a>
                                                <?php
                                                if($i < count($item -> categories) - 1){
                                                    echo ',';
                                                }
                                                ?>
                                            <?php endforeach;?>
                                        </div>
                                        <?php endif;?>
                                        <div class="clearfix">
                                            <?php echo JText::_('JAUTHOR').': '.$this->escape($item->author_name); ?>
                                        </div>
                                    </div>
                                    <?php if(isset($item -> rejected_id) && $item -> rejected_id){ ?>
                                        <div class="tpp-reject__message">
                                            <strong><u><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_REASON'); ?></u></strong>: <?php echo $item -> rejected_message; ?>
                                        </div>
                                    <?php } ?>
                                    <ul class="tpp-myarticles__actions" data-tpp-actions data-tpp-id="<?php echo $item -> id;?>">
                                        <?php
                                        if($canApprove && ($item -> state == 3 || $item -> state == 4) ){ ?>
                                            <li>
                                                <?php echo JHtml::_('tz_portfolio_plus.approveLink', $item -> state, $i,
                                                    $this -> getName());?>
                                            </li>
                                            <li>
                                                <?php echo JHtml::_('tz_portfolio_plus.rejectLink', $i,
                                                    $this -> getName());?>
                                            </li>
                                        <?php
                                        }elseif($item -> state != 4){
                                            if($canChange) {
                                                ?>

                                                <li>
                                                    <?php echo JHtml::_('tz_portfolio_plus.taskLink', $item->state, $i,
                                                        $this->getName()); ?>
                                                </li>

                                                <?php if(!in_array($item -> state, array(-3, -2, 3))){ ?>
                                                <li>
                                                    <?php echo JHtml::_('tz_portfolio_plus.trashLink', $i,
                                                        $this->getName()); ?>
                                                </li>
                                                <?php } ?>

                                                <?php
                                            }
                                                if($canDelete) { ?>
                                                <li>
                                                    <?php echo JHtml::_('tz_portfolio_plus.deleteLink', $i,
                                                        $this -> getName());?>
                                                </li>
                                            <?php
                                            }
                                            if($canApprove && $filterPublished != -2){ ?>
                                                <li><?php echo JHtml::_('tz_portfolio_plus.featuredLink',$item -> featured, $i,
                                                        $this -> getName(), $canChange) ?></li>
                                            <?php } ?>
                                        <?php
                                        }
                                        ?>
                                    </ul>
                                </td>
                                <td class="small hidden-phone">
                                    <?php echo $item -> type;?>
                                </td>
                                <td class="small hidden-phone">
                                    <a href="index.php?option=com_tz_portfolio_plus&task=group.edit&id=<?php echo $item -> groupid?>">
                                        <?php echo $item -> groupname;?>
                                    </a>
                                </td>
                                <?php if ($assoc) : ?>
                                <td class="hidden-phone">
                                    <?php if ($item->association) : ?>
                                        <?php echo JHtml::_('contentadministrator.association', $item->id); ?>
                                    <?php endif; ?>
                                </td>
                                <?php endif;?>
                                <td class="small hidden-phone">
                                    <?php
                                    if($item -> state == 0){ ?>
                                        <span class="text-error text-danger"><?php echo JText::_('JUNPUBLISHED'); ?></span>
                                    <?php }elseif($item -> state == 1){ ?>
                                        <span class="text-success"><?php echo JText::_('JPUBLISHED'); ?></span>
                                    <?php }
                                    elseif($item -> state == -2){?>
                                        <span><?php echo JText::_('JTRASHED'); ?></span>
                                    <?php }
                                    elseif($item -> state == -3){?>
                                        <span><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DRAFT'); ?></span>
                                    <?php }
                                    elseif($item -> state == 4){?>
                                        <span><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_UNDER_REVIEW'); ?></span>
                                    <?php } ?>
                                </td>
                                <td class="small nowrap hidden-phone">
                                    <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
                                </td>
                                <td class="center text-center hidden-phone">
                                    <?php echo (int) $item->hits; ?>
                                </td>
                                <td class="nowrap hidden-phone order" style="text-align: right;">
                                    <?php echo $item -> priority; ?>
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
</div>