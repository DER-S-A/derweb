
CREATE FUNCTION fn_split_str(
		x VARCHAR(3000),
		delim VARCHAR(12),
		pos INT
)

	RETURNS VARCHAR(255) deterministic
	RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
		CHAR_LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) + 1),
		delim, '');
