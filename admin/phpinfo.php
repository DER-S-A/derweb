<?php
  phpinfo();

  include("funcionesSConsola.php");
  $bd = new BDObject();
  echo("<br>Base de datos, Charset: " . $bd->getCharsetName());
  
  echo("<pre>mb_list_encodings():");
  print_r(mb_list_encodings());
  echo("</pre>");
?>