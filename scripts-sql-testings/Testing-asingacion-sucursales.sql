-- Clientes sin sucursales asignadas
SELECT
	*
FROM
	entidades
WHERE
	entidades.id_tipoentidad = 1 AND
	entidades.id NOT IN (
		SELECT
			id_entidad
		FROM
			sucursales);

-- Clientes con sucursales asignadas			
SELECT
	*
FROM
	entidades
WHERE
	entidades.id_tipoentidad = 1 AND
	entidades.id IN (
		SELECT
			id_entidad
		FROM
			sucursales);
			
