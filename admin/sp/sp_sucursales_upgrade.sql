CREATE PROCEDURE sp_sucursales_upgrade (
  xSucursalCode varchar(100),
  xSucursalName varchar(50),
  xCardCode varchar(15),
  xTipoCode varchar(15),
  xCalle varchar(100),
  xCiudad varchar(100),
  xEstadoCode varchar(10),
  xZipCode varchar(20),
  xGln int,
  xCardCodeDER int,
  xCreateDate varchar(20))
BEGIN

    DECLARE vIdFormaEnvio int;
    DECLARE vIdEntidad int;
    DECLARE vIdProvincia int;
    DECLARE vIdVendedor int;
    DECLARE vIdTransporte int;
    DECLARE vTelefono text;
    DECLARE vEmail text;
    DECLARE vDescuento1 decimal(5, 2);
    DECLARE vDescuento2 decimal(5, 2);
    DECLARE vCantReg int;
    DECLARE vCantSuc int;
    DECLARE vMensaje text;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
      ROLLBACK;
      GET DIAGNOSTICS CONDITION 1 vMensaje = MESSAGE_TEXT;
      INSERT INTO log_sp (nombre_sp, mensaje_error)
        VALUES ('sucursales_upgrade', CONCAT(vMensaje, " | Código Sucursal: ", xSucursalCode));
    END;
    START TRANSACTION;

    SET vIdFormaEnvio = (SELECT
        id
      FROM formas_envios
      WHERE descripcion LIKE 'RETIRA CLIENTE');

    SET vIdEntidad = (SELECT
        id
      FROM entidades
      WHERE cliente_cardcode = xCardCode);

    SET vIdProvincia = (SELECT
        id
      FROM provincias
      WHERE codigo = xEstadoCode);

    SET vIdVendedor = (SELECT
        codigo_vendedor
      FROM entidades
      WHERE cliente_cardcode = xCardCode);
    
    /* Acomodo el WHERE porque los filtros estaban mal */
    SELECT vIdVendedor;
    SET vIdVendedor = (SELECT
        ID
      FROM entidades
      WHERE codigo_vendedor = vIdVendedor AND
            cliente_cardcode = xCardCode);

    SET vIdTransporte = (SELECT
        id
      FROM transportes
      WHERE descripcion LIKE 'RETIRA CLIENTE');

    SET vTelefono = (SELECT
        telefono
      FROM entidades
      WHERE cliente_cardcode = xCardCode);

    SET vEmail = (SELECT
        email
      FROM entidades
      WHERE cliente_cardcode = xCardCode);

    SET vDescuento1 = (SELECT
        descuento_1
      FROM entidades
      WHERE cliente_cardcode = xCardCode);

    SET vDescuento2 = (SELECT
        descuento_2
      FROM entidades
      WHERE cliente_cardcode = xCardCode);

    SELECT
      COUNT(*) INTO vCantReg
    FROM Sucursales
    WHERE id_entidad = vIdEntidad;

    IF vCantReg = 0 THEN
      /* Paso por acá cuando se carga el cliente nuevo para generar el registro obligatorio de la sucursal*/
      INSERT INTO sucursales (
          id_formaenvio,
          id_entidad,
          id_provincia,
          id_vendedor,
          id_transporte,
          codigo_sucursal,
          nombre,
          calle,
          numero,
          departamento,
          ciudad,
          codigo_postal,
          telefono,
          mail,
          global_localizacion_number,
          predeterminado,
          descuento_p1,
          descuento_p2,
          rentabilidad_1,
          rentabilidad_2)
        VALUES (
          vIdFormaEnvio, 
          vIdEntidad, 
          vIdProvincia, 
          vIdVendedor, 
          vIdTransporte, 
          xSucursalCode, 
          xSucursalName, 
          xCalle, 
          00, 
          00, 
          xCiudad, 
          xZipCode, 
          vTelefono, 
          vEmail, 
          xGln, 
          1, 
          vDescuento1, 
          vDescuento2,
          0,
          0);
    ELSE
        /* Paso por acá cuando el cliente tiene sucursales cargadas para actualizar datos */
        SELECT
          COUNT(*) INTO vCantSuc
        FROM Sucursales
        WHERE id_entidad = vIdEntidad
        AND codigo_sucursal = xSucursalCode;

        IF vCantSuc = 1 THEN

          UPDATE Sucursales
          SET id_formaenvio = vIdFormaEnvio,
              id_provincia = vIdProvincia,
              id_vendedor = vIdVendedor,
              id_transporte = vIdTransporte,
              calle = xCalle,
              numero = 0,
              departamento = 0,
              ciudad = xCiudad,
              codigo_postal = xZipCode,
              telefono = vTelefono,
              mail = vEmail,
              global_localizacion_number = xGln,
              descuento_p1 = vDescuento1,
              descuento_p2 = vDescuento2
          WHERE id_entidad = vIdEntidad
            AND codigo_sucursal = xSucursalCode;
      ELSE
          /* Pasa por acá cuando ya la entidad tiene una sucursal cargada y se le agregan nuevas */
          INSERT INTO Sucursales (id_formaenvio,
              id_entidad,
              id_provincia,
              id_vendedor,
              id_transporte,
              codigo_sucursal,
              nombre,
              calle,
              numero,
              departamento,
              ciudad,
              codigo_postal,
              telefono,
              mail,
              global_localizacion_number,
              predeterminado,
              descuento_p1,
              descuento_p2)
            VALUES (
              vIdFormaEnvio, 
              vIdEntidad, 
              vIdProvincia, 
              vIdVendedor, 
              vIdTransporte, 
              xSucursalCode, 
              xSucursalName, 
              xCalle, 
              00, 
              00, 
              xCiudad, 
              xZipCode, 
              vTelefono, 
              vEmail, 
              xGln, 
              0, 
              vDescuento1, 
              vDescuento2);
      END IF;
    END IF;
  COMMIT;
END
