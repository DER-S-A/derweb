/**
 * Cola de ejecución para trabajo off-line
 * Los POST se encolan y son ejecutados en background cuando hay conectividad
 * 
 * @fecha nov-2021
 * @author Marcos C.
 */

var gMessageDivId = "";

class ScColaEjecucion {

	/**
	 * Crea cola y define un DIV u objeto html para mostrar los mensajes
	 * @param {string} xMessageDiv 
	 */
	constructor(xMessageDivId) {

		if (xMessageDivId == '')
			xMessageDivId = gMessageDivId;

		this.messageDivId = xMessageDivId;
		gMessageDivId = xMessageDivId;
		this.arrayKey = 'cola-ejecucion';

		//lo recupera y vuelve a grabar, sólo por si no existe. NO borra
		aTbl = sc3LSGetArray(this.arrayKey);
		sc3LSSetArray(this.arrayKey, aTbl);
	}

	/**
	 * Muestra un estado de la cola de ejecución. Si tiene obj id lo manda allí, sino
	 * al console
	 * @param {string} xtext 
	 */
	setStatus(xtext) {
		if (this.messageDivId != '') {
			var div = document.getElementById(this.messageDivId);
			if (div != null)
				div.innerHTML = xtext;
			else
				console.log(xtext);
		}
		else
			console.log(xtext);
	}

	/**
	 * Borra cola de ejecución
	 */
	clear() {
		sc3LSSetArray(this.arrayKey, []);
	}

	/**
	 * Encola un llamado al servidor
	 * @param {string} xStatusText con texto a mostrar en el DIV
	 * @param {string} xUrl a invocar
	 * @param {string} xFuncion funcion a invocar en el url del servidor
	 * @param {Array} xaParams 
	 */
	agregar(xStatusText, xUrl, xFuncion, xaParams, xCallBackFn = null) {

		var guid = GUID();
		//avisa, no traiciona !
		this.setStatus('Preparando ' + xStatusText + '...');

		//clona arreglo, pasandolo por JSON
		var aParams = JSON.parse(JSON.stringify(xaParams));

		aTbl = sc3LSGetArray(this.arrayKey);
		aParams["guid"] = guid;
		aTbl.push([guid, xUrl, xFuncion, aParams, xCallBackFn, xStatusText]);
		sc3LSSetArray(this.arrayKey, aTbl);
	}


	/**
	 * Hr de recorrer la cola y ejecutar los pendientes, borrarlos si hubo éxito
	 */
	procesar() {
		//recuperamos cola
		aTbl = sc3LSGetArray(this.arrayKey);

		aTbl.forEach((serverInvoke) => {
			var guid = serverInvoke[0];
			var url = serverInvoke[1];
			var fn = serverInvoke[2];
			var aParams = serverInvoke[3];
			var fnCB = serverInvoke[4];
			var nombreFantasia = serverInvoke[5];

			//TODO: saber porqué es "[0]"
			aParams[0]["guid"] = guid;

			this.setStatus('Procesando ' + nombreFantasia + '...');

			//llama, a ver si hay algo
			sc3InvokeServerApi(url, fn, aParams[0], fnProcesarCB);
		});
	}

	/**
	 * Ha llegado la rta del server, oremos
	 * @param {array} xaResult 
	 */
	procesarCB(xaResult) {

		var guidRecibo = xaResult['guid'];
		var msg = xaResult['msg'];
		var error = xaResult['error'];

		//recuperamos cola
		var aTbl = sc3LSGetArray(this.arrayKey);
		var aTbl2 = aTbl;

		var i = 0;
		var indexBorrar = -1;
		aTbl.forEach((serverInvoke) => {
			var guid = serverInvoke[0];
			var fnCB = serverInvoke[4];
			var nombreFantasia = serverInvoke[5];

			//encontramos mensaje 
			if (guidRecibo == guid) {
				if (error == '') {
					this.setStatus('Fin ' + nombreFantasia + '. ' + msg);
					aTbl2 = aTbl2.filter((valor) => {
						return valor[0] != guidRecibo;
					});
				}
				else {
					if (error.includes("Comprobante existente")) {
						aTbl2 = aTbl2.filter((valor) => {
							return valor[0] != guidRecibo;
						});
					}
					this.setStatus('Error en ' + nombreFantasia + '. ' + error);
				}
			}

			i++;
			/* await sleep(2000); */
		});
		sc3LSSetArray(this.arrayKey, aTbl2);
	}

};


/**
 * Sólo porque 
 * @param {array} xaResult 
 */
function fnProcesarCB(xaResult) {
	var c = new ScColaEjecucion('')
	c.procesarCB(xaResult);
}