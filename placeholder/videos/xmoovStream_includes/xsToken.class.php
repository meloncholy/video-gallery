<?php
/*

	xmoovStream Token Handler
	########################################################################
	
	xmoovStream Server Version: 0.8.4b
	
	Author: Eric Lorenzo Benjamin jr. stream (AT) xmoov (DOT) com
	License: Creative Commons Attribution-Noncommercial-Share Alike 3.0 United States License. http://stream.xmoov.com/support/licensing/	
	
	Description:
	########################################################################
	
	This file handles token generation and validation.
	
	Notes:
	########################################################################
	
	Tokens are assigned to file names and saved in cookie on the clients computer.
	This system will not work correctly in the following cases:
	* Cookies are not enabled on the clients computer.
	* You are using tokens to serve videos to an iPhone
	
*/

define ('XS_KEY', '4b0a7');
define ('XS_TOKEN_EXPIRE', 3600);

class xsToken
{
	var $tokens = array();
	
	function getKey ($file)
	{
		return md5($file.XS_KEY);
	}
	
	function getToken ($file)
	{
		$key = $this->getKey ($file);
		
		if (isset($this->tokens[$key]))
		{
		    return $this->tokens[$key];
		}
		else if (isset($_COOKIE[$key]))
		{
			return $_COOKIE[$key];
		}
		return false;
	}
	
	function isValid ($file, $token)
	{
		if ($token == $this->getToken ($file))
		{
			return true;
		}
		return false;
	}
	
	function setToken ($file)
	{
		$key = $this->getKey ($file);
		$token = md5(uniqid(rand(),1));
		setcookie ($key, $token, time() + XS_TOKEN_EXPIRE,'/',FALSE);
		$this->tokens[$key] = $token;
		return $token;
	}
}

?>