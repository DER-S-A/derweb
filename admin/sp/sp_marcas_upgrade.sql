create procedure sp_marcas_upgrade(
	xcodigo int,
    xdescripcion varchar(60))
begin
	declare vCantReg int;
    declare vMensaje text;
    declare exit handler for sqlexception
	begin
		rollback;
		GET diagnostics condition 1 vMensaje=message_text;
		insert into log_sp (nombre_sp, mensaje_error)
		values ('marcas_upgrade',vMensaje);    
    end;
    
	start transaction;
    
    select 
		count(*)
	into
		vCantReg
	from
		marcas
	where
		marcas.codigo=xcodigo;
	if vCantReg = 0 then
		insert into marcas(
			codigo,
            descripcion)
		values(
			xcodigo,
            xdescripcion);
	else
		update
			marcas
		set
			marcas.descripcion=xdescripcion
		where
			marcas.codigo=xcodigo;
	end if;
    commit;
end
