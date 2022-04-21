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
}