<?php


/*
Maneja perfiles, menús, consultas de datos relacionados y si el usuario actual tiene un perfil dado
*/
class SecurityManager
{
	function __construct()
	{
	}

	/*
	Retorna las operaciones hijas de un menu para el usuario actual
	*/
	function getRsQuerys($xidmenu = "", $xfiltro = "", $xdeBuscador = false)
	{
		$sql = "SELECT distinct q.*
				FROM sc_querys q
					inner join sc_perfiles_querys pq on (pq.idquery = q.id)
					inner join sc_usuarios_perfiles up on (pq.idperfil = up.idperfil)
				where up.idusuario = " . getCurrentUser();

		if (!sonIguales($xidmenu, ""))
			$sql .= " and q.idmenu = " . $xidmenu;
		if (!sonIguales($xfiltro, "")) {
			$sql .= " and q.querydescription like '%$xfiltro%'";
		}
		if ($xdeBuscador) {
			$sql .= " and q.idmenu is not null";
		}
		$sql .= " order by q.querydescription";
		$sql .= " limit 1000";

		$rs = new BDObject();
		$rs->execQuery($sql);
		return $rs;
	}

	/*
	Retorna las operaciones hijas de un menu para el usuario actual
	*/
	function getRsQuerysForHelp($xfiltro)
	{
		$sql = "SELECT distinct q.*,
					case when men.item is not null then men.item else concat(men2.item, ' / ', qmaster.querydescription, ' (ubicar dato)') end as menu,
					case when men.item is null then 0 else 1 end as acceso_directo, 
					case when up.idperfil is null then 0 else 1 end as tiene_permiso ";

		$sql .= " FROM sc_querys q
					left join sc_menuconsola men on (men.idItemMenu = q.idmenu)
					left join sc_perfiles_querys pq on (pq.idquery = q.id)
					left join sc_usuarios_perfiles up on (pq.idperfil = up.idperfil)";

		//buscando de donde se llega si no es por menu
		$sql .= "   left join sc_referencias ref on (ref.idquerymaster = q.id)
					left join sc_querys qmaster on (ref.idquery = qmaster.id)
					left join sc_menuconsola men2 on (men2.idItemMenu = qmaster.idmenu)
				where up.idusuario = " . getCurrentUser();

		$sql .= " and q.querydescription like '%$xfiltro%'";
		$sql .= " order by men.item, men2.item, q.querydescription";

		$rs = new BDObject();
		$rs->execQuery($sql);
		return $rs;
	}

	/*
	Retorna los querys que aparecen en info
	*/
	function getRsQuerysEnInfo($xidmenu = "", $xfiltro = "", $xdeBuscador = false)
	{
		$sql = "SELECT distinct q.*
				FROM sc_querys q
				   inner join sc_perfiles_querys pq on (pq.idquery = q.id)
				   inner join sc_usuarios_perfiles up on (pq.idperfil = up.idperfil)
				where up.idusuario = " . getCurrentUser();

		$sql .= "   and q.info = 1";
		$sql .= "   and q.idmenu is not null";
		$sql .= " order by q.querydescription";

		$rs = new BDObject();
		$rs->execQuery($sql);
		return $rs;
	}


	/**
	 * Retorna Mis Favoritos para el usuario actual
	 * @return BDObject
	 */
	function getRsQuerysEnEscritorio()
	{
		$sql = "SELECT distinct q.*, pref.valor2";
		$sql .= " FROM sc_querys q";
		$sql .= "   inner join sc_perfiles_querys pq on (pq.idquery = q.id)";
		$sql .= "   inner join sc_usuarios_preferencias pref on (pref.atributo = 'desktop'";
		$sql .= "												and pref.idusuario = " . getCurrentUser();
		$sql .= "												and pref.valor1 = q.queryname)";
		$sql .= "   inner join sc_usuarios_perfiles up on (pq.idperfil = up.idperfil)";
		$sql .= " where up.idusuario = " . getCurrentUser();

		$sql .= "   and q.idmenu is not null";
		$sql .= " order by q.querydescription";

		$rs = new BDObject();
		$rs->execQuery($sql);
		return $rs;
	}

	/**
	 * Retorna Mis Favoritos para el usuario actual (querys y ops)
	 * @return BDObject
	 */
	function getRsFavoritos()
	{
		$sql = "SELECT distinct 'Q' as tipo, q.id, q.querydescription, q.queryname, 
					q.icon, '' as target, pref.valor2, 0 as acceso_offline
				FROM sc_querys q
					inner join sc_perfiles_querys pq on (pq.idquery = q.id)
					inner join sc_usuarios_preferencias pref on (pref.atributo = 'desktop' and 
																pref.idusuario = :IDUSUARIO	and 
																pref.valor1 = q.queryname)";
		$sql .= "   inner join sc_usuarios_perfiles up on (pq.idperfil = up.idperfil)";
		$sql .= " where up.idusuario = :IDUSUARIO and 
					q.idmenu is not null";

		$sql .= " union ";

		$sql .= "SELECT distinct 'O' as tipo, o.id, o.nombre, o.url, 
					o.icon, o.target, o.ayuda, o.acceso_offline";
		$sql .= " FROM sc_operaciones o";
		$sql .= "   inner join sc_perfiles_operaciones po on (po.idoperacion = o.id)";
		$sql .= "   inner join sc_usuarios_preferencias pref on (pref.atributo = 'desktop-op'";
		$sql .= "												and pref.idusuario = " . getCurrentUser();
		$sql .= "												and pref.valor1 = o.id)";
		$sql .= "   inner join sc_usuarios_perfiles up on (po.idperfil = up.idperfil)";
		$sql .= " where up.idusuario = " . getCurrentUser();

		$rs = new BDObject();
		$rs->execQuery($sql);
		return $rs;
	}

	/**
	 * Retorna Mis Notas para el escritorio
	 *
	 * @return BDObject
	 */
	function getRsMisNotas()
	{
		$sql = "select distinct u.login, q.querydescription, q.queryname, 
					q.icon, a.nota, a.color, a.iddato, a.id as idadjunto, a.fecha
				from sc_adjuntos a
					inner join sc_usuarios u on (u.login = a.usuario)
					inner join sc_querys q on (a.idquery = q.id)
					inner join sc_perfiles_querys pq on (pq.idquery = q.id)
					inner join sc_perfiles per on (pq.idperfil = per.id)
					inner join sc_usuarios_perfiles pusu on (pusu.idperfil = per.id and pusu.idusuario = :IDUSUARIO)
				where (a.nota <> '')
				
				order by fecha desc, idadjunto desc
				limit 40";

		$rs = new BDObject();
		$rs->execQuery($sql);
		return $rs;
	}


	/**
	 * Retorna las operaciones de un menu para el usuario actual
	 * @param int $xidmenu 
	 * @return BDObject
	 */
	function getRsOperaciones($xidmenu)
	{
		$sql = "select distinct o.*
					from sc_operaciones o
						inner join sc_perfiles_operaciones po on (po.idoperacion = o.id)
						inner join sc_usuarios_perfiles up on (po.idperfil = up.idperfil)";

		$sql .= " where o.idmenu = $xidmenu and 
					o.activo = 1 and
					up.idusuario = " . getCurrentUser();

		$sql .= " order by o.nombre";

		$rs = new BDObject();
		$rs->execQuery($sql);
		return $rs;
	}


	/**
	 * Retorna las operaciones para el usuario actual que van al escritorio
	 * @return BDObject
	 */
	function getRsOperacionesPantallaInicial()
	{
		$sql = "select distinct o.*

				from sc_operaciones o
					inner join sc_perfiles_operaciones po on (po.idoperacion = o.id)
					inner join sc_usuarios_perfiles up on (po.idperfil = up.idperfil)
				
				where o.activo = 1 and
					o.idquery is null and
					o.pantalla_inicial = 1 and
					up.idusuario = :IDUSUARIO

				order by o.nombre";

		$rs = new BDObject();
		$rs->execQuery($sql);
		return $rs;
	}

	/**
	 * Retorna las operaciones marcadas para ser accedidas fuera de linea
	 * @return BDObject
	 */
	function getRsOperacionesOffline()
	{
		$sql = "select distinct o.*

				from sc_operaciones o
					inner join sc_perfiles_operaciones po on (po.idoperacion = o.id)
					inner join sc_usuarios_perfiles up on (po.idperfil = up.idperfil)
				
				where o.activo = 1 and
					o.idquery is null and
					o.acceso_offline = 1 and
					up.idusuario = :IDUSUARIO

				order by o.nombre";

		$rs = new BDObject();
		$rs->execQuery($sql);
		return $rs;
	}

	/**
	 * Retorna las operaciones validas para un usuario en un query dado
	 * @return BDObject
	 */
	function getRsOperacionesQuery($xidquery, $xgrupales = 0)
	{
		$sql = "select distinct o.*
		
				from sc_operaciones o

					inner join sc_perfiles_operaciones po on (po.idoperacion = o.id)
					inner join sc_usuarios_perfiles up on (po.idperfil = up.idperfil)

				where activo = 1 and 
					grupal = $xgrupales and 
					idquery = $xidquery and 
					up.idusuario = :IDUSUARIO

				order by o.nombre";

		$rs = new BDObject();
		$rs->execQuery($sql);
		return $rs;
	}

	/**
	 * Retorna las operaciones del "detalle". el inline indica que trae las para la edicion inline
	 */
	function getRsOperacionesRelacionadas($xidquery, $xinmaster = 0)
	{
		$sql = "SELECT distinct q.id, q.queryname, 
						q.querydescription, 
						q.icon, 
						ref.campo_ as mfield
						
				FROM sc_querys q
					inner join sc_perfiles_querys pq on (pq.idquery = q.id)
					inner join sc_usuarios_perfiles up on (pq.idperfil = up.idperfil)
					inner join sc_referencias ref on (q.id = ref.idquerymaster and 
													ref.idquery = $xidquery)

				where up.idusuario = :IDUSUARIO and 
					ref.in_master = $xinmaster

				order by q.querydescription";

		$rs = new BDObject();
		$rs->execQuery($sql);
		return $rs;
	}

	/**
	 * Retorna las operaciones del "detalle". retorna todas, tenga o no permisos ya que son para borrar
	 */
	function getRsOperacionesRelacionadasParaBorrar($xidquery)
	{
		$sql = "SELECT distinct q.id, q.queryname, q.querydescription, q.icon, ref.campo_ as mfield, q.table_ as tabla";
		$sql .= " FROM sc_querys q
					inner join sc_referencias ref on (q.id = ref.idquerymaster
										and ref.idquery = " . $xidquery . ")";

		$sql .= " order by q.querydescription";

		$rs = new BDObject();
		$rs->execQuery($sql);
		return $rs;
	}


	/**
	 * Recupera los filtros 
	 */
	function getRsFiltrosUsuarios($xquery)
	{
		//busca un filtro DE presupuestos para el usuario
		$sqlF = "select uf.id, uf.idusuario, uf.idquery, uf.filter
				from sc_usuarios_filtros uf
				  inner join sc_querys q on (q.id = uf.idquery)
				where uf.idusuario = :IDUSUARIO and
					q.queryname = '$xquery'";
		$rsF = new BDObject();
		$rsF->execQuery($sqlF);

		return $rsF;
	}


	/*
	Retorna las operaciones para navegar los "MASTERS" o los datos relacionados al actual
	*/
	function getRsOperacionesMasters($xidquery)
	{
		debug("SecurityManager::getRsOperacionesMasters($xidquery)");
		$sql = "SELECT distinct q.id, q.queryname, q.querydescription, q.icon
				FROM sc_querys q
					inner join sc_perfiles_querys pq on (pq.idquery = q.id)
					inner join sc_usuarios_perfiles up on (pq.idperfil = up.idperfil)
					inner join sc_referencias ref on (q.id = ref.idquery and ref.idquerymaster = " . $xidquery . ")";

		$sql .= " where up.idusuario = " . getCurrentUser();
		$sql .= " order by q.querydescription";

		$rs = new BDObject();
		$rs->execQuery($sql);
		return $rs;
	}


	/*
	Retorna el menu del sistema, pero con los modulos que el usuario puede acceder
	*/
	function getMenuSc3($xMenu = "")
	{
		$sql = "SELECT distinct m.* 
				FROM sc_menuconsola m ";

		$sql .= " WHERE (activo = 1) and 
					(m.item = '$xMenu' or '$xMenu' = '') and
					(m.iditemmenu in (select distinct o.idmenu
									from sc_operaciones o
										inner join sc_perfiles_operaciones po on (po.idoperacion = o.id)
										inner join sc_usuarios_perfiles up on (po.idperfil = up.idperfil)
									where activo=1 and o.idmenu is not null and 
										up.idusuario = " . getCurrentUser() . " ) 
						or (m.iditemmenu in (SELECT distinct q.idmenu
											FROM sc_querys q
												inner join sc_perfiles_querys pq on (pq.idquery = q.id)
												inner join sc_usuarios_perfiles up on (pq.idperfil = up.idperfil)
											where up.idusuario = " . getCurrentUser() . "))) ";

		$sql .= " ORDER BY m.orden";

		$rs = new BDObject();
		$rs->execQuery($sql);
		return $rs;
	}


	/**
	 * Verifica si el usuario actual tiene el perfil dado
	 * @param string $xperfil
	 * @return boolean
	 */
	function tienePerfil($xperfil)
	{
		$sql = "select up.id, idusuario, idperfil, p.nombre
				from sc_usuarios_perfiles up
				  inner join sc_perfiles p on (up.idperfil = p.id)
				where p.nombre = '$xperfil' and 
					up.idusuario = :IDUSUARIO";

		$rs = new BDObject();
		$rs->execQuery($sql);
		$rs->close();
		if ($rs->EOF())
			return false;

		return true;
	}


	/**
	 * Verifica si el usuario tiene acceso a un Query dado
	 * @param string $xquery
	 * @return boolean
	 */
	function tienePermisoQuery($xquery)
	{
		$sql = "select p.nombre, q.queryname
				from sc_usuarios_perfiles up
					inner join sc_perfiles p on (up.idperfil = p.id)
					inner join sc_perfiles_querys pq on (pq.idperfil = p.id)
					inner join sc_querys q on (pq.idquery = q.id)
				where q.queryname = '$xquery' and up.idusuario = :IDUSUARIO
				limit 1";

		$rs = new BDObject();
		$rs->execQuery($sql);
		$rs->close();
		if ($rs->EOF())
			return false;
		return true;
	}

	/**
	 * Retorna si tiene acceso a esta operacion
	 * @return boolean
	 */
	function tienePermisoOperacion($xurl)
	{
		$sql = "select distinct o.*
				from sc_operaciones o
					inner join sc_perfiles_operaciones po on (po.idoperacion = o.id)
					inner join sc_usuarios_perfiles up on (po.idperfil = up.idperfil)
				where o.activo = 1 and 
					o.url = '$xurl' and
					up.idusuario = :IDUSUARIO
				limit 1";

		$rs = new BDObject();
		$rs->execQuery($sql);
		$tiene = !$rs->EOF();
		$rs->close();
		return $tiene;
	}


	/*
	Chequea que el url solicitado esta permitido. Caso contrario desvia a una pagina de error
	*/
	function checkUrl()
	{
		$params = $_SERVER['QUERY_STRING'];
		$script = $_SERVER['SCRIPT_NAME'];

		header("Location:./sc-url-nopermitida.php");
		exit;
	}
}
