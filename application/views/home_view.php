<body>
	<div class="Background"></div>
	<a class="Logo" href="<?php echo site_url(); ?>" title="Go to <?php echo Settings::$site_title; ?> home page."></a>

	<div class="Search Home">
		<div class="SearchForm">
			<input class="SearchInput" type="text" />
			<div class="Autosuggest"></div>
			<div id="searchButton" class="Button">Search</div>
			<div id="browseButton" class="Button">Browse</div>
			<a id="filtersLink" href="#!">Advanced</a>
			<a id="contact" href="#!contact/">Contact</a>
			<div class="Filters">
				<div class="Container">
					<div class="Slider Vertical" id="difficulty"></div>
					<div class="SliderScale">
						<div>Expert</div>
						<div>Advanced</div>
						<div>Intermediate</div>
						<div>Beginner</div>
						<div>Off</div>
					</div>
				</div>
				<div class="Container">
					<div class="SliderValues"><div>Off</div><div>No</div><div>Yes</div></div>
					<div class="SliderValues"><div>Off</div><div>No</div><div>Yes</div></div>
					<div class="SliderValues"><div>Off</div><div>No</div><div>Yes</div></div>
					<div class="SliderCaption"><div class="Slider" id="strength"></div>Strength</div>
					<div class="SliderCaption"><div class="Slider" id="flexibility"></div>Flexibility</div>
					<div class="SliderCaption"><div class="Slider" id="invert"></div>Invert</div>
					<div class="SliderCaption"><div class="Slider" id="spin"></div>Spin</div>
					<div class="SliderCaption"><div class="Slider" id="transition"></div>Transition</div>
				</div>
				<div class="ToggleFilters"></div>
			</div>
		</div>
		<div class="Alert" id="snapshotAlertTarget"></div>
	</div>
	<div class="Results" id="snapshotResultsTarget"></div>
	<div class="Video">
		<div class="ActiveVideo">
			<div class="Content" id="snapshotVideoTarget"></div>
		</div>
		<div class="RelatedVideos" id="snapshotRelatedVideosTarget"></div>
		<div id="vidEmbedSize">Video Size</div>
	</div>
	<div class="Feedback" id="snapshotFeedbackTarget">
		<div class="Footer">
			The <?php echo Settings::$site_title; ?> was lovingly crafted by <a href="http://meloncholy.com/">meloncholy</a>, with <a href="http://www.theycallusanimals.com/">They Call Us Animals</a> providing the fab sample videos. <a href="#!contact/">Contact</a>
		</div>
	</div>
	<noscript>
		<div class="NoScript">
			<div class="NoScriptAlert">
				<h2>Sorry</h2>
				<p>This site uses quite a lot of JavaScript, and you don&rsquo;t appear to have it enabled. Would you mind turning it on and trying again?</p>
			</div>
		</div>
	</noscript>
</body>
</html>
