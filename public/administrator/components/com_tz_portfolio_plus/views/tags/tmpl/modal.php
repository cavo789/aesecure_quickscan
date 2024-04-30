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

if (Factory::getApplication()-> isClient('site')) {
	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
}

require_once JPATH_ROOT . '/components/com_tz_portfolio_plus/helpers/route.php';

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework');
JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$function	= Factory::getApplication() -> input -> getCmd('function', 'jSelectTag');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=tags&layout=modal&tmpl=component&function='.$function.'&'.JSession::getFormToken().'=1');?>"
      method="post" name="adminForm" id="adminForm" class="form-inline tpContainer">

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
                <th width="10">#</th>
                <th class="title">
                    <?php echo JHtml::_('searchtools.sort','JGLOBAL_TITLE','name', $listDirn, $listOrder);?>
                </th>
                <th nowrap="nowrap" width="1%">
                    <?php echo JHtml::_('searchtools.sort','JGRID_HEADING_ID','id', $listDirn, $listOrder);?>
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
		<?php if($this -> items):?>
        <tbody>
            <?php $i=0;?>
            <?php foreach($this -> items as $item):?>
                <tr class="row<?php echo $i%2;?>">
                    <td><?php echo $i+1;?></td>
                    <td>
                        <a style="cursor: pointer;" class="pointer hasTooltip"
                           data-placement="bottom"
                           data-original-title="<?php echo $item -> title;?>"
                           onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '', null, 'index.php?option=com_tz_portfolio_plus&view=tags&id=<?php echo $item -> id;?>');"
                        >
                            <?php echo $item -> title;?>
                        </a>
                            <span class="small">
                                <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                            </span>
                    </td>
                    <td align="center text-center"><?php echo $item -> id;?></td>
                </tr>
            <?php $i++;?>
            <?php endforeach;?>
        </tbody>
        <?php endif;?>
	</table>
    <?php }?>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
</form>