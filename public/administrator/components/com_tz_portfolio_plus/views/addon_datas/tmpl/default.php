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

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

JHtml::_('formbehavior.chosen', 'select');

Factory::getApplication() -> getDocument()->addScriptDeclaration('
	if(typeof Joomla !== "undefined"){
		Joomla.submitform = function(task, form, validate) {
			if (!form) {
				form = document.getElementById("adminForm");
			}

			if (task) {
				form.addon_task.value = task;
			}

			// Toggle HTML5 validation
			form.noValidate = !validate;

			// Submit the form.
			// Create the input type="submit"
			var button = document.createElement("input");
			button.style.display = "none";
			button.type = "submit";

			// Append it and click it
			form.appendChild(button).click();

			// If "submit" was prevented, make sure we don\'t get a build up of buttons
			form.removeChild(button);
		};
	}
');
?>
<?php
if($this -> addonItem && isset($this -> addonItem -> manager) && $this -> addonItem -> manager):
    ?>
    <?php echo $this -> addonItem -> manager;?>
<?php endif;?>