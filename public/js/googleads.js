$(function() {
	loadAds();
});
function loadAds() {
	$( ".google-ad" ).each( function() {
		var _self = $(this);
		$.ajax({
			url: "/adsource",
			type: "POST",
			data: { "position": _self.data('position'),
					"width": $(window).width()
			}
		}).done(function ( data ) {
			_self.html(data.content);
			_self.removeClass('google-ad').addClass(data.width + '-ad');
			(adsbygoogle = window.adsbygoogle || []).push({});
		});
	});
};	
