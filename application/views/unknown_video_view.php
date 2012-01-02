<?php

/**
 * Shown if video is not in the database.
 * 
 * @package    VideoGallery
 * @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
 * @license    MIT licence. See licence.txt for details.
 * @version    0.1
 */

ob_start();
?>
<h1>Video not found!</h1>
<p>
	Sorry, I don&rsquo;t know that video. If it really should be here, please <a href="#!feedback/name/">tell me about it</a> so I can add it to the video gallery. (Thanks!) 
</p>
<p>	
	Otherwise, you could <a href="javascript:history.back();">go back</a> to where you were just now or try <a href="<?php echo site_url(); ?>">searching again</a>.
</p>

<?php
$data['html'] = ob_get_clean();
$data['title'] = "Unknown Video" . Settings::$site_title_post;
echo json_encode($data);