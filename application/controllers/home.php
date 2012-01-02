<?php

/**
* Site home
* 
* @package    VideoGallery
* @subpackage Home
* @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
* @license    MIT licence. See licence.txt for details.
* @version    0.1
*/

class Home extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url', 'form'));
		$this->load->library('Settings');
	}
	
	/**
	 * Show page header and body content. Should only happen once as the rest is AJAXed.
	 */
	function index()
	{
		$this->load->view('header_view');
		$this->load->view('home_view');
	}
	
	/**
	 * Load a new dictionary for search spelling correction. 
	 * Leave this commented out unless you need it.
	 * 
	 * @param $file Text file of words or phrases to learn (one per line)
	 */
	/*function train($file = 'videos.txt')
	{
		$this->load->model('Spell');
		$spell = new Spell();
		$spell->train($file);
	}*/
}
