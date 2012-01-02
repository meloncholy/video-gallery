<?php

/**
 * Contact form thank you view.
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
	<h1>All done :)</h1>
	<p>Thanks for getting in touch! I&rsquo;ll get back to you as soon as I can.</p>
	<a class="Button Close" href="#!">Close</a>
</div>
<?php

$data['html'] = ob_get_clean();
$data['title'] = "Contact" . Settings::$site_title_post;
echo json_encode($data);