<?php
include 'v1/class.plchart.php';
$data = array(39, 23, 43, 31, 29);
$demo = new plchart($data, 'radar', 300, 200);
$demo->set_color(array(224, 224, 224));
$demo->set_title('Radar - PLChart v1.0');
$demo->set_scale(array(50, 40, 50, 40, 50), array('Humility', 'Honesty', 'Sacrifice', 'Justice', 'Honor'));
$demo->set_desc();
$demo->set_graph();
$demo->output();
?>