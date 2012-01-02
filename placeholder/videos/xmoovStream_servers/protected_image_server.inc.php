<?php
/*

	xmoovStream Server Configuration - Hot Link Protected Image Server
	########################################################################
	 
	xmoovStream Server Version: 0.8.4b
	
	Author: Eric Lorenzo Benjamin jr. stream (AT) xmoov (DOT) com
	License: Creative Commons Attribution-Noncommercial-Share Alike 3.0 United States License. http://stream.xmoov.com/support/licensing/	
	
	Description:
	########################################################################
	
	This configuration serves hot link protected images.
	
	* 'key' : the token
	* 'file' : the targeted image file
	
	Url example with htaccess: http://mysite.com/downloads/[token]/[file.ext]
	Ulr example without htaccess: http://mysite.com/downloads?key=[token]&file=[file.ext]
	
	htaccess file contents:
	########################################################################
	
	<IfModule mod_rewrite.c>
	RewriteEngine on
	RewriteRule ^([^/]+)/([^/]+) index.php?key=$1&file=$2 [QSA]
	</IfModule>

*/
define ('XS_PROVIDER', 'Protected Image Server');
function init_server ()
{
	if (isset ($_GET['key']) && isset ($_GET['file']))
	{
		$xsToken = new xsToken ();
		if($xsToken->isValid ($_GET['file'],$_GET['key']))
		{
			$file = $_GET['file'];
		}
		else
		{
			$file = 'no_access.jpg';
		}
		$config = array
		(
			'file' => $file,
			'file_path' => XS_FILES . '/protected_images/',
			'force_download' => 0,
			'burst_size' => 0,
			'throttle' => 0,
			'mime_types' => array
			(
				'jpe' => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'jpg' => 'image/jpeg',
				'png'  => 'image/png',
				'svg' => 'image/svg+xml',
				'tif' => 'image/tiff',
				'tiff' => 'image/tiff',
				'gif' => 'image/gif'
			)
		);
		return $config;
	}
	else
	{
		return false;
	}
}
?>