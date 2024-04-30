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

//no direct access
defined('_JEXEC') or die();

$lang           = JFactory::getLanguage();
$upper_limit    = $lang->getUpperLimitSearchWord();

$params = $this -> params;
JHtml::_('formbehavior.chosen', 'select');

$menu       = JFactory::getApplication() -> getMenu();
$mnuActive  = $menu -> getActive()
?>

<form id="searchForm" action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&Itemid='.$mnuActive -> id);?>" method="post">

    <div class="form-inline form-group input-group">
        <input type="text" name="searchword" placeholder="<?php echo JText::_('COM_TZ_PORTFOLIO_SEARCH_KEYWORD'); ?>"
               id="search-searchword"
               value="<?php echo $this->escape($this->state -> get('filter.searchword')); ?>" class="form-control" />
        <div class="button-group input-group-append">
            <button name="search" onclick="this.form.submit()" class="btn btn-outline-secondary hasTooltip"
                    title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT');?>"><i class="tps tp-search"></i> <?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
        </div>
    </div>

    <?php if($params -> get('show_search_category',0)) { ?>
        <div class="form-group">
            <label class="group-label mr-2"
                   for="catid"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FILTER_CATEGORY'); ?></label>
            <select name="id" class="inputbox" id="catid">
                <?php echo JHtml::_('select.options', $this->catOptions, 'value', 'text',
                    $this->state->get('filter.category_id')); ?>
            </select>
        </div>
        <?php
    }
    if($advFilterFields = $this -> advFilterFields){
        if($params -> get('show_s_gfield_title', 1)) {
            $adv    = $advFilterFields;
            $first  = array_shift($adv);
            echo JHtml::_('bootstrap.startTabSet', 'tz-search-', array('active' => 'tz-search-group-'.$first -> id));
        }
        foreach($advFilterFields as $i => $group) {
            if(isset($group -> fields) && $group -> fields){
                if($params -> get('show_s_gfield_title', 1)){
                    echo JHtml::_('bootstrap.addTab', 'tz-search-', 'tz-search-group-'.$group -> id, $group -> name);
                }
                foreach($group -> fields as $field){
                    if($searchinput = $field -> getSearchInput()){
                        ?>
                        <div class="form-group mt-2">
                            <?php echo $searchinput;?>
                        </div>
                        <?php
                    }
                }
                if($params -> get('show_s_gfield_title', 1)){
                    echo JHtml::_('bootstrap.endTab');
                }
            }
        }

        if($params -> get('show_s_gfield_title', 1)) {
            echo JHtml::_('bootstrap.endTabSet');
        }
    }
    ?>
    <input type="hidden" name="task" value="search.search" />
</form>
