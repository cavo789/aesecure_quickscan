<?php defined('_JEXEC') or die;
/*
 * @package     mod_uk_timeline
 * @copyright   Copyright (C) Joomlaplates. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */
$timeline_date       = $params->get('timeline_date');
$subtitle        = $params->get('subtitle');

$document = JFactory::getDocument();
$modulePath = JURI::base() . 'modules/mod_uk_timeline/';
$document->addStyleSheet($modulePath.'css/mod_uk_timeline.css');
?>

<div class="main-timeline">

	<?php
	foreach ($items as $item)
	{
		
	?>
	<div class="timeline" uk-scrollspy="cls: uk-animation-slide-bottom; target: .timeline-content; repeat:true; delay: 900; repeat: true">
		<span class="timeline-icon"></span>
		<span class="year"><?php echo $item->timeline_date; ?></span>
		<div class="p-4 timeline-content">
			<h3 class="title"><?php echo $item->title; ?></h3>
			<p class="p-0 m-0" ><i class="fas fa-angle-double-right"></i> <?php echo $item->subtitle; ?></p>
			<hr/>
			<p class="description">
				<?php echo $item->content; ?>
			</p>
		</div>
	</div>		
	<?php } ?>

</div>


<style type="text/css">
.main-timeline .timeline:nth-child(2n) .year:before{
    border-left: 18px solid <?php echo $timelinecolor; ?>;
}
.main-timeline .timeline-icon:before{
    background: <?php echo $timelinecolor; ?>;
}
.main-timeline .year{
    background: <?php echo $timelinecolor; ?>;
}
.main-timeline .year::before {
    border-right: 18px solid <?php echo $timelinecolor; ?>;
}
@media only screen and (max-width: 767px){
    .main-timeline .timeline:nth-child(2n) .year:before{
        border-right: 18px solid <?php echo $timelinecolor; ?>;
		border-left:none}
		
	.main-timeline .timeline-content::after {display: none}
}

.main-timeline .timeline-content{
    background: <?php echo $timeline_bg; ?>;
	color:<?php echo $timeline_color; ?>;
}
.main-timeline .timeline:nth-child(2n) .timeline-content::after {
    border-right: 20px solid <?php echo $timeline_bg; ?>;

}
.main-timeline .timeline-content::after {
    border-left: 20px solid <?php echo $timeline_bg; ?>;
}
.main-timeline .timeline-content h3.title{
	color:<?php echo $timeline_color; ?>;
}

</style>