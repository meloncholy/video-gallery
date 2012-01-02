<?php
/**
* Check spelling of searches
*
* Dictionary adapted from code by Vincenzo Russo, Ian Barber
* http://neminis.org/blog/research/text-mining/spelling-correction-with-soundex/
* 
* @package    VideoGallery
* @subpackage Spell
* @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
* @license    MIT licence. See licence.txt for details.
* @version    0.1
*/

class Spell extends CI_Model
{
	// Dictionary
	var $dic;
	// Dictionary minus single letter words
	var $dic_edit;
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();

		$this->dic = $this->load_dic('dic');
		$this->dic_edit = $this->load_dic('dicedit');
	}
	
	function check($word)
	{
		return isset($this->dic[strtolower($word)]);
	}

	// $match == how close to correct spelling word is, roughly
	function correct($word, &$match = 0.0)
	{
		$joined_words = array();
		$edits1 = $edits2 = array();

		$word = strtolower($word);

		if(isset($this->dic[$word]))
		{
			$match = 1.0;
			return $word;
		}
		elseif (strlen($word) == 1)
		{
			$match = 0.0;
			return $word;
		}
		
		foreach($this->dic_edit as $dic_word => $count)
		{
			$dist = levenshtein($word, $dic_word);
			
			if($dist == 1) 
			{	
				$edits1[$dic_word] = $count;
			} 
			elseif($dist == 2)
			{
				$edits2[$dic_word] = $count;
			}
		}

		if(count($edits1)) 
		{
			$match = 0.6;
			arsort($edits1);
			return key($edits1);
		}
		elseif(count($edits2)) 
		{
			$match = 0.3;
			arsort($edits2);
			return key($edits2);
		}

		// Nothing better
		$match = 0.0;
		return $word;
	}
	
	/**
	 * Store a new dictionary of words in the database. Only needed to update the list. 
	 * 
	 * @param string $file List of words or phrases to learn (one per line)
	 */
	
	function train($file)
	{
		$dic = array();
		$dic_edit = array();

		$contents = file_get_contents($file);
		// get all strings of word letters
		preg_match_all('/\w+/', $contents, $matches);
		unset($contents);
		
		foreach($matches[0] as $word) 
		{
			$word = strtolower($word);
			
			if(!isset($dic[$word])) 
			{
				$dic[$word] = 0;
			}
			$dic[$word] += 1;
		}
		
		unset($matches);
		
		foreach ($dic as $word => $matches)
		{
			if (strlen($word) > 1) $dic_edit[$word] = $matches;
		}
		
		$this->dic = $dic;
		$this->dic_edit = $dic_edit;
		
		$this->save_dic($dic, 'dic');
		$this->save_dic($dic_edit, 'dicedit');
		echo "Updated dictionaries from $file.";
	}
	
	/**
	 * Store the new dictionary in the database (called by train)
	 *
	 * @param array $dic New dictionary
	 * @param string $table Target table
	 */	
	private function save_dic($dic, $table)
	{
		// Why can't I execute 2 of these at once through CodeIgniter?
		$this->db->query("DELETE FROM $table");
		
		$sql = "INSERT INTO $table (word, freq) VALUES ";
		
		foreach ($dic as $word => $freq)
		{
			$sql .= "\n('$word', $freq), ";
		}
		
		// Cut ', '
		$sql = substr($sql, 0, strlen($sql) - 2);
		$this->db->query($sql);
	}
	
	/**
	 * Load dictionary from database
	 * 
	 * @param string $table Table to load
	 * 
	 */
	private function load_dic($table)
	{
		$dic = array();
		$query = $this->db->query("SELECT * FROM $table");
		
		foreach ($query->result() as $row)
		{
			$dic[$row->word] = $row->freq;
		}
			
		return $dic;
	}
	
	/**
	 * Join words together to try in dictionary
	 *
	 * @param array $words Search string to check as array of words
	 * @param array $joined_words Array of joined words (internal; passed by ref)
	 * @param int $pos_start Current position in array (internal)
	 * @param array $comb_start Array of joined words current building (internal)
	 * @return array Array of joined words
	 */
	function join($words, &$joined_words = array(), $pos_start = 0, $comb_start = array())
	{
		$count = count($words);
		$len = $count - $pos_start;
		$comb = $comb_start;

		for ( ; $len > 0; $len--)
		{
			$pos = $pos_start;
			$word = implode(array_slice($words, $pos, $len));
			
			if ($len == 1 || $this->check($word))
			{
				if ($pos + $len <= $count)
				{
					array_push($comb, $word);
					if ($pos + $len < $count) $this->join($words, $joined_words, $pos + $len, $comb);
				}
				
				if ($pos + $len == $count) array_push($joined_words, $comb);
			}
			$comb = $comb_start;
		}
		
		return $joined_words;
	}
	
	/**
	 * Split compound words for spell check
	 *
	 * @param string $word Word to split
	 * @return array Array of split words
	 */
	function split($word)
	{
		$new_words = array();
		return $this->split_r(str_split($word), $new_words);
	}

	private function split_r($letters, &$new_words, $pos_start = 0, $len = false, $comb_start = array())
	{
		$count = count($letters);
		$len = $count - $pos_start;
		$comb = $comb_start;
		$unknown = 0;

		for ( ; $len > 0; $len--)
		{
			$pos = $pos_start;
			$word = implode(array_slice($letters, $pos, $len));
						
			if ($pos + $len <= $count)
			{
				array_push($comb, $word);
				if ($pos + $len < $count) $this->split_r($letters, $new_words, $pos + $len, $len, $comb);
			}
				
			if ($pos + $len == $count)
			{
				for ($w = 0; $w < count($comb); $w++)
				{
					if (strlen($comb[$w]) > 1 && $this->check($comb[$w])) 
					{
						$unknown = 0;
					}
					else
					{
						$unknown++;
					}
					if ($unknown == 2) break;
				}
					
				if ($unknown < 2) array_push($new_words, $comb);
			}
			$comb = $comb_start;
		}
		return $new_words;
	}
	
	function join_alts($split_words_sets)
	{
		$split_words_list = array();
		$this->join_alts_r($split_words_sets, $split_words_list);
		return $split_words_list;
	}
	
	private function join_alts_r($split_words_sets, &$split_words_list, $set_idx = 0, $cur_split_words = array())
	{
		
		foreach ($split_words_sets[$set_idx] as $split_word)
		{
			$new_split_words = array_merge($cur_split_words, $split_word);

			if ($set_idx == count($split_words_sets) - 1)
			{
				array_push($split_words_list, $new_split_words);
			}
			else
			{
				$this->join_alts_r($split_words_sets, $split_words_list, $set_idx + 1, $new_split_words);
			}
		}
	}
}