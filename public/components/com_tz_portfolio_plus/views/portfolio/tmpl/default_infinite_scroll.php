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

defined('_JEXEC') or die();

$params     = $this -> params;
$lang       = JFactory::getApplication() -> input -> getCmd('lang');
$language   = JLanguageHelper::getLanguages('lang_code');

$doc    = JFactory::getDocument();
$doc -> addScriptDeclaration('
jQuery(document).ready(function(){
    jQuery("#portfolio").tzPortfolioPlusInfiniteScroll({
        "params"    : '.$this -> params .',
        rootPath    : "'.JUri::root().'",
        Itemid      : '.$this -> Itemid.',
         msgText    : "<i class=\"tz-icon-spinner tz-spin\"><\/i>'.JText::_('COM_TZ_PORTFOLIO_PLUS_LOADING_TEXT').'",
        loadedText  : "'.JText::_('COM_TZ_PORTFOLIO_PLUS_NO_MORE_PAGES').'"
        '.(isset($this -> commentText)?(',commentText : "'.$this -> commentText.'"'):'').',
        lang        : "'.$this -> lang_sef.'"
    });
});');
?>
<div id="tz_append" class="text-center">
    <?php if($params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'ajaxButton'):?>
    <a href="javascript:" class="btn btn-default btn-outline-secondary btn-lg btn-large btn-block mt-3"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADD_ITEM_MORE');?></a>
    <?php endif;?>
</div>

<div id="loadaj" style="display: none;">
    <a href="<?php echo $this -> ajaxLink; ?>"></a>
</div>
