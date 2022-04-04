<?php
include 'v1/class.plchart.php';
$data = array(
			  array('caps', 'shoes', 'gloves'),
			  array(12, 15, 20),
			  array(24, 26, 28),
			  array(32, 29, 40),
			  array(40, 35, 46)
			 );
$demo = new plchart($data, 'percent_column_3d', 300, 200);
$demo->set_color('bg.png', 'columns');
$demo->set_title('3D Percent Column - PLChart v1.0');
$demo->set_scale(array(), array('2004', '2005', '2006', '2007'));
$demo->set_desc(260, 40);
$demo->set_graph(8, 30, 200, 150);
$demo->output();
?>