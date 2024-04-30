<?php 
defined('_JEXEC') or die('Restricted access');

class plgSystemuikit3 extends JPlugin {

	 function onBeforeCompileHead()
  {
      $app = JFactory::getApplication();

      // only insert the script in the frontend
      if ($app->isClient('site')) {
			$document = JFactory::getDocument();

			$document->addStyleSheet('media/uikit3/css/uikit.css',array('version' => 'auto', 'relative' => true), array('defer' => 'defer'));
			$document->addScript('media/uikit3/js/uikit.min.js',array('version' => 'auto', 'relative' => true), array('defer' => 'defer'));
			$document->addScript('media/uikit3/js/uikit-icons.min.js',array('version' => 'auto', 'relative' => true), array('defer' => 'defer'));
        }
    }
}

