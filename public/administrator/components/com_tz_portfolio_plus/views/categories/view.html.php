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

jimport('joomla.application.component.view');

/**
 * Categories view class for the Category package.
 */
class TZ_Portfolio_PlusViewCategories extends JViewLegacy
{
    protected $state;
	protected $items;
    protected $f_levels;
	protected $pagination;
	protected $listsGroup;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
        Factory::getApplication() -> getLanguage()->load('com_categories');

		$this -> state		    = $this->get('State');
		$this -> items		    = $this->get('Items');
		$this -> pagination	    = $this->get('Pagination');
        $this -> filterForm     = $this -> get('FilterForm');
        $this -> activeFilters  = $this -> get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) {
			$this->ordering[$item->parent_id][] = $item->id;
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

		$this -> f_levels   = $options;
        $this -> listsGroup = $this -> get('Groups');

        if($this->getLayout() !== 'modal') {
            $this->addToolbar();
            if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
                $this->sidebar = JHtmlSidebar::render();
            }
        }else{
            // In article associations modal we need to remove language filter if forcing a language.
            if ($forcedLanguage = Factory::getApplication()->input->get('forcedLanguage', '', 'CMD'))
            {
                // If the language is forced we can't allow to select the language, so transform the language selector filter into a hidden field.
                $languageXml = new SimpleXMLElement('<field name="language" type="hidden" default="' . $forcedLanguage . '" />');
                $this->filterForm->setField($languageXml, 'filter', true);

                // Also, unset the active language filter so the search tools is not open by default with this filter.
                unset($this->activeFilters['language']);
            }
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
		// Initialise variables.
        TZ_Portfolio_PlusHelper::addSubmenu('categories');

		$categoryId	= $this->state->get('filter.category_id');
		$component	= $this->state->get('filter.component');
		$section	= $this->state->get('filter.section');
		$canDo		= null;
		$user		= TZ_Portfolio_PlusUser::getUser();

        // Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = Factory::getApplication() -> getLanguage();

 		// Load the category helper.
		require_once JPATH_COMPONENT.'/helpers/categories.php';

		// Get the results for each action.
		$canDo = TZ_Portfolio_PlusHelper::getActions($component, 'category', $categoryId);

		// If a component categories title string is present, let's use it.
		if ($lang->hasKey($component_section_key = strtoupper($component.($section?"_$section":'')))) {
			$title = JText::sprintf( 'COM_TZ_PORTFOLIO_PLUS_CATEGORIES_TITLE', $this->escape(JText::_($component_section_key)));
		}
		// Else use the base title
		else {
			$title = JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_BASE_TITLE');
		}

		// Load specific css component
		JHtml::_('stylesheet', $component.'/administrator/categories.css', array(), true);

		// Prepare the toolbar.
		JToolBarHelper::title($title, 'folder categories '.substr($component, 4).($section?"-$section":'').'-categories');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories($component, 'core.create'))) > 0 ) {
			 JToolBarHelper::addNew('category.add');
		}

		if ($canDo->get('core.edit' ) || $canDo->get('core.edit.own')) {
			JToolBarHelper::editList('category.edit');
		}

		if ($canDo->get('core.edit.state') || $canDo -> get('core.edit.state.own')) {
			JToolBarHelper::publish('categories.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('categories.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::archiveList('categories.archive');
		}

		if (Factory::getUser()->authorise('core.admin')) {
			JToolBarHelper::checkin('categories.checkin');
		}

		if ($this->state->get('filter.published') == -2 && ($canDo->get('core.delete', $component)
                || $canDo -> get('core.delete.own'))) {
			JToolBarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'categories.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state') || $canDo -> get('core.edit.state.own')) {
			JToolBarHelper::trash('categories.trash');
		}
        // Add a batch button
		if ($canDo->get('core.edit'))
		{
            $title = JText::_('JTOOLBAR_BATCH');

            // Instantiate a new JLayoutFile instance and render the batch button
            $layout = new JLayoutFile('joomla.toolbar.batch');

            $dhtml = $layout->render(array('title' => $title));
            $bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($canDo->get('core.admin')) {
            JToolBarHelper::custom('categories.rebuild', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options')) {
			JToolBarHelper::preferences('com_tz_portfolio_plus');
		}

		// Compute the ref_key if it does exist in the component
		if (!$lang->hasKey($ref_key = strtoupper($component.($section?"_$section":'')).'_CATEGORIES_HELP_KEY')) {
			$ref_key = 'JHELP_COMPONENTS_'.strtoupper(substr($component, 4).($section?"_$section":'')).'_CATEGORIES';
		}

		// Get help for the categories view for the component by
		// -remotely searching in a language defined dedicated URL: *component*_HELP_URL
		// -locally  searching in a component help file if helpURL param exists in the component and is set to ''
		// -remotely searching in a component URL if helpURL param exists in the component and is NOT set to ''
		if ($lang->hasKey($lang_help_url = strtoupper($component).'_HELP_URL')) {
			$debug = $lang->setDebug(false);
			$url = JText::_($lang_help_url);
			$lang->setDebug($debug);
		}
		else {
			$url = null;
		}

		JToolBarHelper::help($ref_key, false,
            'https://www.tzportfolio.com/document/administration/48-how-to-create-a-category-in-tz-portfolio-plus.html?tmpl=component'
            , 'com_tz_portfolio_plus');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');
	}
}
