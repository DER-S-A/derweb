<?php
include 'v1/class.plchart.php';
$data = array(
              array(2005, 2006, 2007),
			  array(12, 15, 20),
			  array(24, 26, 28),
			  array(32, 29, 40),
			  array(40, 35, 46)
			 );
$demo = new plchart($data, 'group_column_2d', 300, 200);
$demo->set_color('bg.png', 'columns');
$demo->set_title('2D Group Column - PLChart v1.0');
$demo->set_scale(array(0, 10, 30, 40, 50), array('Jan', 'Feb', 'Mar', 'Apr'));
$demo->set_desc(255, 40);
$demo->set_graph(10, 30, 200, 150);
$demo->output();
?>