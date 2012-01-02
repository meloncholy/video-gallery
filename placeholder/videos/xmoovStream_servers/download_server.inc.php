<?php
/*

	xmoovStream Server Configuration - File Download Server
	########################################################################
	 
	xmoovStream Server Version: 0.8.4b
	
	Author: Eric Lorenzo Benjamin jr. stream (AT) xmoov (DOT) com
	License: Creative Commons Attribution-Noncommercial-Share Alike 3.0 United States License. http://stream.xmoov.com/support/licensing/	
	
	Description:
	########################################################################
	
	This configuration serves file downloads.
	
	* 'file' : the targeted download file
	* 'media' : the targeted sub folder
	
	Url example with htaccess: http://mysite.com/downloads/[media]/[file.ext]
	Ulr example without htaccess: http://mysite.com/downloads?media=[media]&file=[file.ext]
	
	htaccess file contents:
	########################################################################
	
	<IfModule mod_rewrite.c>
	RewriteEngine on
	RewriteRule ^([^/]+)/([^/]+) index.php?media=$1&file=$2 [QSA]
	</IfModule>
	
*/
define ('XS_PROVIDER', 'File Download Server');
function init_server ()
{
	
	if (isset ($_GET['media']) && isset ($_GET['file']))
	{
		if (eregi('protected',$_GET['media']))
		{
			return false;
		}
		$config = array
		(
			'file' => $_GET['file'],
			'file_path' => XS_FILES . '/' . $_GET['media'] . '/',
			'force_download' => true,
			'burst_size' => 0,
			'throttle' => 500,
			'mime_types' => array
			(
				'swf' => 'application/x-shockwave-flash',
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
				'qt' => 'video/quicktime',
				'mp3' => 'audio/mpeg',
				'wav' => 'audio/x-wav',
				'aif' => 'audio/x-aiff',
				'aifc' => 'audio/x-aiff',
				'aiff' => 'audio/x-aiff',
				'jpe' => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'jpg' => 'image/jpeg',
				'png'  => 'image/png',
				'svg' => 'image/svg+xml',
				'tif' => 'image/tiff',
				'tiff' => 'image/tiff',
				'gif' => 'image/gif',
				'txt' => 'text/plain',
				'xml' => 'text/xml',
				'css' => 'text/css',
				'htm' => 'text/html',
				'html' => 'text/html',
				'js' => 'application/x-javascript',
				'pdf' => 'application/pdf',
				'doc' => 'application/msword',
				'vcf' => 'text/x-vcard',
				'vrml' => 'x-world/x-vrml',
				'zip'  => 'application/zip'
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