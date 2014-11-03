if (typeof($.cookie('geo')) != "undefined") {
	getExplore($.cookie('geo'));
} else {
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			$.cookie('geo', position.coords.latitude + ',' + position.coords.longitude, { expires: 30, path: '/' });
			getExplore(position.coords.latitude + ',' + position.coords.longitude);
		}, function() {
			getExplore('');
		});
	} else {
		getExplore('');
	}
}
function getExplore(coords) {
	$.ajax({
		url: "/getexplorelink",
		type: "POST",
		data: { "coords": coords }
	}).done(function ( data ) {
		if ((data.FullName != '')&&(data.Link != '')) {
			$('#explore-btn-link').prop('href', data.Link);
			$('#explore-btn-location').html('Explore ' + data.FullName);
			$('.explore-btn-wrapper').css('display','block');
		}
	});
}

