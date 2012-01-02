<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta description="<?php echo Settings::$site_description; ?>" />
	<base href="<?php echo site_url(); ?>" />
	<title><?php echo isset($title) ? $title . Settings::$site_title_post : Settings::$site_title; ?></title>
	<link type="text/css" rel="Stylesheet" href="style.css" media="screen" />
	<script type="text/javascript" src="<?php echo Settings::$jquery_cdn_url; ?>"></script>
	<script type="text/javascript" src="jwplayer.js"></script>
	<?php if (isset($js)) echo '<script type="text/javascript">' . $js . '</script>'; ?>
</head>
<body>