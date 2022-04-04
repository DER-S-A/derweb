<?php
require("funcionesSConsola.php");

$base = Request("nombre-bdd", $BD_DATABASE);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        include("include-head.php"); 
    ?>
    <title>FK BDD</title>
</head>
<body>
    <header class="w3-container headerTitulo">
            <h4>
                <?php
                    echo("Obtener claves foraneas");
                    echo(linkCerrar(0,"","w3-right"));	
                ?>
            </h4>	
    </header>
    <div class="w3-container">
        <form method="post" name="form1" id="form1">
            <?php
                $req = new FormValidator();
                $txtBDD = new HtmlInputText("nombre-bdd", $base);
                $campo1 = new HtmlEtiquetaValor("Base de datos", $txtBDD->toHtml());
                $txtKeyWord = new HtmlInputText("palabra-clave","");
                $campo2 = new HtmlEtiquetaValor("Tabla",$txtKeyWord->toHtml());
                echo($campo1->toHtml());
                echo($campo2->toHtml() . "<br>");
            ?>
            
            <input name="enviar" type="hidden" id="enviar" value="1" />
            <input type="button" value="Generar" name="bsubmit" accesskey="1"  onclick="javascript:submitForm()" />
            <br>
            <?php    
				if (strcmp(request("enviar"),"1") == 0)
				{
                    $bdd = request("nombre-bdd");
                    $clave = request("palabra-clave");
                    $txtObs = new HtmlInputTextarea("claves-foraneas", "");
                    $txtObs->setSizeBig();      
                
                     $sql = "SELECT tc.CONSTRAINT_NAME, rc.TABLE_NAME, rc.REFERENCED_TABLE_NAME, ku.COLUMN_NAME, ku.REFERENCED_COLUMN_NAME, rc.UPDATE_RULE, rc.DELETE_RULE
							FROM information_schema.TABLE_CONSTRAINTS tc 
								INNER JOIN information_schema.REFERENTIAL_CONSTRAINTS rc on (tc.CONSTRAINT_NAME = rc.CONSTRAINT_NAME)
								INNER JOIN information_schema.KEY_COLUMN_USAGE ku on (rc.CONSTRAINT_NAME = ku.CONSTRAINT_NAME)
							WHERE (rc.TABLE_NAME like '%$clave%' or rc.REFERENCED_TABLE_NAME like '%$clave%') and
									tc.CONSTRAINT_TYPE = 'FOREIGN KEY' and
									tc.CONSTRAINT_SCHEMA = '$bdd' and
									rc.CONSTRAINT_SCHEMA = '$bdd' and
									ku.CONSTRAINT_SCHEMA = '$bdd'
							order by rc.TABLE_NAME, rc.REFERENCED_TABLE_NAME";
             
					$bd = new BDObject();
					$bd->execQuery2($sql);
                    $txt = "";
                    while(!$bd->EOF())
                    {
                        $nombreClaveF = $bd->getValue("CONSTRAINT_NAME");
                        $nombreTabla = $bd->getValue("TABLE_NAME");
                        $tablaReferenciada = $bd->getValue("REFERENCED_TABLE_NAME");
                        $nombreColumna = $bd->getValue("COLUMN_NAME");
                        $nombreColumnaRef = $bd->getValue("REFERENCED_COLUMN_NAME");
                        $uRule = $bd->getValue("UPDATE_RULE");
                        $dRule = $bd->getValue("DELETE_RULE");
						
						$EOF = "\r\n";
						$TAB = "\t";
						$sql = "select * from $nombreTabla {$EOF}where $nombreColumna is not null and $EOF $TAB $nombreColumna not in (select $nombreColumnaRef from $tablaReferenciada);$EOF";
						//sin usar !
						$sqlIf = "IF (not exists (select CONSTRAINT_NAME from information_schema.TABLE_CONSTRAINTS where CONSTRAINT_TYPE = 'FOREIGN KEY' and CONSTRAINT_NAME = '$nombreClaveF')) THEN";

                        $str = "ALTER TABLE $nombreTabla" ;
                        $str1 = "	ADD CONSTRAINT $nombreClaveF FOREIGN KEY $nombreClaveF ($nombreColumna)";
                        $str2 = "		REFERENCES $tablaReferenciada ($nombreColumnaRef)";
                        $str3 = " 			ON DELETE $dRule ON UPDATE $uRule;";
                        
                        $txt .= "$sql $EOF $str $EOF $str1 $EOF $str2 $EOF $str3 $EOF"; 
                        $txt .= "\r\n";  
                        $bd->Next();                             
                     }
                     $txtObs->setValue($txt);
                     echo($txtObs->toHtml());
                }
                
            ?>
            <script type="text/javascript">  

            <?php
                echo($req->toScript());
            ?>
            
            function submitForm() 
            {
                if (validar())
                {
                    pleaseWait2();
                    document.getElementById('form1').submit();
                }
            }
            
            </script>
        </form>    
    </div>    
</body>
</html>