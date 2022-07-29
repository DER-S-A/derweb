<?php
//set the content type this page returns
//Header ("Content-type: image/png");

//set the default values
$cod = $_REQUEST["barcode"];

$width = 480;
if (strlen($cod) < 15)
	$width = 360;
if (strlen($cod) < 10)
	$width = 280;

$height = 40;
$fontsize = 45;
$text = "*" . $_REQUEST["barcode"] . "*";
if (strlen($text) > 20)
	$text = "*0*";

//Get the parameters passed to the page
if (isset($_REQUEST["width"]))
	$width = $_REQUEST["width"];
if (isset($_REQUEST["height"]))
	$height = $_REQUEST["height"];
if (isset($_REQUEST["fontsize"]))
	$fontsize = $_REQUEST["fontsize"];
if (isset($_REQUEST["string"]))
	$text = $_REQUEST["string"];

//align the barcode at the bottom and one pixel from the left
$startlocation_y = $height - 5;
$startlocation_x = 5;

//create and image of the correct size
$img_handle = imagecreate($width, $height);
//add the black and white colours to the image
$white = imagecolorallocate($img_handle, 255, 255, 255);
$black = imagecolorallocate($img_handle, 0, 0, 0);

//write the barcode text to the image using the Free 3 of 9 Extended
//barcode font which must be in the same directory.
//The $black colour is negated to remove anti-aliasing.
//The 0 indicates the angle of the text.
imagettftext($img_handle, $fontsize, 0, $startlocation_x, $startlocation_y, -$black, "./FREE3OF9.TTF", $text);
//imagettftext($img_handle, "7" , 0, $startlocation_x + 10, $startlocation_y + 10, -$black, "Arial", $text);

//return the image and release resources
//imagepng ($img_handle, "./tmp/111.png");
imagepng($img_handle, null, 0);
imagedestroy($img_handle);
