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

jimport('joomla.application.component.view');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 */
class TZ_Portfolio_PlusViewFeatured extends JViewLegacy
{
    protected $state;
	protected $items;
	protected $f_levels;
	protected $listGroup;
	protected $pagination;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items		    = $this->get('Items');
		$this->pagination	    = $this->get('Pagination');
		$this->state		    = $this->get('State');
        $this->authors		    = $this->get('Authors');
        $this -> filterForm     = $this -> get('FilterForm');
        $this -> activeFilters  = $this -> get('ActiveFilters');

        $model  = JModelLegacy::getInstance('Categories','TZ_Portfolio_PlusModel');
        $model -> setState('filter.group',$this -> state -> get('filter.group'));
        $this -> listGroup  = $model -> getGroups();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

        // Levels filter.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('J1'));
		$options[]	= JHtml::_('select.option', '2', JText::_('J2'));
		$options[]	= JHtml::_('select.option', '3', JText::_('J3'));
		$options[]	= JHtml::_('select.option', '4', JText::_('J4'));
		$options[]	= JHtml::_('select.option', '5', JText::_('J5'));
		$options[]	= JHtml::_('select.option', '6', JText::_('J6'));
		$options[]	= JHtml::_('select.option', '7', JText::_('J7'));
		$options[]	= JHtml::_('select.option', '8', JText::_('J8'));
		$options[]	= JHtml::_('select.option', '9', JText::_('J9'));
		$options[]	= JHtml::_('select.option', '10', JText::_('J10'));

        $this -> f_levels    = $options;

        // We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

        if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
            TZ_Portfolio_PlusHelper::addSubmenu('featured');
            $this->sidebar = JHtmlSidebar::render();
        }
        
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
        Factory::getApplication() -> getLanguage() -> load('com_content');
		$state	= $this->get('State');
        $canDo	= TZ_Portfolio_PlusHelper::getActions($this->state->get('filter.category_id'));

        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title(JText::_('COM_CONTENT_FEATURED_TITLE'), 'featured.png');

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('article.add');
		}
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('article.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::publish('articles.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('articles.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::archiveList('articles.archive');
			JToolBarHelper::checkin('articles.checkin');
			JToolBarHelper::custom('featured.delete', 'remove.png', 'remove_f2.png', 'JTOOLBAR_REMOVE', true);
		}

		if ($state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'articles.delete', 'JTOOLBAR_EMPTY_TRASH');
		} elseif ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('articles.trash');
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_tz_portfolio_plus');
		}

		JToolBarHelper::help('JHELP_CONTENT_FEATURED_ARTICLES');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');
	}
}
