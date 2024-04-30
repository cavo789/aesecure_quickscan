<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2015 templaza.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

tzportfolioplusimport('plugin.modelitem');

class PlgTZ_Portfolio_PlusContentVote extends TZ_Portfolio_PlusPlugin
{
    protected $addon;
    protected $cache                = array();
    protected $head                 = array();
    protected $autoloadLanguage     = true;

    public function __construct($subject, array $config = array())
    {
        parent::__construct($subject, $config);

        JLoader::import('addons.content.vote.helpers.vote', COM_TZ_PORTFOLIO_PLUS_PATH_SITE);

        $this -> head  = array();
        $this -> addon = TZ_Portfolio_PlusPluginHelper::getPlugin($this -> _type, $this -> _name);
    }

    public function onAddContentType(){
        $type = new stdClass();
        $lang = JFactory::getLanguage();
        $lang_key = 'PLG_' . $this->_type . '_' . $this->_name . '_TITLE';
        $lang_key = strtoupper($lang_key);

        if ($lang->hasKey($lang_key)) {
            $type->text = JText::_($lang_key);
        } else {
            $type->text = $this->_name;
        }

        $type->value = $this->_name;

        return $type;
    }

    public function onAlwaysLoadDocument($context){
        $document = JFactory::getDocument();
        $document->addStyleSheet(TZ_Portfolio_PlusUri::root(true).'/addons/content/vote/css/vote.css', array('version' => 'auto'));

        $document -> addScript(TZ_Portfolio_PlusUri::root(true) . '/js/modernizr.custom.js',
            array('version' => 'v=2.8.3', 'relative' => true));
        $document -> addScript(TZ_Portfolio_PlusUri::root(true) . '/js/classie.min.js',
            array('version' => 'auto', 'relative' => true));
        $document -> addScript(TZ_Portfolio_PlusUri::root(true) . '/js/notificationfx.min.js',
            array('version' => 'v=1.0.0', 'relative' => true));
    }

    public function onBeforeDisplayAdditionInfo($context, &$article, $params, $page = 0, $layout = 'default'
        , $module = null){
        list($extension, $vName)   = explode('.', $context);

        $item   = $article;

        if(!isset($this -> head[$vName])){
            $this -> head[$vName]   = false;
        }
        if($module && !isset($this -> head[$vName.$module -> id])){
            $this -> head[$vName.$module -> id]   = false;
        }

        JText::script('PLG_CONTENT_VOTE_VOTES');
        JText::script('PLG_CONTENT_VOTE_VOTES_1');

        if($extension == 'module' || $extension == 'modules'){
            if($path = $this -> getModuleLayout($this -> _type, $this -> _name, $extension, $vName, $layout)){
                if(!$this -> head[$vName]){

                    // Add core.js file of Joomla to use Joomla object.
                    JHtml::_('behavior.core');

                    $document   = JFactory::getDocument();

                    // Add core.min.js file of TZ_Portfolio_Plus to use TZ_Portfolio_Plus object.
                    $document -> addScript(TZ_Portfolio_PlusUri::base(true).'/js/core.min.js');

                    $document -> addScript(TZ_Portfolio_PlusUri::root(true)
                        .'/addons/content/vote/js/vote.min.js');

                    $document -> addStyleSheet(TZ_Portfolio_PlusUri::root(true) . '/css/ns-default.min.css',
                        array('version' => 'v=1.0.0'));

                    switch ($params -> get('ct_vote_notice_layout', 'growl')){

                        case 'growl':
                            $document -> addStyleSheet(TZ_Portfolio_PlusUri::root(true)
                                . '/css/ns-style-growl.min.css', array('version' => 'v=1.0.0'));
                            break;
                        case 'attached':
                            $document -> addStyleSheet(TZ_Portfolio_PlusUri::root(true)
                                . '/css/ns-style-attached.min.css', array('version' => 'v=1.0.0'));
                            break;
                        case 'bar':
                            $document -> addStyleSheet(TZ_Portfolio_PlusUri::root(true)
                                . '/css/ns-style-bar.min.css', array('version' => 'v=1.0.0'));
                            break;
                        case 'other':
                            $document -> addStyleSheet(TZ_Portfolio_PlusUri::root(true)
                                . '/css/ns-style-other.min.css', array('version' => 'v=1.0.0'));
                            break;
                    }

                    $document -> addScript(TZ_Portfolio_PlusUri::root(true) . '/js/modernizr.custom.js',
                        array('version' => '2.8.3', 'relative' => true));
                    $document -> addScript(TZ_Portfolio_PlusUri::root(true) . '/js/classie.min.js',
                        array('version' => 'auto', 'relative' => true));
                    $document -> addScript(TZ_Portfolio_PlusUri::root(true) . '/js/notificationfx.min.js',
                        array('version' => 'v=1.0.0', 'relative' => true));

                    $document -> addScriptDeclaration('
                    if(typeof TZ_Portfolio_PlusAddOnContentVote !== undefined){
                        TZ_Portfolio_PlusAddOnContentVote.basePath = "'.TZ_Portfolio_PlusUri::base(true)
                            .'/addons/content/vote";
                        TZ_Portfolio_PlusAddOnContentVote.addonId = '.$this -> addon -> id.';
                    }');
                }


                if(isset($article -> id)){
                    $item -> rating_count   = 0;
                    $item -> rating_sum     = 0;

                    if($vote = TZ_Portfolio_PlusAddOnContentVoteHelper::getVoteByArticleId($item -> id)) {
                        foreach($vote as $key => $value){
                            $item -> $key   = $value;
                        }
                    }
                }

                // Display html
                ob_start();
                require $path;
                $html = ob_get_contents();
                ob_end_clean();
                $html = trim($html);

                $this -> head[$vName]   = true;
                $this -> head[$vName.($module?$module -> id:'')]   = true;

                return $html;
            }
        }
        elseif(in_array($context, array('com_tz_portfolio_plus.portfolio', 'com_tz_portfolio_plus.date'
        , 'com_tz_portfolio_plus.featured', 'com_tz_portfolio_plus.tags', 'com_tz_portfolio_plus.users'))){
            if($html = $this -> _getViewHtml($context,$item, $params, $layout)){
                if(!$this -> head[$vName]){

                    $document   = JFactory::getDocument();
                    $document -> addScriptDeclaration('
                        if(typeof TZ_Portfolio_PlusAddOnContentVote !== undefined){
                            TZ_Portfolio_PlusAddOnContentVote.basePath = "' . TZ_Portfolio_PlusUri::base(true)
                                . '/addons/content/vote";
                            TZ_Portfolio_PlusAddOnContentVote.addonId = "'.$this -> addon -> id.'";
                        }
                    ');
                }
                $this -> head[$vName]   = true;
                return $html;
            }
        }
    }

    public function onContentDisplayArticleView($context, &$article, $params, $page = 0, $layout = null){
        list($extension, $vName)   = explode('.', $context);

        $item   = $article;

        if(isset($article -> id)){
            $item -> rating_count   = 0;
            $item -> rating_sum     = 0;

            if($vote = TZ_Portfolio_PlusAddOnContentVoteHelper::getVoteByArticleId($item -> id)) {
                foreach($vote as $key => $value){
                    $item -> $key   = $value;
                }
            }
        }

        if(!isset($this -> head[$vName])){
            $this -> head[$vName]   = false;
        }


        $html   = parent::onContentDisplayArticleView($context, $item, $params, $page, $layout);
        if(!$this -> head[$vName]){
            $document   = JFactory::getDocument();
            $document -> addScriptDeclaration('
                if(typeof TZ_Portfolio_PlusAddOnContentVote !== undefined){
                    TZ_Portfolio_PlusAddOnContentVote.basePath = "' . TZ_Portfolio_PlusUri::base(true)
                . '/addons/content/vote";
                    TZ_Portfolio_PlusAddOnContentVote.addonId = "'.$this -> addon -> id.'";
                }
            ');
        }
        $this -> head[$vName]   = true;

        return $html;
    }

    public function onContentAfterDelete($context, $table){
        if($context == 'com_tz_portfolio_plus.article') {
            if($model  = $this -> getModel('Vote','PlgTZ_Portfolio_PlusContentVoteModel')) {
                if(method_exists($model,'delete')) {
                    $model->delete($table);
                }
            }
        }
    }

    public function onAfterDisplayAdditionInfo($context, &$article, $params, $page = 0, $layout = 'default', $module = null){}
    public function onContentDisplayListView($context, &$article, $params, $page = 0, $layout = 'default', $module = null){}
    public function onContentAfterSave($context, $data, $isnew){}
}
