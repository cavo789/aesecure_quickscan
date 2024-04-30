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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

$defLayout = $this -> getLayout();
?>
<div class="generator">
    <?php
    if($this -> tzlayout){
        $input  = Factory::getApplication() -> input;
        foreach($this -> tzlayout as $items )
        {
            $containerType  = '';
            if(isset($items -> containertype)){
                $containerType  = $items -> containertype;
            }
            $parentId   = uniqid(rand());
            $id         = uniqid(rand());

            $this -> state -> set('template.rowincolumn', false);
            $this -> rowItem  = $items;
            $this -> setLayout('new-row');
            echo $this -> loadTemplate();
//            die('edit_generator');
        }
    }
    ?>

</div>
<?php
$this -> setLayout($defLayout);