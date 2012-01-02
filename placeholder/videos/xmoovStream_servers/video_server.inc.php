<?php
/*

	xmoovStream Server Configuration - Video Pseudo Streaming Server
	########################################################################
	
	xmoovStream Server Version: 0.8.4b
	
	Author: Eric Lorenzo Benjamin jr. stream (AT) xmoov (DOT) com
	License: Creative Commons Attribution-Noncommercial-Share Alike 3.0 United States License. http://stream.xmoov.com/support/licensing/	
	
	Description:
	########################################################################
	
	This configuration serves video files.
	
	* 'file' the targeted video file
	* 'start' the start byte position for FLV pseudo streaming
	
	Url example with htaccess: http://mysite.com/videos/[file.ext]
	Ulr example without htaccess: http://mysite.com/videos?file=[file.ext]
	
	Changes:
	1. Added intval($_GET['start'])
	########################################################################
	
	htaccess file contents:
	########################################################################
	
	<IfModule mod_rewrite.c>
	RewriteEngine on
	RewriteRule ^([^/]*)$ index.php?file=$1 [QSA]
	</IfModule>
	
*/
define ('XS_PROVIDER', 'Streaming Video Server');
function init_server ()
{
	
	if (isset($_GET['file']))
	{
		$position = isset ($_GET['start']) ? intval($_GET['start']) : 0;
		$config = array
		(
			'file' => $_GET['file'],
			'file_path' => XS_FILES . '/video/',
			'position' => $position,
			'use_http_range' => 1,
			'force_download' => 0,
			'burst_size' => 500,
			'throttle' => 320,
			'mime_types' => array
			(
				'flv' => 'video/x-flv',
				'mp4' => 'video/mp4',
				'f4v' => 'video/mp4',
				'f4p' => 'video/mp4',
				'asf' => 'video/x-ms-asf',
				'asr' => 'video/x-ms-asf',
				'asx' => 'video/x-ms-asf',
				'avi' => 'video/x-msvideo',
				'mpa' => 'video/mpeg',
				'mpe' => 'video/mpeg',
				'mpeg' => 'video/mpeg',
				'mpg' => 'video/mpeg',
				'mpv2' => 'video/mpeg',
				'mov' => 'video/quicktime',
				'movie' => 'video/x-sgi-movie',
				'mp2' => 'video/mpeg',
				'qt' => 'video/quicktime'
			)
		);
		return $config;
	}
	return false;
}
?>