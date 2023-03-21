CREATE TABLE db_derweb.margenes_especiales (
  id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_rubro int,
  id_subrubro int,
  id_marca int,
  id_entidad  int NOT NULL,
  rentabilidad_1 decimal(5,2),
  rentabilidad_2 decimal(5,2),
  habilitado tinyint(3),
  UNIQUE INDEX id (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

ALTER TABLE db_derweb.margenes_especiales
ADD CONSTRAINT margenes_especiales_ibfk_1 FOREIGN KEY (id_rubro)
REFERENCES db_derweb.rubros (id);

ALTER TABLE db_derweb.margenes_especiales
ADD CONSTRAINT margenes_especiales_ibfk_2 FOREIGN KEY (id_subrubro)
REFERENCES db_derweb.subrubros (id);

ALTER TABLE db_derweb.margenes_especiales
ADD CONSTRAINT margenes_especiales_ibfk_3 FOREIGN KEY (id_marca)
REFERENCES db_derweb.marcas (id);

ALTER TABLE db_derweb.margenes_especiales
ADD CONSTRAINT margenes_especiales_ibfk_4 FOREIGN KEY (id_entidad)
REFERENCES db_derweb.entidades(id);