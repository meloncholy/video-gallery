<?php 

/**
 * Contact form.
 * 
 * @package    VideoGallery
 * @copyright  Copyright (c) 2011 Andrew Weeks http://meloncholy.com
 * @license    MIT licence. See licence.txt for details.
 * @version    0.1
 */

ob_start();
?>
<div class="Content">
	<a class="Close Round Button" href="#!"></a>
	<h1>Get in touch&hellip;</h1>
	<?php echo form_open('#!contact/submit_contact/'); ?>
		<div class="ValidateAlert">There are some problems with the form. Please take a look.</div>
		<p>Ask a question. Report a problem. Tell me something fun. You know what to do. </p>
		<fieldset>
			<div class="Field">
				<textarea class="Required" rows="8" cols="30" id="comment" name="comment"></textarea>
				<div class="Validate">Contact forms work better if you include a message. :)</div>
			</div>
		</fieldset>
		<fieldset>
			<div>A little about you (optional, but useful so I can get back to you).</div>
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
	
		<a class="Button" href="#!" id="submit">Go!</a>
		<a class="Close" href="#!">Close</a>
	</form>
</div>
<?php
$data['html'] = ob_get_clean();
$data['title'] = "Contact" . Settings::$site_title_post;
echo json_encode($data);