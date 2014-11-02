function setInfoButtons() {
	$('.info-button').popover('destroy');
	$('.carousel-info-button').popover('destroy');

	$(".info-button.first").popover({html: true, placement: 'right'});
	$(".info-button.in-row").popover({html: true, placement: 'left'});
	$(".info-button.last").popover({html: true, placement: 'left'});
	$(".carousel-info-button").popover({html: true, placement: 'right'});
	
	$('.info-button').on('show.bs.popover', function () {
		$('.video-preview').css('z-index',-100);
		$('.carousel-indicators').css('z-index',-100);
		$(this).parent().parent().css('z-index',1000);
	});
	
	$('.carousel-info-button').on('show.bs.popover', function () {
		$('.carousel-indicators').css('z-index',-100);
	});
	
	$('.info-button').on('hide.bs.popover', function () {
		$('.video-preview').css('z-index',1);
		$('.carousel-indicators').css('z-index',1);
	});

	$('.carousel-info-button').on('hide.bs.popover', function () {
		$('.carousel-indicators').css('z-index',1);
	});
}
