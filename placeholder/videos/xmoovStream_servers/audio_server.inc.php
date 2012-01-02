<?php 
/*

	xmoovStream Server Configuration - Streaming Audio Server
	########################################################################
	
	xmoovStream Server Version: 0.8.4b
	
	Author: Eric Lorenzo Benjamin jr. stream (AT) xmoov (DOT) com
	License: Creative Commons Attribution-Noncommercial-Share Alike 3.0 United States License. http://stream.xmoov.com/support/licensing/	
	
	Description:
	########################################################################
	
	This configuration serves audio files.
	
	* 'file' : the targeted audio file
	
	Url example with htaccess: http://mysite.com/audio/[file.ext]
	Ulr example without htaccess: http://mysite.com/audio?file=[file.ext]
	
	htaccess file contents:
	########################################################################
	
	<IfModule mod_rewrite.c>
	RewriteEngine on
	RewriteRule ^([^/]*)$ index.php?file=$1 [QSA]
	</IfModule>

*/
define ('XS_PROVIDER', 'Streaming Audio Server');
function init_server ()
{
	if (isset ($_GET['file']))
	{
		$config = array
		(
			'file' => $_GET['file'],
			'file_path' => XS_FILES . '/audio/',
			'force_download' => 0,
			'burst_size' => 120,
			'throttle' => 84,
			'mime_types' => array
			(
				'mp3' => 'audio/mpeg'
			)
		
		);
		return $config;
	}
	return false;
}
?>