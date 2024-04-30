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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filesystem\File;
jimport( 'joomla.application.component.view');
class PhocaMapsViewRoute extends HtmlView
{
	protected $t;
	
	function display($tpl = null) {
		//$document		= JFactory::getDocument();		
		$app			= Factory::getApplication();
		$this->t['p']	= $app->getParams();
		
		HTMLHelper::stylesheet('media/com_phocamaps/css/phocamaps.css' );
		if (File::exists(JPATH_SITE.'/media/com_phocamaps/css/custom.css')) {
			HTMLHelper::stylesheet('media/com_phocamaps/css/custom.css' );
		}
		
		//$this->t['printview'] 	= $app->input->get('print', 0, 'get', 'int');
		$this->t['id'] 			= $app->input->get('id', '', 'int');
		$this->t['from'] 		= $app->input->get('from', '', 'string');
		$this->t['to'] 			= $app->input->get('to', '', 'string');
		$this->t['lang'] 		= $app->input->get('lang', '', 'string');
		$this->t['load_api_ssl']= (int)$this->t['p']->get( 'load_api_ssl', 0 );
	
		// Map params - language not used
		if ($this->t['lang'] == '') {
			$this->t['params'] = '';
		} else {
			//$this->t['params'] = '{"language":"'.$item['map']->lang.'", "other_params":"sensor=false"}';
			$this->t['params'] = '{other_params:"language='.$this->t['lang'].'"}';
		}
	
		parent::display($tpl);
	}
}
?>