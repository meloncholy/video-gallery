<?php

/**
 * Build code to show a video. Includes related videos via batch_builder_view.
 * 
 * @package    VideoGallery
 * @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
 * @license    MIT licence. See licence.txt for details.
 * @version    0.1
 */

$alt_list = '';
$hash_pre = '#!search/+';
$difficulty_html = '<a class="Difficulty" href="' . $hash_pre;
$yes_html = '<a class="Yes" href="' . $hash_pre;
$no_html = '<a class="No" href="' . $hash_pre;

if ($difficulty == 0)
{
	$difficulty_html .= 'difficulty=0">Beginner';
}
elseif ($difficulty == 1)
{
	$difficulty_html .= 'difficulty=1">Intermediate';
}
elseif ($difficulty == 2) 
{
	$difficulty_html .= 'difficulty=2">Advanced';
}
elseif ($difficulty == 3)
{
	$difficulty_html .= 'difficulty=3">Expert';
}

$sep = '<span class="Sep">//</span>';

ob_start();
?>
<a href="#!" class="Close Round Button"></a>
<h1 class="VideoName">
<?php
if (isset($ref_name))
{
	$title = "$name ($ref_name)";
}
else
{
	$title = $name; 
}

echo $title;
?>
</h1>

<div class="VideoContainer">
<?php 
if (isset($prev)) echo '<a class="PrevVideo" href="#!' . Settings::$pretty_link . '/' . $prev->url . (isset($prev->otherid) ? '/' . $prev->otherid : '') . '" title="Go to the ' . $prev->name . '."></a>';
if (isset($next)) echo '<a class="NextVideo" href="#!' . Settings::$pretty_link . '/' . $next->url . (isset($next->otherid) ? '/' . $next->otherid : '') . '" title="Go to the ' . $next->name . '."></a>';
echo '<div id="videoPlayer"></div><img src="' . site_url('images/full/' . $image) . '" alt="The ' . $name . '" />'; 
?>
</div>

<ul class="VideoActions">
	<li class="TextButton" id="shareToggleLink"><div class="Share Round Button"></div><div class="ButtonLabel">Share</div></li>
	<li class="Round Button" data-video-size="large">720p</li>
	<li class="Round Button" data-video-size="medium">480p</li>
	<li class="Round Button" data-video-size="small">360p</li>
</ul>

<div class="OtherNames">
	<span class="VideoInfoHeading">Names</span>
	<?php
	$alt_list = $name . ', ';
	
	if (isset($alt_names))
	{
		foreach ($alt_names as $alt_name)
		{
			$alt_list .= $alt_name . ', ';
		}
	}
	
	echo substr($alt_list, 0, strlen($alt_list) - 2);
	?>
	<?php echo $sep; ?> <a class="SuggestName" href="#!">Suggest a name</a>
</div>

<ul class="VideoInfo">
	<li><span class="VideoInfoHeading">Tags</span></li>
	<li><?php echo $difficulty_html . '</a> ' . $sep; ?></li>
	<li><?php echo ($strength ? $yes_html : $no_html) . 'strength=' . ($strength ? 'true">' : 'false">Not ') . 'Strength</a> ' . $sep; ?></li>
	<li><?php echo ($flexibility ? $yes_html : $no_html) . 'flexibility=' . ($flexibility ? 'true">' : 'false">Not ') . 'Flexibility</a> ' . $sep; ?></li>
	<li><?php echo ($invert ? $yes_html : $no_html) . 'invert=' . ($invert ? 'true">' : 'false">Not ') . 'Invert</a> ' . $sep; ?></li>
	<li><?php echo ($spin ? $yes_html : $no_html) . 'spin=' . ($spin ? 'true">' : 'false">Not ') . 'Spin</a> ' . $sep; ?></li>
	<li><?php echo ($transition ? $yes_html : $no_html) . 'transition=' . ($transition ? 'true">' : 'false">Not ') . 'Transition</a>'; ?></li>
</ul>

<div class="ShareToggle">
	<h2>Share</h2>
	<p>Spread the love! Share this video on Facebook, Twitter or your own site.</p>
	<p>
		<input id="shareLink" type="text" class="ReadOnly" value="<?php echo site_url('#!' . Settings::$pretty_link . '/' . $url); ?>" data-long-link="<?php echo site_url('#!' . Settings::$pretty_link . '/' . $url); ?>" data-short-link="http://bit.ly/something" />
		<input id="shortLink" type="checkbox" /><label for="shortLink">Short link</label>
	</p>
	<!--<ul>	
		<li id="shareFacebook"><iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(site_url('#!' . Settings::$pretty_link . '/' . $url)); ?>&amp;layout=button_count&amp;show_faces=false&amp;width=90&amp;action=like&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;" allowTransparency="true"></iframe></li>
		<li id="shareTwitter"><a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="meloncholy">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></li>
		<li id="shareStumble"><script src="http://www.stumbleupon.com/hostedbadge.php?s=1"></script></li>		
	</ul>-->
	<p>
		<div class="Button" id="embedToggleLink">Embed</div>
		<div class="Button" id="shareFacebook">F</div>
		<div class="Button" id="shareTwitter">t</div>
	</p>
	<div class="EmbedToggle">
		<textarea id="shareEmbed" class="ReadOnly">&lt;iframe src="<?php echo site_url('/embed/' . $url . '/'); ?>" style="border: none;" frameborder="0" width="640" height="360" allowfullscreen&gt;&lt;/iframe&gt;</textarea>
		<h3>How big do you want it?</h3>
		<ul class="EmbedSize">
			<li data-width="1280" data-height="720"><div>1280 &times; 720</div><div class="EmbedSizeOp" id="embed1280"></div></li>
			<li data-width="854" data-height="480"><div>854 &times; 480</div><div class="EmbedSizeOp" id="embed854"></div></li>
			<li data-width="640" data-height="360" class="Active"><div>640 &times; 360</div><div class="EmbedSizeOp" id="embed640"></div></li>
			<li data-width="320" data-height="240"><div>320 &times; 240</div><div class="EmbedSizeOp" id="embed320"></div></li>
		</ul>
	</div>
</div>

<div class="SimilarVideos">
	<h2>Similar videos</h2>
	<ul>
	<?php
	foreach ($similar_videos as $similar_video)
	{
		echo '<li>' . 
			'<a href="' . '#!' . Settings::$pretty_link . '/' . $similar_video->url . '" title="View the ' . $similar_video->name . '">' .
			'<img src="' . 'images/thumb/' . $similar_video->image . '" alt="The ' . $similar_video->name . '" />' . 
			'<span>' . $similar_video->name . '</span>' .
			'</a>' .
			'</li>';
	}
	?>
	</ul>
</div>
<?php
$data['html'] = ob_get_clean();

$html = 'relatedHtml';
include('batch_builder_view.php');
$data['videoIdx'] = isset($video_idx) ? $video_idx : 0;
$data['count'] = $count;
$data['title'] = "$title" . Settings::$site_title_post;
$data['image'] = site_url('images/full/' . $image);
$data['url'] = site_url('videos/');
//$data['url'] = site_url('placeholder/videos/index.php?file=');
$data['video'] = $video;
echo json_encode($data);