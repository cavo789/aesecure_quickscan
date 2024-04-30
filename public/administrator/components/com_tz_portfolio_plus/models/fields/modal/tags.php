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

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

/**
 * Supports a modal article picker.
 */
class JFormFieldModal_Tags extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Modal_Tags';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
		$script[] = '	function jSelectTag_'.$this->id.'(id, title, catid, object) {';
		$script[] = '		document.id("'.$this->id.'_id").value = id;';
		$script[] = '		document.id("'.$this->id.'_name").value = title;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		Factory::getApplication() -> getDocument()->addScriptDeclaration(implode("\n", $script));


		// Setup variables for display.
		$html	= array();
		$link	= 'index.php?option=com_tz_portfolio_plus&amp;view=tags&amp;layout=modal&amp;tmpl=component&amp;function=jSelectTag_'.$this->id;

		try{
            $db	= TZ_Portfolio_PlusDatabase::getDbo();
            $db->setQuery(
                'SELECT title' .
                ' FROM #__tz_portfolio_plus_tags' .
                ' WHERE id = '.(int) $this->value
            );
            $title = $db->loadResult();

		}catch (\InvalidArgumentException $e)
        {
            Factory::getApplication()  -> enqueueMessage($e->getMessage(), 'error');
            return false;
        }

		if (empty($title)) {
			$title = JText::_('COM_TZ_PORTFOLIO_PLUS_SELECT_AN_TAG');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		$html[] = '<div class="input-append input-group">';

		$html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';

        $title      = JText::_('COM_TZ_PORTFOLIO_PLUS_SELECT_TAGS_BUTTON_DESC');
        $textLink   = '<i class="icon-file"></i>&nbsp;'.JText::_('COM_TZ_PORTFOLIO_PLUS_SELECT_TAGS_BUTTON');
        $class      = 'modal btn';
        
		// The user select button.
		$html[] = '	<a class="'.$class.'" title="'.$title.'"'
            .' href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'
            .$textLink.'</a>';
		$html[] = '</div>';

		// The active article id field.
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return implode("\n", $html);
	}
}
