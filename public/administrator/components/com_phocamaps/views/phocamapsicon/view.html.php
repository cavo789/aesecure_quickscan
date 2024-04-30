<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
jimport( 'joomla.application.component.view' );

class phocaMapsCpViewPhocaMapsIcon extends HtmlView
{
	protected $state;
	protected $item;
	protected $form;
	protected $t;
	protected $r;

	public function display($tpl = null) {

		$this->t		= PhocaMapsUtils::setVars('icon');
		$this->r		= new PhocaMapsRenderAdminview();
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');


		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocamapsicons.php';
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user		= Factory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocamapsIconsHelper::getActions($this->t, $this->state->get('filter.icon_id'));

		$text = $isNew ? Text::_( 'COM_PHOCAMAPS_NEW' ) : Text::_('COM_PHOCAMAPS_EDIT');
		ToolbarHelper::title(   Text::_( 'COM_PHOCAMAPS_ICON_EXT' ).': <small><small>[ ' . $text.' ]</small></small>' , 'pin');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			ToolbarHelper::apply('phocamapsicon.apply', 'JTOOLBAR_APPLY');
			ToolbarHelper::save('phocamapsicon.save', 'JTOOLBAR_SAVE');
			ToolbarHelper::addNew('phocamapsicon.save2new', 'JTOOLBAR_SAVE_AND_NEW');
		}

		if (empty($this->item->id))  {
			ToolbarHelper::cancel('phocamapsicon.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			ToolbarHelper::cancel('phocamapsicon.cancel', 'JTOOLBAR_CLOSE');
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocamaps', true );
	}
}
?>
