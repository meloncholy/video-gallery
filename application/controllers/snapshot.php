<?php

/**
* Generate whole page snapshots of AJAXed pages for Google et al. 
* 
* @package    VideoGallery
* @subpackage Snapshot
* @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
* @license    MIT licence. See licence.txt for details.
* @version    0.1
* 
*/

class Snapshot extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('Settings');
	}
	
	/**
	 * Home page
	 */
	function index()
	{
		$ch = curl_init();

		// HTML wrapper
		curl_setopt($ch, CURLOPT_URL, site_url());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		echo curl_exec($ch);
		curl_close($ch);
	}
	
	/**
	 * Search. Output all videos (not just the first batch).
	 * 
	 * @param string $fields_string Search parameters
	 */
	function search($fields_string = '')
	{
		// Based on Google HTML snapshot guide
		// http://code.google.com/web/ajaxcrawling/docs/html-snapshot.html

		$doc = new DomDocument();
		$fragment = new DomDocument();
		$fragment_parent = null;
		$title_fragment = null;
		$ch = null;
		$json = null;
		$offset = 0;
		$count = 0;
		$batches = 0;
		$cookie_file = '';
		$node = null;
		$fields = array();
		$data = array();
		$pos = 0;
		$title = '';
		$search_title = '';
		
		if ($fields_string != '')
		{
			// Any filters?
			$pos = strpos($fields_string, '+');
			
			if ($pos === false)
			{
				$data['search'] = $fields_string;
			}
			else
			{
				$data['search'] = substr($fields_string, 0, $pos);
				$fields = explode('+', substr($fields_string, $pos + 1));

				for ($i = 0; $i < count($fields); $i++)
				{
					$pos = strpos($fields[$i], '=');
					$data['filters'][substr($fields[$i], 0, $pos)] = substr($fields[$i], $pos + 1);
				}
			}

			$search_title = $data['search'];
			$data = http_build_query($data);
		}
		
		// Need cookies for /search/batch/
		$cookie_file = tempnam(Settings::$batch_size, Settings::$cookie_file);
		$ch = curl_init();

		// HTML wrapper
		curl_setopt($ch, CURLOPT_URL, site_url());
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$doc->loadHTML(curl_exec($ch));
		
		if ($search_title != '')
		{
			$title = $search_title . ' - Search' . Settings::$site_title_post;
		}
		else
		{
			$title = 'Browse' . Settings::$site_title_post;
		}
		
		$doc->getElementsByTagName('title')->item(0)->nodeValue = $title;
		
		// Search
		curl_setopt($ch, CURLOPT_URL, site_url('/search/'));
		curl_setopt($ch, CURLOPT_POST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		// Appears to be returning a byte order mark at start of string, which mucks up json_decode
		// http://stackoverflow.com/questions/689185/json-decode-returns-null-php/689364#689364
		$json = json_decode(substr(curl_exec($ch), 3), true);
		$count = $json['count'];
		
		if ($count == 0)
		{
			// Add containing element
			$fragment_parent = $doc->createElement('div');
			$doc->getElementById('snapshotAlertTarget')->appendChild($fragment_parent);
			$fragment->loadHTML($json['error']);
			$node = $doc->importNode($fragment->documentElement->firstChild->firstChild, true);
			$fragment_parent->appendChild($node);
		}
		else
		{
			$batches = ceil($count / Settings::$batch_size);
			$fragment->loadHTML($json['html']);

			// Add containing element
			$fragment_parent = $doc->createElement('div');
			$fragment_parent->setAttribute('class', 'ScrollPane');
			$doc->getElementById('snapshotResultsTarget')->appendChild($fragment_parent);

			// Skip past <html><body> added by loadHTML
			foreach ($fragment->documentElement->firstChild->childNodes as $child_node)
			{
				$node = $doc->importNode($child_node, true);
				$fragment_parent->appendChild($node);
			}
			
			// Batches
			curl_setopt($ch, CURLOPT_URL, site_url('/search/batch/'));
			curl_setopt($ch, CURLOPT_POST, 1);
			
			for ($b = 1; $b < $batches; $b++)
			{
				$offset += Settings::$batch_size;
				curl_setopt($ch, CURLOPT_POSTFIELDS, "offset=$offset");
				$json = json_decode(substr(curl_exec($ch), 3), true);
				$fragment->loadHTML($json['html']);

				foreach ($fragment->documentElement->firstChild->childNodes as $child_node)
				{
					$node = $doc->importNode($child_node, true);
					$fragment_parent->appendChild($node);
				}
			}
			
			// Add alert if there is one.
			if (isset($json['error']) || isset($json['warning']))
			{
				// Add containing element
				$fragment_parent = $doc->createElement('div');
				$doc->getElementById('snapshotAlertTarget')->appendChild($fragment_parent);
				$fragment->loadHTML(isset($json['error']) ? $json['error'] : $json['warning']);
				$node = $doc->importNode($fragment->documentElement->firstChild->firstChild, true);
				$fragment_parent->appendChild($node);
			}
		}

		curl_close($ch);
		echo $doc->saveHTML();
	}
	
	/**
	 * Browse videos (get all videos).
	 */	
	function browse()
	{
		$this->search();
	}
	
	/**
	 * Get a particular video
	 *
	 * @param string $url Relative URL to video
	 * @param string $otherid ID of alternate name being used, if any
	 */	
	function video($url = '', $otherid = '')
	{
		$doc = new DomDocument();
		$fragment = new DomDocument();
		$fragment_parent = null;
		$ch = null;
		$json = null;
		$offset = 0;
		$count = 0;
		$batches = 0;
		$cookie_file = '';
		$node = null;
		$fields = array();
		$data = array();
		$pos = 0;

		// Need cookies for /search/batch/ (related videos)
		$cookie_file = tempnam(Settings::$batch_size, Settings::$cookie_file);
		$ch = curl_init();

		// HTML wrapper
		curl_setopt($ch, CURLOPT_URL, site_url());		
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$doc->loadHTML(curl_exec($ch));
		
		// Get video
		curl_setopt($ch, CURLOPT_URL, site_url("/video/load/$url/" . ($otherid == '' ? '' : "$otherid/")));
		$json = json_decode(curl_exec($ch), true);

		$title = $json['title'] . Settings::$site_title_post;
		$doc->getElementsByTagName('title')->item(0)->nodeValue = $title;

		// Video
		$fragment->loadHTML($json['html']);
		$fragment_parent = $doc->getElementById('snapshotVideoTarget');
		
		// Skip past <html><body> added by loadHTML
		foreach ($fragment->documentElement->firstChild->childNodes as $child_node)
		{
			$node = $doc->importNode($child_node, true);
			$fragment_parent->appendChild($node);
		}
		
		// Related videos
		if (isset($json['count']))
		{
			$related_count = $json['count'];
			$fragment->loadHTML($json['relatedHtml']);

			// Add containing element
			$fragment_parent = $doc->createElement('div');
			$fragment_parent->setAttribute('class', 'ScrollPane');
			$doc->getElementById('snapshotRelatedVideosTarget')->appendChild($fragment_parent);

			// Skip past <html><body> added by loadHTML
			foreach ($fragment->documentElement->firstChild->childNodes as $child_node)
			{
				$node = $doc->importNode($child_node, true);
				$fragment_parent->appendChild($node);
			}
			
			// Related video batches
			curl_setopt($ch, CURLOPT_URL, site_url('/search/batch/'));
			curl_setopt($ch, CURLOPT_POST, 1);
			
			for ($b = 1; $b < $batches; $b++)
			{
				$offset += Settings::$batch_size;
				curl_setopt($ch, CURLOPT_POSTFIELDS, "offset=$offset");
				$json = json_decode(substr(curl_exec($ch), 3), true);
				$fragment->loadHTML($json['html']);

				foreach ($fragment->documentElement->firstChild->childNodes as $child_node)
				{
					$node = $doc->importNode($child_node, true);
					$fragment_parent->appendChild($node);
				}
			}
		}
		
		curl_close($ch);
		echo $doc->saveHTML();
	}
	
	/**
	 * Show feedback form.
	 * 
	 * @param string $url Which form to show (contact or suggest name)
	 */
	function feedback($url = '')
	{
		$doc = new DomDocument();
		$fragment = new DomDocument();
		$fragment_parent = null;
		$ch = null;
		$json = null;
		$node = null;
		$url = strtolower($url);

		if ($url != 'contact' && $url != 'name') return;

		$ch = curl_init();		

		// HTML wrapper
		curl_setopt($ch, CURLOPT_URL, site_url());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$doc->loadHTML(curl_exec($ch));
		
		// Get contact form
		curl_setopt($ch, CURLOPT_URL, site_url("/feedback/$url/"));
		$json = json_decode(curl_exec($ch), true);
		$title = $json['title'] . Settings::$site_title_post;
		$doc->getElementsByTagName('title')->item(0)->nodeValue = $title;
		$fragment_parent = $doc->getElementById('snapshotFeedbackTarget');
		$fragment->loadHTML($json['html']);
		$node = $doc->importNode($fragment->documentElement->firstChild->firstChild, true);
		$fragment_parent->appendChild($node);

		curl_close($ch);
		echo $doc->saveHTML();		
	}
}