$(".info-button.first").popover({html: true, placement: 'right'});
$(".info-button.in-row").popover({html: true, placement: 'left'});
$(".info-button.last").popover({html: true, placement: 'left'});

$('.info-button.first').on('show.bs.popover', function () {
	$('.video-preview.in-row').css('z-index',-100);
	$('.video-preview.last').css('z-index',-100);
	$('.carousel-indicators').css('z-index',-100);
});
$('.info-button.in-row').on('show.bs.popover', function () {
	$('.video-preview.first').css('z-index',-100);
	$('.video-preview.last').css('z-index',-100);
	$('.carousel-indicators').css('z-index',-100);
});
$('.info-button.last').on('show.bs.popover', function () {
	$('.video-preview.first').css('z-index',-100);
	$('.video-preview.in-row').css('z-index',-100);
	$('.carousel-indicators').css('z-index',-100);
});

$('.info-button.first').on('hide.bs.popover', function () {
	$('.video-preview.in-row').css('z-index',1);
	$('.video-preview.last').css('z-index',1);
	$('.carousel-indicators').css('z-index',1);
});
$('.info-button.in-row').on('hide.bs.popover', function () {
	$('.video-preview.first').css('z-index',1);
	$('.video-preview.last').css('z-index',1);
	$('.carousel-indicators').css('z-index',1);
});
$('.info-button.last').on('hide.bs.popover', function () {
	$('.video-preview.first').css('z-index',1);
	$('.video-preview.in-row').css('z-index',1);
	$('.carousel-indicators').css('z-index',1);
});