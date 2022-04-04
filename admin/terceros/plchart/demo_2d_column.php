<?php
include 'v1/class.plchart.php';
$data = array(12, 15, 20, 24, 26, 28, 31);
$demo = new plchart($data, 'column_2d', 300, 200, 'gif');
$demo->set_color('bg.png', 'columns');
$demo->set_title('2D Column - PLChart v1.0');
$demo->set_scale(array(0, 10, 20, 30, 40), array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'));
$demo->set_desc();
$demo->set_graph(10, 30, 240, 150);
$demo->output();
?>