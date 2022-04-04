<?php
include 'v1/class.plchart.php';
$data = array(array(17, 20, 12, 15), array(20, 30, 18, 26), array(25, 25, 20, 20), array(26, 36, 26, 36));
$demo = new plchart($data, 'stock_shle', 300, 200, 'gif');
$demo->set_color('bg.png', 'stock');
$demo->set_title('Stock type 2 - PLChart.net');
$demo->set_scale(array(0, 10, 20, 30, 40), array('Jan', 'Feb', 'Mar', 'Apr'));
$demo->set_desc();
$demo->set_graph(10, 30, 240, 150);
$demo->output();
?>