<?php
/**
 * Don't recognise that video...
 * 
 * @package    VideoGallery
 * @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
 * @license    MIT licence. See licence.txt for details.
 * @version    0.1
 */

ob_start();
?>
<h2>Oops</h2>
<p>
	You seem to be looking for a video, but you haven&rsquo;t told me which one you want. And, well, I&rsquo;d love to help, but you need to be a bit more specific&hellip; 
</p>
<p>	
	How about <a href="javascript:history.back();">going back</a> to where you were just now or <a href="<?php echo base_url(); ?>">searching again</a>?
</p>

<?php
$data['html'] = ob_get_clean();
$data['title'] = "Error" . Settings::$site_title_post;
echo json_encode($data);