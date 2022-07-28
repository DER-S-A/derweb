
/**
 * Cuando hay cambios recalcula lo que se muestra
 * @param {varchar} id 
 */
function facturaCargar(id, control) {

	span = document.getElementById(id + "_span");
	hidden = document.getElementById(id);
	tFact = document.getElementById(id + "_tipo");
	PV = document.getElementById(id + "_punto_venta");
	talon = document.getElementById(id + "_talon");
	nroFactura = document.getElementById(id + "_comprobante");

	if (control == 'tipo') {
		if (tFact.value == 'RC') {
			talon.value = 'X';
			PV.value = '0';
			nroFactura.focus();
		}
		else
			if (tFact.value == 'RE') {
				talon.value = 'X';
				PV.value = '0';
				nroFactura.focus();
			}
			else
				if (tFact.value == '') {
					talon.value = '';
					PV.value = '';
					nroFactura.value = '';
				}
				else
					talon.focus();
	}

	if (tFact.value == "-") {
		res = "s/comp.";
	}
	else
		if (tFact.value == "") {
			res = "";
		}
		else {
			res = tFact.value;
			res += "-" + talon.value;
			valorpv = PV.value;
			while (valorpv.length < 5) {
				valorpv = '0' + valorpv;
			}
			res += "-" + valorpv;
			res += "-" + nroFactura.value;
		}

	span.innerHTML = res;
	hidden.value = res;
}

/**
 * Valida que esté bien armado el comprobante
 * @param {string} xid 
 */
function facturaValidar(xid) {

	span = document.getElementById(xid + "_span");
	hidden = document.getElementById(xid);
	tipo = document.getElementById(xid + "_tipo").value;
	talon = document.getElementById(xid + "_talon").value;
	puntoVenta = document.getElementById(xid + "_punto_venta").value;
	nroFactura = document.getElementById(xid + "_comprobante").value;

	if (tipo == 'FC' || tipo == 'FCE' || tipo == 'ND' || tipo == 'NC') {

		if (talon == '') {
			alert('Ingrese talon A|B|C');
			document.getElementById(xid + "_talon").focus();
			return false;
		}

		if (puntoVenta.length == 0) {
			alert('Ingrese punto de venta');
			document.getElementById(xid + "_punto_venta").focus();
			return false;
		}

		if (nroFactura.length == 0) {
			alert('Ingrese nro de comprobante');
			document.getElementById(xid + "_comprobante").focus();
			return false;
		}
		
	}

	return true;
}
