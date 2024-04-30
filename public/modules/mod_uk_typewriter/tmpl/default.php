<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_typewriter
 * @copyright   Copyright (C) Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

$type_text    = $params->get('type_text');
$type_font    = $params->get('type_font');
$type_speed    = $params->get('type_speed');
$type_size   = $params->get('type_size');
$content_before_class       = $params->get('content_before_class');
$content_before    = $params->get('content_before');


?>
<style type="text/css">
.hidden {
  opacity: 0;
}
.cursor {
  color:inherit;
  position:relative;
  font:inherit;
  color:inherit;
  line-height: inherit;
  animation: Cursor 1s infinite;
}

@keyframes Cursor{
  0%{opacity: 1;}
  50%{opacity: 0;}
  100%{opacity: 1;}
}

</style>

<?php if ($content_before) { ?>
<div class="<?php echo $content_before_class; ?>">
<?php echo $content_before; ?>
</div>
<?php } ?>


<<?php echo $type_font; ?>> <span class="typeWriter" data-checkVisible="true" data-speed="<?php echo $type_speed; ?>" data-start="200" data-max=""  data-end="10000000" data-text='["<?php echo $type_text; ?>"]'></span></<?php echo $type_font; ?>>


<script type="text/javascript" src="<?php echo JUri::base(); ?>modules/mod_uk_typewriter/js/typewriter.js"></script>
