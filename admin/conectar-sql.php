<?php 
include("funcionesSConsola.php");
include("dbobject-sql.php");

$bd = new BDObjectSQL("SERVER-I7\SQLEXPRESS", "mario", "mario", "Seguros");

echo("<br>conectado..");

$sql = "select c.Cli_Email, c.Cli_Cod, c.Cli_Nombre, 
			p.Oper_Poliza,
			p.Oper_Nic,
			p.Oper_VigHasta,
			Z.CuoCli_Nro,
			Z.CuoCli_Vto,
			Z.CuoCli_Importe 
		from Clientes c,
			Polizas p 
			OUTER APPLY(select top 1 cu.CuoCli_Nro, cu.CuoCli_Vto, cu.CuoCli_Importe 
					from CuotasCli cu 
					where p.Oper_Nic = cu.CuoCli_Nic and cu.CuoCli_Vto >= CAST(CONVERT(VARCHAR(10),GETDATE(),105) as DATETIME) and 
						cu.CuoCli_Importe > 0 and cu.CuoCli_Saldo > 0 order by CuoCli_Vto ASC) Z 
		where c.Cli_Cod = p.Oper_Cliente and CAST(CONVERT(VARCHAR(10),GETDATE(),105) as DATETIME) 
			between p.Oper_VigDesde and p.Oper_VigHasta and p.Oper_Operacion IN (1,2) and 
			p.Oper_Poliza <> '' filtroSoloClienteConEmail and 
			Z.CuoCli_Nro is not null and 
			Z.CuoCli_Vto <= CAST(CONVERT(VARCHAR(10),GETDATE() + filtroCantDias,105) as DATETIME) 
		order by c.Cli_Nombre asc";

$sql = "select top 1000 c.Cli_Email, c.Cli_Cod, c.Cli_Nombre
		from Clientes
		where Cli_Email is not null and 
			Cli_Email like '%@%'";
		
$bd->execQuery($sql);
echo("<br>ejecutado..");
while (!$bd->EOF())
{
	echo("<br>" . $bd->getValue("Cli_Nombre") . " - " . $bd->getValue("Cli_Email"));
	$bd->Next();
}


?>