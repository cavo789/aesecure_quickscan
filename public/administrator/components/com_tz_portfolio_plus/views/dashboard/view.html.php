<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Dashboard view.
 *
 * @package		Joomla.Administrator
 * @subpakage	TZ.Portfolio
 */
class TZ_Portfolio_PlusViewDashboard extends JViewLegacy {
//    protected $xml;

    /* @since 2.2.7 */
    protected $license;

    /**
     * Display the view.
     */
    public function display($tpl = null) {
        $this -> license    = TZ_Portfolio_PlusHelper::getLicense();

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this->addToolbar();
        }

        if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
            TZ_Portfolio_PlusHelper::addSubmenu('dashboard');
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
        $user   = TZ_Portfolio_PlusUser::getUser();
        $canDo	= TZ_Portfolio_PlusHelper::getActions();

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_DASHBOARD'), 'home-2');

        if ($user->authorise('core.admin', 'com_tz_portfolio_plus')
            || $user->authorise('core.options', 'com_tz_portfolio_plus')) {
            JToolBarHelper::preferences('com_tz_portfolio_plus');
        }

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');

        JToolbarHelper::link('javascript:', JText::_('COM_TZ_PORTFOLIO_PLUS_INTRO_GUIDE'), 'support');

        JHtmlSidebar::setAction('index.php?option=com_tz_portfolio_plus&view=dashboard');

    }

    /**
     * Display quick icon button.
     *
     * @param	string	$link
     * @param	string	$image
     * @param	string	$text
     */
    protected function _quickIcon($link, $image, $text) {
        $button	= array(
            'link'	=> JRoute::_($link),
            'image'	=> 'administrator/components/com_tz_portfolio_plus/assets/' . $image,
            'text'	=> JText::_($text)
        );

        $this->button	= $button;
        echo $this->loadTemplate('button');
    }
}