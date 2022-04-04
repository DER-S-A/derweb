<?php

//autor: Marcos C.
//libreria de encriptado y cifrado de datos.

class Sc3Encriptador 
{
    private $securekey, $iv;
    
    function __construct() 
    {
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$this->iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $this->securekey = md5("siganmenolosvoyadefraudar");
    }
    
    function encrypt1($input, $xvolatile = false) 
    {
    	$key = $this->securekey;
    	if ($xvolatile)
    		$key .= session_id();
        return base64_encode_safe(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $input, MCRYPT_MODE_ECB, $this->iv));
    }
    
    function decrypt1($input, $xvolatile = false) 
    {
    	$key = $this->securekey;
    	if ($xvolatile)
    		$key .= session_id();
    	return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode_safe($input), MCRYPT_MODE_ECB, $this->iv));
    }
    
    function encryptUrl($input, $xvolatile = false)
    {
    	$key = $this->securekey;
    	if ($xvolatile)
    		$key .= session_id();
    
    //	return base64_encode_safe(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $input, MCRYPT_MODE_ECB, $this->iv));
    	return base64_encode_safe($input);
    }
    
    function decryptUrl($input, $xvolatile = false)
    {
    	$key = $this->securekey;
    	if ($xvolatile)
    		$key .= session_id();
    	//return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode_safe($input), MCRYPT_MODE_ECB, $this->iv));
    	return base64_decode_safe($input);
    }
}

?>