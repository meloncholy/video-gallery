<?php

/**
* Information about a video search result. Stored in the cookie to keep results.
* 
* @package    VideoGallery
* @subpackage VideoItem
* @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
* @license    MIT licence. See licence.txt for details.
* @version    0.1
*/

class VideoItem extends CI_Model
{
	public $name;
	public $url;
	public $image;
	public $otherid;
	
	function __construct()
	{
		// Don't call parent as adds refs to many other classes (some recursively) & becomes too big to store in DB cookie entry.
		//parent::__construct();
	}

	// Gah. Multiple constructors via factory method.
	// http://alfonsojimenez.com/computers/multiple-constructors-in-php/
	
	public static function create($name, $url, $image, $otherid = null)
	{
		$video_item = new VideoItem();
		$video_item->name = $name;
		$video_item->url = $url;
		$video_item->image = $image;
		$video_item->otherid = $otherid;

		return $video_item;
	}
}
