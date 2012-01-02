<?php
/*

	Default configuration file
	########################################################################
	
	xmoovStream Server Version: 0.8.4b
	
	Author: Eric Lorenzo Benjamin jr. stream (AT) xmoov (DOT) com
	License: Creative Commons Attribution-Noncommercial-Share Alike 3.0 United States License. http://stream.xmoov.com/support/licensing/	
	
	Description:
	########################################################################
	
	This is the default xmoovStream configuration. These settings are overridden by the server configuration.
	
*/

# Define logs directory
define ("XS_LOGS", XS_DIR . "/logs");

# Define file path
define ("XS_FILES", XS_DIR . "/xmoovStream_files");

$defaults = array
(
	'file' => null,
	'output_file_name' => null,
	'file_path' => XS_FILES,
	'use_error_handling' => true,
	'use_activity_handling' => true,
	'log_errors' => array ('file_open', 'file_empty', 'file_path', '404', 'security', 'file_type', 'time_limit', 'magic_quotes'),
	'show_errors' => array ('file_open', 'file_empty', 'file_path', '404', 'security', 'file_type'),
	'notify_errors' => array ('file_open', 'file_empty', 'file_path', '404', 'security', 'file_type'),
	'notify_email' => null,
	'log_activity' => array ('flv_random_access', 'file_access', 'download_complete', 'partial_download_complete', 'user_abort'),
	'use_http_range' => 1,
	'force_download' => 0,
	'burst_size' => 0,
	'buffer_size' => 8,
	'throttle' => 0,
	'mime_types' => array
	(
		'swf' => 'application/x-shockwave-flash',
		'flv' => 'video/x-flv',
		'f4v' => 'video/mp4',
		'f4p' => 'video/mp4',
		'mp4' => 'video/mp4',
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
?>