<?php
include 'v1/class.plchart.php';
$data = array(12, 73, 23, 43, 31);
$demo = new plchart($data, 'pie_2d', 300, 200);
$demo->set_color(array(224, 224, 224));
$demo->set_title('2D Pie - PLChart v1.0');
$demo->set_scale(array('Jan', 'Feb', 'Mar', 'Apr', 'May'));
$demo->set_desc();
$demo->set_graph();
$demo->output();
?>