<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Copyright Copyright (C) 2015-2019 TZ Portfolio (http://tzportfolio.com). All Rights Reserved.

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$app        = Factory::getApplication();
$user		= Factory::getUser();
$userId     = $user -> id;
$vName      = $this -> getName();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$isMultiple = $app -> input -> get('ismultiple');
$function   = $app->input->getCmd('function', 'tpAddonSelect');
$onclick    = $this->escape($function);

$this -> document -> addScriptDeclaration('
function tpAddonGetDatas(){
    if (window.parent){
        var j= 0,titles  = new Array(),ids = new Array(),categories = new Array();
        if(document.getElementsByName(\'cid[]\').length){
            var idElems  = document.getElementsByName(\'cid[]\'),
                titleElems  = document.getElementsByName(\'tpaddon[]\');
            for(var i = 0; i<idElems.length; i++){
                if(idElems[i].checked){
                    ids[j]  = idElems[i].value;
                    titles[j]  = titleElems[i].value;
                    j++;
                }
            }
            if(!ids.length){
                alert("'.JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST').'");
                return false;
            }
            window.parent.'.$this->escape($function).'(ids,titles);
        }
    }
}
');
?>
<form name="adminForm" id="adminForm" method="post" action="<?php
    echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=addons&layout=modal&tmpl=component&function='
        .$function.($isMultiple?'&ismultiple=true':'').'&'.JSession::getFormToken().'=1'); ?>">

    <?php echo JHtml::_('tzbootstrap.addrow');?>
    <?php if(!empty($this -> sidebar)){?>
    <div id="j-sidebar-container" class="span2 col-md-2">
        <?php echo $this -> sidebar; ?>
    </div>
    <?php } ?>

    <?php echo JHtml::_('tzbootstrap.startcontainer', '10', !empty($this -> sidebar));?>

    <div class="tpContainer">
        <a class="btn btn-success pull-left" style="margin-right: 5px;" href="javascript:void(0)" onclick="tpAddonGetDatas();">
            <span class="tps tp-check mr-1" aria-hidden="true"></span>
            <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_INSERT'); ?></a>
        <?php
        // Search tools bar
        echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
        ?>

        <?php if (empty($this->items)){ ?>
        <div class="alert alert-warning alert-no-items">
            <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
        <?php }else{ ?>
                <table class="table table-striped" id="tp-addon__list">
                    <thead>
                        <tr>
                            <?php if($isMultiple){ ?>
                                <th width="1%">
                                    <?php echo JHtml::_('grid.checkall'); ?>
                                </th>
                            <?php } ?>
                            <th width="7%" class="text-nowrap">
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
                            <th width="1%" class="text-nowrap"><?php
                                echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder);
                            ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $iconStates = array(
                        -2 => 'icon-trash',
                        0  => 'icon-unpublish',
                        1  => 'icon-publish',
                    );
                    foreach ($this -> items as $i => $item){
                        $canCheckin = $user->authorise('core.admin',      'com_checkin')
                            || $item->checked_out == $userId || $item->checked_out == 0;
                        $canChange  = $user->authorise('core.edit.state', 'com_tz_crm' . $item->id) && $canCheckin;
                        $canCheckin = $user->authorise('core.manage',     'com_checkin') ||
                            $item->checked_out == $userId || $item->checked_out == 0;
                    ?>
                        <tr>
                            <?php if($isMultiple){ ?>
                                <td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                            <?php } ?>
                            <td class="center text-center">
                                <span class="<?php echo $iconStates[$this->escape($item->published)]; ?>" aria-hidden="true"></span>
                            </td>
                            <td>
                                <a class="js-select-link" href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php
                                echo $function; ?>([<?php echo $item -> id; ?>], ['<?php echo $item -> name; ?>']);"><?php
                                    echo $item -> name; ?></a>
                                <input type="hidden" name="tpaddon[]" value="<?php echo $this->escape(addslashes($item->name));?>">
                            </td>
                            <td class="center text-center">
                                <?php echo $item -> folder;?>
                            </td>
                            <td class="center text-center">
                                <?php echo $item -> element;?>
                            </td>
                            <td><?php echo $item -> id; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
        <?php } ?>

        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <?php echo JHtml::_('form.token');?>
    <?php echo JHtml::_('tzbootstrap.endcontainer');?>
<?php echo JHtml::_('tzbootstrap.endrow');?>
</form>