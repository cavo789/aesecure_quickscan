<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Helper\ModuleHelper;

class PlgTZ_Portfolio_PlusContentVoteControllerVote  extends TZ_Portfolio_Plus_AddOnControllerLegacy{

    public function getModel($name = 'vote', $prefix = 'PlgTZ_Portfolio_PlusContentVoteModel',
                             $config = array('ignore_request' => true))
    {
        $cid            = $this -> input -> getInt('cid');
        if($article    = TZ_Portfolio_PlusContentHelper::getArticleById($cid)) {

            $app = \JFactory::getApplication('site');
            $params = $app->getParams('com_tz_portfolio_plus');

            $artParams = new Registry();
            if ($article->attribs) {
                $artParams->loadString($article->attribs);
            }
            $category = TZ_Portfolio_PlusFrontHelperCategories::getMainCategoriesByArticleId($cid);

            $catParams = new Registry();
            if ($category && is_array($category)) {
                $category = $category[0];
            }
            if ($category && $category->params) {
                $catParams->loadString($category->params);
            }

            $addonParams = new Registry();
            if ($this->addon->params) {
                $addonParams->loadString($this->addon->params);
            }

            $params->merge($addonParams);
            $params->merge($catParams);
            $params->merge($artParams);

            $article->params = $params;

            $this->article = $article;
            $this->trigger_params = $params;

            $_config = array('article' => $article, 'trigger_params' => $params, 'addon' => $this->addon);
            $config = array_merge($config, $_config);
        }

        return parent::getModel($name, $prefix, $config);
    }

    public function vote(){

        $result     = true;
        $message    = '';
        $html       = '';
        $dataReturn = new stdClass();
        $input      = $this -> input;
        $app        = \JFactory::getApplication();

        // Get current Ip
        $currip = $input -> server -> get('REMOTE_ADDR', '', 'string');

        $cid            = $input -> getInt('cid');
        $user_rating    = $input -> getInt('user_rating');

        if(($votesdb = TZ_Portfolio_PlusAddOnContentVoteHelper::getVoteByArticleId($cid)) && $votesdb -> lastip == $currip){

            // You are voted
            $message    = JText::_('PLG_CONTENT_VOTE_RATED');
        }else {
            if($model  = $this -> getModel()){

                $message  = JText::_('PLG_CONTENT_VOTE_THANKS');

                if(!$model -> save(array('cid' => $cid, 'user_rating' => $user_rating))){
                    $message    = $model -> getError();
                    $result     = false;
                }
                else{

                    if($item = $model -> getItem()){
                        $dataReturn -> rating_sum   = $item -> rating_sum;
                        $dataReturn -> rating_count = $item -> rating_count;
                    }
                }
            }
        }

        echo new JsonResponse($dataReturn, $message, !$result);

        $app -> close();
    }
}