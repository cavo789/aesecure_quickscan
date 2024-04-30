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

jimport('joomla.application.component.controllerform');

JLoader::import('com_tz_portfolio_plus.libraries.controller.article', JPATH_ADMINISTRATOR.'/components');

class TZ_Portfolio_PlusControllerArticle extends TZ_Portfolio_PlusControllerArticleBase
{
    /**
     * Method override to check if you can add a new record.
     *
     * @param   array  $data  An array of input data.
     *
     * @return  boolean
     *
     * @since   2.2.7
     */
    protected function allowAdd($data = array())
    {
        // In the absense of better information, revert to the component permissions.
        $allow  = parent::allowAdd($data);

        if(COM_TZ_PORTFOLIO_PLUS_EDITION == 'free'){
            // Check total articles is less 50 (only use for free version)
            $model = $this -> getModel('articles');
            if(!$model){
                $allow  = false;
            }

            $maxArticles    = 50;
            $total          = $model->getTotal();

            if($total >= $maxArticles){
                Factory::getApplication() -> enqueueMessage(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_ARTICLE_LIMIT_ERROR',
                    $maxArticles), 'error');
                $allow  = false;
            }
        }

        return $allow;
    }
}