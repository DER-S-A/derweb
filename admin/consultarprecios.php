<?php
include("funcionesSConsola.php");

// registra poder llamar a la funcion desde javascript
$ajaxH = sc3GetAjaxHelper();
$ajaxH->registerFunction("stoRsArticulosConsulta", "app-sto.php");
$ajaxH->registerFunction("stoRsRubrosConsulta", "app-sto.php");
sc3SaveAjaxHelper($ajaxH);
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<title>Consultar Precios</title>

	<?php include("include-head.php"); ?>

	<link rel="stylesheet" href="terceros/bootstrap-4.0.0/css/bootstrap.min.css">
	<script src="scripts/jquery-3.2.1.slim.min.js"></script>
	<script src="terceros/bootstrap-4.0.0/js/bootstrap.js"></script>

	<script>

	</script>
	<style>
		.seccion-title {
			height: 60px;
			margin-bottom: 5px;
			padding-top: 10px;
			color: #fff;
			background-color: #5083a6 !important;
		}

		.td_monto {
			font-size: 16px;
			color: #2196F3;
			padding: 3px;
			background-color: white;
		}

		.info {
			font-size: 10px;
			background-color: white;
			color: teal;
			padding: 6px;
			margin: 4px;
			border-radius: 3px;
		}

		.footer-sistema {
			color: #fff !important;
			background-color: #88CF8D;
			padding: 10px;
		}

		.footer-sistema a {
			color: #fff !important;
			text-decoration: none;
		}

		@media screen and (max-width: 768px) {
			.hide-small {
				display: none
			}

			#rubro_2 {
				display: none
			}
		}
	</style>

</head>

<body onload="document.getElementById('busqueda').focus();">

	<div id="app">
		<div class="container-fluid seccion-title">
			<div class="row">
				<div class="col" style="text-align: center; max-width: 80px;">
					<img src="app/logo.png" height="40" />
				</div>
				<div class="col" style="text-align: center;">
					<h3>Precios</h3>
				</div>
				<div class="info" v-html="infoHr" title="Datos vigentes a esta hr">
					--:--
				</div>
			</div>
		</div>
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-9 input-group mb-3">
					<input id="busqueda" type="text" class="form-control" placeholder="Buscar articulo" v-model="busqueda" aria-label="Buscar articulo" aria-describedby="basic-addon2" onfocus="sc3SelectAll('busqueda');">
				</div>
			</div>
			<div class="row">
				<div class="col table-responsive">
					<table class="table table-bordered">
						<thead class="thead-dark">
							<tr>
								<th v-for="(header, index) of aHeaderTable" :id="'rubro_' + index">{{header}}</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(articulo, index) of buscarArticulo" :id="'art_' + index">
								<td :id="articulo.id">{{articulo.id}}</td>
								<td class="izquierda">{{articulo.nombre}}</td>
								<td class="derecha hide-small">{{articulo.codigo}}</td>
								<td class="izquierda">

									<div v-if="verificarFoto(articulo.foto)">
										<img width="80px" v-bind:src="'ufiles/'+articulo.foto">
									</div>
									<div v-else>
										<img width="70px" v-bind:src="'images/nofoto.jpg'">
									</div>

								</td>
								<td style="width: 130px;" class="td_monto derecha">${{articulo.precio}}</td>
								<td style="width: 130px;" class="derecha">{{articulo.mayorista}}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="footer-sistema">
		<a href="https://www.sc3.com.ar" target="_blank">
			<img src="images/sc3-logo45x45.png" height="40" />
		</a>
	</div>

	<script src="scripts/vue.min.js"></script>
	<!-- script src="scripts/axios.min.js"></script -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.16.1/axios.min.js"></script>

	<script>
		new Vue({
			el: '#app',
			data: {
				aRubros: [],
				aArticulos: [],
				aHeaderTable: ["id", "Articulo", "Codigo", "Foto", "Final", "Bultos"],
				busqueda: "",
				idrubro: "",
				idarticulo: "",
				busqueda: "",
				busquedaSelect: "",
				infoHr: "hh:mm",
				reload: ""
			},
			methods: {
				verificarFoto(foto) {
					return foto != ''
				},
				getArticulos() {
					checksum = sc3SSGet('checksum');
					if (checksum === null)
						checksum = -1;

					xfn = 'stoRsArticulosConsulta';
					var articulos = "sc-ajax-invoke.php?fn=" + xfn + "&p=" + '[{"checksum":' + checksum + '}]';

					axios.get(articulos).then(response => {
						this.infoHr = response.data.reload_hr;
						if (response.data.reload == 1) {
							this.aArticulos = response.data.rs;
							checksum = response.data.checksum;
							sc3SSSet('checksum', checksum);
							sc3SSSetArray('aArticulos', response.data.rs);
							this.reload = response.data.reload;
						} else {
							this.aArticulos = sc3SSGetArray('aArticulos');
						}

					}).catch(error => {
						console.log(error);
					});
				},
				getRubros() {
					var checksum = -1;
					xfn = 'stoRsRubrosConsulta';
					var rubros = "sc-ajax-invoke.php?fn=" + xfn + "&p=" + '[{"checksum":' + checksum + '}]';

					axios.get(rubros).then(response => {
						this.aRubros = response.data.rs;
					}).catch(error => {
						console.log(error);
					});
				}
			},
			computed: {
				buscarArticulo() {
					aResult = this.aArticulos.filter((item) => {
						if (this.busqueda.length <= 2 && this.busquedaSelect == "") {
							return true;
						} else {
							buscando = this.busqueda.toLowerCase();
							aBusca = buscando.split(" ");
							return ((this.busquedaSelect == '' || (item.idrubro == this.busquedaSelect))) &&
								(this.busqueda == '' ||
									multiSearchAnd(item.nombre.toLowerCase(), aBusca) ||
									multiSearchAnd(item.codigo, aBusca) ||
									multiSearchAnd(item.id, aBusca));
						}
					});

					//los primeros 5000!
					return aResult.slice(0, 5000);
				}
			},
			created: function() {
				this.getArticulos();
				this.getRubros();
				//cada 30' mira si hay nuevos datos
				setInterval(this.getArticulos, 30 * 60 * 1000);
			}
		});
	</script>
</body>

</html>