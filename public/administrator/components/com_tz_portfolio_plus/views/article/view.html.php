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
 * View to edit an article.
 *
 */
class TZ_Portfolio_PlusViewArticle extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
    protected $pluginsTab;
    protected $pluginsMediaTypeTab	= array();
	protected $formfields	= null;
	protected $extraFields	= null;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
	    $app    = Factory::getApplication();
	    $input  = $app -> input;
        if($input->get('task')!='lists'){
            if ($this->getLayout() == 'pagebreak') {
                $eName		= Factory::getApplication()->input->get('e_name');
                $eName		= preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $eName );
                $document	= Factory::getApplication() -> getDocument();
                $document->setTitle(JText::_('COM_CONTENT_PAGEBREAK_DOC_TITLE'));
                $this-> eName    = $eName;
                parent::display($tpl);
                return;
            }

            // Initialiase variables.
            $this->form		= $this->get('Form');
            $this->item		= $this->get('Item');
            $this->state	= $this->get('State');

            $canDo	= TZ_Portfolio_PlusHelper::getActions(COM_TZ_PORTFOLIO_PLUS, 'article', $this -> item -> id);
			$this -> canDo	= $canDo;

            // Check for errors.
            if (count($errors = $this->get('Errors'))) {
                JError::raiseError(500, implode("\n", $errors));
                return false;
            }

            $this -> extraFields	= $this -> get('ExtraFields');

            // Import all add-ons
            TZ_Portfolio_PlusPluginHelper::importAllAddOns();

            $this -> advancedDesc       = $app -> triggerEvent('onAddFormToArticleDescription', array($this -> item));
            $this -> beforeDescription  = $app -> triggerEvent('onAddFormBeforeArticleDescription', array($this -> item));
            $this -> afterDescription   = $app -> triggerEvent('onAddFormAfterArticleDescription', array($this -> item));

            // Load Tabs's title from plugin group tz_portfolio_plus_mediatype
//			TZ_Portfolio_PlusPluginHelper::importPlugin('mediatype');
			if($mediaType  = $app -> triggerEvent('onAddMediaType')){
			    $mediaType  = array_filter($mediaType);
			    $mediaType  = array_reverse($mediaType);
				$mediaForm	= $app -> triggerEvent('onMediaTypeDisplayArticleForm',array($this -> item));
                $mediaForm  = array_filter($mediaForm);
                $mediaForm  = array_reverse($mediaForm);
				if(count($mediaType)){
					$plugin	= array();
					foreach($mediaType as $i => $type){
						$plugin[$i]			= new stdClass();
						$plugin[$i] -> type	= $type;
						$plugin[$i] -> html	= '';
						if($mediaForm && count($mediaForm) && isset($mediaForm[$i])) {
							$plugin[$i]->html = $mediaForm[$i];
						}
					}
					$this -> pluginsMediaTypeTab    = $plugin;
				}
			}

            // If we are forcing a language in modal (used for associations).
            if ($this->getLayout() === 'modal' && $forcedLanguage = $input->get('forcedLanguage', '', 'cmd')) {
                // Set the language field to the forcedLanguage and disable changing it.
                $this->form->setValue('language', null, $forcedLanguage);
                $this->form->setFieldAttribute('language', 'readonly', 'true');
            }

            $this->addToolbar();
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
		Factory::getApplication()->input->set('hidemainmenu', true);
		$user		= TZ_Portfolio_PlusUser::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo		= $this -> canDo;

		JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_PAGE_'.($checkedOut ? 'VIEW_ARTICLE' : ($isNew ? 'ADD_ARTICLE' : 'EDIT_ARTICLE'))), 'pencil-2');

		// Built the actions for new and existing records.

		// For new records, check the create permission.
        $approvePer     = TZ_Portfolio_PlusHelperACL::allowApprove($this -> item);
        $applyText      = $approvePer?'JTOOLBAR_APPLY':'COM_TZ_PORTFOLIO_PLUS_SUBMIT_APPROVE';
        $saveText       = $approvePer?'JTOOLBAR_SAVE':'COM_TZ_PORTFOLIO_PLUS_SUBMIT_APPROVE_AND_CLOSE';
        $save2newText   = $approvePer?'JTOOLBAR_SAVE_AND_NEW':'COM_TZ_PORTFOLIO_PLUS_SUBMIT_APPROVE_AND_NEW';

		if ($isNew && (count($user->getAuthorisedCategories('com_tz_portfolio_plus', 'core.create')) > 0)) {
			JToolBarHelper::apply('article.apply', $applyText);
            JToolBarHelper::save('article.save', $saveText);
			JToolBarHelper::save2new('article.save2new', $save2newText);
			if(!$approvePer){
			    TZ_Portfolio_PlusToolbarHelper::draft('article.draft');
            }
			JToolBarHelper::cancel('article.cancel');
		}
		else {
			// Can't save the record if it's checked out.
			if (!$checkedOut) {
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($approvePer || ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId))) {

				    if($approvePer && ($this -> item -> state == 3 || $this -> item -> state == 4)){
				        $applyText  = JText::_('COM_TZ_PORTFOLIO_PLUS_APPROVE_AND_PUBLISH');
                        $saveText  = JText::_('COM_TZ_PORTFOLIO_PLUS_APPROVE_AND_PUBLISH_AND_CLOSE');
                        $save2newText  = JText::_('COM_TZ_PORTFOLIO_PLUS_APPROVE_AND_PUBLISH_AND_NEW');
                    }

					JToolBarHelper::apply('article.apply', $applyText);
                    JToolBarHelper::save('article.save', $saveText);

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create')) {
						JToolBarHelper::save2new('article.save2new', $save2newText);
					}
                    if(!$approvePer){
                        TZ_Portfolio_PlusToolbarHelper::draft('article.draft');
                    }
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create') && $approvePer && $this -> item -> state != 3 && $this -> item -> state != 4) {
				JToolBarHelper::save2copy('article.save2copy');
			}

            if($approvePer && ($this -> item -> state == 3 || $this -> item -> state == 4)){
                JToolbarHelper::custom('article.reject', 'minus text-danger text-error',
                    '',  JText::_('COM_TZ_PORTFOLIO_PLUS_REJECT'), false);
            }

			JToolBarHelper::cancel('article.cancel', 'JTOOLBAR_CLOSE');
		}

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER_EDIT',false,
            'https://www.tzportfolio.com/document/administration/41-how-to-create-edit-an-article-in-tz-portfolio-plus.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');

	}
}
