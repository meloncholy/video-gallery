<?php

/**
 * Get (first) results for a search. 
 * 
 * @package    VideoGallery
 * @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
 * @license    MIT licence. See licence.txt for details.
 * @version    0.1
 */

$data['count'] = $count;
$data['videoIdx'] = 0;
include('batch_builder_view.php');
echo json_encode($data);