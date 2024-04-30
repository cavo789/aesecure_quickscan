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

$params = &$this -> params;
?>
<ul class="nav nav-pills">
<?php if($letters = $params -> get('tz_letters','a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z')):
    $letters = explode(',',$letters);
    $availLetter    = $this -> availLetter;
?>
    <?php foreach($letters as $i => $letter):?>
        <?php
            $letter = trim($letter);
            $disabledClass  = null;
            $activeClass    = null;
            $date           = null;
            if($availLetter[$i] != true):
                $disabledClass  = ' disabled';
            endif;
            if($this -> char == $letter):
                $activeClass    = ' active';
            endif;
        ?>
        <li>
        <a<?php if($availLetter[$i] != false && $this -> char != $letter) echo ' href="'.JRoute::_(TZ_Portfolio_PlusHelperRoute::getDateRoute(
                    $this -> state -> get('filter.year'), $this -> state -> get('filter.month'))
                .'&char='.mb_strtolower(trim($letter))).'"';?>
           class="btn-sm<?php echo $disabledClass.$activeClass;?>"><?php echo mb_strtoupper(trim($letter));?></a>
        </li>
    <?php endforeach;?>
<?php endif;?>
</ul>