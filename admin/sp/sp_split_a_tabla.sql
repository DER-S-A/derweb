
/*
DROP PROCEDURE IF EXISTS sp_split_a_tabla;

DELIMITER $$  
 
Pasa todos los valores separados por coma a una tabla tmp_lista
*/
CREATE PROCEDURE sp_split_a_tabla(IN xidusuario int, IN xlista varchar(3000))

BEGIN
	DECLARE a INT Default 0 ;
	DECLARE str VARCHAR(3000);
	SET str = '';
	
	-- borra lo que tenga el usuario
	delete from tmp_lista 
	where idusuario = xidusuario;
	
	simple_loop: LOOP
		SET a = a + 1;
		SET str = fn_split_str(xlista, ",", a);
		IF str = '' THEN
			LEAVE simple_loop;
		END IF;

		insert into tmp_lista (idusuario, id)
			values (xidusuario, str);
			
END LOOP simple_loop;
END 
/*
$$
*/
