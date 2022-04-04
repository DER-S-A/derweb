<?php
include 'v1/class.plchart.php';
$data = array(12, 73, 23, 43, 31);
$demo = new plchart($data, 'pie_3d', 350, 250);
$demo->set_color(array(100, 100, 100));
$demo->set_title('3D Pie - PLChart v1.0');
$demo->set_scale(array('Jan', 'Feb', 'Mar', 'Apr', 'May'));
$demo->set_desc();
$demo->set_graph(10, 80, 180, 80, 0.2);
$demo->output();
?>