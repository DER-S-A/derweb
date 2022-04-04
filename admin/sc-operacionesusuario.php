<?php 
include("funcionesSConsola.php"); 
checkUsuarioLogueado();

$error = "";
if (enviado())
{

}

$idusuario = RequestIntMaster("idusuario", "sc_usuarios");
$mquery = Request("mquery");
?>
<!DOCTYPE html>
<html>
<head>

<title>SC3 - Operaciones</title>

<?php include("include-head.php"); ?>

</head>

<body onload="firstFocus()">

<form method="post" name="form1" id="form1">
  <?php
  $req = new FormValidator();
  ?>
  <table class="dlg">
    <tr>
      <td colspan="2" align="center" class="td_titulo">
        <table width="100%" border="0" cellspacing="1" cellpadding="1">
          <tr>
            <td align="center"><?php echo(getOpTitle(Request("opid"))); ?></td>
            <td width="50"><?php echo(linkCerrar(0)); ?></td>
          </tr>
        </table>
      </td>
    </tr>
	<?php
	if ($error != "")
	{
	?>
        <tr>
        <td colspan="2" class="td_error"><?php echo($error); ?></td>
        </tr>
    <?php
	}
	?>
</table>

	<?php 
	$tab = new HtmlTabs2();

    $sql = "select m.Item as menu, q.querydescription as dato, 
                o.nombre as operacion, o.ayuda, 
                p.nombre as perfil
            from sc_operaciones o
                inner join sc_perfiles_operaciones po on (po.idoperacion = o.id)                
                inner join sc_usuarios_perfiles up on (po.idperfil = up.idperfil)
                inner join sc_perfiles p on (up.idperfil = p.id)
                left join sc_menuconsola m on (o.idmenu = m.idItemMenu)
                left join sc_querys q on (o.idquery = q.id)
            where up.idusuario = $idusuario
            order by m.Item, q.querydescription, o.nombre";

    $rs = new BDObject();
    $rs->execQuery($sql);

    $grid = new HtmlGrid($rs);
    $grid->setTitle("Operaciones");
    $tab->agregarSolapa("Operaciones", "fa-bolt", $grid->toHtml());

    //TODO: usar group concat
    $sql = "select distinct m.Item as menu, q.querydescription as dato, 
                p.nombre as perfil
            from sc_querys q
                inner join sc_perfiles_querys pq on (pq.idquery = q.id)             
                inner join sc_usuarios_perfiles up on (up.idperfil = pq.idperfil)
                inner join sc_perfiles p on (up.idperfil = p.id)                
                left join sc_menuconsola m on (q.idmenu = m.idItemMenu)
            where up.idusuario = $idusuario
            order by m.Item, q.querydescription";

    $rs->execQuery($sql);

    $grid = new HtmlGrid($rs);
    $grid->setTitle("Datos");
    $tab->agregarSolapa("Datos", "fa-table", $grid->toHtml());

	echo($tab->toHtml());
    ?>
</div> 

  <script language="JavaScript" type="text/javascript">
	<?php
	echo($req->toScript());
	?>
	
	function submitForm() 
	{
		if (validar())
			document.getElementById('form1').submit();
	}
	</script>

</form>
<?php include("footer.php"); ?>
</body>
</html>
