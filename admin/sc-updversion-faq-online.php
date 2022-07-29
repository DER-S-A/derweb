<?php
require("funcionesSConsola.php");
require("sc-updversion-utils.php");
require("sc-updversion-sc3.php");
require("sc-updversion-faq.php");


sc3UpdateVersionSc3Varios2021();
sc3UpdateVersionSc3Varios2022();

sc3UpdateVersionFaqSalimos();

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