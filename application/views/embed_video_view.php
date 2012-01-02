<?php
/**
 * Build code for embedded video.
 * 
 * @package    VideoGallery
 * @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
 * @license    MIT licence. See licence.txt for details.
 * @version    0.1
 */

	if (isset($ref_name))
{
	$title = "$name ($ref_name)";
}
else
{
	$title = $name; 
}

$url = site_url('/videos/');

ob_start();
?>
(function ($)
{
	$(function()
	{
		var embedWidth = $(window).width();
		var embedHeight = Math.round(embedWidth * 0.5625);
		
		$("img").width(embedWidth);
		
		jwplayer("videoPlayer").setup(
		{
			flashplayer: "player.swf",
			width: embedWidth,
			height: embedHeight,
			image: "<?php echo $image; ?>",
			allowfullscreen: true,
			provider: "http",
			levels: 
			[
				{ bitrate: 2048, file: "<?php echo $url; ?>l-<?php echo $video; ?>", width: 1280 },
				{ bitrate: 1024, file: "<?php echo $url; ?>m-<?php echo $video; ?>", width: 854 },
				{ bitrate: 768, file: "<?php echo $url; ?>s-<?php echo $video; ?>", width: 640 },
				{ bitrate: 384, file: "<?php echo $url; ?>i-<?php echo $video; ?>", width: 320 }
			]
		});
	});
})(jQuery);
<?php

$js = ob_get_clean();
include('embed_header_view.php');
?>
<div class="Video">
	<div id="videoPlayer"></div>
	<img src="<?php echo site_url('images/full/' . $image); ?>" alt="The ' . <?php echo $name; ?>" />
</div>
<div class="EmbedHook">
	Find this and nearly <?php echo Settings::$video_count; ?> other videos in the <a href="<?php echo site_url() . '">' . Settings::$site_title; ?></a>.
</div>
<?php
include('embed_footer_view.php');