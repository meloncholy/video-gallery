<?php

/*if (isset($prev_moves))
{
	for ($i = 0; $i < count($prev_moves); $i++)
	{
		$data['prevMoves'][] = '<li><a href="' . site_url('#!pole-dancing-move/' . $prev_moves[$i]['url']) . (isset($prev_moves[$i]['ref_id']) ? '/' . $prev_moves[$i]['ref_id'] : '') . '">' . 
			'<h2 class="MoveName">' . $prev_moves[$i]['name'] . '</h2>' . 
			'<img src="' . site_url('images/thumb/' . $prev_moves[$i]['image']) . '" alt="The ' . $prev_moves[$i]['name'] . '" />' .
			'</a></li>';
	}
}

if (isset($next_moves))
{	
	for ($i = 0; $i < count($next_moves); $i++)
	{
		$data['nextMoves'][] = '<li><a href="' . site_url('#!pole-dancing-move/' . $next_moves[$i]['url']) . (isset($next_moves[$i]['ref_id']) ? '/' . $next_moves[$i]['ref_id'] : '') . '">' . 
			'<h2 class="MoveName">' . $next_moves[$i]['name'] . '</h2>' . 
			'<img src="' . site_url('images/thumb/' . $next_moves[$i]['image']) . '" alt="The ' . $next_moves[$i]['name'] . '" />' .
			'</a></li>';
	}
}*/

if (isset($other_moves))
{
	$data['otherMoves'] = '<ul>';
	
	for ($i = 0; $i < count($other_moves); $i++)
	{
		$data['otherMoves'] .= '<li><a href="' . site_url('#!pole-dancing-move/' . $other_moves[$i]['url']) . (isset($other_moves[$i]['ref_id']) ? '/' . $other_moves[$i]['ref_id'] : '') . '">' . 
			'<h2 class="MoveName">' . $other_moves[$i]['name'] . '</h2>' . 
			'<img src="' . site_url('images/thumb/' . $other_moves[$i]['image']) . '" alt="The ' . $other_moves[$i]['name'] . '" />' .
			'</a></li>';
	}
	
	$data['otherMoves'] .= '</ul>';
}