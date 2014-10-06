if(navigator.geolocation) {
	navigator.geolocation.getCurrentPosition(function(position) {
		getExplore(position.coords.latitude + ',' + position.coords.longitude);
	}, function() {
		getExplore('');
	});
} else {
	getExplore('');
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