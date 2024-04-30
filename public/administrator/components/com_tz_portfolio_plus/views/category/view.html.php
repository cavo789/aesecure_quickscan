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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Categories component
 *
 */
class TZ_Portfolio_PlusViewCategory extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{

        Factory::getApplication() -> getLanguage()->load('com_categories');

		$this -> form	= $this->get('Form');
		$this -> item	= $this->get('Item');
		$this -> state	= $this->get('State');
        $this -> assoc  = $this->get('Assoc');

		$this -> canDo	= TZ_Portfolio_PlusHelper::getActions('com_tz_portfolio_plus'
            ,  'category', $this->item->id);

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

        // If we are forcing a language in modal (used for associations).
        if ($this->getLayout() === 'modal' && $forcedLanguage = Factory::getApplication()->input->get('forcedLanguage', '', 'cmd'))
        {
            // Set the language field to the forcedLanguage and disable changing it.
            $this->form->setValue('language', null, $forcedLanguage);
            $this->form->setFieldAttribute('language', 'readonly', 'true');

            // Only allow to select categories with All language or with the forced language.
            $this->form->setFieldAttribute('parent_id', 'language', '*,' . $forcedLanguage);

        }

		parent::display($tpl);
		Factory::getApplication()->input->set('hidemainmenu', true);
		$this->addToolbar();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		// Initialise variables.
		$extension	= Factory::getApplication() -> input -> getCmd('extension');
		$user		= TZ_Portfolio_PlusUser::getUser();
		$userId		= $user->id;

		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Avoid nonsense situation.
		if ($extension && $extension != 'com_tz_portfolio_plus') {
			return;
		}

 		// The extension can be in the form com_foo.section
		$parts = is_string($extension) ? explode('.', $extension) : [];
		$component = 'com_tz_portfolio_plus';
		$section = (count($parts) > 1) ? $parts[1] : null;

		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = Factory::getApplication() -> getLanguage();
			$lang->load($component, JPATH_BASE, null, false, false)
		||	$lang->load($component, JPATH_ADMINISTRATOR.'/components/'.$component, null, false, false)
		||	$lang->load($component, JPATH_BASE, $lang->getDefault(), false, false)
		||	$lang->load($component, JPATH_ADMINISTRATOR.'/components/'.$component, $lang->getDefault(), false, false);

		// Load the category helper.
		require_once JPATH_COMPONENT.'/helpers/categories.php';

		// Get the results for each action.
		$canDo = $this -> canDo;

		// If a component categories title string is present, let's use it.
		if ($lang->hasKey($component_title_key = $component.($section?"_$section":'').'_CATEGORY_'.($isNew?'ADD':'EDIT').'_TITLE')) {
			$title = JText::_($component_title_key);
		}
		// Else if the component section string exits, let's use it
		elseif ($lang->hasKey($component_section_key = $component.($section?"_$section":''))) {
			$title = JText::sprintf( 'COM_CATEGORIES_CATEGORY_'.($isNew?'ADD':'EDIT').'_TITLE', $this->escape(JText::_($component_section_key)));
		}
		// Else use the base title
		else {
			$title = JText::_('COM_CATEGORIES_CATEGORY_BASE_'.($isNew?'ADD':'EDIT').'_TITLE');
		}

		// Load specific css component
		JHtml::_('stylesheet', $component.'/administrator/categories.css', array(), true);

		// Prepare the toolbar.
		JToolBarHelper::title($title, 'folder category-'.($isNew?'add':'edit').' '
            .substr($component, 4).($section?"-$section":'').'-category-'.($isNew?'add':'edit'));

		// For new records, check the create permission.
		if ($isNew && (count($user->getAuthorisedCategories($component, 'core.create')) > 0)) {
			JToolBarHelper::apply('category.apply');
			JToolBarHelper::save('category.save');
			JToolBarHelper::save2new('category.save2new');
            JToolBarHelper::cancel('category.cancel');
		}
		// If not checked out, can save the item.
		else{
            $itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own')
                    && $this->item->created_user_id == $userId);

		    if (!$checkedOut && $itemEditable) {
                JToolBarHelper::apply('category.apply');
                JToolBarHelper::save('category.save');

                if ($canDo->get('core.create')) {
                    JToolBarHelper::save2new('category.save2new');
                }
            }

            // If an existing item, can save to a copy.
            if ($canDo->get('core.create')) {
                JToolBarHelper::save2copy('category.save2copy');
            }

            JToolBarHelper::cancel('category.cancel', 'JTOOLBAR_CLOSE');
		}

		// Compute the ref_key if it does exist in the component
		if (!$lang->hasKey($ref_key = strtoupper($component.($section?"_$section":'')).'_CATEGORY_'.($isNew?'ADD':'EDIT').'_HELP_KEY')) {
			$ref_key = 'JHELP_COMPONENTS_'.strtoupper(substr($component, 4).($section?"_$section":'')).'_CATEGORY_'.($isNew?'ADD':'EDIT');
		}

		// Get help for the category/section view for the component by
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
