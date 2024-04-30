<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

jimport('joomla.application.component.view');

/**
 * Dashboard view.
 *
 * @package		Joomla.Administrator
 * @subpakage	TZ.Portfolio
 */
class TZ_Portfolio_PlusViewDashboard extends JViewLegacy {
    protected $feedBlog;

    /**
     * Display the view.
     */
    public function display($tpl = null) {
        $app        = Factory::getApplication();

        $this -> feedBlog   = $this -> get('FeedBlog');

        $json       = new JResponseJson();

        $app -> setHeader('Content-Type', 'application/json; charset=' . $app->charSet, true);
        $app -> sendHeaders();

        $result = $this->loadTemplate($tpl);

        $json -> data   = $result;
        echo json_encode($json);

        $app -> close();
    }
}