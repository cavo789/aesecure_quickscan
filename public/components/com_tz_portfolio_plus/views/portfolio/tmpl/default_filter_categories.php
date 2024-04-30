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
?>

<?php
$params     = $this -> params;
$bootstrap4 = ($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4);

if(!$params -> get('show_all_filter', 0) || $params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'default'){
    if($this -> itemCategories){ ?>
        <?php foreach($this -> itemCategories as $item):?>
            <a href="#<?php echo str_replace(' ','-',$item -> title)?>"
               class="btn btn-default btn-outline-secondary btn-sm"
               data-option-value=".<?php echo $item -> alias.'_'.$item -> id;?>">
                <?php echo $item -> title;?>
            </a>
        <?php endforeach;?>
    <?php }
}else{ ?>
    <?php
    $categories = array();
    if(isset($this -> parentCategory -> id) && isset($this -> categories[$this -> parentCategory -> id])) {
        $categories = $this->categories[$this->parentCategory->id];
    }
    if(count($categories) > 0){
        foreach($categories as $item){
            if (count($item->getChildren()) > 0 || $item -> numitems){
            ?>
            <a href="#<?php echo str_replace(' ','-',$item -> title)?>"
               class="btn btn-default btn-outline-secondary btn-sm"
               data-term="<?php echo $item -> id; ?>"
               data-option-value=".<?php echo $item -> alias.'_'.$item -> id;?>">
                <?php echo $item -> title;?>
            </a>
            <?php if (count($item->getChildren()) > 0){
                $this->categories[$item->id] = $item->getChildren();
                $this->parentCategory = $item;
                ob_start();
                ?>
                <div class="sub-category" data-sub-category-of="<?php echo $item -> id; ?>">
                    <a href="javascript:" data-term="<?php echo $item -> id;
                    ?>" class="btn btn-default btn-outline-secondary btn-sm js-subcategory-back-href"><?php
                        echo $item -> title;?>:</a>
                    <?php echo $this -> loadTemplate('filter_categories');?>
                </div>
                <?php
                $this -> filterSubCategory[]    = ob_get_contents();
                ob_end_clean();
            } ?>
        <?php }
        }
    }
} ?>
