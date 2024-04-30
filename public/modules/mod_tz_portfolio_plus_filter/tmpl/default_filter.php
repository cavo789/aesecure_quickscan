<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2016 tzportfolio.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Family Website: http://www.templaza.com

# Technical Support:  Forum - http://tzportfolio.com/Forum

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

if($advfilter):
    if($params -> get('show_group_title', 0)) {
        $adv    = $advfilter;
        $first  = array_shift($adv);
        echo JHtml::_('bootstrap.startTabSet', 'tz-filter-'.$module -> id, array('active' => 'tz-filter-group-'.$first -> id));
    }
    foreach($advfilter as $group) {
        if(isset($group -> fields) && $group -> fields){
            if($params -> get('show_group_title', 0)){
                echo JHtml::_('bootstrap.addTab', 'tz-filter-'.$module -> id, 'tz-filter-group-'.$group -> id, $group -> name);
                echo '<div class="row py-3">';
            }
            foreach($group -> fields as $field){
                if($searchinput = $field -> getSearchInput()){ ?>
                    <div class="<?php echo implode(' ', $column); ?>">
                        <div class="form-group">
                            <?php echo $searchinput;?>
                        </div>
                    </div>
<?php
                }
            }
            if($params -> get('show_group_title', 0)){
                echo '</div>';
                echo JHtml::_('bootstrap.endTab');
            }
        }
    }

    if($params -> get('show_group_title', 0)) {
        echo JHtml::_('bootstrap.endTabSet');
    }
endif;