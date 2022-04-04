<?php
include 'v1/class.plchart.php';
$data = array(12, 15, 16, 17, 20, 24, 26, 28, 30, 30, 31, 31, 32, 33, 32);
$demo = new plchart($data, 'line_single', 600, 400);
$demo->set_color('bg.png', 'line_scatter');
$demo->set_title('Single Line - PLChart v1.0');
$demo->set_scale(array(10, 30, 60), array('Jan', 'Feb', 'Mar', 'Apr'));
$demo->set_desc();
$demo->set_graph(10, 30, 240, 150, 0);
$demo->output();
?>