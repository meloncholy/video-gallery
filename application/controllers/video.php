<?php

/**
 * Show a video and load related videos.
 * 
 * @package    VideoGallery
 * @subpackage Video
 * @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
 * @license    MIT licence. See licence.txt for details.
 * @version    0.1
 * 
 */

class Video extends CI_Controller 
{
	private $batch_size;
	
	function __construct()
	{
		parent::__construct();
		$this->load->model(array('VideoItem', 'Crumbs'));
		$this->load->helper('url');
		$this->load->library(array('session', 'Bitly', 'Settings'));
		$this->batch_size = Settings::$batch_size;
	}
	
	/**
	 * No default function so can use consistent parameters elsewhere.
	 */
	function index()
	{
		$this->load->view('no_video_view');
	}
	
	/**
	 * Show a video
	 *
	 * @param string $url Relative URL to the video
	 * @param string $otherid ID of alternate name, if any
	 */	
	function load($url = null, $otherid = null)
	{
		if ($url == null)
		{
			$this->load->view('no_video_view');
			return;
		}
		
		$this->_video($url, $otherid, false);
	}
	
	/**
	 * Show a video embedded on another site.
	 * 
	 * @param string $url Relative URL to the video
	 * @param string $otherid ID of alternate name, if any
	 */	
	function embed($url = null, $otherid = null)
	{
		if ($url == null)
		{
			$this->load->view('no_video_embed_view');
			return;
		}
		
		$this->_video($url, $otherid, true);
	}
	
	/**
	 * Get info about a video from the database and build a list of similar and related videos.
	 * Similar videos: those that seem to match the current video by name and filter.
	 * Related videos: others from the current search (or, if no search, the similar videos).
	 * 
	 * @param string $url Relative URL to the video
	 * @param string $otherid ID of alternate name, if any
	 * @param boolean $embed Is the video embedded elsewhere?
	 */	
	protected function _video($url, $otherid, $embed)
	{
		$view = array();
		$video = null;
		$related_videos = null;
		
		$url = $this->db->escape_str($url);
		$video = $this->db->query("SELECT videos.name, videos.difficulty, videos.strength, videos.flexibility, videos.invert, videos.spin, videos.transition, videos.url, videos.image, videos.video, othernames.id AS otherid, othernames.name AS othername FROM videos LEFT JOIN othernames ON videos.id=othernames.videoid WHERE videos.url='$url'");
		
		if ($video->num_rows() == 0)
		{
			if ($embed)
			{
				$this->load->view('embed_unknown_video_view');
			}
			else
			{
				$this->load->view('unknown_video_view');
			}				
			return;
		}
		
		$view['name'] = $video->row(0)->name;
		$view['difficulty'] = $video->row(0)->difficulty;
		$view['strength'] = $video->row(0)->strength;
		$view['flexibility'] = $video->row(0)->flexibility;
		$view['invert'] = $video->row(0)->invert;
		$view['spin'] = $video->row(0)->spin;
		$view['transition'] = $video->row(0)->transition;
		$view['url'] = $video->row(0)->url;
		$view['image'] = $video->row(0)->image;
		$view['video'] = $video->row(0)->video;
		$view['otherid'] = $otherid;
		
		if ($video->row(0)->othername != null)
		{
			foreach ($video->result() as $alt_name)
			{
				if ($alt_name->otherid == $otherid) $view['ref_name'] = $alt_name->othername;
				$view['alt_names'][] = $alt_name->othername;
			}
		}

		if ($embed)
		{
			$this->load->view('embed_video_view', $view);
		}
		else
		{
			$this->_get_related_videos($view);

			// Similar videos. Build first for if no related videos.
			$similar_videos = $this->db->query("(SELECT * FROM (SELECT name, url, image FROM videos WHERE name<>'" . $view['name'] . "' AND difficulty=" . $view['difficulty'] . " AND strength=" . $view['strength'] . " AND flexibility=" . $view['flexibility'] . " AND invert=" . $view['invert'] . " AND SPIN=" . $view['spin'] . " AND transition=" . $view['transition'] . " ORDER BY RAND()) AS t1) UNION" . 
				"(SELECT * FROM (SELECT name, url, image FROM videos WHERE name<>'" . $view['name'] . "' AND difficulty=" . $view['difficulty'] . " AND (strength=" . $view['strength'] . " OR flexibility=" . $view['flexibility'] . " OR invert=" . $view['invert'] . " OR spin=" . $view['spin'] . " OR transition=" . $view['transition'] . ") ORDER BY RAND()) AS t2) LIMIT " . Settings::$max_related_videos);

			// This video = first video on list. Use other name if that's to what was linked. 
			$view['similar_videos'][] = VideoItem::create((isset($view['ref_name']) ? $view['ref_name'] : $view['name']), $view['url'], $view['image'], $view['otherid']);
			
			foreach ($similar_videos->result() as $similar_video)
			{
				$view['similar_videos'][] = VideoItem::create($similar_video->name, $similar_video->url, $similar_video->image);
			}
			
			if (!isset($view['videos']))
			{
				// Use similar videos for related videos list.
				$view['videos'] = $view['similar_videos'];
				$view['count'] = count($view['videos']);
			}

			$offset = 0;
			$view['start'] = $offset;
			$view['end'] = min($view['count'], $offset + $this->batch_size);
			// Limit similar videos to at most 7. Got more results so could use for related videos. 
			$view['similar_videos'] = array_slice($view['similar_videos'], 1, Settings::$max_similar_videos);			

			$this->load->view('video_view', $view);
		}		
	}
	
	/**
	 * Return related videos for list at the side.
	 * 
	 * @param array $view Byref info added here to be passed to the view.
	 */
	private function _get_related_videos(&$view)
	{
		$video_width = 95;
		$video_idx = 0;
		$prev_idx = 0;
		$next_idx = 0;
		$screen_size = array();
		$content_width = 0;
		$videos = 0;
		
		// If the current search results cache is valid, use it. 
		if (Crumbs::is_cache_valid())
		{
			$videos_list = $this->session->userdata('videos_list');
			
			if (is_array($videos_list))
			{
				// Find index of current video.
				for ($i = 0; $i < count($videos_list); $i++)
				{
					if ((is_null($view['otherid']) && is_null($videos_list[$i]->otherid) && $videos_list[$i]->url == $view['url']) || (!is_null($view['otherid']) && $videos_list[$i]->otherid == $view['otherid'] && $videos_list[$i]->url == $view['url']))
					{
						if ($i > 0) $view['prev'] = $videos_list[$i - 1];
						if ($i < count($videos_list) - 1) $view['next'] = $videos_list[$i + 1];
						$video_idx = $i;
						break;
					}
				}
				
				$view['video_idx'] = $video_idx;
				$view['videos'] = $videos_list;
				$view['count'] = count($videos_list);
			}
		}
		else
		{
			// Clear videos list.
			$this->session->userdata('videos_list', array());
			$view['count'] = 0;
		}
	}
}