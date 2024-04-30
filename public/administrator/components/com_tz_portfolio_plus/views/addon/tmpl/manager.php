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
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

JHtml::_('behavior.formvalidator');
Factory::getApplication() -> getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "addon.cancel" || document.formvalidator.isValid(document.getElementById("adminForm"))) {
		    var form = document.getElementById("adminForm");
		    console.log(document.getElementsByName("task")[0]);
		    document.getElementsByName("addon_task")[0].value = task;

		    task    = "addon.manager";
			Joomla.submitform(task, document.getElementById("adminForm"));
		}
	};
');
?>
<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=addon_datas&addon_id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate">
    <?php
    if($this -> addonItem && isset($this -> addonItem -> manager) && $this -> addonItem -> manager):
    ?>
        <?php echo $this -> addonItem -> manager;?>
    <?php endif;?>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="addon_task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>