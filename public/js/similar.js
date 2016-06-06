(function($){
    $.fn.Similar = function( options ) {
        var videoCollection = new VideoCollection();
        var videoView = new VideoView({el: '#matching-videos'});
        var defaults = {
            mediaId: '',
            limit: 3,
            pages: {},
            page: 1
        };
        var methods =
        {
            //getResults: function () {
            //    $('.loading-spinner').css('display', 'block');
            //    $.ajax({
            //        url: "/videos/similar-videos/" + options.mediaId + "/" + options.limit,
            //        type: "GET",
            //        data: {}
            //    }).done(function (data) {
            //        options.data = data;
            //        methods.renderPage();
            //    });
            //},
            renderPage: function () {
                $('#matching-videos').children().remove();
                $.each(options.data, function () {
                    videoCollection.add(new Video(this));
                });
                videoView.render(videoCollection);
                setInfoButtons();
                loadAds();
                setTimeout(function () {
                    $('#Loading').modal('hide');
                    $('.loading').removeClass('loading');
                    $('.loading-spinner').css('display', 'none');
                }, 100);
            },
            getPage: function (page,refresh = false) {
                var nextPage = options.page + 1;
                $.ajax({
                    url: "/videos/similar-videos/" + options.mediaId + "/" + options.limit,
                    type: "GET",
                    data: {}
                }).done(function (data) {
                    options.pages[page] = data;
                    if (refresh) {
                        options.data = options.pages[page];
                        methods.renderPage();
                    }
                });
            },
            nextPage: function() {
                options.page++;
                methods.getPage(options.page, true);
                $('#prev-similar').show();
            },
            prevPage: function() {
                options.page--;
                options.data = options.pages[options.page];
                methods.renderPage();
                if (option.page == 1) {
                    $('#prev-similar').hide();
                }
            }
        };
        var options = $.extend(defaults, options);
        methods.nextPage();
        $('#next-similar').click(function(){
            methods.nextPage();
        });
        $('#prev-similar').click(function(){
            methods.prevPage();
        });
    }
})(jQuery);

