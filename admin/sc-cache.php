<?php

/*
Cache de tablas del sistema. 
En un array se cargan las tablas indexadas por un campo clave.
*/
class ScCache
{
	var $tables = Array();
	var $qobjs = Array();
	var $dirty = true;

	function ___construct()
	{
		$this->flushCache();
		$this->iamDirty();
	}
	
	/*
	borra todo lo de la cach�
	*/
	function flushCache()
	{
		$this->tables = array();
		$this->qobjs = array();
	}

	function isDirty()
	{
		return $this->dirty;
	}
	
	/*
	Indica que debe grabarse
	*/
	function iamDirty()
	{
		$this->dirty = true;
	}

	function iamClean()
	{
		$this->dirty = false;
	}

	/*
	Carga un RS en la cache con el nombre de la tabla dada y el key field dado
	*/
	function loadRs($xtable, $xrs, $xkfield)
	{
		debug("ScCache::loadRs($xtable, ..., $xkfield)");
		while (!$xrs->EOF())
		{
			$keyvalue = $xrs->getValue($xkfield);
			$this->tables[$xtable][$keyvalue] = $xrs->getRow();
			$xrs->Next();
		}
		$this->iamDirty();
	}

	/*
	Carga una tabla COMPLETA en la caché utilizando un campo como índice del array
	*/
	function loadTable($xtable, $xkfield = "id")
	{
		debug("ScCache::loadTable($xtable, $xkfield)");
		$rs = getRsTabla($xtable);
		$this->loadRs($xtable, $rs, $xkfield);
	}

	/*
	Carga una tabla para un KEY dado
	*/
	function loadTableAndKey($xtable, $xkfield, $xkeyvalue, $xneedscomillas = false)
	{
		debug("ScCache::loadTableAndKey($xtable, $xkfield, $xkeyvalue, $xneedscomillas)");
		if ($xneedscomillas)
			$rs = locateRecordWhere($xtable, $xkfield . " = '" . $xkeyvalue . "'", true);
		else
			$rs = locateRecordWhere($xtable, $xkfield . " = " . $xkeyvalue, true);
		$this->loadRs($xtable, $rs, $xkfield);
	}

	/*
	Retorna el queryinfo de un query en sc_querys	
	*/
	function getQueryInfo($xqueryname)
	{
		debug("ScCache::getQueryInfo($xqueryname)");
		$this->checkTableAndKey("sc_querys", "queryname", $xqueryname, true);
		return $this->tables["sc_querys"][$xqueryname];
	}

	/**
	 * Retorna el objeto ScQueryInfo cacheado
	 *
	 * @param string $xqueryname
	 * @return ScQueryInfo
	 */
	function getQueryObj($xqueryname)
	{
		debug("ScCache::getQueryObj($xqueryname)");
		
		$qobj = $this->deserializar($this->qobjs[$xqueryname]);
		return $qobj;		
	}

	/**
	 * Almacena un objeto  el objeto ScQueryInfo cacheado
	 * @param string $xqueryname
	 * @param unknown $xobj
	 */
	function saveQueryObj($xqueryname, $xobj)
	{
		debug("ScCache::saveQueryObj($xqueryname)");
		$this->qobjs[$xqueryname] = $this->serializar($xobj);
		$this->iamDirty();
	}
	
	/**
	 * Convierte a texto
	 * @param object $xobj
	 * @return string
	 */
	function serializar($xobj)
	{
	    return base64_encode(gzcompress(serialize($xobj)));
	}

	/**
	 * Dado un texto, lo hace obj
	 * @param unknown $xtxt
	 * @return mixed
	 */
	function deserializar($xtxt)
	{
	    return unserialize(gzuncompress(base64_decode($xtxt)));
	}
	
	/**
	* Retorna si existe el objeto cacheado
	*/
	function existsQueryObj($xqueryname)
	{
		debug("ScCache::existsQueryObj($xqueryname)");
		return array_key_exists($xqueryname, $this->qobjs);
	}
	
	
	/*
	Verifica si est� en cach� y la carga si no lo est�
	*/
	function checkTable($xtable, $xkfield)
	{
		debug("ScCache::checkTable($xtable, $xkfield)");
		if (!$this->tableInCache($xtable))
			$this->loadTable($xtable, $xkfield);
		else
		{
			debug("ScCache::checkTable($xtable): CACHE HIT !");
		}	
	}

	/*
	Verifica si est� en cach� y la carga si no lo est�
	*/
	function checkTableAndKey($xtable, $xkfield, $xkeyvalue, $xneedscomillas = false)
	{
		debug("ScCache::checkTableAndKey($xtable, $xkfield, $xkeyvalue, $xneedscomillas)");
		if (!$this->tableAndKeyInCache($xtable, $xkeyvalue))
			$this->loadTableAndKey($xtable, $xkfield, $xkeyvalue, $xneedscomillas);
		else
		{
			debug("ScCache::checkTableAndKey($xtable-$xkeyvalue): CACHE HIT !");
		}
	}

	/*
	Retorna si la tabla est� en cach�
	*/
	function tableInCache($xtable)
	{
		debug("ScCache::tableInCache($xtable)");
		return array_key_exists($xtable, $this->tables);
	}
	
	/*
	Retorna si la tabla est� en cach� y el key dado tambien
	*/
	function tableAndKeyInCache($xtable, $xkey)
	{
		debug("ScCache::tableAndKeyInCache($xtable, $xkey)");
		if (!array_key_exists($xtable, $this->tables))
			return false;
		if (!array_key_exists($xkey, $this->tables[$xtable]))
			return false;
		return true;
	}
		
	/*
	Muestra el estado de la cache
	*/
	function debug()
	{
		$inside = "ScCache::debug(): ";
		foreach ($this->tables as $k => $v) 
		{
		    $inside .= " -Tabla: " . $k . ", rows: " . count($v) . "\n ";
			foreach ($v as $key => $row) 
			{
			    if ($row !== null)
			         $inside .= " (key: " . $key . ", columns: " . count(array_keys($row)) . ")";
			}
		}
		debug($inside);
	}
//fin de clase ScCache
} 


/*
Inicializa la cache y la guarda en la sesion
*/
function initCache()
{
	$cache = new ScCache();
	saveCache($cache);
}

/**
 * Recupera la cache de la sesion y la crea
 * @return ScCache
 */
function getCache()
{
	if (!isset($_SESSION["_CACHE"])) 
	{
		initCache(); 
	}	
	$cache = new ScCache();
	$cache = unserialize($_SESSION["_CACHE"]);
	return $cache;		
}

function saveCache($xcache)
{
	$xcache->debug();
	if ($xcache->isDirty())
	{
		$_SESSION["_CACHE"] = serialize($xcache);
		$xcache->iamClean();	
	}
}

/**
 * Cache de archivos, permite guardar el html a retornar en una cache.
 * Los arministra en un directorio y los guarda por usuario y nivel.
 */
class ScFileCache
{
	var $mcachefile = Array();
	var $mcacheext = ".htmlc";
	var $mcachedir = "./tmp/"; 
	var $mcacheprefix = "cache-";
	var $mactive = true;
	 
	function __construct()
	{
		debug("ScFileCache::constructor()");
	}
	
	/**
	 * Inicia la cache y guarda 
	 *
	 */
	function start($xnivel, $xstackname = "")
	{
		debug("ScFileCache::start($xnivel)");
		$this->mactive = getParameterInt("sc3-view-filecache", 1);
		if ($this->mactive)
		{
		    $this->mcachefile = $this->buildFileName($xnivel, $xstackname); 
		    ob_start();
		}
	    return $this->mcachefile;
	}
	
	/**
	 * Arma el nombre del archivo con el usuario y el nivel del stack 
	 * @param int $xnivel
	 * @return string
	 */
	function buildFileName($xnivel, $xstackname = "")
	{
		$key = "u" . getCurrentUser() . "-n" . $xnivel . $xstackname;
		return $this->mcachedir . $this->mcacheprefix . $key . $this->mcacheext;
	}
	
	/**
	 * guardar todo lo generado en cach� y retorna el valor
	 */
	function end($xstackSize = 0, $xstackname = "")
	{
		debug("ScFileCache::end()");

		if ($this->mactive)
		{
			//no guarda en cach� los niveles de navegaci�n superiores
			if ($xstackSize <= 3)
			{
				$fp = fopen($this->mcachefile, 'w'); 
			    fwrite($fp, ob_get_contents());
			    fclose($fp); 
			}
			ob_end_flush();
		}
	}
	
	/**
	 * Borra todos los archivos de la cache
	 */
	function clear($xstackname = "")
	{
		debug("ScFileCache::clear()");
		$i = 1;
		while ($i <= 3)
		{
			$filename = $this->buildFileName($i, $xstackname); 
			if (file_exists($filename))
				unlink($filename);	
			$i++;	
		}
	}
	
	/**
	 * Retorna si la pagina existe y esta vigente >> el nombre del archivo
	 * @param string $xnivel
	 */
	function fileExists($xnivel, $xstackname = "")
	{
		debug("ScFileCache::fileExists($xnivel)");
		
	    $this->mcachefile = $this->buildFileName($xnivel, $xstackname); 
	    
		if (!file_exists($this->mcachefile))
			return "";

        //TODO: ver error de volver en diferentes solapas !			
		//return "";	
			
		//verifica la existencia de la pagina en cache
	    $cachefile_created = filemtime($this->mcachefile);
	    clearstatcache();
	 
		// 5 minutos
		$cachetime = 300; 
		// si no se vencio, retorna el nombre del archivo
	    if (time() - $cachetime < $cachefile_created) 
		{
			debug("ScFileCache::fileExists($xnivel) - HIT: $this->mcachefile");
			return $this->mcachefile;
	    }
		debug("ScFileCache::fileExists($xnivel) - archivo viejo: $this->mcachefile");
	    
		return "";
	}
}

?>