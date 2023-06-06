<?php 
/**
 * Funciones de utilidades para actualizar el sistema
 * Autor: SC3 - Marcos Casamayor
 */


function sc3log($xstr)
{
	echo("<br>$xstr");
}


function sc3existeTabla($xtabla)
{
	debug("sc3existeTabla($xtabla)");
	
	$str = "select id
			from sc_querys 
			where table_ = '$xtabla'";

	$rs = new BDObject();	
	$rs->execQuery($str);
	$existe = !$rs->EOF();
	$rs->close();
	return $existe;
}

/*
Retorna si existe la columna en la tabla dada
*/
function sc3existeCampo($xtabla, $xcolumna)
{
	debug("sc3existeCampo($xtabla, $xcolumna)");
	$fields = getFieldsInArray($xtabla, "");
	return in_array($xcolumna, $fields);
}

/*
Actualiza la descripcion del campo
*/
function sc3updateField($xquery, $xfield, $xdesc, $xrequired = 0, $xdefault = "", $xfile_field = 0, $xgrupo = "")
{
	debug("sc3UpdateField($xquery, $xfield, $xdesc, $xrequired)");

	$str = "update sc_fields f, sc_querys q
			set f.show_name = '$xdesc', 
				f.is_required = $xrequired,  
				f.file_field = $xfile_field,
				f.default_value_exp = '$xdefault', 
				f.grupo = '$xgrupo' ";
	
	$str .= " where q.queryname = '$xquery' and 
				f.field_ = '$xfield' and 
				f.idquery = q.id";
	
	$rs = new BDObject();	
	$rs->execQuery2($str);
	$rs->close();
}

/*
Actualiza la descripcion del campo
*/
function sc3updateFieldForTable($xtabla, $xfield, $xdesc, $xrequired = 0, $xdefault = "", $xgrupo = "")
{
	debug("sc3updateFieldForTable($xtabla, $xfield, $xdesc, $xrequired)");

	$str = "update sc_fields f, sc_querys q
			set f.show_name = '$xdesc',
				f.is_required = $xrequired,
				f.default_value_exp = '$xdefault',
				f.grupo = '$xgrupo' ";

	$str .= " where q.table_ = '$xtabla' and
				f.field_ = '$xfield' and
				f.idquery = q.id";

	$rs = new BDObject();
	$rs->execQuery2($str);
	$rs->close();
}


/*
Le pone la ayuda al campo
*/
function sc3updateFieldHelp($xquery, $xfield, $xhelp)
{
	$str = "update sc_fields f, sc_querys q
			set f.field_help = '$xhelp' 
			where q.queryname = '$xquery' and 
				f.field_ = '$xfield' and 
				f.idquery = q.id";

	$rs = new BDObject();	
	$rs->execQuery2($str);
	$rs->close();
}

/*
Le pone subgrupo al campo
*/
function sc3updateFieldSubgrupo($xquery, $xfield, $xsubgroup)
{
	debug("sc3updateFieldSubgrupo($xquery, $xfield, $xsubgroup)");

	$str =  "update sc_fields f, sc_querys q";
	$str .= " set f.subgrupo = '$xsubgroup' ";
	$str .= " where q.queryname = '$xquery'";
	$str .= " 	    and f.field_ = '$xfield'";
	$str .= " 	    and f.idquery = q.id";
	$rs = new BDObject();	
	$rs->execQuery2($str);
	$rs->close();
}

/*
Le pone read only al campo
*/
function sc3updateFieldReadOnly($xquery, $xfield)
{
	debug("sc3updateFieldReadOnly($xquery, $xfield)");

	$str =  "update sc_fields f, sc_querys q
			set f.is_editable = 0 
			where q.queryname = '$xquery' and 
				f.field_ = '$xfield' and 
				f.idquery = q.id";
	$rs = new BDObject();	
	$rs->execQuery2($str);
	$rs->close();
}

/*
Le pone read only al campo
*/
function sc3updateFieldColor($xquery, $xfield)
{
	debug("sc3updateFieldColor($xquery, $xfield)");

	$str =  "update sc_fields f, sc_querys q";
	$str .= " set f.color_field = 1 ";
	$str .= " where q.queryname = '$xquery'";
	$str .= " 	    and f.field_ = '$xfield'";
	$str .= " 	    and f.idquery = q.id";
	$rs = new BDObject();
	$rs->execQuery2($str);
	$rs->close();
}

/**
 * Actualiza que este campo se oculta al estar vacío
 * @param {string} $xquery 
 * @param {string} $xfield
 * @param {int} $xOcultarVacio
 */
function sc3updateFieldOcultarVacio($xquery, $xfield, $xOcultarVacio)
{
	$str = "update sc_fields f, sc_querys q
			set f.ocultar_vacio = $xOcultarVacio
			where q.queryname = '$xquery' and 
				f.field_ = '$xfield' and 
				f.idquery = q.id";

	$rs = new BDObject();	
	$rs->execQuery2($str);
	$rs->close();
}

/**
 * 
 *
 * @param unknown_type $xquery
 * @param unknown_type $xprefix
 * @param unknown_type $xgroup
 */
function sc3updateFieldsDescription($xquery, $xprefix, $xgroup)
{

	$str =  "update sc_fields f, sc_querys q";
	$str .= " set f.show_name = replace(f.show_name, '$xprefix', ''), ";
	$str .= " f.grupo = '$xgroup' ";
	$str .= " where q.queryname = '$xquery'";
	$str .= " 	    and f.show_name like '$xprefix%'";
	$str .= " 	    and f.idquery = q.id";
	$rs = new BDObject();	
	$rs->execQuery2($str);
	$rs->close();
}



function sc3updateFieldRich($xquery, $xfield)
{
	$str = "update sc_fields f, sc_querys q
			set rich_text = 1
			where q.queryname = '$xquery' and 
				f.field_ = '$xfield' and 
				f.idquery = q.id";

	$rs = new BDObject();	
	$rs->execQuery2($str);
	$rs->close();
}

function sc3updateFieldClass($xquery, $xfield, $xclass)
{
	debug("sc3updateFieldClass($xquery, $xfield, $xclass)");

	echo("<br />Actualizando clase $xquery.<b>$xfield</b>:...");
	
	$str = 'update sc_fields f, sc_querys q';
	$str .= ' set class = \'' . $xclass . '\'';
	$str .= " where q.queryname = '$xquery'";
	$str .= " 	    and f.field_ = '$xfield'";
	$str .= " 	    and f.idquery = q.id";
	
	$rs = new BDObject();
	$rs->execQuery2($str);
	$rs->close();
}

function sc3updateFieldGooglePoint($xquery, $xfield)
{
	debug("sc3UpdateField($xquery, $xfield)");

	$str = "update sc_fields f, sc_querys q";
	$str .= " set is_google_point = 1";
	$str .= " where q.queryname = '$xquery'";
	$str .= " 	    and f.field_ = '$xfield'";
	$str .= " 	    and f.idquery = q.id";
	$rs = new BDObject();	
	$rs->execQuery2($str);
	$rs->close();
}

function sc3UpdateQueryProperty($xquery, $xcampo, $xvalor)
{
	debug("sc3QueryProperty($xquery, $xcampo, $xvalor)");

	echo("<br><b>$xquery</b>.$xcampo = $xvalor");
	
	$str = "update sc_querys q";
	$str .= " set $xcampo = $xvalor";
	$str .= " where q.queryname = '$xquery'";
	$rs = new BDObject();	
	$rs->execQuery2($str);

	$rs->close();		
}


function sc3addlink($xquerym, $xcampo, $xquery, $xinmaster = 0)
{
	debug("sc3addlink($xquerym, $xcampo, $xquery, $xinmaster)");

	$str = "select ref.id 
			from sc_referencias ref
				inner join sc_querys qm on (ref.idquerymaster = qm.id)
				inner join sc_querys q on (ref.idquery = q.id)
			where campo_ = '$xcampo' and 
				qm.queryname = '$xquerym' and 
				q.queryname = '$xquery'";

	$rs = new BDObject();	
	$rs->execQuery($str);
	if ($rs->EOF())
	{
		if ($xinmaster != 0)
		{
			$str = "insert into sc_referencias(idquerymaster, campo_, idquery, in_master)
					select distinct qm.id, '$xcampo', qfk.id, $xinmaster";
		}
		else
		{
			$str = "insert into sc_referencias(idquerymaster, campo_, idquery, in_master)
						select distinct qm.id, '$xcampo', qfk.id, 0";
		}
		$str .= "	from sc_querys qm, sc_querys qfk
					where qm.queryname = '$xquerym' and 
						qfk.queryname = '$xquery'";

		$rs = new BDObject();	
		$rs->execQuery($str);
		echo("<br>add link: $xquerym, <b>$xcampo</b>, $xquery ($xinmaster)...");
	}
	$rs->close();
}


function sc3DelFilter($xquery, $xfiltername)
{
	$rs = new BDObject();
	$str = " delete f
			from sc_querys_filters f, sc_querys q
			where (f.idquery = q.id and q.queryname='$xquery') and 
				(f.descripcion = '$xfiltername')";
	$rs->execQuery($str);
	$rs->close();
}

function sc3addFilter($xquery, $xfiltername, $xfilterwhere, $xregenerar = false)
{
	debug("sc3addFilter($xquery, $xfiltername, $xfilterwhere)");

	$rs = new BDObject();	
	
	if ($xregenerar)
	{
		$str = "     delete f";
		$str .= "     from sc_querys_filters f, sc_querys q";
		$str .= "     where (f.idquery = q.id and q.queryname='$xquery') and (f.descripcion = '$xfiltername')";
		$rs->execQuery($str);
	}
	
	$str = "     select *";
	$str .= "     from sc_querys_filters f";
	$str .= "     	inner join sc_querys q on (f.idquery = q.id and q.queryname='$xquery')";
	$str .= "     where descripcion = '$xfiltername'";
	$rs->execQuery($str);
	if ($rs->EOF())
	{
		$str = "insert into sc_querys_filters(idquery, descripcion, filter)";
		$str .= "     select distinct q.id, '$xfiltername', '$xfilterwhere'";
		$str .= "     from sc_querys q";
		$str .= "     where q.queryname = '$xquery'";
		$rs = new BDObject();	
		$rs->execQuery($str, false, false, false);
		echo("<br>agregando filtro: $xquery, <b>$xfiltername</b>: $xfilterwhere...");
	}
	$rs->close();
}

function sc3addFk($xtable1, $xfield1, $xtable2, $xfield2 = "id", $xCascade = false, $xRegenerar = false)
{
	$fk = "FK_{$xtable1}_{$xtable2}_$xfield1";
	$fk = substr($fk, 0, 45);
	$str = "ALTER TABLE $xtable1 ADD CONSTRAINT $fk FOREIGN KEY $fk ($xfield1)";
	$str .= " REFERENCES $xtable2 ($xfield2)";

	if ($xCascade)
	{
		$str .= " ON DELETE CASCADE";
		$str .= " ON UPDATE CASCADE";
	}
	else
	{
		$str .= " ON DELETE RESTRICT";
		$str .= " ON UPDATE RESTRICT";
	}
	$rs = new BDObject();

	//borra la FK
	if ($xRegenerar && $rs->existeFk($xtable1, $fk))
		$rs->dropIndex($xtable1, $fk);

	if (!$rs->existeFk($xtable1, $fk))
	{
		echo("<br>Creando <b>$fk</b>...");
		$rs->execQuery($str);
	}
	$rs->close();

}


function sc3addIndexUq($xtable, $xfields)
{
	$indexName = "UQ_" . $xtable . "_" . str_replace(" ", "", str_replace(",", "_", $xfields));

	/*
	CREATE [UNIQUE|FULLTEXT|SPATIAL] INDEX index_name
	[USING index_type]
	ON tbl_name (index_col_name,...)
	*/
	$str = "CREATE UNIQUE INDEX $indexName on $xtable ($xfields)";
	$rs = new BDObject();
	if (!$rs->existeIndex($xtable, $indexName))
	{
		echo("<br>Agregando Index UQ en <b>$xtable</b> ($xfields)...");
		$rs->execQuery($str);
	}
	$rs->close();
}


function sc3dropFk($xtable1, $xfk)
{
	debug("sc3dropFk($xtable1, $xfk)");
	
	$str = "ALTER TABLE $xtable1 DROP FOREIGN KEY $xfk";
	$rs = new BDObject();

	if ($rs->existeFk($xtable1, $xfk))
	{
		echo("<br>Borrando <b>$xtable1<b>.$xfk...");
		$rs->execQuery($str);
	}
	$rs->close();
}

function sc3dropField($xtable, $xfield)
{
	$str = "ALTER TABLE $xtable DROP column $xfield";
	$rs = new BDObject();

	echo("<br>Borrando campo <b>$xtable.$xfield</b>...");
	$rs->execQuery($str);

	$rs->close();
}

function sc3dropIndex($xtable1, $xindex)
{
	debug("sc3dropIndex($xtable1, $xindex)");

	$rs = new BDObject();

	if ($rs->existeIndex($xtable1, $xindex))
		$rs->dropIndex($xtable1, $xindex);

	$rs->close();
}

/**
 * Genera los campos para todos los querys de la tabla dada
 */
function sc3generateFieldsInfo($xtabla, $xhideId = true)
{
	debug("sc3generateFieldsInfo($xtabla)");
	
	$str = "select * 
			from sc_querys 
	 		where table_ = '" . $xtabla . "'";
	$rs = new BDObject();	
	$rs->execQuery($str);
	while (!$rs->EOF())
	{
		$tc = getCache();
		$query_info = $tc->getQueryInfo($rs->getValue("queryname"));
		saveCache($tc);

		$qinfo = new ScQueryInfo($query_info);
		$qinfo->generateFieldsInfo(true, $xhideId);
		$rs->Next();
	}
	$rs->close();
}


function sc3AgregarMenu($xitem, $xorden, $xfaIcon = "fa-folder-open-o", $xcolor = "#596275")
{
	$rs = locateRecordWhere("sc_menuconsola", "item like '$xitem'");
	if ($rs->EOF())
	{
		$sql = "insert into sc_menuconsola(Item, activo, orden, icon, color)";		
		$sql .= " values('$xitem', 1, $xorden, '$xfaIcon', '$xcolor')";
		
		$bd = new BDObject();	
		$id = $bd->execInsert($sql);
		return $id; 
	}
	$id = $rs->getValue("idItemMenu");
	return $id;
}


function sc3agregarCampoStr80($xtable, $xcampo, $xrequired, $xafter, $xgrupo = "")
{
	return sc3agregarCampoStr($xtable, $xcampo, $xrequired, $xafter, $xgrupo, 80);
}

/*
Agrega un campo de tipo varchar
*/
function sc3agregarCampoStr($xtable, $xcampo, $xrequired, $xafter = "", $xgrupo_NOUSAR = "", $xsize = 60)
{
	debug("sc3agregarCampoStr($xtable, $xcampo, $xrequired)");
	
	if (!sc3existeCampo($xtable, $xcampo))
	{
		echo("<br />agregando campo $xtable.<b>$xcampo</b>...");
		
		$sql = "alter table " . $xtable;
		$sql .= " add column " . $xcampo;
		$sql .= " varchar(" . $xsize . ")";
		if ($xrequired)
			$sql .= " not null default ''";
		else
			$sql .= " null";

		if (!sonIguales($xafter, ""))
			$sql .= " after $xafter";
						
		$rs = new BDObject();	
		$rs->execQuery($sql);
		$rs->close();
	}	
}

/*
Agrega un campo de tipo varchar
*/
function sc3agregarCampoText($xtable, $xcampo, $xrequired, $xafter = "", $xgrupo = "")
{
	debug("sc3agregarCampoText($xtable, $xcampo, $xrequired)");
	
	if (!sc3existeCampo($xtable, $xcampo))
	{
		$sql = "alter table " . $xtable;
		$sql .= " add column " . $xcampo;
		$sql .= " text";
		if ($xrequired)
			$sql .= " not null default ''";
		else
			$sql .= " null";
			
		if (!sonIguales($xafter, ""))
			$sql .= " after $xafter";	

		$rs = new BDObject();	
		$rs->execQuery($sql);
	}	
}

/*
Agrega un campo de tipo int unsigned
*/
function sc3agregarCampoInt($xtable, $xcampo, $xrequired, $xafter = "", $xdefault = "")
{
	debug("sc3agregarCampoInt($xtable, $xcampo, $xrequired, $xafter)");
	if (!sc3existeCampo($xtable, $xcampo))
	{
		echo("<br />agregando campo $xtable.<b>$xcampo</b>...");
		
		$sql = "alter table " . $xtable;
		$sql .= " add column " . $xcampo;
		$sql .= " int unsigned ";
		if ($xrequired)
			$sql .= " not null";
		else
			$sql .= " null";
		if (!sonIguales($xdefault, ""))
			$sql .= " default $xdefault";	
		if (!sonIguales($xafter, ""))
			$sql .= " after $xafter";	
		$rs = new BDObject();	
		$rs->execQuery($sql);
	}	
}


function sc3BorrarCampo($xtable, $xcampo)
{
	debug("sc3BorrarCampo($xtable, $xcampo)");
	
	echo("<br />borrando campo $xtable.<b>$xcampo</b>...");
	
	$sql = "alter table " . $xtable . " ";
	$sql .= " drop column " . $xcampo . "";

	$rs = new BDObject();	
	$rs->execQuery($sql);
}


/*
Agrega un campo de tipo int unsigned que se interpreta como boolean
*/
function sc3agregarCampoBoolean($xtable, $xcampo, $xrequired, $xafter = "", $xdefault = 0)
{
	debug("sc3agregarCampoBoolean($xtable, $xcampo, $xrequired, $xafter, $xdefault)");
	if (!sc3existeCampo($xtable, $xcampo))
	{
		echo("<br />agregando campo $xtable.<b>$xcampo</b>...");
		
		$sql = "alter table " . $xtable;
		$sql .= " add column " . $xcampo;
		$sql .= " tinyint(3) unsigned ";
		if ($xrequired)
			$sql .= " not null";
		else
			$sql .= " null";
		$sql .= " default $xdefault";	
		if (!sonIguales($xafter, ""))
			$sql .= " after $xafter";	
		$rs = new BDObject();	
		$rs->execQuery($sql);
	}	
}

/*
Agrega un campo de tipo FLOAT
*/
function sc3agregarCampoFloatFloat($xtable, $xcampo, $xrequired, $xafter = "")
{
	debug("sc3agregarCampoFloat($xtable, $xcampo, $xrequired, $xafter)");
	if (!sc3existeCampo($xtable, $xcampo))
	{
		echo("<br>agregando campo float $xtable.$xcampo...");
		
		$sql = "alter table " . $xtable;
		$sql .= " add column " . $xcampo;
		$sql .= " float ";
		if ($xrequired)
			$sql .= " not null";
		else
			$sql .= " null";
		if (!sonIguales($xafter, ""))
			$sql .= " after $xafter";	
		$rs = new BDObject();	
		$rs->execQuery($sql);
	}	
}

/*
Agrega un campo de tipo DECIMAL 12, 4
*/
function sc3agregarCampoFloat($xtable, $xcampo, $xrequired, $xafter = "", $xdefault = "", $xdecimals = 2)
{
	debug("sc3agregarCampoFloat($xtable, $xcampo, $xrequired, $xafter)");
	if (!sc3existeCampo($xtable, $xcampo))
	{
		echo("<br>agregando campo decimal(18, $xdecimals) en $xtable.<b>$xcampo </b>...");

		$sql = "alter table " . $xtable;
		$sql .= " add column " . $xcampo;
		$sql .= "  decimal(18, $xdecimals) ";
		if ($xrequired)
			$sql .= " not null";
		else
			$sql .= " null";
		if (!sonIguales($xdefault, ""))
			$sql .= " default $xdefault";
		if (!sonIguales($xafter, ""))
			$sql .= " after $xafter";
		$rs = new BDObject();
		$rs->execQuery($sql);
	}
}

/*
Agrega un campo de tipo fecha
*/
function sc3agregarCampoFecha($xtable, $xcampo, $xrequired, $xafter = "", $xdefaultNO_USAR = false, $xsoloFecha = false)
{
	debug("sc3agregarCampoFecha($xtable, $xcampo, $xrequired, $xafter)");
	if (!sc3existeCampo($xtable, $xcampo))
	{
		echo("<br>agregando campo date $xtable.<b>$xcampo</b>...");
		
		$sql = "alter table " . $xtable;
		$sql .= " add column " . $xcampo;
		
		//en versiones 5.6.5 ya puede ser datetime con default timestamp
		$tipo = " datetime ";
		if ($xsoloFecha)
			$tipo = " date ";
			
		$sql .= $tipo;	
			
		if ($xrequired)
		{
			$sql .= " not null";
		}
		else
			$sql .= " null";
		
		if (!esVacio($xafter))
			$sql .= " after $xafter";
		
		$rs = new BDObject();	
		$rs->execQuery($sql);
	}	
}


function sc3HacerGrupal($xurl, $xquery)
{
	$bd = new BDObject();
	$sql = "update sc_operaciones ope, sc_querys q";
	$sql .= " set ope.grupal = 1, ope.idquery = q.id, ope.condicion = '', ope.idmenu = null";
	$sql .= " where ope.url = '$xurl' and q.queryname = '$xquery'";
	$bd->execQuery($sql);	
}

function sc3updateOperacionCondicion($xurl, $xcondicion, $xtabla = "")
{
	$bd = new BDObject();
	$sql = "update sc_operaciones ope";
	$sql .= " set ope.condicion = '$xcondicion'";
	$sql .= " where ope.url = '$xurl'";
	if (!sonIguales($xtabla, ""))
		$sql .= " and ope.idquery in (select id from sc_querys where table_ = '$xtabla')";
	$bd->execQuery($sql);	
}

	
/**
 * Agrega una operacion a un perfil dado
 */
function sc3AgregarOpAPerfil($xidoperacion, $xnombreperfil)
{
	//DELETE t1 FROM t1 LEFT JOIN t2 ON t1.id=t2.id WHERE t2.id IS NULL;
	
	$sql1 = "delete sc_perfiles_operaciones from sc_perfiles_operaciones pope ";
	$sql1 .= " left join sc_operaciones ope on (pope.idoperacion = ope.id)";
	$sql1 .= " left join sc_perfiles per on (pope.idperfil = per.id)";
	$sql1 .= " where ope.id is null and per.id is null";
	
	//regenera perfiles, sera bueno !?!?!
	$sql1 = "delete from sc_perfiles_operaciones ";
	$sql1 .= " where idoperacion = $xidoperacion and idperfil in (select distinct p.id";
	$sql1 .= " from sc_perfiles p";
	$sql1 .= " where nombre like '$xnombreperfil%')";
	
	$sql = "INSERT INTO `sc_perfiles_operaciones` (`idperfil`, `idoperacion`)"; 
	$sql .= " select distinct p.id, $xidoperacion";
	$sql .= " from sc_perfiles p";
	$sql .= " where nombre like '$xnombreperfil%'";

	if ($xidoperacion != 0)
	{
		$bd = new BDObject();
		$bd->execQuery($sql);
		$bd->close();
	}	
}

	
/**
 * Agrega un query a un perfil dado
 */
function sc3AgregarQueryAPerfil($xquery, $xnombreperfil)
{
	if (esVacio($xnombreperfil))
	{
		echo("<br>ERROR: agregando $xquery a perfil vacio...");
		return 0;
	}
	
	$bd = new BDObject();

	$sql = " select p.id, q.id";
	$sql .= " from sc_perfiles p, sc_querys q, sc_perfiles_querys pq ";
	$sql .= " where p.nombre like '$xnombreperfil%'";
	$sql .= "   and q.queryname = '$xquery'";
	$sql .= "	and pq.idperfil = p.id and pq.idquery = q.id"; 
	
	$bd->execQuery($sql);
	if ($bd->EOF())
	{
		$sql = "INSERT INTO `sc_perfiles_querys` (`idperfil`, `idquery`)"; 
		$sql .= " select distinct p.id, q.id";
		$sql .= " from sc_perfiles p, sc_querys q";
		$sql .= " where p.nombre like '$xnombreperfil%'";
		$sql .= "   and q.queryname = '$xquery'";
		
		$bd->execQuery($sql);
	}
}

/**
 * Agrega una operación 
 * @param string $xoperacion
 * @param string $xurl Nombre del PHP a invocar
 * @param string $ximg Icono
 * @param string $xayuda Descripcion breve de la operación
 * @param string $xtabla Tabla a la que se aplica. Vacio si viene Menú
 * @param string $xmenu Menú de la izquierda a donde aparecerá. Vacío si viene tabla
 * 
 */
function sc3AgregarOperacion($xoperacion, $xurl, $ximg, $xayuda, $xtabla, $xmenu = "", $xgrupal = 0, $xperfil = "", $xtarget = "", $xemergente = 0, $xquery = "")
{
	$id = 0;
	if (!esVacio($xtabla))
	{
		$rs = locateRecordWhere("sc_operaciones", "url = '$xurl' and 
													grupal = $xgrupal and 
													idquery in (select id 
																from sc_querys 
																where table_ = '$xtabla')");
		if ($rs->EOF())
		{
			echo("<br>agregando operacion de (tabla $xtabla) (menu $xmenu) <b>$xoperacion</b> ($xurl)");
		
			$sql = "INSERT INTO sc_operaciones (nombre, idmenu, url, orden, 
										activo, icon, idquery, ayuda, 
										condicion, target, grupal, emergente)
					select '$xoperacion', null, '$xurl', 520, 
								1, '$ximg', q.id, '$xayuda', 
								'', '$xtarget', $xgrupal, $xemergente
					from sc_querys q
					where table_ = '$xtabla' and
						(q.queryname = '$xquery' or '$xquery' = '') ";
				
			$bd = new BDObject();
			$id = $bd->execInsert($sql);
			if (!esVacio($xperfil) && $id != 0)
			{
				echo($id);
				sc3AgregarOpAPerfil($id, $xperfil);
			}
			$bd->close();
		}
		$rs->close();
	}
	
	if (!esVacio($xmenu))
	{
		$rs = locateRecordWhere("sc_operaciones", "url = '$xurl' and idmenu is not null");
		if ($rs->EOF())
		{
			echo("<br>agregando operacion de menu $xmenu / <b>$xoperacion</b> ($xurl)");
		
			$sql = "INSERT INTO `sc_operaciones` (`nombre`, `idmenu`, `url`, `orden`, `activo`, `icon`, `idquery`, `ayuda`, `condicion`, `target`, `grupal`)";
			$sql .= " select '$xoperacion', m.iditemmenu, '$xurl', 520, 1, '$ximg', null, '$xayuda', '', '$xtarget', 0";
			$sql .= " from sc_menuconsola m";
			$sql .= " where item like '$xmenu'";
			
			$bd = new BDObject();
			$id = $bd->execInsert($sql);
			if (!esVacio($xperfil))
			{
				echo($id);
				sc3AgregarOpAPerfil($id, $xperfil);
			}
			$bd->close();
		}
		$rs->close();
	}
	return $id;
}


/**
 * Agrega un query a un perfil dado
 */
function sc3AgregarPerfil($xnombreperfil)
{
	$bd = new BDObject();

	$sql = " select id
			from sc_perfiles 
			where nombre like '$xnombreperfil'";
	
	$bd->execQuery($sql);
	if ($bd->EOF())
	{
		$sql = "INSERT INTO sc_perfiles (nombre) 
				values ('$xnombreperfil')"; 
		$bd->execQuery($sql);
	}

	$bd->close();
}



/*
Agrega un qry con la tabla dada
*/
function sc3agregarQuery($xqueryname, $xtabla, $xdescripcion, $xmenuName, $xcomboField, $xinsert, $xedit, $xdelete, $xorder = "", $xcantcampos = 9, $xicon = "", $xdebil = 0, $xskipfield = "")
{
	debug("sc3agregarQuery($xqueryname, $xtabla, $xdescripcion, $xmenuName, $xcomboField, $xinsert, $xedit, $xdelete, $xorder, $xcantcampos, $xicon)");
	
	$bd = new BDObject();	
	$iditemmenu = "null";
	if (!sonIguales($xmenuName, ""))
	{
		$rs = locateRecordWhere("sc_menuconsola", "item like '$xmenuName%'");
		$iditemmenu = $rs->getValue("idItemMenu");	
		if (sonIguales($iditemmenu, ""))
			$iditemmenu = "null";
	}
	
	$values = array_fill(1, 15, "null");
	//en las debiles, intenta esquivar el campo ID
	if ($xdebil > 0 && sonIguales($xskipfield, ""))
		$xskipfield = "id";
	$fields = getFieldsInArray($xtabla, $xskipfield, $xcantcampos);
	
	$rsq = locateRecordWhere("sc_querys", "queryname = '$xqueryname'");
	if ($rsq->EOF())
	{
		if (esVacio($xcomboField))
			$xcomboField = $fields[1];
		if (esVacio($xorder))
			$xorder = $fields[1];

		echo("<br />agregando query $xqueryname...");

		$sql = "insert into sc_querys(`queryname`, `querydescription`, `table_`, `fields_`, 
									`combofield_`, `keyfield_`, `whereexp`, 
									`whereeval`, `order_by`, `caninsert`, `canedit`, 
									`candelete`, `idmenu`, `icon`, `debil`)";
		$sql .= " values(";
		$values[1] = "'$xqueryname'";
		$values[2] = "'" . $xdescripcion . "'";
		$values[3] = "'" . $xtabla . "'";
		$values[4] = "'" . implode(", ", $fields) . "'";
		$values[5] = "'" . $xcomboField . "'";
		
		//key field: el primero !
		if (sonIguales($xskipfield, "id"))
			$values[6] = "'id'"; 
		else
			$values[6] = "'" . $fields[0] . "'";  
		
		$values[9] = "'$xorder'";
		
		//insert, update, delete
		$values[10] = $xinsert; 
		$values[11] = $xedit;
		$values[12] = $xdelete;
	
		$values[13] = $iditemmenu;
		$values[14] = "'$xicon'";
		$values[15] = $xdebil;
		$sql .= implode(", ", $values) . ")";
		$bd->execQuery($sql);
	}
}

/**
 * Regenera los campos que van al selitems 
 * @param string $xquery
 * @param string $xtabla
 * @param string $xskipfield
 * @param number $xcant
 */
function sc3UpdateQueryFields($xquery, $xtabla, $xskipfield = "", $xcant = 10)
{
	$fields = getFieldsInArray($xtabla, $xskipfield, $xcant);
	$campos = "'" . implode(", ", $fields) . "'";
	sc3UpdateQueryProperty($xquery, "fields_", $campos);
}

/**
 * Actualiza los campos requeridos de una tabla en funcion del NOT NULL de la tabla
 * @param string $xtable
 */
function sc3UpdateRequeridos($xtable)
{
	sc3generateFieldsInfo($xtable);
	
	$sql = "show create table $xtable";
	$db = new BDObject();
	$db->execQuery($sql);
	$strTable = $db->getValue("Create Table");
	
	$afields = explode("\n", $strTable);
	foreach ($afields as $index => $field) 
	{
		//descarta lineas del create table que no son def de campos
		if (!strContiene($field, "engine") && !strContiene($field, "primary") && !strContiene($field, "auto_increment")
			&& !strContiene($field, "create table"))
		{
			$afieldsParts = explode("`", $field);
			$campo = "";
			if (count($afieldsParts) > 1)
			{
				$campo =  trim($afieldsParts[1]);
			
				//echo("<br> analizando campo $campo");
				if (strContiene($afieldsParts[2], "NOT NULL"))
				{
					echo("<br> $xtable.$campo: requerido..."); 
					
					$sql = "update sc_fields f, sc_querys q";
					$sql .= " set is_required = 1";
					$sql .= " where f.idquery = q.id and q.table_ = '$xtable' and f.field_ = '$campo' and (file_field = 0 or file_field is null)";
					
					$db->execQuery($sql);
				}
			}
		}
	}
}


function sc3UpdateAllRequeridos()
{
	$param = "sc3-notnulls-updated";
	$notnulls = getParameterInt($param, "0");
	if ($notnulls == 0)
	{
		$sql = "select distinct table_ 
				from sc_querys 
				order by table_";
		$rs = new BDObject();
		$rs->execQuery($sql);
		while (!$rs->EOF())
		{
			sc3UpdateRequeridos($rs->getValue("table_"));
			$rs->Next();		
		}
		saveParameter($param, "1");
	}
}


/**
 * sc3GetUltimoId
 * Obtiene el último ID. de una tabla
 * @param  mixed $tabla Nombre de la tabla
 * @return int Ultimo ID.
 */
function sc3GetUltimoId($xtabla, $xfield) {

	$sql = "SELECT MAX( $xfield ) AS ultimoID FROM $xtabla";
	$rs = getRs($sql);
	return $rs->getValue("ultimoID");
}


/**
 * sc3ConvertirAAutoincrement
 * Convierte un campo a auto_increment.
 * @param  mixed $tabla
 * @param  mixed $field
 * @return void
 */
function sc3ConvertirAAutoincrement($xtabla, $xfield) {
	$bd = new BDObject();

	$sql = "ALTER TABLE $xtabla CHANGE COLUMN $xfield $xfield INT(11) NOT NULL AUTO_INCREMENT";
	$bd->execQuery($sql);
	$bd->close();
}


/**
 * sc3InicializarAutoincrement
 * Permite inicializar el campo autoincremental de una tabla en un valor
 * determinado.
 * @param  mixed $xtabla    Tabla a inicializar
 * @param  mixed $xvalor    Valor inicial
 * @return void
 */
function sc3InicializarAutoincrement($xtabla, $xvalor) {

	$sql = "ALTER TABLE $xtabla AUTO_INCREMENT = $xvalor";

	$bd = new BDObject();
	$bd->execQuery($sql);
	$bd->close();
}


/**
 * sc3DropPK
 * Elimina la clave primaria de una tabla
 * @param  mixed $xtabla
 * @return void
 */
function sc3DropPK($xtabla) {

	$sql = "ALTER TABLE $xtabla DROP PRIMARY KEY";
	$bd = new BDObject();
	$bd->execQuery($sql);
	$bd->close();
}


function sc3CambiarAAutoIncrementYGenerarPK($xtabla, $xfield) {

	$sql = "ALTER TABLE $xtabla CHANGE COLUMN $xfield $xfield INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, 
				ADD PRIMARY KEY ($xfield)";
	$bd = new BDObject();
	$bd->execQuery($sql);
	$bd->close();
}

/**
 * sc3ConfigurarCamposAMostrar
 * Configura los campos a mostrar en la grilla de un Query.
 * @param  mixed $xquery Nombre del query a modificar
 * @param string $xfieldsList Lista de campos a mostrar en el grid separado por comas.
 * @return void
 */
function sc3ConfigurarCamposAMostrar($xquery, $xfieldsList) {
	echo "<br>Configurando campos a mostrar en $xquery";
	
	$sql = "UPDATE sc_querys 
			SET fields_ = '$xfieldsList'
			WHERE queryname = '$xquery'";

	$bd = new BDObject();
	$bd->execQuery($sql);
	$bd->close();
}


//Funcion para borrar archivos, recibe como parametro la carpeta donde estan guardados los mismos
function ejecutarSps($dir) {

	$bd = new BDObject();

	//valida no borrar carpeta vacía (otra vez)
	if (isset($dir) && $dir != "" && $dir != " "){
		$path = realpath($dir);
		if ($path !== false && is_dir($path)) {
			echo("<br><br>Recuperando archivos en la carpeta $dir");

			foreach (glob($dir . 'fn*.sql') as $v) {
				echo("<br>Por ejecutar <b>$v</b>");

				$sql = file_get_contents($v);
				$sp = str_replace(".sql", "", basename($v));
				$bd->execQuery("DROP function IF EXISTS $sp;");
				$bd->execQuery($sql);
			}

			foreach (glob($dir . 'sp*.sql') as $v) {
				echo("<br>Por ejecutar <b>$v</b>");

				$sql = file_get_contents($v);
				$sp = str_replace(".sql", "", basename($v));
				$bd->execQuery("DROP PROCEDURE IF EXISTS $sp;");
				$bd->execQuery($sql);

			}
		}
	} else
		echo("<br>La carpeta " . $dir . " no existe!");
}


/**
 * sc3GetIDQueryByName
 * Levanta el ID. Query para ejecutar UPDATEs sobre la tabla sc_querys
 * @param  string $xqueryname Nombre del query
 * @return int Id. de query
 */
function sc3GetIDQueryByName($xqueryname) {
    $sql = "SELECT * 
			FROM sc_querys 
			WHERE queryname = '$xqueryname'";
    $rs = getRs($sql);
    return $rs->getId();
}

/**
 * sc3SetMenuAQuery
 * Establece un menú a un determinado query.
 * @param  string $xqueryname
 * @param  string $xmenuName
 * @return void
 */
function sc3SetMenuAQuery($xqueryname, $xmenuName) {
	$sql = "SELECT *
			FROM sc_menuconsola 
			WHERE Item = '$xmenuName'";
	$rsMenu = getRs($sql);
	$idMenu = $rsMenu->getValueInt("idItemMenu");
	$rsMenu->close();
	$sql = "UPDATE sc_querys 
			SET idmenu = $idMenu
			WHERE queryname = '$xqueryname'";
	$bd = new BDObject();
	$bd->execQuery($sql);
	$bd->close();
}
?>