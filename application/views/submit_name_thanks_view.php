<?php

/**
 * Thanks for submitting a new video name.
 * 
 * @package    VideoGallery
 * @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
 * @license    MIT licence. See licence.txt for details.
 * @version    0.1
 */

ob_start();
?>
<div class="Content">
	<a href="#!" class="Close Round Button"></a>
	<h1>Yay!</h1>
	<p>
		Thanks for suggesting a new name for the <?php echo Settings::$site_title; ?>! If you gave me your email, I&rsquo;ll get in touch with you soon &ndash; maybe to ask question or two, or otherwise to let you know that it&rsquo;s been added to the site.
	</p>
	<a class="Button Close" href="#!">Close</a>
</div>
<?php
$data['html'] = ob_get_clean();
$data['title'] = "Suggest a Name" . Settings::$site_title_post;
echo json_encode($data);