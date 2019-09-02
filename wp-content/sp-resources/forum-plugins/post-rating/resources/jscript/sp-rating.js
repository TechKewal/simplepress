(function(spj, $, undefined) {
	function ratingRatePost(postid, url, type) {
		var ratingpost = '#spPostIndexPostRating' + postid;
		$(ratingpost).load(url);
	}

	function ratingStarHover(postid, stars, img_src, glyphs) {
		for (i=stars; i>0; i--) {
			var img_name = 'star-' + postid + '-' + i;
			if (glyphs) {
				document.getElementById(img_name).setAttribute('class', 'spIcon ' + img_src);
			} else {
				document.getElementById(img_name).src = img_src;
			}
		}
	}

	function ratingStarUnhover(postid, stars, img1_src, img2_src, glyphs) {
		for (i=5; i>stars; i--) {
			img_name = 'star-' + postid + '-' + i;
			if (glyphs) {
				document.getElementById(img_name).setAttribute('class', 'spIcon ' + img2_src);
			} else {
				document.getElementById(img_name).src = img2_src;
			}
		}

		for (i=stars; i>0; i--) {
			img_name = 'star-' + postid + '-' + i;
			if (glyphs) {
				document.getElementById(img_name).setAttribute('class', 'spIcon ' + img1_src);
			} else {
				document.getElementById(img_name).src = img1_src;
			}
		}
	}

	/***********************************************
	event handlers
	***********************************************/

	sprating_post_rate = {
		init : function() {
			$('.spPostRatingThumbRate, .spPostRatingStarRate').click( function() {
				var mydata = $(this).data();
				ratingRatePost(mydata.postid, mydata.site, mydata.type);
			});
		}
	};

	sprating_stars_mouseover = {
		init : function() {
			$('.spPostRatingStarRate').mouseover( function() {
				var mydata = $(this).data();
				ratingStarHover(mydata.postid, mydata.stars, mydata.img, mydata.glyphs);
			});
		}
	};

	sprating_stars_mouseout = {
		init : function() {
			$('.spPostRatingStarRate').mouseout( function() {
				var mydata = $(this).data();
				ratingStarUnhover(mydata.postid, mydata.cur, mydata.on, mydata.off, mydata.glyphs);
			});
		}
	};

	/***********************************************
	load event handlers on document ready
	***********************************************/

	$(document).ready(function() {
		sprating_post_rate.init();
		sprating_stars_mouseover.init();
		sprating_stars_mouseout.init();
	});
}(window.spj = window.spj || {}, jQuery));
