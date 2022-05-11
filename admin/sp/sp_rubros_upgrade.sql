create procedure sp_rubros_upgrade(
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
		values ('rubros_upgrade',vMensaje);
    end;
    
	start transaction;
    
    select 
		count(*)
	into
		vCantReg
	from
		rubros
	where
		rubros.codigo=xcodigo;
	if vCantReg = 0 then
		insert into rubros(
			codigo,
            descripcion)
		values(
			xcodigo,
            xdescripcion);
	else
		update
			rubros
		set
			rubros.descripcion=xdescripcion
		where
			rubros.codigo=xcodigo;
	end if;
    commit;
end
