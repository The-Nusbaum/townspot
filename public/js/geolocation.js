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
//	$.ajax({
//		url: "/city/getExplore",
//		type: "POST",
//		data: { "coords": coords }
//	}).done(function ( data ) {
//		obj = JSON.parse(data);
//		if ((obj.FullName != '')
//		  &&(obj.Link != '')) {
//			$('#explore-btn-link').prop('href',"/videos/" + obj.Link);
//			$('#explore-btn-location').html('Explore ' + obj.FullName);
//			$('.explore-btn-wrapper').css('display','block');
//		}
//	});
}

