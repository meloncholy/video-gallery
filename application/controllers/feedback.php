<?php

/**
* Feedback forms (new name & contact) controller.
* 
* @package    VideoGallery
* @subpackage Feedback
* @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
* @license    MIT licence. See licence.txt for details.
* @version    0.1
*/

class Feedback extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url', 'form'));
		$this->load->library('Settings');
	}
	
	/**
	 * Load site home page by if no form specified
	 */	
	function index()
	{
		$this->load->view('home_view');
	}
	
	/**
	 * Show new name for video form.
	 *
	 * @post string $url relative URL of the video
	 * @post string $name Current name of the video
	 */	
	function name()
	{
		$sql = '';
		$url = $this->input->post('url', true);
		$name = $this->input->post('name', true);
		
		$url = $url == 'null' || $url == '' ? null : $url;
		$name = $name == 'null' || $name == '' ? null : $name;
		
		$view['url'] = $url;		
		$view['name'] = $name;
		$this->load->view('suggest_name_view', $view);
	}
	
	/**
	 * Save new name for the video and show thank you form. 
	 *
	 * @post string $url Relative URL of video
	 * @post string $name Current name of video
	 * @post string $suggestion New (alternate) name for video
	 * @post string $comment Extra info from submitter
	 * @post string $author Submitter name
	 * @post string $email Submitter email
	 */	
	function submit_name()
	{
		/*if (!is_null($url))
		{
			// NEED TO PASS IN ACTUAL NAME, NOT RELY ON URL AS WILL ONLY BE PROPER NAME
			//$sql = "SELECT name FROM videos, othernames WHERE name.id=othernames.videoid AND videos.url=$url";
			$sql = "SELECT name FROM videos WHERE videos.url=$url";
			$blah = $this->db->query($sql);
			$blah->*/

		$url = $this->db->escape_str($this->input->post('url', true));
		$name = $this->db->escape_str($this->input->post('name', true));
		$suggestion = $this->db->escape_str($this->input->post('suggestion', true));
		$comment = $this->db->escape_str($this->input->post('comment', true));
		$author = $this->db->escape_str($this->input->post('author', true));
		$email = $this->db->escape_str($this->input->post('email', true));
		
		$sql = "INSERT INTO suggestions (url, name, suggestion, comment, author, email) VALUES ('$url', '$name', '$suggestion', '$comment', '$author', '$email');";
		$this->db->query($sql);
		$this->load->view('submit_name_thanks_view');
	}
	
	/**
	 * Show contact form.
	 */
	function contact()
	{
		$this->load->view('contact_view');
	}

	/**
	 * Accept contact form and show thank you form.
	 *
	 * @post string $comment Contact form body text
	 * @post string $author Submitter name
	 * @post string $email Submitter email
	 */	
	function submit_contact()
	{
		$comment = $this->db->escape_str($this->input->post('comment', true));
		$author = $this->db->escape_str($this->input->post('author', true));
		$email = $this->db->escape_str($this->input->post('email', true));
		
		$sql = "INSERT INTO contact (comment, author, email) VALUES ('$comment', '$author', '$email');";
		$this->db->query($sql);
		$this->load->view('contact_thanks_view');
	}
}