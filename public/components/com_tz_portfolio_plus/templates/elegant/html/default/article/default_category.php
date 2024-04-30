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

// no direct access
defined('_JEXEC') or die('Restricted access');

$params = $this -> item -> params;
if($params -> get('show_category',1)){
?>
<div class="tpArticleCategory">
    <i class="tp tp-folder-open"></i>
    <?php
    $title = $this->escape($this->item->category_title);
    $url    = $title;
    $target = '';
    if(isset($tmpl) AND !empty($tmpl)):
        $target = ' target="_blank"';
    endif;
    $url = '<a href="'.$this -> item -> category_link.'"'.$target.' itemprop="genre">'.$title.'</a>';

    $lang_text  = 'COM_TZ_PORTFOLIO_PLUS_CATEGORY';
    ?>
    <?php if(isset($this->item -> second_categories) && $this->item -> second_categories
        && count($this -> item -> second_categories)){
        $lang_text  = 'COM_TZ_PORTFOLIO_PLUS_CATEGORIES';
        foreach($this->item -> second_categories as $j => $scategory){
            if($j <= count($this->item -> second_categories)) {
                $title  .= ', ';
                $url    .= ', ';
            }
            $url    .= '<a href="' . $scategory -> link
                . '" itemprop="genre">' . $scategory -> title . '</a>';
            $title  .= $this->escape($scategory -> title);
        }
    }?>

    <?php if ($params->get('link_category',1) and $this->item->catslug) : ?>
        <?php echo $url; ?>
    <?php else : ?>
        <?php echo '<span itemprop="genre">' . $title . '</span>'; ?>
    <?php endif; ?>
</div>
<?php }?>