<?php
/*

	xmoovStream Server Configuration - Embedded Media Server
	########################################################################
	
	xmoovStream Server Version: 0.8.4b
	
	Author: Eric Lorenzo Benjamin jr. stream (AT) xmoov (DOT) com
	License: Creative Commons Attribution-Noncommercial-Share Alike 3.0 United States License. http://stream.xmoov.com/support/licensing/	
	
	Description: 
	########################################################################
	
	This configuration serves the xmoovStream player loader.
	The xmoovStream player loader uses its own url to assemble a new url and flashvars containing the player and the media to load.
	
	* 'player' : the type of player to retrieve: video or audio
	* 'media' : the targeted file type: flv, mp4 etc
	* 'file' : the targeted file name without an extension
	
	The player loader.swf assembles the player url and flashvars from the following url.
	
	Url example with htaccess: http://mysite.com/embed/[player_type]/[media_type]/[file.ext]
	Ulr example without htaccess: http://mysite.com/embed?player=[player_type]&media=[media_type]&file=[file]
	
	htaccess file contents:
	########################################################################
	
	<IfModule mod_rewrite.c>
	RewriteEngine on
	RewriteRule ^([^/]+)/([^/]+)/([^/]+) index.php?player=$1&media=$2&file=$3 [QSA]
	</IfModule>
	
*/
define ('XS_PROVIDER', 'Embedded Media Server');
function init_server ()
{
	if (isset ($_GET['player']) && isset ($_GET['media']) && isset ($_GET['file']))
	{
	
		$config = array
		(
			'file' => "loader.swf",
			'file_path' => XS_FILES . '/players/',
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
