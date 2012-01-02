<?php

/**
RANDOM-BUT-CONSISTENT FILE SELECTOR
	
Version 0.1
Copyright (c) 2011 Andrew Weeks http://meloncholy.com

Licensed under the MIT licence
	
Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Originally based on AUTOMATIC IMAGE ROTATOR (Version 2.2) by Dan P. Benjamin http://benjamin.org/dan/,
though there's not much of the original code left. 
		 
Also with a little help (introducing me to crc32) from here http://www.webmasterworld.com/php/3782696.htm


ABOUT
This PHP script will randomly select a file from a folder on your webserver.  You can then link to
it as you would any standard file and you'll see a random-but-consistently-selected file.
	
When you want to add or remove files from the rotation-pool, just add or remove them from the rotation folder.


INSTRUCTIONS
1. Modify the $folder setting in the configuration section below.
2. Add file types if needed (most users can ignore that part).
3. Upload this file to your server, probably in the same folder as the files you want to serve.
4. Link to the file as you would any other file:

	<a href="http://example.com/rotate.php?file=gorilla.flv">link</a>
	<img src="http://example.com/rotate.php?file=gorilla.jpg" />
	
	The file returned will be of that same type (flv or jpg above). It will not be gorilla.flv, 
	but you will get the same one every time you ask for gorilla.flv. 
*/

/* 
	Set $folder to the full or relative path to the location of your files to serve.
	If this file will be in the same folder as your	images then just leave it as '.'
*/

$folder = '.';

/*	
	If you'd like to enable additional image types, add them here. For example:
	
	PDF Files:

		$ext_list['pdf'] = 'application/pdf';
	
	CSS Files:

		$ext_list['css'] = 'text/css';

	You can even serve up HTML files:

		$ext_list['html'] = 'text/html';
		$ext_list['htm'] = 'text/html';

	Just be sure your mime-type definition is correct!

*/

$ext_list = array();
$ext_list['gif'] = 'image/gif';
$ext_list['jpg'] = 'image/jpeg';
$ext_list['jpeg'] = 'image/jpeg';
$ext_list['png'] = 'image/png';
$ext_list['flv'] = 'video/x-flv';
	
$file = null;
$vid = null;
$vids = null;
$content_type = null;

if (!isset($_GET['file'])) return;
$file = pathinfo($_GET['file']);
// Just in case there's other stuff in the folder too (like this file)
if (!array_key_exists($file['extension'], $ext_list)) return;
if (substr($folder, -1) != '/') $folder .= '/';

// Select a file based on the given extension.
$vids = glob($folder . '*.' . $file['extension']);
$vid = $vids[abs(crc32($file['basename'])) % count($vids)];

$content_type = 'Content-type: ' . $ext_list[$file['extension']];
header ($content_type);
readfile($vid);
