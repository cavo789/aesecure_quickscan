<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_gallery
 * @copyright   © 2020 Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */
use Joomla\Utilities\ArrayHelper;
 class ModUkGalleryTextHelper {
     public static function getTagsList($items)
     {
         $tagsIndex = array();
         $tags = array();
         $tagsArray = array();
         foreach ($items as $key => $item) {
             $itemTags = explode(",", $item->tags);
             $itemTags = array_map('strtolower', $itemTags);
             if(is_array($itemTags)) {
                 for($i=0; $i < count($itemTags); $i++){
                    $itemTags[$i] = trim($itemTags[$i]);
                 }
                $tagsArray[$key] = $itemTags;
                $tags = array_merge($tags, $itemTags);
             }
         }

         $tags = ArrayHelper::arrayUnique($tags);
         foreach($tags as $tag){
             $tagClass = str_replace(' ', '-', strtolower($tag));
             $tagsIndex[$tag] = 'tag-' . $tagClass; 
         }
         $tagsList = array(
             'index' => $tagsIndex,
             'items' => $tagsArray
         );
         return $tagsList;
     }
 }