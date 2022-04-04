
CREATE FUNCTION fn_isnumeric(
        val varchar(1024))
    returns tinyint(1) deterministic

    return val regexp '^(-|\\+)?([0-9]+\\.[0-9]*|[0-9]*\\.[0-9]+|[0-9]+)$';

