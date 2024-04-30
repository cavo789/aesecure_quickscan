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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
jimport( 'joomla.application.component.view' );

class PhocaMapsCpViewPhocaMapsMaps extends HtmlView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $t;
	protected $r;
	public $filterForm;
    public $activeFilters;

	function display($tpl = null) {

		$this->t			= PhocaMapsUtils::setVars('map');
		$this->r 			= new PhocaMapsRenderAdminViews();

		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

		$paramsC 					= ComponentHelper::getParams('com_phocamaps');
		$this->t['maps_api_key']	= $paramsC->get( 'maps_api_key', '' );
		$this->t['map_type']		= $paramsC->get( 'map_type', 2 );
		//$this->t['load_api_ssl'] 	= $paramsC->get( 'load_api_ssl', 1 );

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) {
			$this->ordering[0][] = $item->id;
		}


		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocamapsmaps.php';

		$state	= $this->get('State');
		$canDo	= PhocaMapsMapsHelper::getActions($this->t, $state->get('filter.map_id'));
		$user  	= Factory::getUser();
		$bar 	= Toolbar::getInstance('toolbar');

		ToolbarHelper::title( Text::_( 'COM_PHOCAMAPS_MAPS' ), 'ph-earth' );

		if ($canDo->get('core.create')) {
			ToolbarHelper::addNew('phocamapsmap.add','JTOOLBAR_NEW');
		}

		if ($canDo->get('core.edit')) {
			ToolbarHelper::editList('phocamapsmap.edit','JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.edit.state')) {

			ToolbarHelper::divider();
			ToolbarHelper::custom('phocamapsmaps.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			ToolbarHelper::custom('phocamapsmaps.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		}

		if ($canDo->get('core.delete')) {
			ToolbarHelper::deleteList( 'COM_PHOCAMAPS_WARNING_DELETE_ITEMS' , 'phocamapsmaps.delete', 'COM_PHOCAMAPS_DELETE');
		}

		// Add a batch button
		if ($user->authorise('core.edit'))
		{
			/*HTMLHelper::_('bootstrap.renderModal', 'collapseModal');
			$title = Text::_('JTOOLBAR_BATCH');
			$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
						<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
						$title</button>";
			$bar->appendButton('Custom', $dhtml, 'batch');
			*/
			$bar->popupButton('batch')
				->text('JTOOLBAR_BATCH')
				->selector('collapseModal')
				->listCheck(true);
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocamaps', true );
	}

	protected function getSortFields() {
		return array(
			'a.ordering'	=> Text::_('JGRID_HEADING_ORDERING'),
			'a.title' 		=> Text::_($this->t['l'] . '_TITLE'),
			'a.published' 	=> Text::_($this->t['l'] . '_PUBLISHED'),
			'language' 		=> Text::_('JGRID_HEADING_LANGUAGE'),
			'a.id' 			=> Text::_('JGRID_HEADING_ID')
		);
	}
}
?>
