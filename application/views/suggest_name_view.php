<?php 

/**
 * Form to suggest a new name for a video.
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
	<h1>Suggest a name</h1>
	<?php
	echo form_open('#!feedback/submit_name/'); 
	echo form_hidden('entry_id', $this->uri->segment(2));
	?>
	<div class="ValidateAlert">There are some problems with the form. Please take a look.</div>
	<?php
	if (is_null($name) || is_null($url))
	{
	?>
		Suggest a new name for a video.
		<label for="name">The video</label>
		<div class="Field">
			<input type="hidden" id="url" value="" />
			<input type="text" class="Required" id="name" name="name" />
			<div class="Validate">I need to know the name used here for the video.</div>
		</div>
		<?php
	}
	else
	{
		?>
		Suggest a new name for <span><?php echo $name; ?></span>.
		<input type="hidden" id="url" value="<?php echo $url; ?>" />
		<input type="hidden" id="name" value="<?php echo $name; ?>" />
		<?php
	}
	?>
		<fieldset>
			<label for="suggestion">New name</label>
			<div class="Field">
				<input type="text" class="Required" id="suggestion" name="suggestion" />
				<div class="Validate">Your new name can&rsquo;t be blank. Sorry.</div>
			</div>
		
			<label for="comment">Anything else I should know? <span>(Where you learnt this name, video or web links for the video, stuff like that. Though if you&rsquo;re not sure, that&rsquo;s cool.)</span></label>
			<div class="Field">
				<textarea rows="8" cols="30" id="comment" name="comment"></textarea>
			</div>
		</fieldset>
		<fieldset>
			<p>A little about you (optional, but useful so I can get back to you).</p>
			<label for="author">Name</label> 
			<div class="Field">
				<input type="text" id="author" name="author" />
			</div>
			<label for="email">Email</label> 
			<div class="Field">
				<input type="text" class="Email" id="email" name="email" />
				<div class="Validate">Your email address doesn&rsquo;t look valid.</div>
			</div>
		</fieldset>
	
		<a class="Button" href="#!" id="submit">Send</a>
		<a href="#!" class="Close">Close</a>
	</form>
</div>
<?php
$data['html'] = ob_get_clean();
$data['title'] = "Suggest a Name" . Settings::$site_title_post;
echo json_encode($data);