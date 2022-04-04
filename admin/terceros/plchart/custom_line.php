<?php
include 'v1/class.plchart.php';
$data = array(10, 15, 16, 20);
$demo = new plchart($data, 'custom_line', 300, 200);
$demo->set_color(array(4, 2, 252), 'line_scatter');
$demo->set_title('Custom Line', 12, 0, 100, 15, 'arial', array(244, 26, 4));
$demo->set_scale(array(5, 10, 15, 20, 25), array('Jan', 'Feb', 'Mar', 'Apr'));
$demo->set_desc();
$demo->set_graph(10, 30, 220, 150, 0);
$demo->output();
?>