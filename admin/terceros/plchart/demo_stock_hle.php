<?php
include 'v1/class.plchart.php';
$data = array(array(20, 12, 15), array(30, 18, 26), array(25, 20, 20), array(28, 36, 36));
$demo = new plchart($data, 'stock_hle', 300, 200, 'gif');
$demo->set_color('bg.png', 'stock');
$demo->set_title('Stock type 1 - PLChart.net');
$demo->set_scale(array(0, 10, 20, 30, 40), array('Jan', 'Feb', 'Mar', 'Apr'));
$demo->set_desc();
$demo->set_graph(10, 30, 240, 150);
$demo->output();
?>