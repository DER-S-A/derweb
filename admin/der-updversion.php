<?php
/**
 * Este script contiene la actualización de versión del sistema
 * DERWEB.
 */

require("funcionesSConsola.php");
require("sc-updversion-utils.php");
require("sc-updversion-sc3.php");
require("app/upd-version-derwebapp/upd-version-derweb-utils.php");
require("app/update-version/upd-version.php");
require("app/update-version/upd-version-entidades.php");
require("app/update-version/upd-version-articulos.php");
include("der-updversion-clientes-potenciales.php");
require("app/update-version/upd-version-pedidos.php");
require("app/update-version/upd-version-ofertas.php");
require("app/update-version/upd-version-rentabilidades.php");
require("app/update-version/upd-version-avisos-pagos.php");

// DERWEB Core
agregarOperGenerarEndPoint();
agregarPerfiles();

// Clientes potenciales.
agregarOperCliPot_CambiarEstado();
agregarOperCliPot_AgregarNotas();
agregarCpoCliPotRegistro();

// Actualización de entidades
UpdateVersionEntidades::actualizarTablaSucursales();
UpdateVersionEntidades::actualizarFormaEnvio();
UpdateVersionEntidades::agregarCamposTiposEntidades();
UpdateVersionEntidades::cambiarTamanioCampoUsuarioEnEntidades();
UpdateVersionEntidades::instalarOpEstablecerClave();

// Actualizo la versión del módulo de pedidos.
UPDVersionPedidos::actualizar();
UpdateVersionArticulos::actualziar();

// DERWEB APP Vendedores
UpdateVersion::instalarOpPedidosPendientes();

// DERWEB Ofertas
UpdateVersionOfertas::actualizar();

UpdateVersionRentabilidades::actualizar();
UpdateAvisosDePagos::actualizar();

// Actualización de SPs.
ejecutarSps("sp/");
echo("<br>");
Sc3FileUtils::borrarArchivos("tmp/");
Sc3FileUtils::borrarArchivos("tmpcache/");
Sc3FileUtils::borrarArchivos("scripts/tmpcache/");
Sc3FileUtils::borrarArchivos("css/tmpcache/");

/**
 * agregarOperGenerarEndPoint
 * Agrega la operación que permite generar el código base para un EndPoint.
 * @return void
 */
function agregarOperGenerarEndPoint() {
	$opid = sc3AgregarOperacion(
		"Agregar EndPoint", 
		"der-agregar-end-point.php", 
		"images/code.gif", 
		"Permite generar el código de un EndPoint a partir del nombre de una tabla.", 
		"", 
		"Desarrollador", 
		0, 
		"Root", 
		"", 
		0, 
		"");
}

function agregarPerfiles() {
	sc3AgregarPerfil("Administración");
}
?>