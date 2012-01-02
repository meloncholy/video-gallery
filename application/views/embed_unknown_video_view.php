<?php

/**
 * Shown if embedded video is not in the database.
 * 
 * @package    VideoGallery
 * @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
 * @license    MIT licence. See licence.txt for details.
 * @version    0.1
 */

$title = 'Unknown Video';
include('embed_header_view.php');
?>
<h1>Video not found!</h1>
<p>
	Sorry, I don&rsquo;t know that video. As this video&rsquo;s been embedded somewhere, either something&rsquo;s gone wrong at my end or the person who put it there needs to work on their copy paste technique. (If that&rsquo;s you then hi! :)
</p>	
<p>	
	I&rsquo;d suggest heading over to the <a href="<?php echo site_url(); ?>" target="_blank"><?php echo Settings::$site_title; ?></a> to see if the video you&rsquo;re after is there. Or if you&rsquo;re sure it&rsquo;s my fault it&rsquo;s broken and you just want to shout at me, <a href="<?php echo site_url(); ?>#!contact/" target="_blank">send me a message here</a>.
</p>
<?php
include('embed_footer_view.php');
