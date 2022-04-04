<?php
include 'v1/class.plchart.php';
$data = array(324, 152, 387, 243, 265, 287, 355);
$demo = new plchart($data, 'my_vote_column', 300, 200);
$demo->set_color(array(255, 255, 255), 'my_vote_column');
$demo->set_title('Vote Data', 12, 0, 100, 15, 'arial', array(244, 26, 4));
$demo->set_scale(array(), array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'));
$demo->set_desc();
$demo->set_graph(10, 40, 280, 140);
$demo->output();
?>