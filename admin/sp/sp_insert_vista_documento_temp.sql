CREATE PROCEDURE sp_insert_vista_documento_temp (
    xform_code varchar(20), xdoc_entry int(11), xdoc_num bigint(20), xcanceled varchar(1),
    xdoc_status varchar(1), xobj_type varchar(10), xdoc_date datetime, xcard_code varchar(20),
    xdoc_cur varchar(3), xdoc_total decimal(20,2), xpaid_to_date decimal(20,2), xcomments text,
    xprojects varchar(100), xfrm_l_num varchar(20), xu_onesl_sisori varchar(1), xu_onesl_sisorinum varchar(20),
    xline_num int(11), xitem_code varchar(20), xquantity decimal(10,2), xopen_qty decimal(10,2),
    xline_cur varchar(3), xprice decimal(20,2), xline_total decimal(20,2), xbase_entry int(11),
    xbase_type int(11), xbase_line int(11), xcant_asignada decimal(10,2), xgestion varchar(1),
    xact_message varchar(100), xid__ int(11)
)
BEGIN
    DECLARE vMensaje text;
    DECLARE exit handler for sqlexception
	BEGIN
		GET diagnostics condition 1 vMensaje=message_text;
		INSERT INTO log_sp (nombre_sp, mensaje_error)
		VALUES ('sp_insert_vista_documento_temp', vMensaje);    
    END;


    INSERT INTO tmp_vista_documento (
        form_code, doc_entry, doc_num, canceled,
        doc_status, obj_type, doc_date, card_code,
        doc_cur, doc_total, paid_to_date, comments,
        projects, frm_l_num, u_onesl_sisori, u_onesl_sisorinum,
        line_num, item_code, quantity, open_qty, line_cur,
        price, line_total, base_entry, base_type, base_line,
        cant_asignada, gestion, act_message, id__
    ) VALUES (
        xform_code, xdoc_entry, xdoc_num, xcanceled,
        xdoc_status, xobj_type, xdoc_date, xcard_code,
        xdoc_cur, xdoc_total, xpaid_to_date, xcomments,
        xprojects, xfrm_l_num, xu_onesl_sisori, xu_onesl_sisorinum,
        xline_num, xitem_code, xquantity, xopen_qty, xline_cur,
        xprice, xline_total, xbase_entry, xbase_type, xbase_line,
        xcant_asignada, xgestion, xact_message, xid__
    );
END