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
JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/helpers/html');

/**
 * View class for a list of articles.

 */
class TZ_Portfolio_PlusViewArticles extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{
		$this->items		    = $this->get('Items');
		$this->pagination	    = $this->get('Pagination');
		$this->state		    = $this->get('State');
		$this->authors		    = $this->get('Authors');
        $this -> filterForm     = $this -> get('FilterForm');
        $this -> activeFilters  = $this -> get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
		    $app    = Factory::getApplication();
		    $app -> enqueueMessage(implode("\n", $errors), 'error');
//		    var_dump($errors); die();
//			JError::raiseError(500, implode("\n", $errors));
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

		$this-> f_levels    = $options;

        TZ_Portfolio_PlusHelper::addSubmenu('articles');

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal')
        {
            $this->addToolbar();
            if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
                $this->sidebar = JHtmlSidebar::render();
            }
        }
        else
        {
            // In article associations modal we need to remove language filter if forcing a language.
            // We also need to change the category filter to show show categories with All or the forced language.
            if ($forcedLanguage = Factory::getApplication()->input->get('forcedLanguage', '', 'CMD'))
            {
                // If the language is forced we can't allow to select the language, so transform the language selector filter into a hidden field.
                $languageXml = new SimpleXMLElement('<field name="language" type="hidden" default="' . $forcedLanguage . '" />');
                $this->filterForm->setField($languageXml, 'filter', true);

                // Also, unset the active language filter so the search tools is not open by default with this filter.
                unset($this->activeFilters['language']);

                // One last changes needed is to change the category filter to just show categories with All language or with the forced language.
                $this->filterForm->setFieldAttribute('category_id', 'language', '*,' . $forcedLanguage, 'filter');
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
        $user           = TZ_Portfolio_PlusUser::getUser();
        $filterPublish  = $this -> state -> get('filter.published');
        $canDo	        = TZ_Portfolio_PlusHelper::getActions(COM_TZ_PORTFOLIO_PLUS, 'category', $this->state->get('filter.category_id'));

        $canEdit        = $canDo->get('core.edit');

		JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_ARTICLES_TITLE'), 'stack article');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_tz_portfolio_plus', 'core.create'))) > 0 ) {
			JToolBarHelper::addNew('article.add');
		}

//		if (($canDo->get('core.edit') || $canDo->get('core.edit.own'))
//            && ($filterPublish != 4 || $filterPublish == 4 && $user -> authorise('core.approve', 'com_tz_portfolio_plus'))) {

        $canEdit    = $user->authorise('core.edit','com_tz_portfolio_plus');
        $canApprove = $user->authorise('core.approve','com_tz_portfolio_plus');
        $canEditOwn = $user->authorise('core.edit.own','com_tz_portfolio_plus');

//        var_dump($canApprove); die();
        if(($canApprove && ($canEdit || $canEditOwn || $filterPublish == 3 || $filterPublish == 4)) ||
            (!$canApprove && ($filterPublish == 3 || $filterPublish == -3)
                && TZ_Portfolio_PlusContentHelper::getArticleCountsByAuthorId($user -> id,
                    array('filter.published' => $filterPublish)) > 0)){
            JToolBarHelper::editList('article.edit');
        }

//		}

		if($filterPublish == 3 || $filterPublish == 4){
		    if($user -> authorise('core.approve', 'com_tz_portfolio_plus')) {
                TZ_Portfolio_PlusToolbarHelper::approve('articles.approve', 'COM_TZ_PORTFOLIO_PLUS_APPROVE', true);
                TZ_Portfolio_PlusToolbarHelper::reject('articles.reject', 'COM_TZ_PORTFOLIO_PLUS_REJECT', true);
            }
            if (($canDo->get('core.edit.state') || $canDo->get('core.edit.state.own'))
                &&  $filterPublish != 4) {
                JToolBarHelper::trash('articles.trash');
            }
        }else{
            if ($canDo->get('core.edit.state') || $canDo->get('core.edit.state.own')) {
                if($filterPublish != -3) {
                    JToolBarHelper::publish('articles.publish', 'JTOOLBAR_PUBLISH', true);
                    JToolBarHelper::unpublish('articles.unpublish', 'JTOOLBAR_UNPUBLISH', true);
                    JToolBarHelper::custom('articles.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
                }
                JToolBarHelper::checkin('articles.checkin');
            }

            if ($filterPublish == -2 && ($canDo->get('core.delete')
                    || $canDo -> get('core.delete.own'))) {
                JToolBarHelper::deleteList('', 'articles.delete', 'JTOOLBAR_EMPTY_TRASH');
            }
            elseif ($canDo->get('core.edit.state') || $canDo->get('core.edit.state.own')) {
                JToolBarHelper::trash('articles.trash');
            }
        }
        
//         //Add a batch button
//		if ($user->authorise('core.edit'))
//		{
//			JHtml::_('bootstrap.modal', 'collapseModal');
//
//            $title      = JText::_('JTOOLBAR_BATCH');
//            $batchIcon  = '<i class="icon-checkbox-partial" title="'.$title.'"></i>';
//            $batchClass = ' class="btn btn-small"';
//
//			$dhtml = '<a'.$batchClass.' href="#" data-toggle="modal" data-target="#collapseModal">';
//            $dhtml .= $batchIcon.$title.'</a>';
//
//			$bar->appendButton('Custom', $dhtml, 'batch');
//		}

        if($user->authorise('core.admin', 'com_tz_portfolio_plus')
            || $user->authorise('core.options', 'com_tz_portfolio_plus')){
			JToolBarHelper::preferences('com_tz_portfolio_plus');
		}

		JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');
	}
}
