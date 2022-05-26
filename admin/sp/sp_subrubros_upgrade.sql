create procedure sp_subrubros_upgrade(
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
		values ('subrubros_upgrade',vMensaje);
    end;
    
	start transaction;
    
    select 
		count(*)
	into
		vCantReg
	from
		subrubros
	where
		subrubros.codigo=xcodigo;
	if vCantReg = 0 then
		insert into subrubros(
			codigo,
            descripcion)
		values(
			xcodigo,
            xdescripcion);
	else
		update
			subrubros
		set
			subrubros.descripcion = xdescripcion
		where
			subrubros.codigo = xcodigo;
	end if;
    commit;
end
