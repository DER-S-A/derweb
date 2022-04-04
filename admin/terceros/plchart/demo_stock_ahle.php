<?php
include 'v1/class.plchart.php';
$data = array(array(2813, 20, 12, 15), array(2621, 30, 18, 26), array(863, 25, 20, 20), array(1935, 36, 20, 34));
$demo = new plchart($data, 'stock_ahle', 300, 200, 'gif');
$demo->set_color('bg.png', 'stock');
$demo->set_title('Stock type 3 - PLChart.net');
$demo->set_scale(array(array(0, 500, 1000, 1500, 2000, 2500, 3000), array(0, 10, 20, 30, 40)), array('Jan', 'Feb', 'Mar', 'Apr'));
$demo->set_desc();
$demo->set_graph(10, 30, 210, 150);
$demo->output();
?>