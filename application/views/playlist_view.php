<?php
// NOT USED
/**
 * Bitrates similar to YouTube
 * http://adterrasperaspera.com/blog/2010/05/24/approximate-youtube-bitrates
 */
//$player_url = site_url('/xmoovStream/');
$player_url = site_url('/xmoovStream/index.php?file=');
?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:jwplayer="http://developer.longtailvideo.com/">
  <channel>
	<title><?php echo $name; ?> - Pole Dance Dictionary</title>

	<item>
	  <media:group>
		<media:thumbnail url="<?php echo site_url('images/'. $image); ?>" />
		<media:content bitrate="2048" url="<?php echo $player_url . 'hi-' . $video; ?>"  width="1280" />
		<media:content bitrate="1024" url="<?php echo $player_url . 'med-' . $video; ?>"  width="854" />
		<media:content bitrate="768" url="<?php echo $player_url . 'low-' . $video; ?>"  width="640" />
		<media:content bitrate="384" url="<?php echo $player_url . 'mob-' . $video; ?>"  width="320" />
	  </media:group>
	  <jwplayer:type>http</jwplayer:type>
	  <jwplayer:provider>http</jwplayer:provider>
	  <jwplayer:http.startparam>apstart</jwplayer:http.startparam>
	</item>

  </channel>
</rss>
