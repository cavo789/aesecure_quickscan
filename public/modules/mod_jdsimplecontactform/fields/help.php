<?php
/**
 * @package   JD Simple Contact Form
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2021 Joomdev, Inc. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldHelp extends JFormField {
	
	protected $type = 'Help';

	// getLabel() left out

	public function getInput() {
		return '<a href="https://docs.joomdev.com/article/jd-simple-contact-form/" target="_blank" class="btn primary-btn"><span class="icon-question-sign" aria-hidden="true"></span> Help</a>';
	}
}