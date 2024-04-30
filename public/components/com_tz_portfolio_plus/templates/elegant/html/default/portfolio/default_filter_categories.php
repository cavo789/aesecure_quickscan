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
$params = $this -> params;
$tpstyleParams  = TZ_Portfolio_PlusTemplate::getTemplate(true) -> params;
$dropdownStyle  = $tpstyleParams -> get('filter_style') == 'dropdown';
$bootstrap4     = ($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4);

if(!$params -> get('show_all_filter', 0) || $params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'default'){
    if($this -> itemCategories){ ?>
        <?php foreach($this -> itemCategories as $item):?>
            <a href="#<?php echo str_replace(' ','-',$item -> title)?>"
               class="<?php echo $dropdownStyle?'dropdown-item':'btn btn-primary';?>"
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
                   class="<?php echo $dropdownStyle?'dropdown-item':'btn btn-primary';?>"
                   data-term="<?php echo $item -> id; ?>"
                   data-option-value=".<?php echo $item -> alias.'_'.$item -> id;?>">
                    <?php echo (($dropdownStyle)?str_repeat('-', $item -> level).' ':'').$item -> title;?>
                </a>
                <?php if (count($item->getChildren()) > 0){
                    $this->categories[$item->id] = $item->getChildren();
                    $this->parentCategory = $item;
                    if(!$dropdownStyle) {
                        ob_start();
                    ?>
                    <div class="sub-category" data-sub-category-of="<?php echo $item -> id; ?>">
                        <a href="javascript:" data-term="<?php echo $item -> id;
                        ?>" class="<?php echo $dropdownStyle?'dropdown-item':'btn btn-primary';?> js-subcategory-back-href"><?php
                            echo $item -> title;?>:</a>
                    <?php } ?>
                        <?php echo $this -> loadTemplate('filter_categories');?>
                    <?php if(!$dropdownStyle){ ?>
                    </div>
                    <?php
                        $this -> filterSubCategory[]    = ob_get_contents();
                        ob_end_clean();
                    }
                } ?>
            <?php }
        }
    }
} ?>
