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

class phocaMapsCpViewPhocaMapsMap extends HtmlView
{
	protected $state;
	protected $item;
	protected $form;
	protected $t;
	protected $r;

	public function display($tpl = null) {

		$this->t		= PhocaMapsUtils::setVars('map');
		$this->r		= new PhocaMapsRenderAdminview();
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocamapsmaps.php';
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user		= Factory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		//$canDo		= PhocamapsMapsHelper::getActions($this->state->get('filter.map_id'), $this->item->id);
		$canDo 		= PhocamapsMapsHelper::getActions($this->t, $this->state->get('filter.map_id'));
		//$paramsC 	= JComponentHelper::getParams('COM_PHOCAMAPS');



		$text = $isNew ? Text::_( 'COM_PHOCAMAPS_NEW' ) : Text::_('COM_PHOCAMAPS_EDIT');
		ToolbarHelper::title(   Text::_( 'COM_PHOCAMAPS_MAP' ).': <small><small>[ ' . $text.' ]</small></small>' , 'ph-earth');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			ToolbarHelper::apply('phocamapsmap.apply', 'JTOOLBAR_APPLY');
			ToolbarHelper::save('phocamapsmap.save', 'JTOOLBAR_SAVE');
			ToolbarHelper::addNew('phocamapsmap.save2new', 'JTOOLBAR_SAVE_AND_NEW');
		}

		if (empty($this->item->id))  {
			ToolbarHelper::cancel('phocamapsmap.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			ToolbarHelper::cancel('phocamapsmap.cancel', 'JTOOLBAR_CLOSE');
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocamaps', true );
	}
}
?>
