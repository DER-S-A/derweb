/**
 * Esta clase permite manejar LocalStorage y SessionStorage para gestionar los datos en cache.
 */

class CacheUtils {
    /**
     * 
     * @param {boolean} xuseLocalStorage Indica true si los datos se grabarán en localStorage. Por default usa sessionStorage.
     */

    constructor(xApplicationName, xuseLocalStorage = false) {
        this.useLocalStorage = xuseLocalStorage;
        this.applicationName = xApplicationName;
    }

    /**
     * Permite almacenar datos en la cache del navegador.
     * @param {string} xcacheName Nombre de cache
     * @param {array} xdata Datos a guardar
     */
    set(xKey, xdata) {
        var datos = JSON.stringify(xdata);

        if (this.useLocalStorage)
            localStorage.setItem(this.applicationName + "_" + xKey, datos);
        else
            sessionStorage.setItem(this.applicationName + "_" + xKey, datos);
    }

    /**
     * Permite recuperar los datos de la cache del navegador.
     * @param {string} xcacheName Nombre de cache
     * @returns {array}
     */
    get(xKey) {
        var datos;
        if (this.useLocalStorage)
            datos = JSON.parse(localStorage.getItem(this.applicationName + "_" + xKey));
        else
            datos = JSON.parse(sessionStorage.getItem(this.applicationName + "_" + xKey));
        return datos;
    }

    /**
     * Permite eliminar un elemento de la cache.
     * @param {string} xcacheName Nombre de la cache.
     */
    remove(xKey) {
        if (this.useLocalStorage)
            localStorage.removeItem(this.applicationName + "_" + xKey);
        else
            sessionStorage.removeItem(this.applicationName + "_" + xKey);
    }

    /**
     * Verifica si los datos existen o no en cache.
     * @param {string} xKey Nombre de clave
     * @returns {boolean} true si existen y false en caso contrario.
     */
    isExists(xKey) {
        var datos = this.get(xKey);
        var result = false;
        if (datos === null)
            result = false;
        else
            result = true;
        return result;
    }
}