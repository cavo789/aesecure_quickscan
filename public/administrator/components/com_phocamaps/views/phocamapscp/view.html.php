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
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
jimport( 'joomla.application.component.view' );

class PhocaMapsCpViewPhocaMapsCp extends HtmlView
{
	protected $t;
	protected $r;
	protected $view;
	
	function display($tpl = null) {

		$this->t	= PhocaMapsUtils::setVars('cp');
		$this->r	= new PhocaMapsRenderAdminview();
		$i = ' icon-';
		$d = 'duotone ';

		$this->views= array(
		'maps'		=> array($this->t['l'] . '_MAPS', $i.'global', '#01868B'),
		'markers'		=> array($this->t['l'] . '_MARKERS', $d.$i.'location', '#D75348'),
		'icons'		=> array($this->t['l'] . '_ICONS', $d.$i.'flag', '#5CA4CD'),
		'info'		=> array($this->t['l'] . '_INFO', $d.$i.'info-circle', '#3378cc')
		);
		$this->t['version'] = PhocaMapsHelper::getPhocaVersion('com_phocamaps');

		$this->addToolbar();
		parent::display($tpl);
		
	}
	
	protected function addToolbar() {
		require_once JPATH_COMPONENT.'/helpers/phocamapscp.php';

		$state	= $this->get('State');
		$canDo	= PhocaMapsCpHelper::getActions();
		ToolbarHelper::title( Text::_( 'COM_PHOCAMAPS_PM_CONTROL_PANEL' ), 'home' );
		
		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = Toolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocamaps" class="btn btn-small"><i class="icon-home-2" title="'.Text::_('COM_PHOCAMAPS_CONTROL_PANEL').'"></i> '.Text::_('COM_PHOCAMAPS_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);
		
		if ($canDo->get('core.admin')) {
			ToolbarHelper::preferences('com_phocamaps');
			ToolbarHelper::divider();
		}
		
		ToolbarHelper::help( 'screen.phocamaps', true );
	}
}
?>