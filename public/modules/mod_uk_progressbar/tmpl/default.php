<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_progressbar
 * @copyright   Copyright (C) Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */
$document = JFactory::getDocument();
$modulePath = JURI::base() . 'modules/mod_uk_progressbar/';
$document->addStyleSheet($modulePath.'css/mod_uk_progressbar.css');

$skill_title       = $params->get('skill_title');
$progress_bg       = $params->get('progress_bg');
$skill_bg        = $params->get('skill_bg');
$skill_value     = $params->get('skill_value');
$content_before_class       = $params->get('content_before_class');
$content_before    = $params->get('content_before');

?>
<?php if ($content_before) { ?>
<div class="<?php echo $content_before_class; ?>">
<?php echo $content_before; ?>
</div>
<?php } ?>
<style type="text/css">
.hide-progress-bar {
  opacity: 0;
}


</style>



<?php
		foreach ($items as $item)
		{
		
		?>
		<div class="col">
			<h3 class="progress-title"><?php echo $item->title; ?></h3>
			<div class="progress" style="background-color:<?php echo $item->progress_bg; ?>">
				<div class="hide-progress-bar progress-bar-striped <?php echo $item->skill_bg; ?> active" style="width:<?php echo $item->skill_value; ?>%;">
					<div  class="progress-value"><?php echo $item->skill_value; ?>%</div>
				</div>
			</div>
			
		</div>

<?php } ?>

<script type="text/javascript">
(function() {
  var elements;
  var windowHeight;

  function init() {
    elements = document.querySelectorAll('.hide-progress-bar');
    windowHeight = window.innerHeight;
  }

  function checkPosition() {
    for (var i = 0; i < elements.length; i++) {
      var element = elements[i];
      var positionFromTop = elements[i].getBoundingClientRect().top;

      if (positionFromTop - windowHeight <= 0) {
        element.classList.add('progress-bar');
        element.classList.remove('hide-progress-bar');
      }
    }
  }

  window.addEventListener('scroll', checkPosition);
  window.addEventListener('resize', init);

  init();
  checkPosition();
})();


</script>