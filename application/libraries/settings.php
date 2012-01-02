<?php

/**
 * Program constants all in one place. 
 * 
 * @package    VideoGallery
 * @subpackage Settings
 * @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
 * @license    MIT licence. See licence.txt for details.
 * @version    0.1
 */

class Settings
{
	// Number of results to return for infinite scroll
	public static $batch_size = 20;
	// Approx number of videos in the database (used for info purposes only in a view)
	public static $video_count = 300;
	// CURL cookie settings (for Snapshot class)
	public static $cookie_folder = '/tmp';
	public static $cookie_file = 'snapshotcookie';
	public static $site_title = 'Video Gallery';
	public static $site_title_post = ' | Video Gallery';
	public static $site_description = 'This Video Gallery is a web app with lots of jQuery goodness. Yum.';
	// If not searching (because loaded site on a video page), show this many related videos at the side.
	public static $max_related_videos = 19;
	// And this many at the bottom. See Video::$video() for more.
	public static $max_similar_videos = 7;
	public static $pretty_link = 'video';
	public static $jquery_cdn_url = 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js';
}