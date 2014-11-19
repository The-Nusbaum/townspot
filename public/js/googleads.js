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

$(window).on( "resolutionchange", function( e ) {
	var deviceChange = false;
	if ($(window).width() <= 400) {
		if ($('.desktop-ad').length > 0) {
			$('.desktop-ad').removeClass('desktop-ad').addClass('google-ad').html('');
			deviceChange = true;
		}
		if ($('.tablet-ad').length > 0) {
			$('.tablet-ad').removeClass('tablet-ad').addClass('google-ad').html('');
			deviceChange = true;
		}
		if ($('.mobile-ad').length > 0) {
			$('.mobile-ad').removeClass('mobile-ad').addClass('google-ad').html('');
			deviceChange = true;
		}
	} else if ($(window).width() <= 500) {
		if ($('.desktop-ad').length > 0) {
			$('.desktop-ad').removeClass('desktop-ad').addClass('google-ad').html('');
			deviceChange = true;
		}
		if ($('.tablet-ad').length > 0) {
			$('.tablet-ad').removeClass('tablet-ad').addClass('google-ad').html('');
			deviceChange = true;
		}
		if ($('.phone-ad').length > 0) {
			$('.phone-ad').removeClass('phone-ad').addClass('google-ad').html('');
			deviceChange = true;
		}
	} else if ($(window).width() <= 800) {
		if ($('.desktop-ad').length > 0) {
			$('.desktop-ad').removeClass('desktop-ad').addClass('google-ad').html('');
			deviceChange = true;
		}
		if ($('.mobile-ad').length > 0) {
			$('.mobile-ad').removeClass('mobile-ad').addClass('google-ad').html('');
			deviceChange = true;
		}
		if ($('.phone-ad').length > 0) {
			$('.phone-ad').removeClass('phone-ad').addClass('google-ad').html('');
			deviceChange = true;
		}
	} else {
		if ($('.tablet-ad').length > 0) {
			$('.tablet-ad').removeClass('tablet-ad').addClass('google-ad').html('');
			deviceChange = true;
		}
		if ($('.mobile-ad').length > 0) {
			$('.mobile-ad').removeClass('mobile-ad').addClass('google-ad').html('');
			deviceChange = true;
		}
		if ($('.phone-ad').length > 0) {
			$('.phone-ad').removeClass('phone-ad').addClass('google-ad').html('');
			deviceChange = true;
		}
	}
	if (deviceChange) {
		loadAds();
	}
});