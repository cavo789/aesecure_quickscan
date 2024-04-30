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

if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
    JHtml::_('formbehavior.chosen', 'select');
}
else{
    JHtml::_('formbehavior.chosen', 'select[multiple]');
}

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$user		= Factory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_tz_portfolio_plus.addons');
$saveOrder	= $listOrder == 'ordering';

$j4Compare  = COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE;
if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_tz_portfolio_plus&task=addons.saveOrderAjax&tmpl=component' . JSession::getFormToken() . '=1';

    if($j4Compare){
        JHtml::_('draggablelist.draggable');
    }else {
        JHtml::_('sortablelist.sortable', 'addonList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
    }
}

$this->document->addStyleSheet(TZ_Portfolio_PlusUri::base() . '/vendor/intro/introjs.min.css', array('version' => 'v=2.9.3'));
$this->document->addScript(TZ_Portfolio_PlusUri::base() . '/vendor/intro/intro.min.js', array('version' => 'v=2.9.3'));
$this->document->addScript(TZ_Portfolio_PlusUri::base() . '/js/introguide.min.js', array('version' => 'v=2.9.3'));

if(Factory::getApplication() -> getLanguage() -> isRtl()) {
    $this->document->addStyleSheet(TZ_Portfolio_PlusUri::base() . '/vendor/intro/introjs-rtl.min.css', array('version' => 'v=2.9.3'));
}

$this -> document -> addScriptDeclaration('
(function($){
    "use strict";
    
    $(document).ready(function(){
        var addonSteps  = [
        {
            /* Step 1: Install */
            element: $("#toolbar-new > button")[0],
            intro: "<div class=\\"head\\">'.$this -> escape(JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALL_UPDATE')
                .' '.JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON')).'</div>'
                .$this -> escape(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INTRO_GUIDE_INSTALL_MANUAL_ONLINE_DESC',JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON') )).'",
            position: "right"
        },
        {
            /* Step 2: Config options of addon */
            element: $("#addonList .js-tpp-title")[0],
            intro: "<div class=\\"head\\">'.$this -> escape(JText::_('COM_TZ_PORTFOLIO_PLUS_INTRO_GUIDE_CONFIG_ADDON'))
                .'</div>'.$this -> escape(JText::_('COM_TZ_PORTFOLIO_PLUS_INTRO_GUIDE_CONFIG_ADDON_DESC')).'",
            position: "top"
        }];
        if($("#addonList .js-tpp-data-manage").length){
            addonSteps[2]   = {
                /* Step 2: Config options of addon */
                element: $("#addonList .js-tpp-data-manage")[0],
                intro: "<div class=\\"head\\">'.$this -> escape('COM_TZ_PORTFOLIO_PLUS_DATA_MANAGEMENT').'</div>'.
                    $this -> escape(JText::_('COM_TZ_PORTFOLIO_PLUS_INTRO_GUIDE_DATA_MANAGEMENT_DESC')).'",
                position: "right"
            }
        }
        
        tppIntroGuide("'.$this -> getName().'",addonSteps , '.(TZ_Portfolio_PlusHelper::introGuideSkipped($this -> getName())?1:0).', "'.JSession::getFormToken().'");
    });
})(jQuery);
');
?>
<form action="index.php?option=com_tz_portfolio_plus&view=addons" method="post" name="adminForm"
      class="tz_portfolio_plus-addons"
      id="adminForm">
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
        <table class="table table-striped"  id="addonList">
            <thead>
            <tr>
                <th width="1%" class="nowrap center text-center">
                    <?php echo JHtml::_('searchtools.sort', '', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                </th>
                <th width="1%" class="hidden-phone">
                    <?php echo JHtml::_('grid.checkall'); ?>
                </th>
                <th width="1%" class="nowrap center text-center">
                    <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
                </th>
                <th class="title">
                    <?php echo JHtml::_('searchtools.sort','JGLOBAL_TITLE','name',$listDirn,$listOrder);?>
                </th>
                <th width="7%" class="nowrap center text-center">
                    <?php echo JHtml::_('searchtools.sort', 'COM_TZ_PORTFOLIO_PLUS_TYPE', 'folder', $listDirn, $listOrder); ?>
                </th>
                <th width="10%" class="nowrap center text-center">
                    <?php echo JHtml::_('searchtools.sort', 'COM_TZ_PORTFOLIO_PLUS_ELEMENT', 'element', $listDirn, $listOrder); ?>
                </th>
                <th width="5%" class="nowrap hidden-phone">
                    <?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'f.access', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap center text-center" width="6%">
                    <?php echo JText::_('JVERSION'); ?>
                </th>
                <th class="nowrap center text-center" width="10%">
                    <?php echo JText::_('JDATE'); ?>
                </th>
                <th class="nowrap" width="10%">
                    <?php echo JText::_('JAUTHOR'); ?>
                </th>
                <th class="nowrap" width="1%">
                    <?php echo JHtml::_('searchtools.sort','JGRID_HEADING_ID','id',$listDirn,$listOrder);?>
                </th>
            </tr>
            </thead>

            <?php if($this -> items):?>
            <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl;
            ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
                <?php foreach($this -> items as $i => $item):

                    $canCreate = $user->authorise('core.create',     'com_tz_portfolio_plus.addon');
                    $canEdit   = ($user->authorise('core.edit', 'com_tz_portfolio_plus.addon.'.$item -> id)
                        || $user->authorise('core.admin', 'com_tz_portfolio_plus.addon.'.$item -> id)
                            || $user->authorise('core.options', 'com_tz_portfolio_plus.addon.'.$item -> id));
                    $canCheckin = $user->authorise('core.manage',     'com_checkin')
                        || $item->checked_out == $user->get('id') || $item->checked_out == 0;
                    $canChange = $user->authorise('core.edit.state', 'com_tz_portfolio_plus.addon') && $canCheckin;

                    ?>
                    <tr class="<?php echo ($i%2==0)?'row0':'row1';?>" sortable-group-id="<?php echo $item->folder
                    ?>" data-draggable-group="<?php echo $item->folder?>">
                        <td class="order nowrap center text-center hidden-phone">
                            <?php
                            $iconClass = '';
                            if (!$canChange)
                            {
                                $iconClass = ' inactive';
                            }
                            elseif (!$saveOrder)
                            {
                                $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                            }
                            ?>
                            <span class="sortable-handler<?php echo $iconClass ?>">
                            <span class="icon-menu"></span>
                        </span>
                            <?php if ($canChange && $saveOrder) : ?>
                                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
                            <?php endif; ?>
                        </td>
                        <td class="center text-center">
                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="center text-center">
                            <?php
                            $states	= array(
                                2 => array(
                                    '',
                                    'COM_TZ_PORTFOLIO_PLUS_ADDON_PROTECTED',
                                    '',
                                    'COM_TZ_PORTFOLIO_PLUS_ADDON_PROTECTED',
                                    true,
                                    'protected',
                                    'protected',
                                ),
                                1 => array(
                                    'unpublish',
                                    'COM_TZ_PORTFOLIO_PLUS_ADDON_ENABLED',
                                    'COM_TZ_PORTFOLIO_PLUS_ADDON_DISABLE',
                                    'COM_TZ_PORTFOLIO_PLUS_ADDON_ENABLED',
                                    true,
                                    'publish',
                                    'publish',
                                ),
                                0 => array(
                                    'publish',
                                    'COM_TZ_PORTFOLIO_PLUS_ADDON_DISABLED',
                                    'COM_TZ_PORTFOLIO_PLUS_ADDON_ENABLE',
                                    'COM_TZ_PORTFOLIO_PLUS_ADDON_DISABLED',
                                    true,
                                    'unpublish',
                                    'unpublish',
                                ),
                            );

                            if($item ->protected) {
                                echo JHtml::_('jgrid.state', $states, 2, $i, 'addon.', false, true, 'cb');
                            }else{
                                echo JHtml::_('jgrid.state', $states, $item->published, $i, 'addons.', $canChange, true, 'cb');
                            }
                            ?>
                        </td>
                        <td class="nowrap has-context">
                            <div class="pull-left float-left">
                                <?php if ($item->checked_out) : ?>
                                    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'addons.', $canCheckin); ?>
                                <?php endif; ?>
                                <?php if($canEdit){?>
                                <a href="index.php?option=com_tz_portfolio_plus&task=addon.edit&id=<?php
                                echo $item -> id;?>" class="js-tpp-title"><?php
                                    echo $item->name;
                                ?></a>
                                <?php }else{
                                    echo $item -> name;
                                } ?>

                                <?php
                                if($item -> data_manager){
                                ?>
                                    <a href="<?php echo JRoute::_(TZ_Portfolio_PlusHelperAddon_Datas::getRootURL($item -> id));?>"
                                       class="btn btn-secondary btn-small btn-sm hasTooltip js-tpp-data-manage"
                                       title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON_DATA_MANAGER')?>">
                                        <span class="icon-book me-1"></span><span><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON_DATA_MANAGER')?></span>
                                    </a>
                                <?php
                                }
                                ?>
                            </div>
                        </td>
                        <td class="center text-center">
                            <?php echo $item -> folder;?>
                        </td>
                        <td class="center text-center">
                            <?php echo $item -> element;?>
                        </td>
                        <td class="nowrap small hidden-phone">
                            <?php echo $this->escape($item->access_level); ?>
                        </td>
                        <td class="nowrap center text-center hidden-phone">
                            <?php echo @$item -> version != '' ? $item -> version : '&#160;';?>
                        </td>
                        <td class="nowrap center text-center hidden-phone">

                            <?php echo @$item-> creationDate != '' ? $item-> creationDate : '&#160;'; ?>
                        </td>
                        <td class="nowrap hidden-phone">
                            <span class="editlinktip hasTooltip" title="<?php echo JHtml::tooltipText(JText::_('COM_TZ_PORTFOLIO_PLUS_AUTHOR_INFORMATION'), $item -> author_info, 0); ?>">
                                <?php echo @$item->author != '' ? $item->author : '&#160;'; ?>
                            </span>
                        </td>

                        <td align="center text-center hidden-phone"><?php echo $item -> id;?></td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            <?php endif;?>

            <tfoot>
            <tr>
                <td colspan="11">
                    <?php echo $this -> pagination -> getListFooter();?>
                </td>
            </tr>
            </tfoot>

        </table>
        <?php } ?>

        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <?php echo JHtml::_('form.token');?>
    <?php echo JHtml::_('tzbootstrap.endcontainer');?>
<?php echo JHtml::_('tzbootstrap.endrow');?>
</form>