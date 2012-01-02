<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta description="<?php echo Settings::$site_description; ?>" />
	<base href="<?php echo site_url(); ?>" />
	<title><?php echo isset($title) ? $title . Settings::$site_title_post : Settings::$site_title; ?></title>
	<link type="text/css" rel="Stylesheet" href="style.css" media="screen" />

	<!--[if lte IE 8]>
	<link type="text/css" rel="Stylesheet" href="ie.css" media="screen" />
	<![endif]-->
	
	<?php
	/*
	// Debug scripts
	<script type="text/javascript" src="js/jquery-1.6.4.js"></script>
	<script type="text/javascript" src="js/jwplayer.js"></script>
	<script type="text/javascript" src="js/jquery.fn.vg.effects.js"></script>
	<script type="text/javascript" src="js/jquery.fn.vg.slider.js"></script>
	<script type="text/javascript" src="js/jquery.fn.vg.validateform.js"></script>
	<script type="text/javascript" src="js/jquery.fn.cookie.js"></script>
	<script type="text/javascript" src="js/jquery.ba-hashchange.js"></script>
	<script type="text/javascript" src="js/jquery.ui.core.js"></script>
	<script type="text/javascript" src="js/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="js/jquery.ui.widget.subclass.js"></script>
	<script type="text/javascript" src="js/jquery.ui.vg.start.js"></script>
	<script type="text/javascript" src="js/jquery.ui.vg.crumbs.js"></script>
	<script type="text/javascript" src="js/jquery.ui.vg.alert.js"></script>
	<script type="text/javascript" src="js/jquery.ui.vg.results.js"></script>
	<script type="text/javascript" src="js/jquery.ui.vg.dialog.js"></script>
	<script type="text/javascript" src="js/jquery.ui.vg.feedback.js"></script>
	<script type="text/javascript" src="js/jquery.ui.vg.contact.js"></script>
	<script type="text/javascript" src="js/jquery.ui.vg.suggest.js"></script>
	<script type="text/javascript" src="js/jquery.ui.vg.video.js"></script>
	*/
	?>

	<script type="text/javascript" src="<?php echo Settings::$jquery_cdn_url; ?>"></script>
	<script type="text/javascript" src="js/jwplayer.js"></script>
	<script type="text/javascript" src="js/jquery.widget-funcs.min.js"></script>
	<script type="text/javascript" src="js/jquery.ui.vg.min.js"></script>
	
	<script type="text/javascript">
		$(function ()
		{
			$(".Slider:not(.Vertical)").slider({ max: 1, minNull: true, $target: $(".Results") });
			$(".Slider.Vertical").slider({ max: 3, minNull: true, invert: true, vertical: true, $target: $(".Results") });
			$(".Results").vgStart({ title: "<?php echo Settings::$site_title; ?>", titlePost: "<?php echo Settings::$site_title_post; ?>", prettyLink: "<?php echo Settings::$pretty_link; ?>/" }); 
		});
	</script>
	
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-17270468-1']);
		_gaq.push(['_setDomainName', 'meloncholy.com']);
		_gaq.push(['_trackPageview']);

		(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
</head>
