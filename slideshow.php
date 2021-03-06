<?php
if($_GET['exhibit'])

	$exhibit = strip_tags($_GET['exhibit']);
?>
<!--
	HTML document for media display
	by Caleb Lucas-Foley
	Jun 2016

	Requires HTML5
-->
<!DOCTYPE html>

<html>
<head>
	<meta charset="utf-8">
	<title>Exhibit Not Found</title>
	<style>
	* {
		/* Make everything flush */
		padding:0px;
		margin:0px;
	}
	html, body {
		/* Required for child elements to properly inherit heights */
		height:100%;
	}
	body {
		/* Format text and background color for body */
		color:lightgrey;
		background-color:black;
	}
	#slideshow {
		/* Container element for slides and videos. Fills all of the screen
		 * not taken up by text box. Height must be defined for percent
		 * height to work properly for children */
		position:absolute;
		width:100%;
		left:0px;
		top:0px;
		bottom:150px;
	}
	#textbox {
		/* Put the text (title and description) in a div at the bottom of
		 * the window. It is always 150px tall */
		padding-left:10px;
		padding-right:10px;
		position:absolute;
		height:150px;
		bottom:0px;
	}
	/*#slide {
		 * Setting these margins centers the slide when it is a block element.
		 * 100% height stretches it to fill the container
		margin-left:"auto";
		margin-right:"auto";
		display:"block";
		height:"100%";
	}*/
	</style>
	
</head>

<body>
<div id="slideshow"></div>

<div id="textbox">
	<h1 id="title">Exhibit Not Found</h1>
	<p id="description">
		Add the exhibit name as a query string.
	</p>
</div>

<!--Requires JQuery-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script>
// Gets the exhibit name from a query string in the URL
// If there is no query string, return ""
// This signals the page to load from Exhibits/HOMEPAGE
function getExhibitName() {
	return "<?php echo $exhibit ?>";
}
// Sets the title of the window and the title of the description to the contents of title.txt
function setTitle(exhibitName) {
	$('title').load("Exhibits/" + exhibitName + "/title.txt");
	$('#title').load("Exhibits/" + exhibitName + "/title.txt");
}
// Sets the description contents to the contents of description.txt
function setDescription(exhibitName) {
	$('#description').load("Exhibits/" + exhibitName + "/description.txt");
}

// The home page has a single image in the slideshow div
function homePage() {
	var slideShow = document.getElementById("slideshow");
	var slide = document.createElement("img");
	formatSlide(slide);
	slide.setAttribute('src', "Exhibits/HOMEPAGE/logo.png");
	slideshow.appendChild(slide);
}

function beginVideos(exhibitName) {
	$.get("Exhibits/" + exhibitName + "/videos/index.json", function(videoList) {
		// Request successful; videoList is data from a text file containing the URLs
		if (videoList.length == 0) {
			// There are no videos to play
			beginSlides(exhibitName);
		} else {
			var slideshow = document.getElementById("slideshow");
			// Create and format the HTML5 video element
			var video = document.createElement("video");
			formatSlide(video);
			var source = document.createElement("source");
			var videoIndex = 0;
			// Get the source video from a local directory
			source.setAttribute('src', "Exhibits/" + exhibitName + "/videos/" + videoList[videoIndex]);
			video.appendChild(source);
			slideshow.appendChild(video);
			video.addEventListener('ended', nextVideo);

			function nextVideo() {
				video.pause();
				++videoIndex;
				if (videoIndex < videoList.length) {
					source.setAttribute('src', "Exhibits/" + exhibitName + "/videos/" + videoList[videoIndex]);
					video.load();
					video.play();
				} else {
					// Clean up and remove video elements, begin image slideshow
					video.removeEventListener('ended', nextVideo);
					video.removeChild(source);
					slideshow.removeChild(video);
					beginSlides(exhibitName);
				}
			}
			video.play();
		}
	}, "json");
}

function beginSlides(exhibitName) {
	$.get("Exhibits/" + exhibitName + "/images/index.json", function(imageList) {
		// Request successful; imageList is data from a text file containing the URLs
		if (imageList.length == 0) {
			directToHomePage();
		} else {
			var slideshow = document.getElementById("slideshow");
			// Create and format the img element used for the slides
			var slide = document.createElement("img");
			formatSlide(slide);
			slide.setAttribute('alt', exhibitName); // set an alt text
			var slideIndex = 0;
			// Get the first source image from a local directory
			slide.setAttribute('src', "Exhibits/" + exhibitName + "/images/" + imageList[slideIndex]);
			slideshow.appendChild(slide);
			timer = setInterval(nextSlide, 3000); // 3 second interval between slides
			function nextSlide() {
				++slideIndex;
				if (slideIndex < imageList.length) {
					slide.setAttribute('src', "Exhibits/" + exhibitName + "/images/" + imageList[slideIndex]);
				} else {
					// Direct to the home page now that all the media has been displayed
					clearInterval(timer);
					slideshow.removeChild(slide);
					directToHomePage();
				}
			}
		}
	}, "json");
}

// Formats an element to fit in the slideshow div (enters and stretches to fill it)
function formatSlide(element) {
	element.style.marginLeft = "auto";
	element.style.marginRight = "auto";
	element.style.display = "block";
	element.style.height = "100%";
}
// Directs the user to the default page, with no query strings
function directToHomePage() {
	window.location.href = window.location.pathname;
}

$(document).ready(function onPageLoad() {
	var exhibitName = getExhibitName();
	if (exhibitName == "") {
		setTitle("HOMEPAGE");
		setDescription("HOMEPAGE");
		homePage("HOMEPAGE");
	} else {
		setTitle(exhibitName);
		setDescription(exhibitName);
		beginVideos(exhibitName);
	}
});
</script>

</body>

</html>
