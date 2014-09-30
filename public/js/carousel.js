$('.carousel').carousel({
  interval: 2000000,
  pause: "hover"
})

$('.carousel').on('slide.bs.carousel', function () {
	$(this).find(".carousel-inner").css('overflow','hidden');
});
$('.carousel').on('slid.bs.carousel', function () {
	$(this).find(".carousel-inner").css('overflow','visible');
});

