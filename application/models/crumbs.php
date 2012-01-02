<?php

/**
* Simple cookie class for storing video player size and whether moves cache is valid.
*
* @package    VideoGallery
* @subpackage Crumbs
* @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
* @license    MIT licence. See licence.txt for details.
* @version    0.1
*/

class Crumbs extends CI_Model
{
	/**
	 * See if other videos cache is still valid.
	 * Set false by JS if resize window or on search.
	 * Set true (in here) by PHP on search. 
	 *
	 * @return bool Is cache valid?
	 */
	function is_cache_valid()
	{
		if (($crumbs = $this->input->cookie('vgcrumbs')) !== false)
		{
			$crumbs = explode(';', $crumbs);
			return $crumbs[1] == 'true';
		}

		return false;
	}
	
	/**
	 * Mark moves cache as valid and create cookie if there isn't one.
	 */
	
	function set_cache_valid()
	{
		if (($crumbs = $this->input->cookie('vgcrumbs')) !== false)
		{
			$crumbs = explode(';', $crumbs);
			$this->input->set_cookie(array(
				'name' => 'vgcrumbs',
				'value' => $crumbs[0] . ';true',
				'expire' => 0, 
				'path' => '/'
			));
		}
		else
		{
			$this->input->set_cookie(array(
				'name' => 'vgcrumbs',
				'value' => 'Medium;true',
				'expire' => 0,
				'path' => '/'
			));
		}
	}
}