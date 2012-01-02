<?php 

/**
 * Build HTML for a batch of results. Called by other views.
 * 
 * @package    VideoGallery
 * @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
 * @license    MIT licence. See licence.txt for details.
 * @version    0.1
 */

if ($count > 0)
{
	ob_start();

	for ($i = $start; $i < $end; $i++)
	{
		$v = $videos[$i];
		echo '<a id="video-' . $v->url . (isset($v->otherid) ? '-' . $v->otherid : '') . '" href="#!' . Settings::$pretty_link . '/' . $v->url . (isset($v->otherid) ? '/' . $v->otherid : '') . '" class="Box' . ($i == $start ? " Batch" : "") . '" title="Click to view the ' . $v->name . '"><img src="' . site_url('images/thumb/' . $v->image) . '" alt="The ' . $v->name . '" /><span>' . $v->name . '</span></a>';
	}
	
	$html = isset($html) ? $html : 'html';
	$data[$html] = ob_get_clean();
	$data['start'] = $start;
	$data['end'] = $end;
}

if (isset($few_videos)) $data['warning'] = "<p>Did you mean to search for <a class=\"SearchLink\" href=\"#!\">$alt_search</a> instead? <a class=\"SuggestName\" href=\"#!\">Suggest a name</a>.</p>";
if (isset($alt_videos)) $data['error'] = "<p>No videos found for <span class=\"Search\">$unescaped_search</span>. Showing results for <a class=\"SearchLink\" href=\"#!\">$alt_search</a> instead. <a class=\"SuggestName\" href=\"#!\">Suggest a name</a>.</p>";
if (isset($no_videos)) $data['error'] = "<p>No videos match your search. Sorry. <a class=\"SuggestName\" href=\"#!\">Suggest a name</a>.</p>";
if (isset($filters_videos)) $data['error'] = "<p>No videos match your search, but I do have something <a class=\"FiltersLink\" href=\"#!\">without your filters</a>. <a class=\"SuggestName\" href=\"#!\">Suggest a name</a>.</p>";