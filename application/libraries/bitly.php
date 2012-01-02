<?php

/**
 * Bit.ly short URL generator. Uses Bit.ly API 3
 * 
 * Code basically from http://davidwalsh.name/bitly-api-php
 * 
 * @package    VideoGallery
 * @subpackage Bitly
 * @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com (perhaps), 2010 David Walsh http://davidwalsh.name
 * @license    MIT licence. See licence.txt for details.
 * @version    0.1
 */

class Bitly
{
	private $login = 'meloncholy';
	private $api_key = 'xxxxxxxxxxx';
	
	/**
	 * Returns shortened URL
	 *
	 * @param string $url URL to shorten
	 * @param string $login Bit.ly username
	 * @param string $api_key Bit.ly API key
	 * @param string $format ?
	 * @return string Shortened URL
	 */
	function shorten($url, $login, $api_key, $format='txt') 
	{
		$connectURL = 'http://api . bit . ly/v3/shorten?login=' . $login . '&apiKey=' . $api_key . '&uri=' . urlencode($url) . '&format=' . $format;
		return curl_get_result($connectURL);
	}

	/**
	 * Returns expanded URL for Bit.ly short URL
	 *
	 * @param string $url Short URL to expand
	 * @param string $login Bit.ly username
	 * @param string $api_key Bit.ly API key
	 * @param string $format ?
	 * @return string Expanded URL
	 */
	function expand($url, $login, $api_key, $format='txt') 
	{
		$connectURL = 'http://api . bit . ly/v3/expand?login=' . $login . '&apiKey=' . $api_key . '&shortUrl=' . urlencode($url) . '&format=' . $format;
		return curl_get_result($connectURL);
	}

	/**
	 * Call Bit.ly with cURL
	 *
	 * @param string $url Bit.ly URL to which to connect and parameters and stuff
	 * @return string Shortened URL
	 */
	private function curl_get_result($url) 
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}
