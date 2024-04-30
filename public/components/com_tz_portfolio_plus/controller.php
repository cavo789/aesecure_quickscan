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

jimport('joomla.application.component.controller');

/**
 * Content Component Controller.
 */
class TZ_Portfolio_PlusController extends TZ_Portfolio_PlusControllerLegacy
{
    protected $input;

	function __construct($config = array())
	{
        $this->input    = JFactory::getApplication()->input;
        $params         = JFactory::getApplication() -> getParams();

		// Article frontpage Editor pagebreak proxying:
		if (($this->input -> get('view') == 'article')
            && $this->input -> get('layout') == 'pagebreak') {
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}
		// Article frontpage Editor article proxying:
		elseif($this->input -> get('view') == 'articles' && $this->input -> get('layout') == 'modal') {
			JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}

		parent::__construct($config);
	}

	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$app		= JFactory::getApplication('site');
		$doc    	= JFactory::getDocument();
		$params     = $app -> getParams();
		$cachable 	= true;

		$user = JFactory::getUser();

		// Set the default view name and format from the Request.
		// Note we are using a_id to avoid collisions with the router and the return page.
		// Frontend is a bit messier than the backend.
		$id		= $this -> input -> get('a_id');
		$vName	= $this -> input -> get('view', 'portfolio');

        $this->input->set('view', $vName);

        $condition   = false;
        if($this -> input -> getString('char',null) || $this -> input -> getInt('tid') ||
            $this -> input -> getString('tagAlias') || $this -> input -> getInt('uid') ||
            $this -> input -> getInt('id') || $this -> input -> get('fields', null, 'array')){
            $condition   = true;
        }

		if ($user->get('id') || strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' || $vName == 'search' ||
            ($vName == 'portfolio' && $condition))
		{
			$cachable = false;
		}

        $safeurlparams = array('catid' => 'INT', 'id' => 'INT', 'cid' => 'ARRAY', 'year' => 'INT', 'month' => 'INT', 'limit' => 'UINT', 'limitstart' => 'UINT',
        			'showall' => 'INT', 'return' => 'BASE64', 'filter' => 'STRING', 'filter_order' => 'CMD', 'filter_order_Dir' => 'CMD', 'filter-search' => 'STRING', 'print' => 'BOOLEAN', 'lang' => 'CMD', 'Itemid' => 'INT');

		// Check for edit form.
		if ($vName == 'form' && !$this->checkEditId('com_tz_portfolio_plus.edit.article', $id)) {
			// Somehow the person just went to the form - we don't allow that.
            throw new \Exception(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 403);
            return false;
		}

		// Check for edit form.
		if ($vName == 'myarticles') {
            if(!$user || ($user && !$user -> get('id'))){
                $link   = JRoute::_('index.php?option=com_users&view=login');
                $this -> setRedirect(str_replace('&amp;', '&', $link), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                return false;
            }
		}

        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
            $wa = $doc->getWebAssetManager();
            $wa->useScript('core')
                ->useScript('jquery');
        }

        //Add Script to the header
        JHtml::_('bootstrap.framework');

        // Add core.js file of Joomla to use Joomla object.
        JHtml::_('behavior.core');

        if($params -> get('enable_jquery',0)){
            $doc -> addScript(TZ_Portfolio_PlusUri::base(true).'/js/jquery-1.11.3.min.js', array('version' => 'auto'));
            $doc -> addScript(TZ_Portfolio_PlusUri::base(true).'/js/jquery-noconflict.min.js', array('version' => 'auto'));
            $doc -> addScript(TZ_Portfolio_PlusUri::base(true).'/js/jquery-migrate-1.2.1.js', array('version' => 'auto'));
        }
        if($params -> get('enable_bootstrap',1) && $params -> get('enable_bootstrap_js', 1)) {
            if($params -> get('bootstrapversion', 4) == 4){
                $doc -> addScript(TZ_Portfolio_PlusUri::base(true).'/vendor/bootstrap/js/bootstrap.min.js', array('version' => 'auto'));
                $doc -> addScript(TZ_Portfolio_PlusUri::base(true).'/vendor/bootstrap/js/bootstrap.bundle.min.js', array('version' => 'auto'));
            }
            else{
                $doc -> addScript(TZ_Portfolio_PlusUri::base(true).'/bootstrap/js/bootstrap.min.js', array('version' => 'auto'));
            }
            $doc -> addScriptDeclaration('
            (function($){
                $(document).off("click.modal.data-api")
                .on("click.modal.data-api", "[data-toggle=modal]", function (e) {
                    var $this = $(this)
                      , href = $this.attr("href")
                      , $target = $($this.attr("data-target") || (href && href.replace(/.*(?=#[^\s]+$)/, ""))) //strip for ie7
                      , option = $target.data("modal") ? "toggle" : $.extend({ remote:!/#/.test(href) && href }, $target.data(), $this.data());
                
                    e.preventDefault();
                
                    $target
                      .modal(option)
                      .one("hide", function () {
                        $this.focus()
                      });
                  });
                  
                $(document).off("click.bs.tab.data-api")
                            .on("click.bs.tab.data-api", "[data-toggle=tab]", function (e) {
                    e.preventDefault();
                      $(this).tab("show");
                });
                
                $(document).off("click.bs.dropdown.data-api")
                            .on("click.bs.dropdown.data-api", "[data-toggle=dropdown]", function (e) {
                    e.preventDefault();
                    var $this   = $(this)
                    ,data    = $this.data("bs.dropdown")
                    ,option  = data ? "toggle" : $this.data();            
                    $(this).dropdown(option);
                });                
                
                $(document).off("click.bs.collapse.data-api")
                        .on("click.bs.collapse.data-api", "[data-toggle=collapse]", function (e) {                        
                    var $this   = $(this), href = $this.attr("href");
                    var $target = $($this.attr("data-target")
                      || (href = $this.attr("href")) && href.replace(/.*(?=#[^\s]+$)/, "")); // strip for ie7
                    var data    = $target.data("bs.collapse");
                    var option  = data ? "toggle" : $this.data();
                    if (!$this.attr("data-target")) e.preventDefault();
        
                    $target.collapse(option);
                });
            })(jQuery);
			');
        }

        if($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 3) {
            $doc->addStyleSheet(TZ_Portfolio_PlusUri::base(true)
                . '/bootstrap/css/bootstrap.min.css', array('version' => 'auto'));
        }

        if($params -> get('enable_lazyload', 0)) {
            $doc->addScript(TZ_Portfolio_PlusUri::base(true) . '/js/jquery.lazyload.min.js', array('version' => 'auto'));
            $doc -> addScriptDeclaration('(function($){
            $(document).ready(function(){
                if(typeof $.fn.lazyload !== "undefined"){
                    var $main = $(".tpItemPage, .blog, .search-results, .categories-list"),
                        $imgs   = $main.find("img.lazyload");
                        
                    if(!$imgs.length){
                        $imgs = $main.find("img:not(.lazyloaded)").addClass("lazyload");
                    }

                    $imgs.attr("data-src", function () {
                        var _imgEl = $(this),
                            src = _imgEl.attr("src");
                        _imgEl.css({
                            "padding-top": function () {
                                return this.height;
                            },
                            "padding-left": function () {
                                return this.width;
                            }
                        });
                        return src;
                    });
                    $imgs.lazyload({
                        failure_limit: Math.max($imgs.length - 1, 0),
                        placeholder: "",
                        data_attribute: "src",
                        appear: function (elements_left, settings) {
                            if (!this.loaded) {
                                $(this).removeClass("lazyload").addClass("lazyloading");
                            }
                        },
                        load: function (elements_left, settings) {
                            if (this.loaded) {
                                $(this).removeClass("lazyloading").addClass("lazyloaded").css({
                                    "padding-top": "",
                                    "padding-left": ""
                                });
                            }
                        }
                    });
                }
            });
        })(jQuery);');
        }

        $doc -> addScript(TZ_Portfolio_PlusUri::base(true).'/js/core.min.js', array('version' => 'auto'));

		$result = parent::display($cachable, $safeurlparams);

		return $result;
	}
}
