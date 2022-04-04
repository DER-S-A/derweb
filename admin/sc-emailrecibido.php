<?php
include("funcionesSConsola.php"); 

$id = RequestInt("id");
if ($id != 0)
{
    $sql = "update ema_envios
            set abierto = 1, 
                fecha_visualizacion = CURRENT_TIMESTAMP()
            where id = $id and 
                abierto = 0";
    
    $bd = new BDObject();
    $bd->execQuery($sql);
}

$imgPath='images/check1.png';
header("Content-type: image/png");
$image = imagecreatefrompng($imgPath);
imagesavealpha($image, true);
imagealphablending($image, true);
imagepng($image);
imagedestroy($image);
?>