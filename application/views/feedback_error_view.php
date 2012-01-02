<?php

/**
 * If there is a problem with the suggest name form, this is shown. 
 * 
 * @package    VideoGallery
 * @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
 * @license    MIT licence. See licence.txt for details.
 * @version    0.1
 */

ob_start();
?>
<div class="Content">
	<h2>Oops</h2>
	<p>
		You seem to be trying to tell me something about a video (which is great &ndash; thanks very much!), but I don&rsquo;t know which video you mean. This is probably because the URL has got mangled somehow. Sorry.
	</p>
	<p>	
		Could you <a href="javascript:history.back();">go back</a> to where you were just now and try again? If it still doesn&rsquo;t work, please <a href="#!contact/">get in touch</a> so I can try to sort out the problem. 
	</p>
</div>
<?php
$data['html'] = ob_get_clean();
$data['title'] = "Error" . Settings::$site_title;
echo json_encode($data);