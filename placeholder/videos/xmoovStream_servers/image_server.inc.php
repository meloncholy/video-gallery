<?php
/*

	xmoovStream Server Configuration - Image Server
	########################################################################
	 
	xmoovStream Server Version: 0.8.4b
	
	Author: Eric Lorenzo Benjamin jr. stream (AT) xmoov (DOT) com
	License: Creative Commons Attribution-Noncommercial-Share Alike 3.0 United States License. http://stream.xmoov.com/support/licensing/	
	
	Description:
	########################################################################
	
	This configuration serves images.
	
	* 'file' : the targeted image file
	
	Url example with htaccess: http://mysite.com/images/[file.ext]
	Ulr example without htaccess: http://mysite.com/images?file=[file.ext]
	
	htaccess file contents:
	########################################################################
	
	<IfModule mod_rewrite.c>
	RewriteEngine on
	RewriteRule ^([^/]*)$ index.php?file=$1 [QSA]
	</IfModule>
	
*/
define ('XS_PROVIDER', 'Image Server');
function init_server ()
{
	if (isset ($_GET['file']))
	{
		$config = array
		(
			'file' => $_GET['file'],
			'file_path' => XS_FILES . '/images/',
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