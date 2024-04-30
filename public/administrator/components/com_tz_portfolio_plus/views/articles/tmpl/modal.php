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

$app    = Factory::getApplication();

if ($app->isClient('site')) {
    JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
}

require_once JPATH_ROOT . '/components/com_tz_portfolio_plus/helpers/route.php';

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$j4Compare  = COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE;

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

if(!$j4Compare) {
    JHtml::_('bootstrap.tooltip');
    JHtml::_('behavior.multiselect');
}else{
    JHtml::_('formbehavior.chosen', 'select[multiple]');
}

$function	= $app->input->getCmd('function', 'tppSelectArticle');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$isMultiple = $app -> input -> get('ismultiple', false, 'boolean');

$doc    = Factory::getApplication() -> getDocument();
$doc -> addScriptDeclaration('
function tzGetDatas(){
    if (window.parent){
        var j= 0,titles  = new Array(),ids = new Array(),categories = new Array();
        if(document.getElementsByName("cid[]").length){
            var idElems  = document.getElementsByName("cid[]"),
                titleElems  = document.getElementsByName("tztitles[]"),
                categoryElems  = document.getElementsByName("tzcategories[]");
            for(var i = 0; i<idElems.length; i++){
                if(idElems[i].checked){
                    ids[j]  = idElems[i].value;
                    titles[j]  = titleElems[i].value;
                    categories[j]  = categoryElems[i].value;
                    j++;
                }
            }
        }
        window.parent.'.$this->escape($function).'(ids,titles,categories);
    }
}');
?>

<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=articles&layout=modal&tmpl=component&function='
    .$function.($isMultiple?'&ismultiple=true':'').'&'.JSession::getFormToken().'=1');?>"
      method="post" name="adminForm" id="adminForm" class="tpContainer">
    <?php if($isMultiple){?>
    <div class="btn-toolbar">
        <button type="button" class="btn btn-primary" onclick="tzGetDatas();">
            <i class="icon-checkmark"></i> <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_INSERT');?></button>
        <hr class="hr-condensed" />
    </div>
    <?php } ?>

    <?php
    // Search tools bar
    echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>

    <?php if (empty($this->items)){ ?>
        <div class="alert alert-no-items">
            <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php }else{ ?>

    <table class="table table-striped table-condensed">
        <thead>
        <tr>
            <?php if($isMultiple){?>
            <th width="1%">
                <?php echo JHtml::_('grid.checkall'); ?>
            </th>
            <?php } ?>
            <th class="title">
                <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
            </th>
            <th width="6%" class="nowrap">
                <?php echo JHtml::_('searchtools.sort', 'COM_TZ_PORTFOLIO_PLUS_TYPE_OF_MEDIA', 'groupname', $listDirn, $listOrder); ?>
            </th>
            <th width="15%">
                <?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
            </th>
            <th width="15%">
                <?php echo JHtml::_('searchtools.sort', 'JCATEGORY', 'a.catid', $listDirn, $listOrder); ?>
            </th>
            <th width="5%">
                <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
            </th>
            <th width="5%">
                <?php echo JHtml::_('searchtools.sort',  'JDATE', 'a.created', $listDirn, $listOrder); ?>
            </th>
            <th width="1%" class="nowrap">
                <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
            </th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="8">
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
        </tfoot>
        <tbody>
        <?php foreach ($this->items as $i => $item) :?>
            <tr class="row<?php echo $i % 2; ?>">
                <?php if($isMultiple){?>
                <td class="center text-center">
                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <?php } ?>
                <td>
                    <a style="cursor: pointer;" class="pointer"
                       onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>(['<?php echo $item->id; ?>'], ['<?php echo $this->escape(addslashes($item->title)); ?>'],['<?php echo $this->escape(addslashes($item->category_title)); ?>']);">
                        <?php echo $this->escape($item->title); ?></a>
                    <input type="hidden" name="tztitles[]" value="<?php echo $this->escape(addslashes($item->title));?>">
                </td>
                <td class="small hidden-phone">
                    <?php echo $item -> type;?>
                </td>
                <td class="center text-center small">
                    <?php echo $this->escape($item->access_level); ?>
                </td>
                <td class="small">
                    <?php echo $this->escape($item->category_title); ?>
                    <?php if(isset($item -> categories) && $item -> categories && count($item -> categories)):?>
                        <?php
                        echo ',';
                        foreach($item -> categories as $i => $category):
                            ?>
                            <?php echo $this->escape($category->title); ?>
                            <?php
                            if($i < count($item -> categories) - 1){
                                echo ',';
                            }
                            ?>
                        <?php endforeach;?>
                    <?php endif;?>
                    <input type="hidden" name="tzcategories[]" value="<?php echo $this->escape(addslashes($item->category_title));?>">
                </td>
                <td class="center text-center small">
                    <?php if ($item->language=='*'):?>
                        <?php echo JText::alt('JALL', 'language'); ?>
                    <?php else:?>
                        <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
                    <?php endif;?>
                </td>
                <td class="center text-center small nowrap">
                    <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
                </td>
                <td class="center text-center small">
                    <?php echo (int) $item->id; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php } ?>

    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>