from packages.catalogo.catalogo import Catalogo
import time

start_time = time.perf_counter()
oCatalogo = Catalogo()
oCatalogo.updatePaises()
oCatalogo.updateProvincias()
oCatalogo.updateFormasEnvios()
oCatalogo.updateRubros()
oCatalogo.updateSubrubros()
oCatalogo.updateMarcas()
oCatalogo.updateArticulos()
oCatalogo.updateStock()
oCatalogo.updateListasDePrecios()
oCatalogo.updateTeleVentas()
oCatalogo.updateClientes()
oCatalogo.updateSucursales()




elapsed_time = time.perf_counter() - start_time
minute = int(elapsed_time / 60)
seconds = int(elapsed_time % 60)
print(f"Tiempo duracion total: {minute}.{seconds} ")