<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Administrator\View\Profile;

defined('_JEXEC') or die;

use Akeeba\Component\AkeebaBackup\Administrator\Model\ProfileModel;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

#[\AllowDynamicProperties]
class HtmlView extends BaseHtmlView
{
	/**
	 * The Form object
	 *
	 * @var    Form
	 * @since  1.5
	 */
	protected $form;

	/**
	 * The active item
	 *
	 * @var    object
	 * @since  1.5
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var    object
	 * @since  1.5
	 */
	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   9.0.0
	 *
	 */
	public function display($tpl = null): void
	{
		/** @var ProfileModel $model */
		$model       = $this->getModel();
		$this->form  = $model->getForm();
		$this->item  = $model->getItem();
		$this->state = $model->getState();

		// Check for errors.
		$errors = $this->get('Errors');

		if (
			(is_array($errors) || $errors instanceof \Countable)
				? count($errors)
				: 0
		)
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   9.0.0
	 */
	protected function addToolbar(): void
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		ToolbarHelper::title($isNew ? Text::_('COM_AKEEBABACKUP_PROFILES_PAGETITLE_NEW') : Text::_('COM_AKEEBABACKUP_PROFILES_PAGETITLE_EDIT'), 'icon-akeeba');

		$toolbar = Toolbar::getInstance();
		$toolbar->apply('profile.apply');

		$saveGroup = $toolbar->dropdownButton('save-group');
		$saveGroup->configure(
			function (Toolbar $childBar) use ($isNew) {
				$childBar->save('profile.save');
				$childBar->save2new('profile.save2new');

				// If an existing item, can save to a copy.
				if (!$isNew)
				{
					$childBar->save2copy('profile.save2copy');
				}
			}
		);

		$toolbar->cancel('profile.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');

		$toolbar->divider();

		$toolbar->help(null, false, 'https://www.akeeba.com/documentation/akeeba-backup-joomla/using-basic-operations.html#profiles-management');
	}
}