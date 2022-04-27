/**
 * Esta clase permite obtener información de las APIS (EndPoint)
 */

class APIs {
    /**
     * Permite obtener los datos de una tabla a partir de un EndPoint.
     * @param {string} xURL 
     * @param {string} xCacheName 
     * @returns 
    */
    getFromAPI(xURL, xfilter = "") {
        var aResult;
        var url = xURL;

        if (xfilter !== "")
            url = url + "?filter=" + xfilter;

        getAPI(url, (xresponse) => {
            aResult = JSON.parse(xresponse);
        });

        return aResult;
    }

    put(xURL, xcallback, xasync = false) {
        let xmlRequest = new XMLHttpRequest();
        xmlRequest.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                xcallback(this.responseText);
            }
        };
        xmlRequest.open("PUT", xURL, xasync);
        xmlRequest.send();        
    }
}