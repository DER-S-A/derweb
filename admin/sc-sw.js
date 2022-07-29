
var today = new Date();
var dd = String(today.getDate()).padStart(2, '0');
var mm = String(today.getMonth() + 1).padStart(2, '0'); //Enero es 0
var yyyy = today.getFullYear();

today = mm + '-' + yyyy;
var CACHE = "cache-v1-" + today;

//patrones de recursos que se guardan en cache y usan estrategia CACHE FIRST
const aFirstCacheThenNetwork = ['.js', '.css', 'ico/', '/images/', 'pwacache=1', 'sc-offline.php'];
const aFirstNetworkThenCache = ['pwacache=1'];


//estrategias: https://blog.bitsrc.io/5-service-worker-caching-strategies-for-your-next-pwa-app-58539f156f52

/**
 * Retorna si un texto (o parte de el) está en el arreglo
 * EJ, xStr: ..../css/sc3.css, xArr ['.css', '.js']
 * @param {string} xStr 
 * @param {Array} xArr 
 */
function stringPatternInArray(xStr, xArr) {

	//el .foreach() sigue iterando hasta el final
	return xArr.some((element) => {
		if (xStr.includes(element)) {
			return true;
		}
		return false;
	});
}


//Registrar Service Worker
self.addEventListener('install', function (event) {
	// Instalar de inmediato
	if (self.skipWaiting) {
		self.skipWaiting();
	}

	console.log('Service Worker install...' + CACHE);
	event.waitUntil(
		caches.open(CACHE).then(function (cache) {
			return cache.addAll([
				'sc-offline.php'
			]);
		})
	);
});


self.addEventListener('fetch', (event) => {

	//Todo request POST va al servidor si o si
	if (event.request.method == 'POST') {
		event.respondWith(
			fetch(event.request).then(function (networkResponse) {
				return networkResponse
			})
		)
	}
	else {
		//Metodo GET, algunas cosas las cacheamos
		var cacheContent = false;

		//busca si es una extensión que debería cachear
		if (self.stringPatternInArray(event.request.url, aFirstCacheThenNetwork)) {
			cacheContent = true;
		}

		//aplica estrategia CACHE FIRST
		if (cacheContent) {

			//verifico si es primero networkResponse
			var firstNetwork = false;
			if (self.stringPatternInArray(event.request.url, aFirstNetworkThenCache)) {
				firstNetwork = true;
			}

			if (firstNetwork) {
				event.respondWith(
					fetch(event.request).then((response) => {
						return caches.open(CACHE).then((cache) => {
							cache.put(event.request, response.clone());
							return response
						})
					}).catch((e) => {
						//en caso de error, arroja cache
						return caches.open(CACHE).then((cache) => {
							return cache.match(event.request.url).then((resultado) => {
								if (resultado) {
									return cache.match(event.request.url);
								}
								else {
									return cache.match("sc-offline.php");
								}
							})
						});
					})
				);
			}
			else {

				//Primero cache, luego red
				event.respondWith(
					caches.open(CACHE).then((cache) =>
						cache.match(event.request).then((response) => {
							return response ||
								fetch(event.request).then((response) => {
									cache.put(event.request, response.clone());
									return response;
								}).catch((error) => {
									return cache.match("sc-offline.php");
								});
						}))
				);
			}
		}
		else {
			//Responde con la RED
			event.respondWith(
				fetch(event.request).then(function (networkResponse) {
					return networkResponse
				}).catch((e) => {
					//en caso de error arroja pantalla de sin conexion
					return caches.open(CACHE).then(function (cache) {
						return cache.match("sc-offline.php");
					});
				})
			);
		}

	}
});

// Elimina archivos de cache viejos
var cacheWhitelist = [CACHE];
caches.keys().then((CACHEs) => {

	return Promise.all(
		CACHEs.map((CACHE) => {
			if (cacheWhitelist.indexOf(CACHE) === -1) {
				return caches.delete(CACHE);
			}
		})
	);
});
