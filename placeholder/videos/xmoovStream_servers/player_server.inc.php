<?php
/*

	Embedded Media Server - Flash Player Server
	########################################################################
	
	xmoovStream Server Version: 0.8.4b
	
	Author: Eric Lorenzo Benjamin jr. stream (AT) xmoov (DOT) com
	License: Creative Commons Attribution-Noncommercial-Share Alike 3.0 United States License. http://stream.xmoov.com/support/licensing/	
	
	Description:
	########################################################################
	
	This configuration serves flash players.
	
	* 'file' : the targeted player folder
	
	Url example with htaccess: http://mysite.com/players/[file.ext]
	Ulr example without htaccess: http://mysite.com/players?file=[file.ext]
	
	htaccess file contents:
	########################################################################
	
	<IfModule mod_rewrite.c>
	RewriteEngine on
	RewriteRule ^([^/]*)$ index.php?file=$1 [QSA]
	</IfModule>
	
*/
define ('XS_PROVIDER', 'Player Server');
function init_server ()
{
	if (isset ($_GET['file']))
	{
		$file = rtrim($_GET['file'],strstr($_GET['file'], '.'));
		$config = array
		(
			'file' => 'player.swf',
			'file_name' => $file . 'player.swf',
			'file_path' => XS_FILES . '/players/'.$file.'/',
			'force_download' => 0,
			'burst_size' => 0,
			'throttle' => 0,
			'mime_types' => array
			(
				'swf' => 'application/x-shockwave-flash'
			)
		);
		return $config;
	}
	return false;
}
?>