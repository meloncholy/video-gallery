<?php
/**
 * Added some code from the random-but-consistent file selector to serve a video that exists. 
 * 
 * Andrew Weeks http://meloncholy.com
 */

if (isset($_GET['file']))
{
	$folder = 'xmoovStream_files/video/';

	$ext_list = array();
	$ext_list['gif'] = 'image/gif';
	$ext_list['jpg'] = 'image/jpeg';
	$ext_list['jpeg'] = 'image/jpeg';
	$ext_list['png'] = 'image/png';
	$ext_list['flv'] = 'video/x-flv';
	
	$file = null;
	$vid = null;
	$vids = null;

	$file = pathinfo($_GET['file']);
	// Just in case there's other stuff in the folder too (like this file)
	if (!array_key_exists($file['extension'], $ext_list)) return;
	if (substr($folder, -1) != '/') $folder .= '/';

	// Select a file based on the given extension.
	$vids = glob($folder . '*.' . $file['extension']);
	// Overwrite file parameter for streamer.
	$_GET['file'] = pathinfo($vids[abs(crc32($file['basename'])) % count($vids)], PATHINFO_BASENAME);
}

include(dirname(__FILE__) . '/include.php');
xmoovStream('video_server', true);
