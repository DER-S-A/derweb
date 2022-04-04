<?php
include 'v1/class.plchart.php';
$data = array(
              'x' => array(12, 15, 16, 17, 20, 24, 26, 28, 30, 30, 31, 31, 32, 33, 32, 14),
              'y' => array(34, 25, 24, 34, 19, 12, 25, 24, 28, 31, 26, 39, 24, 22, 31, 24)
			 );
$demo = new plchart($data, 'scatter', 300, 200);
$demo->set_color('bg.png', 'line_scatter');
$demo->set_title('Scatter - PLChart v1.0');
$demo->set_scale(array(10, 15, 20, 25, 30, 35, 40), array(10, 20, 40, 80));
$demo->set_desc();
$demo->set_graph(10, 30, 240, 150);
$demo->output();
?>