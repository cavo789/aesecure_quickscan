<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2017 tzportfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum

# Family website: http://www.templaza.com

-------------------------------------------------------------------------*/

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

/**
 * Supports a modal article picker.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 * @since       3.1
 */
class JFormFieldModal_Category extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   1.6
	 */
	protected $type     = 'Modal_Category';
    protected $layout   = 'form.field.modals.category';

//	/**
//	 * Method to get the field input markup.
//	 *
//	 * @return  string	The field input markup.
//	 * @since   1.6
//	 */
//    protected function getInput()
//    {
//        if ($this->element['extension'])
//        {
//            $extension = (string) $this->element['extension'];
//        }
//        else
//        {
//            $extension = (string) Factory::getApplication()->input->get('extension', 'com_tz_portfolio_plus');
//        }
//
//        $allowNew    = ((string) $this->element['new'] == 'true');
//        $allowEdit   = ((string) $this->element['edit'] == 'true');
//        $allowClear  = ((string) $this->element['clear'] != 'false');
//        $allowSelect = ((string) $this->element['select'] != 'false');
//
//        // Load language.
//        Factory::getApplication() -> getLanguage()
//            ->load('com_categories', JPATH_ADMINISTRATOR);
//
//        // The active category id field.
//        $value = (int) $this->value > 0 ? (int) $this->value : '';
//
//        // Create the modal id.
//        $modalId = 'Category_' . $this->id;
//
//        // Add the modal field script to the document head.
//        JHtml::_('jquery.framework');
//        JHtml::_('script', 'system/modal-fields.js', array('version' => 'auto', 'relative' => true));
//
//        // Script to proxy the select modal function to the modal-fields.js file.
//        if ($allowSelect)
//        {
//            static $scriptSelect = null;
//
//            if (is_null($scriptSelect))
//            {
//                $scriptSelect = array();
//            }
//
//            if (!isset($scriptSelect[$this->id]))
//            {
//                Factory::getApplication() -> getDocument()->addScriptDeclaration("
//				function jSelectCategory_" . $this->id . "(id, title, object) {
//					window.processModalSelect('Category', '" . $this->id . "', id, title, '', object);
//				}
//				");
//
//                $scriptSelect[$this->id] = true;
//            }
//        }
//
//        // Setup variables for display.
//        $linkCategories = 'index.php?option=com_tz_portfolio_plus&amp;view=categories&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1'
//            . '&amp;extension=' . $extension;
//        $linkCategory  = 'index.php?option=com_tz_portfolio_plus&amp;view=category&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1'
//            . '&amp;extension=' . $extension;
//        $modalTitle    = JText::_('COM_CATEGORIES_CHANGE_CATEGORY');
//
//        if (isset($this->element['language']))
//        {
//            $linkCategories .= '&amp;forcedLanguage=' . $this->element['language'];
//            $linkCategory   .= '&amp;forcedLanguage=' . $this->element['language'];
//            $modalTitle     .= ' &#8212; ' . $this->element['label'];
//        }
//
//        $urlSelect = $linkCategories . '&amp;function=jSelectCategory_' . $this->id;
//        $urlEdit   = $linkCategory . '&amp;task=category.edit&amp;id=\' + document.getElementById("' . $this->id . '_id").value + \'';
//        $urlNew    = $linkCategory . '&amp;task=category.add';
//
//        if ($value)
//        {
//            $db    = TZ_Portfolio_PlusDatabase::getDbo();
//            $query = $db->getQuery(true)
//                ->select($db->quoteName('title'))
//                ->from($db->quoteName('#__tz_portfolio_plus_categories'))
//                ->where($db->quoteName('id') . ' = ' . (int) $value);
//            $db->setQuery($query);
//
//            try
//            {
//                $title = $db->loadResult();
//            }
//            catch (RuntimeException $e)
//            {
//                JError::raiseWarning(500, $e->getMessage());
//            }
//        }
//
//        $title = empty($title) ? JText::_('COM_CATEGORIES_SELECT_A_CATEGORY') : htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
//
//        // The current category display field.
//        $html  = '<span class="input-append input-group">';
//        $html .= '<input class="input-medium" id="' . $this->id . '_name" type="text" value="' . $title . '" disabled="disabled" size="35" />';
//
//        // Select category button.
//        if ($allowSelect)
//        {
//            $html .= '<a'
//                . ' class="btn hasTooltip' . ($value ? ' hidden' : '') . '"'
//                . ' id="' . $this->id . '_select"'
//                . ' data-toggle="modal" data-bs-toggle="modal"'
//                . ' role="button"'
//                . ' href="#ModalSelect' . $modalId . '"'
//                . ' title="' . JHtml::tooltipText('COM_CATEGORIES_CHANGE_CATEGORY') . '">'
//                . '<span class="icon-file" aria-hidden="true"></span> ' . JText::_('JSELECT')
//                . '</a>';
//        }
//
//        // New category button.
//        if ($allowNew)
//        {
//            $html .= '<a'
//                . ' class="btn hasTooltip' . ($value ? ' hidden' : '') . '"'
//                . ' id="' . $this->id . '_new"'
//                . ' data-toggle="modal" data-bs-toggle="modal"'
//                . ' role="button"'
//                . ' href="#ModalNew' . $modalId . '"'
//                . ' title="' . JHtml::tooltipText('COM_CATEGORIES_NEW_CATEGORY') . '">'
//                . '<span class="icon-new" aria-hidden="true"></span> ' . JText::_('JACTION_CREATE')
//                . '</a>';
//        }
//
//        // Edit category button.
//        if ($allowEdit)
//        {
//            $html .= '<a'
//                . ' class="btn hasTooltip' . ($value ? '' : ' hidden') . '"'
//                . ' id="' . $this->id . '_edit"'
//                . ' data-toggle="modal" data-bs-toggle="modal"'
//                . ' role="button"'
//                . ' href="#ModalEdit' . $modalId . '"'
//                . ' title="' . JHtml::tooltipText('COM_CATEGORIES_EDIT_CATEGORY') . '">'
//                . '<span class="icon-edit" aria-hidden="true"></span> ' . JText::_('JACTION_EDIT')
//                . '</a>';
//        }
//
//        // Clear category button.
//        if ($allowClear)
//        {
//            $html .= '<a'
//                . ' class="btn' . ($value ? '' : ' hidden') . '"'
//                . ' id="' . $this->id . '_clear"'
//                . ' href="#"'
//                . ' onclick="window.processModalParent(\'' . $this->id . '\'); return false;">'
//                . '<span class="icon-remove" aria-hidden="true"></span>' . JText::_('JCLEAR')
//                . '</a>';
//        }
//
//        $html .= '</span>';
//
//        // Select category modal.
//        if ($allowSelect)
//        {
//            $html .= JHtml::_(
//                'bootstrap.renderModal',
//                'ModalSelect' . $modalId,
//                array(
//                    'title'       => $modalTitle,
//                    'url'         => $urlSelect,
//                    'height'      => '400px',
//                    'width'       => '800px',
//                    'bodyHeight'  => '70',
//                    'modalWidth'  => '80',
//                    'footer'      => '<a role="button" class="btn" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true">' . JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
//                )
//            );
//        }
//
//        // New category modal.
//        if ($allowNew)
//        {
//            $html .= JHtml::_(
//                'bootstrap.renderModal',
//                'ModalNew' . $modalId,
//                array(
//                    'title'       => JText::_('COM_CATEGORIES_NEW_CATEGORY'),
//                    'backdrop'    => 'static',
//                    'keyboard'    => false,
//                    'closeButton' => false,
//                    'url'         => $urlNew,
//                    'height'      => '400px',
//                    'width'       => '800px',
//                    'bodyHeight'  => '70',
//                    'modalWidth'  => '80',
//                    'footer'      => '<a role="button" class="btn" aria-hidden="true"'
//                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'category\', \'cancel\', \'item-form\'); return false;">'
//                        . JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
//                        . '<a role="button" class="btn btn-primary" aria-hidden="true"'
//                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'category\', \'save\', \'item-form\'); return false;">'
//                        . JText::_('JSAVE') . '</a>'
//                        . '<a role="button" class="btn btn-success" aria-hidden="true"'
//                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'category\', \'apply\', \'item-form\'); return false;">'
//                        . JText::_('JAPPLY') . '</a>',
//                )
//            );
//        }
//
//        // Edit category modal.
//        if ($allowEdit)
//        {
//            $html .= JHtml::_(
//                'bootstrap.renderModal',
//                'ModalEdit' . $modalId,
//                array(
//                    'title'       => JText::_('COM_CATEGORIES_EDIT_CATEGORY'),
//                    'backdrop'    => 'static',
//                    'keyboard'    => false,
//                    'closeButton' => false,
//                    'url'         => $urlEdit,
//                    'height'      => '400px',
//                    'width'       => '800px',
//                    'bodyHeight'  => '70',
//                    'modalWidth'  => '80',
//                    'footer'      => '<a role="button" class="btn" aria-hidden="true"'
//                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'category\', \'cancel\', \'item-form\'); return false;">'
//                        . JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
//                        . '<a role="button" class="btn btn-primary" aria-hidden="true"'
//                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'category\', \'save\', \'item-form\'); return false;">'
//                        . JText::_('JSAVE') . '</a>'
//                        . '<a role="button" class="btn btn-success" aria-hidden="true"'
//                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'category\', \'apply\', \'item-form\'); return false;">'
//                        . JText::_('JAPPLY') . '</a>',
//                )
//            );
//        }
//
//        // Note: class='required' for client side validation
//        $class = $this->required ? ' class="required modal-value"' : '';
//
//        $html .= '<input type="hidden" id="' . $this->id . '_id"' . $class . ' data-required="' . (int) $this->required . '" name="' . $this->name
//            . '" data-text="' . htmlspecialchars(JText::_('COM_CATEGORIES_SELECT_A_CATEGORY', true), ENT_COMPAT, 'UTF-8') . '" value="' . $value . '" />';
//
//        return $html;
//    }


    protected function getLayoutData()
    {
        $data = parent::getLayoutData();

        $data['title']      = null;
        $data['items']      = false;
        $data['submitform'] = false;
        $data['link']       = 'index.php?option=com_tz_portfolio_plus&view=categories&layout=modal'
            .($this -> multiple?'&ismultiple=true':'').'&tmpl=component';

        if($items  = $this -> _getItems($this -> value)){
            if($this -> multiple) {
                $data['items'] = $items;
            }else {
                $data['title'] = $items[0]->title;
            }
        }
        if (isset($this->element['language']))
        {
            $data['language']   = $this -> element['language'];
            $data['link']      .= '&forcedLanguage='.$this->element['language'];
        }

        if(isset($this -> element['submitform']) && $this -> element['submitform']){
            $data['submitform'] = boolval($this -> element['submitform']);
        }

        return $data;
    }
    protected function getRenderer($layoutId = 'default')
    {
        $renderer = new FileLayout($layoutId,COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/layouts');

        $renderer->setDebug($this->isDebugEnabled());

        $layoutPaths = $this->getLayoutPaths();

        if ($layoutPaths)
        {
            $renderer->setIncludePaths($layoutPaths);
        }

        return $renderer;
    }

    protected function getLayoutPaths()
    {
        return array();
    }

    protected function _getItems($ids){
        if($ids){
            $db     = TZ_Portfolio_PlusDatabase::getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('a.id,a.title');
            $query -> from('#__tz_portfolio_plus_categories AS a');
//            $query -> join('LEFT','#__tz_portfolio_plus_content_category_map AS m ON m.contentid = a.id');
//            $query -> join('LEFT','#__tz_portfolio_plus_categories AS c ON c.id = m.catid');
            if(is_array($ids)){
                $query -> where('a.id IN('.implode(',',$ids).')');
            }else{
                if(is_string($ids) && strpos($ids, ',')){
                    $query -> where('a.id IN('.$ids.')');
                }else {
                    $query->where('a.id =' . ((int)$ids));
                }
            }
            $query -> group('id');
            $db -> setQuery($query);
            if($rows = $db -> loadObjectList()){
                return $rows;
            }
        }
        return false;
    }
}
