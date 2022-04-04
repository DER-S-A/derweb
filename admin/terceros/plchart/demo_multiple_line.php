<?php
include 'v1/class.plchart.php';
$data = array(
              '2005' => array(12, 15, 16, 17, 20, 24, 26, 28, 30, 30, 31, 31, 32, 33, 32),
			  '2006' => array(11, 11, 12, 15, 14, 15, 17, 18, 20, 24, 27, 32, 34, 30, 30),
			  '2007' => array(14, 16, 17, 18, 17, 22, 21, 23, 25, 26, 23, 27, 26, 28, 30)
			 );
$demo = new plchart($data, 'line_multiple', 300, 200);
$demo->set_color('bg.png', 'line_scatter');
$demo->set_title('Multiple Line - PLChart v1.0');
$demo->set_scale(array(10, 20, 30, 40, 50), array('Jan', 'Feb', 'Mar', 'Apr'));
$demo->set_desc(220);
$demo->set_graph(10, 30, 240, 150, 0);
$demo->output();
?>