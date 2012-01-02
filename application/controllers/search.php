<?php

/**
* Video search and browse
* 
* @package    VideoGallery
* @subpackage Search
* @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
* @license    MIT licence. See licence.txt for details.
* @version    0.1
* 
* HOW SEARCH WORKS
* 
* The difficult
*/

class Search extends CI_Controller
{
	// Number of results to return at once with infinite scroll
	private $batch_size;
	//private $idx = 0;
	// Dictionary of video names (for spell checking)
	public $spell;
	
	function __construct()
	{
		parent::__construct();
		$this->load->model(array('VideoItem', 'Spell', 'Crumbs'));
		$this->load->helper('url');
		$this->load->library(array('session', 'PorterStemmer', 'Settings'));
		$this->spell = new Spell();
		$this->batch_size = Settings::$batch_size;
	}
	
	/**
	 * Search for videos
	 *
	 * @post string $search String to match
	 * @post array $filters Filters to apply to search
	 *
	 */	
	function index()
	{
		$search = null;
		$unescaped_search = null;
		$alt_search = null;
		$sql = null;
		$alt_sql = null;
		$filters_sql = null;
		$videos = null;
		$alt_videos = null;
		$filters_videos = null;
		$count = 0;
		$alt_count = 0;
		$filters_count = 0;
		$view = array();
		$videos_list = array();
		$filters = array();
		
		// Want to escape LIKE wildcards in the query, but not if saying that string wasn't found in an error.
		$unescaped_search = $this->input->post('search', true);
		$search = $this->db->escape_like_str($unescaped_search);
		$filters = $this->input->post('filters', true);
		
		if ($filters == 'null' || $filters == '')
		{
			unset($filters);
			$filters['has_filters'] = false;
		}
		else
		{
			$filters['has_filters'] = true;
			
			foreach ($filters as $key => $val)
			{
				if ($val == 'true') $filters[$key] = '1';	
				elseif ($val == 'false') $filters[$key] = '0';
				elseif ($val == 'null') $filters[$key] = null;
			}
		}
		
		// Got a search string?
		if ($search)
		{
			$this->_build_query($search, $alt_search, $filters, $sql, $alt_sql, $filters_sql);
			
			$videos = $this->db->query($sql);
			$count = $videos->num_rows();
			
			if (isset($alt_sql))
			{
				$alt_videos = $this->db->query($alt_sql);
				$alt_count = $alt_videos->num_rows();
			}
			
			if ($filters['has_filters'])
			{
				$filters_videos = $this->db->query($filters_sql);
				$filters_count = $filters_videos->num_rows();
			}
			
			if ($count == 0)
			{
				if ($alt_count > 0)
				{
					
					$view['alt_videos'] = true;
				}
				elseif ($filters_count > 0)
				{
					$view['filters_videos'] = true;
				}
				else
				{
					$view['no_videos'] = true;
				}
			}
			elseif ($count < 3 && $alt_count > 2 * $count)
			{
				$view['few_videos'] = true;
			}
			
			// Use array_values to reindex
			if (isset($alt_videos))
			{
				$view['videos'] = array_values(array_unique(array_merge($videos->result(), $alt_videos->result()), SORT_REGULAR));
			}
			else
			{
				$view['videos'] = $videos->result();
			}

			$merged_count = count($view['videos']);
			$view['count'] = $merged_count;
			$view['start'] = 0;
			$view['end'] = min($merged_count, $this->batch_size);
			$view['search'] = $search;
			$view['unescaped_search'] = $unescaped_search;
			$view['alt_search'] = $alt_search;
			$view['filters_count'] = $filters_count;
			
			//$session['search'] = $search;
			$session['sql'] = $sql;
			$session['alt_sql'] = $alt_sql;
			$session['count'] = $count;
			$session['merged_count'] = $merged_count;
			
			for ($i = 0; $i < $merged_count; $i++)
			{
				//$videos_list[$view['videos'][$i]->url] = $view['videos'][$i]->name;
				//$videos_list[] = new array('name' => $view['videos'][$i]->name, 'url' => $view['videos'][$i]->url, 'ref_id' => $view['videos'][$i]->otherid, 'image' => $view['videos'][$i]->image);
				$videos_list[] = VideoItem::create($view['videos'][$i]->name, $view['videos'][$i]->url, $view['videos'][$i]->image, $view['videos'][$i]->otherid);
			}
			
			$session['videos_list'] = $videos_list;
		}
		elseif ($filters['has_filters'])
		{
			$sql = $this->_build_filters_query($filters);
			$videos = $this->db->query($sql);
			$count = $videos->num_rows();
			
			$filters_sql = $this->_build_browse_query();
			$filters_videos = $this->db->query($filters_sql);
			$filters_count = $filters_videos->num_rows();
			
			if ($count == 0)
			{
				if ($filters_count > 0)
				{
					$view['filters_videos'] = true;
				}
				else
				{
					$view['no_videos'] = true;
				}
			}
			
			$view['videos'] = $videos->result();
			$view['count'] = $count;
			$view['start'] = 0;
			$view['end'] = min($count, $this->batch_size);
			$view['search'] = $search;
			$view['unescaped_search'] = $unescaped_search;
			
			//$session['search'] = '';
			$session['sql'] = $sql;
			$session['alt_sql'] = '';
			$session['count'] = $count;
			$session['merged_count'] = $count;

			for ($i = 0; $i < $count; $i++)
			{
				//$videos_list[] = array('name' => $view['videos'][$i]->name, 'url' => $view['videos'][$i]->url, 'ref_id' => $view['videos'][$i]->otherid, 'image' => $view['videos'][$i]->image);
				$videos_list[] = VideoItem::create($view['videos'][$i]->name, $view['videos'][$i]->url, $view['videos'][$i]->image, $view['videos'][$i]->otherid);
			}
			
			$session['videos_list'] = $videos_list;
		}
		else
		{
			$sql = $this->_build_browse_query();
			$videos = $this->db->query($sql);
			$count = $videos->num_rows();

			if ($count == 0) $view['no_videos'] = true;
			$view['videos'] = $videos->result();
			$view['count'] = $count;
			$view['start'] = 0;
			$view['end'] = min($count, $this->batch_size);
			$view['search'] = $search;
			$view['unescaped_search'] = $unescaped_search;
			
			$session['sql'] = $sql;
			$session['alt_sql'] = '';
			$session['count'] = $count;
			$session['merged_count'] = $count;
			
			for ($i = 0; $i < $count; $i++)
			{
				//$videos_list[] = array('name' => $view['videos'][$i]->name, 'url' => $view['videos'][$i]->url, 'ref_id' => $view['videos'][$i]->otherid, 'image' => $view['videos'][$i]->image);
				$videos_list[] = VideoItem::create($view['videos'][$i]->name, $view['videos'][$i]->url, $view['videos'][$i]->image, $view['videos'][$i]->otherid);
			}

			$session['videos_list'] = $videos_list;
		}

		$this->session->set_userdata($session);		
		Crumbs::set_cache_valid();
		$this->load->view('search_view', $view);
	}
	
	function autosuggest()
	{
		$filters = array();
		$query_filters = '';
		$filters = null;
		$search = '';
		
		if ($search = $this->db->escape_like_str($this->input->post('search', true)))
		{
			$filters = $this->input->post('filters', true);
			
			if ($filters == 'null' || $filters == '')
			{
				$query_filters = ')';
			}
			else
			{
				$filters['has_filters'] = true;

				foreach ($filters as $key => $val)
				{
					if ($val == 'true') $filters[$key] = '1';	
					elseif ($val == 'false') $filters[$key] = '0';
					elseif ($val == 'null') $filters[$key] = null;
				}
				
				$query_filters = $this->_query_filters_builder($filters);
			}
			// Query sorts all results by name, so alt names can come before proper names. **BUG** 
			$video = $this->db->query("(SELECT searchname FROM videos WHERE (searchname LIKE '$search%'$query_filters) UNION (SELECT othernames.searchname AS searchname FROM videos, othernames WHERE (videos.id = othernames.videoid AND othernames.searchname LIKE '$search%'$query_filters) ORDER BY searchname LIMIT 1");

			if ($video->num_rows() == 0)
			{
				$view['autosuggest'] = '';
			}
			else
			{
				$view['autosuggest'] = $video->row()->searchname;
			}
		}
		else
		{
			$view['autosuggest'] = '';
		}
		
		$this->load->view('autosuggest_view', $view);
	}
	
	function batch()
	{
		$sql = null;
		$alt_sql = null;
		$videos = null;
		$alt_videos = null;
		
		if (($offset = (int)$this->input->post('offset', true)) && ($sql = $this->session->userdata('sql')))
		{
			$alt_sql = $this->session->userdata('alt_sql');
			$count = $this->session->userdata('count');
			$merged_count = $this->session->userdata('merged_count');
						
			if ($offset < $count)
			{
				$videos = $this->db->query($sql);
			}
			
			if ($offset + $this->batch_size >= $count && $merged_count > $count)
			{
				$alt_videos = $this->db->query($alt_sql);
			}
			
			if (isset($videos) && isset($alt_videos))
			{
				$videos = array_values(array_unique(array_merge($videos->result(), $alt_videos->result()), SORT_REGULAR));
			}
			elseif (isset($alt_videos))
			{
				$videos = $alt_videos->result();
				$offset -= $count;
				$merged_count -= $count;
			}
			else
			{
				$videos = $videos->result();
			}

			//$count = $videos->num_rows();
			//$data['query'] = array_slice($this->query, $this->idx, $this->batch_size);
			
			$view['videos'] = $videos;
			$view['count'] = $merged_count;
			$view['start'] = $offset;
			//$this->idx = min($this->count, $this->idx + $this->batch_size);
			$view['end'] = min($merged_count, $offset + $this->batch_size);

			//$view['session'] = $this->session->all_userdata();
			$this->load->view('batch_view', $view);
		}
		else
		{
			//implode(',', $this->session->all_userdata()) . 'offset ' . $offset . ' sql ' . $sql . ' 
			$view['error'] = 'You seem to be in the middle of a search, but I&rsquo;ve forgotten what you were looking for. Sorry. You also need to allow <a href="http://www.allaboutcookies.org/cookies/session-cookies-used-for.html">session cookies</a> for this site to work.';

			$this->load->view('json_error_view', $view);
		}
	}
	
	function _build_filters_query($filters)
	{
		$query_filters = $this->_query_filters_builder($filters);
		
		return "(SELECT id, null AS otherid, name, searchname, difficulty, strength, flexibility, invert, spin, transition, url, image FROM videos WHERE (true$query_filters) 
		UNION (SELECT videos.id, othernames.id AS otherid, othernames.name AS name, othernames.searchname AS searchname, difficulty, strength, flexibility, invert, spin, transition, url, image FROM videos, othernames WHERE videos.id = othernames.videoid AND (true$query_filters) ORDER BY searchname";
	}
	
	function _build_browse_query()
	{
		return "(SELECT id, null AS otherid, name, searchname, difficulty, strength, flexibility, invert, spin, transition, url, image FROM videos) 
		UNION (SELECT videos.id, othernames.id AS otherid, othernames.name AS name, othernames.searchname AS searchname, difficulty, strength, flexibility, invert, spin, transition, url, image FROM videos, othernames WHERE videos.id = othernames.videoid) ORDER BY searchname";
	}
	
	// If have filters, the no filters check uses alt_words if available for widest possible match.
	function _build_query($search, &$alt_search, $filters, &$sql, &$alt_sql, &$filters_sql)
	{
		$words = null;
		$alt_words = null;
		$joined_words_sets = array();
		$split_words_sets = array();
		$split_words_list = array();
		$rows = 0;
		$count = 0;
		$best_idx = 0;
		$best_correct = 0.0;
		$correct_count = 0.0;
		$match = 0.0;
		
		$search = $this->_clean_string($search, $stem_search);
		
		// Make array for word (not phrase) search.
		$words = explode(' ', $search);

		// Exact match? If not, find splits & joins
		if($this->db->query($this->_spell_query($search))->num_rows() == 0)
		{
			$joined_words_sets = $this->spell->join($words);

			foreach ($joined_words_sets as $joined_words)
			{
				foreach ($joined_words as $joined_word)
				{
					array_push($split_words_sets, $this->spell->split($joined_word));
				}
			}
			
			$split_words_list = $this->spell->join_alts($split_words_sets);
			$counts = $this->db->query($this->_spell_count($split_words_list));
			$rows = $counts->num_rows();
			
			for ($i = 0; $i < $rows; $i++)
			{
				$count = $counts->row($i);
				
				// Match
				if ($count->count != 0)
				{
					$alt_words = $split_words_list[$i];
					$alt_search = implode(' ', $alt_words);
					break;
					//echo "perfect match<br>";
					//exit;
				}
			}
			
			if (!$alt_words)
			{
				for ($j = 0; $j < count($split_words_list); $j++)
				{
					$correct_count = 0.0;
					
					for ($w = 0; $w < count($split_words_list[$j]); $w++)
					{
						$split_words_list[$j][$w] = $this->spell->correct($split_words_list[$j][$w], $match);
						$correct_count += $match;
					}
					
					$correct = $correct_count / count($split_words_list[$j]);
					
					if ($correct > $best_correct)
					{
						$best_correct = $correct;
						$best_idx = $j;
						//echo "updating best to " . implode(' ' , $split_words_list[$j]) . "<br />";
					}
				}
				$alt_words = $split_words_list[$best_idx];
				$alt_search = implode(' ', $alt_words);
			}
		}
		
		$sql = $this->_build_query_2($search, $words, $filters);
		
		if ($alt_words)
		{
			$alt_sql = $this->_build_query_2($alt_search, $alt_words, $filters);
		}
		else
		{
			$alt_sql = null;
			$alt_search = $search;
			$alt_words = $words;
		}

		if ($filters['has_filters'])
		{
			$filters_sql = $this->_build_query_2($alt_search, $alt_words, $filters, true);
		}
	}
	
	function _build_query_2($search, $words, $filters, $filters_check = false)
	{
		$sql = '';
		$stem_search = $search;
		$stem_words = array();
		$table_idx = 0;

		// Stem words
		for ($i = 0; $i < count($words); $i++)
		{
			$stem_words[$i] = PorterStemmer::stem($words[$i]);
		}
		
		// Sort arrays so get matches to largest words first
		if (count($words) > 1) usort($words, array('Search', '_sort_by_length'));
		if (count($stem_words) > 1) usort($stem_words, array('Search', '_sort_by_length'));

		//$sql = $this->_query_chunk($table_idx, array($search));
		
		if (count($words) > 1 && !$filters_check)
		{
			$sql = $this->_query_chunk($table_idx, array($search), $filters, $filters_check);
		}
		
		$sql .= $this->_query_chunk($table_idx, $words, $filters, $filters_check, $stem_words);
		
		// Remove last ' UNION '
		return substr($sql, 0, strlen($sql) - 7);
	}
	
	/**
	 * Sort array by length of items
	 * http://stackoverflow.com/questions/838227/php-sort-an-array-by-the-length-of-its-values
	 * 
	 * Use with usort($array, array('Search', '_sort_by_length'));
	 *
	 * @param string $a 1st string
	 * @param string $b 2nd string
	 * @return int 
	 *
	 */
	private function _sort_by_length($a, $b)
	{
		return strlen($b) - strlen($a);
	}

	protected function _clean_string($search, &$stem_search = false)
	{
		$to = null;
		$from = null;
		
		// Change accented characters to non-accented equivalents.
		// setlocale doesn't seem to work (with this char set, anyway) on IIS.
		setlocale(LC_CTYPE, 'en_US.UTF8');
		$search = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $search);

		// Strip anything that's not a-z or whitespace (punctuation, numbers, etc.), trim whitespace, strip excess whitespace.
		// $stem_search keeps some apostrophes for stemming.
		$to = array('', ' ', ' ');

		if ($stem_search)
		{
			// Strip apostrophes except when in 've, n't, 'd. Doesn't actually work, as leaves .'t, not just n't.
			$from = array("/`|'(?!(ve|d|t[^a-z]))|^\s+|\s+$/", "/[^a-z'\s]/", "/\s{2,}/");
			$stem_search = preg_replace($from, $to, strtolower($search));
		}
		
		$from = array("/['`]|^\s+|\s+$/", "/[^a-z\s]/", "/\s{2,}/");
		$search = preg_replace($from, $to, strtolower($search));

		// Strip words for which don't want to search.
		$from = array('/(^|\s+)and($|\s+)/', '/(^|\s+)or($|\s+)/', '/(^|\s+)to($|\s+)/', '/(^|\s+)with($|\s+)/', '/(^|\s+)through($|\s+)/', '/(^|\s+)the($|\s+)/', '/(^|\s+)into($|\s+)/', '/(^|\s+)in($|\s+)/', '/(^|\s+)from($|\s+)/');
		$to = ' ';
		$search = preg_replace($from, $to, $search);
		
		if ($stem_search)
		{
			$stem_search = preg_replace($from, $to, $stem_search);
			trim($stem_search);
		}
		// If word to remove at start or end, now have extra space. I'm sure there's a way to do this with a regex.
		return trim($search);
	}
	
	protected function _query_chunk(&$table_idx, $words, $filters, $filters_check, $stem_words = null)
	{
		$name_field = "searchname";
		$othername_field = "othernames.searchname";
		$query_pre_name = "(SELECT * FROM (SELECT id, null AS otherid, name, searchname, difficulty, strength, flexibility, invert, spin, transition, url, image FROM videos WHERE ((";
		$query_pre_othername = "(SELECT * FROM (SELECT videos.id, othernames.id AS otherid, othernames.name AS name, othernames.searchname AS searchname, difficulty, strength, flexibility, invert, spin, transition, url, image FROM videos, othernames WHERE videos.id = othernames.videoid AND ((";
		$query_post1 = ") ORDER BY searchname) AS t";
		$query_post2 = ") UNION ";
		$sql = '';
		$query_filters = $filters_check ? ') ' : $this->_query_filters_builder($filters);
		
		// Stemmed words will either be the same as original or with end cut off, so use stemmed in LIKEs. 
		if (!isset($stem_words)) $stem_words = $words;
		
		// Remove single char words for OR sections
		$w = count($stem_words);
		while ($w > 0 && strlen($stem_words[--$w]) == 1);
		$or_stem_words = array_slice($stem_words, 0, $w + 1);
		
		if (!$filters_check)
		{
			if (count($words) == 1)
			{
				$sql .= "(SELECT * FROM (SELECT id, null AS otherid, name, searchname, difficulty, strength, flexibility, invert, spin, transition, url, image FROM videos WHERE ((" . "$name_field='" . $words[0] . "'" . $query_filters . $query_post1 . $table_idx++ . $query_post2;
				$sql .= "(SELECT * FROM (SELECT videos.id, othernames.id AS otherid, othernames.name AS name, othernames.searchname AS searchname, difficulty, strength, flexibility, invert, spin, transition, url, image FROM videos, othernames WHERE videos.id = othernames.videoid AND ((" . "$othername_field='" . $words[0] . "'" . $query_filters . $query_post1 . $table_idx++ . $query_post2;
			}
			else
			{
				$sql .= $this->_query_and_builder($table_idx, $stem_words, $name_field, $query_pre_name, $query_post1, $query_post2, $query_filters);
				$sql .= $this->_query_and_builder($table_idx, $stem_words, $othername_field, $query_pre_othername, $query_post1, $query_post2, $query_filters);
				$sql .= $this->_query_last_like_builder($table_idx, $stem_words, $name_field, $query_pre_name, $query_post1, $query_post2, $query_filters, "AND");
				$sql .= $this->_query_last_like_builder($table_idx, $stem_words, $othername_field, $query_pre_othername, $query_post1, $query_post2, $query_filters, "AND");
			}
			
			for ($w = 1; $w <= count($or_stem_words); $w++)
			{
				$sql .= $this->_query_or_builder($table_idx, $or_stem_words, $name_field, $query_pre_name, $query_post1, $query_post2, $query_filters, $w);
				$sql .= $this->_query_or_builder($table_idx, $or_stem_words, $othername_field, $query_pre_othername, $query_post1, $query_post2, $query_filters, $w);
			}
		}

		if (count($or_stem_words) > 1 || $filters_check)
		{
			$sql .= $this->_query_last_like_builder($table_idx, $or_stem_words, $name_field, $query_pre_name, $query_post1, $query_post2, $query_filters, "OR");
			$sql .= $this->_query_last_like_builder($table_idx, $or_stem_words, $othername_field, $query_pre_othername, $query_post1, $query_post2, $query_filters, "OR");
		}
		
		return $sql;
	}
	
	/*private function _remove_single_chars($words)
	{
		$i = count($words) - 1;
		while (strlen($words[$i--]) == 1);
		return array_slice($words, 0, $i);
	}*/
	
	protected function _query_filters_builder($filters)
	{
		$sql = '';
		
		if ($filters['has_filters'])
		{
			foreach ($filters as $key => $val)
			{
				if ($key != 'has_filters' && !is_null($val)) 
				{
					$sql .= " AND $key='$val'";
				}
			}
		}
		
		return ') ' . $sql;
	}
	
	protected function _query_and_builder(&$table_idx, $words, $field, $query_pre, $query_post1, $query_post2, $query_filters)
	{
		$sql = '';
		
		for ($i = 0; $i < count($words); $i++)
		{
			$sql .= $query_pre . "$field LIKE '" . $words[$i] . "%' ";
			
			for ($j = 0; $j < count($words); $j++)
			{
				if ($j != $i)
				{
					$sql .= " AND $field LIKE '%" . $words[$j] . "%' ";
				}
			}
			
			$sql .= $query_filters . $query_post1 . $table_idx++ . $query_post2;
		}
		
		return $sql;
	}
	
	protected function _query_or_builder(&$table_idx, $words, $field, $query_pre, $query_post1, $query_post2, $query_filters, $word_count)
	{
		$sql = '';
		$or_pre = array('', '%');
		$or_post = array('%', '%');
		
		for ($i = 0; $i < count($or_pre); $i++)
		{
			$sql .= $query_pre;
			
			for ($j = 0; $j < $word_count; $j++)
			{
				$sql .= "$field LIKE '" . $or_pre[$i] . $words[$j] . $or_post[$i] . "' OR ";
			}
			
			// Remove last ' OR '
			$sql = substr($sql, 0, strlen($sql) - 4) . $query_filters . $query_post1 . $table_idx++ . $query_post2;
		}

		return $sql;
	}
	
	/*protected function _query_or_builder(&$table_idx, $words, $field, $query_pre, $query_post1, $query_post2)
	{
		$sql = '';
		$or_pre = array('', '%');
		$or_post = array('%', '%');
		
		for ($i = 0; $i < count($or_pre); $i++)
		{
			$sql .= $query_pre;
			
			for ($j = 0; $j < count($words); $j++)
			{
				$sql .= "$field LIKE '" . $or_pre[$i] . $words[$j] . $or_post[$i] . "' OR ";
			}
			
			// Remove last ' OR '
			$sql = substr($sql, 0, strlen($sql) - 4) . $query_post1 . $table_idx++ . $query_post2;
		}
		
		return $sql;
	}*/
	
	protected function _query_last_like_builder(&$table_idx, $words, $field, $query_pre, $query_post1, $query_post2, $query_filters, $junct)
	{
		$sql = $query_pre;
		
		for ($i = 0; $i < count($words); $i++)
		{
			$sql .= "$field LIKE '%" . $words[$i] . "%' $junct ";
		}
		// Remove last ' AND ' or ' OR '
		return substr($sql, 0, strlen($sql) - strlen($junct) - 2) . $query_filters . $query_post1 . $table_idx++ . $query_post2;
	}

	function _spell_query($search)
	{
		return "(SELECT id, null AS otherid, name FROM videos WHERE name='$search')
		UNION (SELECT videos.id, othernames.id AS otherid, othernames.name AS name FROM videos, othernames WHERE videos.id = othernames.videoid AND othernames.name='$search')";
	}
	
	function _spell_count($search_alts)
	{
		$sql = '';
		$idx = 0;
		
		// Get count from each subquery so only do 1 query
		foreach ($search_alts as $search)
		{
			$sql .= "(SELECT $idx as idx, COUNT(*) AS count FROM (" . $this->_spell_query(implode(' ', $search)) . ") AS t$idx) UNION ";
			$idx++;
		}
		
		// Remove ' UNION '
		return substr($sql, 0, strlen($sql) - 7);
	}
}
	
/*	protected function _query_junct_builder($words, $test_pre, $test_post, $junct, $word_idx = 0)
	{
		$new_sql_list = array();
		$i = 0;
		$sql = '';
		
		//foreach ($words as $word)
		//{
		//foreach ($sql_list as $sql)
		{
			for ($t = 0; $t < count($test_pre); $t++)
			{
			//$sql .= "$test_pre" . $word . "$test_post $junct ";
				//foreach ($junct_list as $junct)
				{
					$sql .= $test_pre[$t] . $words[$word_idx] . $test_post[$t] . " $junct ";
					$new_sql_list[$i++] = $sql;
					echo "<p>sql is $sql </p>";
				}
			}

			if ($word_idx != count($words) - 1)
			{
				echo "<p><b>calling again</b></p>";
				$sql .= $this->_query_junct_builder($words, $test_pre, $test_post, $junct, $word_idx + 1);
				echo "<p><b>after call $sql</b></p>";
			}
		}
		
		//return substr($sql, 0, strlen($sql) - strlen($junct) - 1);
		return $sql;
	}*/

	
	/*function _spell_correct($word, $dic)
	{
		$word = strtolower($word);
		
		if (array_key_exists($word, $dic))
		{
			return $word;
		}  
		
		$search_result = $dic[soundex($word)];
		
		foreach ($search_result as $key => &$res) 
		{
			$dist = levenshtein($key, $word);
			// consider just distance equals to 1 (the best) or 2
			if ($dist == 1 || $dist == 2) 
			{
				$res = $res / $dist;
			}
			// discard all the other candidates that have distances other than 1 and 2
			// from the original word
			else 
			{
				unset($search_result[$key]);
			}
		}
		
		// reverse sorting of the words by frequence
		arsort($search_result);
		
		// return the first key of the array (which will be the word suggested)
		foreach ($search_result as $key => $res) 
		{
			return $key;
		}
	}
	
	function _spell_train($file = 'big.txt')
	{
		$contents = file_get_contents($file);
		// get all strings of word letters
		//preg_match_all('/\w+/', $contents, $matches);
		preg_match_all('/[a-zA-Z]+/', $contents, $matches);
		unset($contents);
		$dictionary = array();
		
		foreach($matches[0] as $word) 
		{
			$word = strtolower($word);
			$soundex_key = soundex($word);
			
			if(!isset($dictionary[$soundex_key][$word])) 
			{
				$dictionary[$soundex_key][$word] = 0;
			}
			
			$dictionary[$soundex_key][$word] += 1;
		}
		
		unset($matches);
		echo "<pre>";
		print_r($dictionary);
		echo "</pre>";
		return $dictionary;
	}*/

/*protected function _query_chunk($words, $stem_words = null)
{
	$query1 = ") ORDER BY name) UNION (SELECT id, null AS otherid, name FROM videos 
	WHERE ";
	$query2 = "ORDER BY name) UNION (SELECT videos.id, othernames.id AS otherid, othernames.name AS name FROM videos, othernames 
	WHERE videos.id = othernames.videoid AND (";
	
	// Stemmed words will either be the same as original or with end cut off, so use stemmed in LIKEs. 
	if (!isset($stem_words)) $stem_words = $words;
	$juncts = count($words) == 1 ? array('') : array('AND', 'OR');
	$sql = '';
	
	// Only loop twice if $words has > 1 element. 
	for ($j = 0; $j < count($juncts); $j++)
	{
		if ($j == count($juncts) - 1)
		{
			//$sql .= $query1;
			$search_pre = array("name='");
			$search_post = array("'");
			$sql .= $query1 . $this->_query_junct_builder($words, $search_pre, $search_post, $juncts[$j]);

			//$sql .= $query2;
			$search_pre = array("othernames.name='");
			$sql .= $query2 . $this->_query_junct_builder($words, $search_pre, $search_post, $juncts[$j]);				
		}
		else
		{
			//for ($w = 0; $w < count($words); $w++)
			//{
			//$sql .= $query1;
			$search_pre = array("name='", "name LIKE '", "name LIKE '%");
			$search_post = array("'", "%'", "%'");
			$sql .= $query1 . $this->_query_junct_builder($words, $search_pre, $search_post, $juncts[$j]);

			//$sql .= $query2;
			$search_pre = array("othernames.name='");
			$search_post = array("'");
			$sql .= $query2 . $this->_query_junct_builder($words, $search_pre, $search_post, $juncts[$j]);
		}

		//$sql .= $query1;
		$search_pre = array("name LIKE '");
		$search_post = array("%'");
		$sql .= $query1 . $this->_query_junct_builder($stem_words, $search_pre, $search_post, $juncts[$j]);

		//$sql .= $query2;
		$search_pre = array("othernames.name LIKE '");
		$search_post = array("%'");
		$sql .= $query2 . $this->_query_junct_builder($stem_words, $search_pre, $search_post, $juncts[$j]);

		//$sql .= $query1;
		$search_pre = array("name LIKE '%");
		$search_post = array("%'");
		$sql .= $query1 . $this->_query_junct_builder($stem_words, $search_pre, $search_post, $juncts[$j]);

		//$sql .= $query2;
		$search_pre = array("othernames.name LIKE '%");
		$search_post = array("%'");
		$sql .= $query2 . $this->_query_junct_builder($stem_words, $search_pre, $search_post, $juncts[$j]);
	}
	
	// Revideo ') ORDER BY name) UNION '
	return substr($sql . ') ORDER BY name)', 23);
}*/

// Only loop twice if $words has > 1 element. 
/*for ($j = 0; $j < count($juncts); $j++)
{
	if ($j == count($juncts) - 1)
	{
		$search_pre = array("name='");
		$search_post = array("'");
		$sql .= $query1 . $this->_query_junct_builder($words, $search_pre, $search_post, $juncts[$j]);

		$search_pre = array("othernames.name='");
		$sql .= $query2 . $this->_query_junct_builder($words, $search_pre, $search_post, $juncts[$j]);				
	}
	else
	{
		//for ($w = 0; $w < count($words); $w++)
		//{
		//$sql .= $query1;
		$search_pre = array("name='", "name LIKE '", "name LIKE '%");
		$search_post = array("'", "%'", "%'");
		$sql .= $query1 . $this->_query_junct_builder($words, $search_pre, $search_post, $juncts[$j]);

		//$sql .= $query2;
		$search_pre = array("othernames.name='");
		$search_post = array("'");
		$sql .= $query2 . $this->_query_junct_builder($words, $search_pre, $search_post, $juncts[$j]);
	}

	//$sql .= $query1;
	$search_pre = array("name LIKE '");
	$search_post = array("%'");
	$sql .= $query1 . $this->_query_junct_builder($stem_words, $search_pre, $search_post, $juncts[$j]);

	//$sql .= $query2;
	$search_pre = array("othernames.name LIKE '");
	$search_post = array("%'");
	$sql .= $query2 . $this->_query_junct_builder($stem_words, $search_pre, $search_post, $juncts[$j]);

	//$sql .= $query1;
	$search_pre = array("name LIKE '%");
	$search_post = array("%'");
	$sql .= $query1 . $this->_query_junct_builder($stem_words, $search_pre, $search_post, $juncts[$j]);

	//$sql .= $query2;
	$search_pre = array("othernames.name LIKE '%");
	$search_post = array("%'");
	$sql .= $query2 . $this->_query_junct_builder($stem_words, $search_pre, $search_post, $juncts[$j]);
}
		
// Revideo ') ORDER BY name) UNION '
return substr($sql . ') ORDER BY name)', 23);*/
