<?php
require("funcionesSConsola.php");
require("sc-updversion-utils.php");
require("sc-updversion-sc3.php");
require("sc-updversion-reclamos.php");
require("sc-updversion-cel.php");


//enero 2021: utf, copiar perfiles....
sc3UpdateVersionSc3Varios2021();

sc3UpdateVersionReclamosSalimos();

scUpdateVersionCelSalimos();
sc3UpdateVersionSc3Varios2022();

Sc3FileUtils::borrarArchivos("tmp/");
Sc3FileUtils::borrarArchivos("tmpcache/");
Sc3FileUtils::borrarArchivos("scripts/tmpcache/");
Sc3FileUtils::borrarArchivos("css/tmpcache/");

if (!file_exists("./logs/"))
	mkdir("./logs");

echo ("<br>" . date("d-m-y H:i:s"));


echo ("<br><br>Limpiando cache !");
$tc = getCache();
$tc->flushCache();
saveCache($tc);

echo ("<br><br>EXITO ! t=" . Sc3FechaUtils::formatFecha(getdate()));
?>
<br>
</body>

</html>