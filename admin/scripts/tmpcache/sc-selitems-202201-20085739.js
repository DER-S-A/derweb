/**
 * Scripts de sc-selitems.php 
 * Baja, busqueda, editar, menú contextual
*/




function dardebaja(xrecid)
{
	if (confirm("Esta seguro que desea borrar este dato ?"))
		document.location.href = "sc-delitem.php<?php echo(getParams()) ?>" + "&registrovalor=" + xrecid + "&mantener=1"
}

function buscar()
{
	f=document.getElementById('form1');
	palabra = document.getElementById('palabra');
	f.submit();
}

function ver(xurl)
{
	document.location.href = xurl;
}

function expandMaster(x_strExpand, x_strExpansor, xnombre) 
{
	styleObj = document.getElementById(x_strExpand).style;
	if (styleObj.display=='none') 
	{
		styleObj.display = '';
	}
	else 
	{
		styleObj.display = 'none';
	}
}

function submitFilter()
{
	if (document.getElementById('filter').options[document.getElementById('filter').selectedIndex].value != 0)
		document.getElementById('filtername').value = document.getElementById('filter').options[document.getElementById('filter').selectedIndex].text;
	else
		document.getElementById('filtername').value = '';
	document.getElementById('form1').submit();
}

/**
 * 
 */
function irMenu(xOp, xurl)
{
	if (xOp.target == '') {
		if (xOp.emergente == 0)
			document.location.href = xOp.url + xurl;
		else
			openWindow(xOp.url + xurl);
	}
	else {
		window.open(xOp.url + xurl, xOp.target).focus();
	}
}


/**
 * Cuando presiona menú derecho sobre una fila
 */
function crearMenuContextual(xid, xtitulo, xrow)
{
	//recupera registro actual, vino como BASE64 de JSON
	record = JSON.parse(atob(xrow));
	
	var contextMenuTwo = CtxMenu("#tr" + xid);

	//Prende por instante
	iluminarFila(xid, 4000);

	//borra todo y nadie sabe porqué
	if (contextMenuTwo.getItems().length > 0) 
		return;

	//recupera operaciones cacheadas para esta consulta
	tbl = sc3SSGetTable(queryname + '-op');
	if (!sc3ArrayVacio(tbl))
	{
		urlEdit = "?query=" + queryname;
		urlEdit += "&registrovalor=" + xid;
		urlEdit += "&stackname=" + stackname;

		opVer = [];
		opVer.target = '';
		opVer.emergente = 0;
		opVer.condicion = '';
		opVer.url = "sc-viewitem.php";
		contextMenuTwo.addItem(xtitulo, (elem) => { irMenu(opVer, urlEdit)}, 'fa-file-o', true);

		op = [];
		op.target = '';
		op.condicion = '';
		op.emergente = 0;

		if (tbl.canedit)
		{
			op.url = "sc-edititem.php";
			contextMenuTwo.addItem('Editar', (elem) => { irMenu(op, urlEdit)}, 'fa-pencil-square-o', true);
		}

		if (tbl.canedit && tbl.caninsert)
		{
			urlCopy = urlEdit + "&insert=1";
			op.url = "sc-edititem.php";
			contextMenuTwo.addItem('Copiar', (elem) => { irMenu(op, urlCopy) }, 'fa-copy', true);
		}
		if (tbl.candelete)
		{
			contextMenuTwo.addItem('Borrar', (elem) => { dardebaja(xid); }, 'fa-trash', true);
		}

		//EJ: ?stackname=sel_articulos&mquery=qstoarticulos&mid=51&opid=401
		urlParams = "?mquery=" + queryname;
		urlParams += "&mid=" + xid;
		urlParams += "&stackname=" + stackname;

		//lista de operaciones
		aOp = tbl.rs;

		if (aOp.length > 0)
			contextMenuTwo.addSeparator();

		aOp.forEach((op) => {
				condicion = true;
				cond1 = op.condicion;
				if (cond1 != '')
				{
					cond2 = cond1.replaceAll("$", '');
					//console.log('condicion', cond2);
					eval(cond2);
				}

				if (condicion)
				{
					var parametrosOp = urlParams;
					
					//quizás la Operación ya tiene ?p1=v1 entonces lo saca de urlParamas						
					if (op.url.includes("?"))
						parametrosOp = parametrosOp.replaceAll("?", "&");
					
					var url = parametrosOp + "&opid=" + op.opid;
					contextMenuTwo.addItem(op.nombre, (elem) => { irMenu(op, url, record) }, op.icon);
				}
				else
				{
					contextMenuTwo.addItem('<i>' + op.nombre + '</i>', () => {});
				}
			});
	}
}
