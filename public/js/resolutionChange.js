function resolutionChange() {
    $(window).trigger('resolutionchange');
};

var resizeTimer;
$(window).resize(function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(resolutionChange, 500);
});
