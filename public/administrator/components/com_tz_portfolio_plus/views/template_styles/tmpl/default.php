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

if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
    JHtml::_('formbehavior.chosen', 'select[multiple]');
}else {
    JHtml::_('formbehavior.chosen', 'select');
}

$user		= Factory::getApplication() -> getIdentity();

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="index.php?option=com_tz_portfolio_plus&view=template_styles" method="post" name="adminForm" id="adminForm">

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

            <table class="table table-striped" id="templatesList">
                <thead>
                <tr>
                    <th width="1%"></th>
                    <th class="title">
                        <?php echo JHtml::_('searchtools.sort','COM_TEMPLATES_HEADING_STYLE','name',$listDirn,$listOrder);?>
                    </th>
                    <th width="1%" style="min-width:55px" class="nowrap center text-center">
                        <?php echo JHtml::_('searchtools.sort', 'COM_TEMPLATES_HEADING_DEFAULT', 'home', $listDirn, $listOrder); ?>
                    </th>
                    <th nowrap="nowrap" width="1%">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_HEADING_ASSIGNED'); ?>
                    </th>
                    <th nowrap="nowrap" class="center text-center" width="15%">
                        <?php echo JHtml::_('searchtools.sort', 'COM_TZ_PORTFOLIO_PLUS_TEMPLATE', 'template', $listDirn, $listOrder); ?>
                    </th>
                    <th nowrap="nowrap" width="1%">
                        <?php echo JHtml::_('searchtools.sort','JGRID_HEADING_ID','id',$listDirn,$listOrder);?>
                    </th>
                </tr>
                </thead>

                <?php if($this -> items):?>
                    <tbody>
                    <?php foreach($this -> items as $i => $item):

                        $canCreate = $user->authorise('core.create',     'com_tz_portfolio_plus.style');
                        $canEdit   = $user->authorise('core.edit',       'com_tz_portfolio_plus.style');
                        $canChange = $user->authorise('core.edit.state', 'com_tz_portfolio_plus.style');

                    ?>
                        <tr class="<?php echo ($i%2==0)?'row0':'row1';?>">
                            <td class="center text-center">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="nowrap has-context">
                                <div class="pull-left float-left">
                                    <?php if($canEdit){ ?>
                                    <a href="index.php?option=com_tz_portfolio_plus&task=template_style.edit&id=<?php echo $item -> id;?>">
                                        <?php echo $this -> escape($item -> title);?>
                                    </a>
                                    <?php }else{ ?>
                                        <?php echo $this -> escape($item -> title);?>
                                    <?php } ?>
                                </div>
                            </td>

                            <td class="center text-center">
                                <?php if ($item->home == '0' || $item->home == '1'):?>
                                    <?php echo JHtml::_('jgrid.isdefault', $item->home != '0', $i, 'template_styles.', $canChange && $item->home != '1');?>
                                <?php elseif ($canChange):?>
                                    <a href="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&task=template_styles.unsetDefault&cid[]='.$item->id.'&'.JSession::getFormToken().'=1');?>">
                                        <?php echo JHtml::_('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title' => JText::sprintf('COM_TEMPLATES_GRID_UNSET_LANGUAGE', $item->language_title)), true);?>
                                    </a>
                                <?php else:?>
                                    <?php echo JHtml::_('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title' => $item->language_title), true);?>
                                <?php endif;?>
                            </td>

                            <td class="center text-center">
                                <?php if((isset($item -> category_assigned) AND $item -> category_assigned > 0)
                                    OR (isset($item -> content_assigned) AND $item -> content_assigned > 0)
                                    OR (isset($item -> menu_assigned) AND $item -> menu_assigned > 0)):?>
                                <i class="icon-ok tip hasTooltip" title="<?php echo JText::plural('COM_TZ_PORTFOLIO_PLUS_ASSIGNED_MORE',$item -> menu_assigned, $item->category_assigned,$item -> content_assigned); ?>"></i>
                                <?php endif;?>
                            </td>
                            <td class="center text-center"><?php echo $item -> template;?></td>
                            <td class="center text-center"><?php echo $item -> id;?></td>
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
        </div>
        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <?php echo JHtml::_('form.token');?>
    <?php echo JHtml::_('tzbootstrap.endcontainer');?>
<?php echo JHtml::_('tzbootstrap.endrow');?>
</form>