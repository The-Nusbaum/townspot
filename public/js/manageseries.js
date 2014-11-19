$.fn.Series = function( options )
{
    var defaults = {
        user_id: 0,
        series_page: 1,
        series_limit: 3,
        series_totalpages: 0,
        videos_page: 1,
        videos_limit: 10,
        videos_totalpages: 0
    };
    var methods =
    {
        init: function() {
            $('#manage-series button').click(function(el){
                el.preventDefault();
                methods.createSeries($('#manage-series .title').val());
            });
            $('.series-list').on('click','.series',function(e){
                $('.series-list .active').removeClass('active');
                $(this).toggleClass('active');
            });
            $('.available-video-list').on('click','.video .fa-plus',function(e){
                if( $('.series-list .series.active').length > 0) {
                    $(this).parents('li').remove().appendTo('.series-list .active .series-media-list');
                    methods.saveActiveSeries(true);
                }
            });
            $('.series-list').on('click','.series .panel-heading .fa-minus',function(e){
                var series_id = $(this).parents('.series').attr('data-id');
                $.ajax({
                    url: '/api/series/delete/'+series_id,
                    type: 'get',
                    success: function(data) {
                        methods.getSeries(true);
                    }
                });
            });
            $('.series-list').on('click','.series .video .fa-minus',function(e){
                $(this).parents('.series').click();
                $(this).parents('li.video').remove();
                methods.saveActiveSeries(true);
            });
            $('.series-list').on('click','.series .video .fa-chevron-up',function(e){
                $(this).parents('.series').click();
                $insertBefore = $(this).parents('li.video').prev();
                $(this).parents('li.video').insertBefore($insertBefore);
                methods.saveActiveSeries();

            });
            $('.series-list').on('click','.series .video .fa-chevron-down',function(e){
                $(this).parents('.series').click();
                $insertAfter = $(this).parents('li.video').next();
                $(this).parents('li.video').insertAfter($insertAfter);
                methods.saveActiveSeries();

            });
            $('.series-list').on('click','.pagination .next',function(){methods.series_next()});
            $('.series-list').on('click','.pagination .prev',function(){methods.series_prev()});
            $('.series-list').on('click','.pagination .first',function(){methods.series_first()});
            $('.series-list').on('click','.pagination .last',function(){methods.series_last()});
            $('.series-list').on('click','.pagination .page',function(el){methods.series_pageClick(el)});
            $('.available-video-list').on('click','.pagination .next',function(){methods.videos_next()});
            $('.available-video-list').on('click','.pagination .prev',function(){methods.videos_prev()});
            $('.available-video-list').on('click','.pagination .first',function(){methods.videos_first()});
            $('.available-video-list').on('click','.pagination .last',function(){methods.videos_last()});
            $('.available-video-list').on('click','.pagination .page',function(el){methods.videos_pageClick(el)});
            methods.getSeries();
            methods.getVideos();
        },
        videos_next: function(){
            options.videos_page = options.videos_page +1;

            methods.getVideos();
        },
        videos_last: function(){
            options.videos_page = options.videos_totalpages;
            methods.getVideos();
        },
        videos_first: function(){
            options.videos_page = 1;
            methods.getVideos();
        },
        videos_prev: function(){
            options.videos_page = options.videos_page -1;
            methods.getVideos();
        },
        videos_pageClick: function(el){
            options.videos_page = parseInt($(el.currentTarget).text());
            methods.getVideos();
        },
        series_next: function(){
            options.series_page = options.series_page +1;

            methods.getSeries();
        },
        series_last: function(){
            options.series_page = options.series_totalpages;
            methods.getSeries();
        },
        series_first: function(){
            options.series_page = 1;
            methods.getSeries();
        },
        series_prev: function(){
            options.series_page = options.series_page -1;
            methods.getSeries();
        },
        series_pageClick: function(el){
            options.series_page = parseInt($(el.currentTarget).text());
            methods.getSeries();
        },
        getSeries : function(refresh){
            if(!refresh) refresh = false;
            $.getJSON(
                '/api/series/getUserSeries/'+ options.user_id,
                {
                    page: options.series_page
                },
                function(data){
                    var html = '';
                    options.series_totalpages = data.data.pages;
                    for (x in data.data.series) {
                            var series = data.data.series[x];
                        console.log(series);
                            html += '<li class="series" data-id="'+series.id+'"><div class="col-sm-12">'+
                                '<div class="panel panel-default">'+
                                '<div class="panel-heading">'+ series.name +'<span class="pull-right"><i class="fa fa-minus"></i></span></div>'+
                                '<div class="panel-body">'+
                                '<ul class="series-media-list list-unstyled">';
                            for (i in series.episodes){
                                var media = series.episodes[i];
                                html +=         '<li class="video" data-id="'+media.id+'"><span class="pull-left"><i class="fa fa-plus"></i> <i class="fa fa-minus"></i></span> '+media.title+' <span class="pull-right"><i class="fa fa-chevron-up"></i> <i class="fa fa-chevron-down"></i></span></li>';
                            }
                            html +=             '</ul>'+
                                '</div>'+
                                '</div>'+
                                '</div></li>';
                    }


                    html += methods.generatePagination(options.series_totalpages, options.series_page);
                    $('#manage-series .series-list').children().remove();
                    $('#manage-series .series-list').append(html);
                    if(options.series_page == 1) {
                        $('#manage-series .series-list .pagination .first, #manage-series .series-list .pagination .prev').hide();
                    } else {
                        $('#manage-series .series-list .pagination .first, #manage-series .series-list .pagination .prev').show();
                    }

                    if(options.series_page < options.series_totalpages) {
                        $('#manage-series .series-list .pagination .next, #manage-series .series-list .pagination .last').show();
                    } else {
                        $('#manage-series .series-list .pagination .next, #manage-series .series-list .pagination .last').hide();
                    }

                    if (refresh) methods.getVideos();

                }
            );
        },getVideos : function(){
        $.getJSON(
            '/api/media/getAvailableSeriesMedia/'+options.user_id+'?page='+options.videos_page,
            function(data){
                var html = '';
                options.video_totalpages = data.data.pages;
                var media = data.data.media;
                for (x in media) {
                        video = media[x];
                        html += '<li class="video" data-id="'+video.id+'"><span class="pull-left"><i class="fa fa-plus"></i> <i class="fa fa-minus"></i></span> '+video.title+' <span class="pull-right"><i class="fa fa-chevron-up"></i> <i class="fa fa-chevron-down"></i></span></li>';
                }
                html += methods.generatePagination(options.video_totalpages, options.videos_page);
                $('#manage-series .available-video-list').children().remove();
                $('#manage-series .available-video-list').append(html);
                if(options.videos_page == 1) {
                    $('#manage-series .available-video-list .pagination .first, #manage-series .available-video-list .pagination .prev').hide();
                } else {
                    $('#manage-series .available-video-list .pagination .first, #manage-series .available-video-list .pagination .prev').show();
                }

                if(options.videos_page < options.video_totalpages) {
                    $('#manage-series .available-video-list .pagination .next, #manage-series .available-video-list .pagination .last').show();
                } else {
                    $('#manage-series .available-video-list .pagination .next, #manage-series .available-video-list .pagination .last').hide();
                }
            }
        );
    },
        generatePagination: function(totalPages, currentPage){
            console.log(totalPages);
            console.log(currentPage);
            var liClass = '';
            var html =  '<div class="row pagination">'+
                '<ul class="nav nav-pills">'+
                '<li class="first"><a href="javascript:void(0)">&lt;</a></li> ' +
                '<li class="prev"><a href="javascript:void(0)">&lt;&lt;</a></li> ';

            for(i=1;i<totalPages+1;i++) {
                if(currentPage == i) liClass = ' active';
                html +=         '<li class="page'+liClass+'"><a href="javascript:void(0)">'+i+'</a></li>';
                liClass = '';
            }
            html +=             '<li class="next"><a href="javascript:void(0)">&gt;</a></li> ' +
                '<li class="last"><a href="javascript:void(0)">&gt;&gt;</a></li> ';
            html +=         '</ul>';
            html +=     '</div>';
            return html;
        },
        createSeries: function(title){
            $.post(
                '/api/series/create',
                {
                    user_id: options.user_id,
                    name: title
                },
                function(data) {
                    //methods.createSeason()
                    methods.getSeries();
                }
            );
        },
        saveActiveSeries: function(refresh){
            if(!refresh) refresh = false
            var $series = $('.series-list .series.active');
            var i = 0;
            var media = {};
            $series.find('.video').each(function(){
                media[i] = {};
                media[i]['series_id'] = $series.attr('data-id');
                media[i]['media_id'] = $(this).attr('data-id');
                media[i]['episode_number'] = i;
                i++;
            });

            var data = {
                    user_id: options.user_id,
                    series_id: $series.attr('data-id'),
                    episodes: media
            };

            $.post(
                '/api/series/save',
                data,
                function(){
                    if (refresh) methods.getVideos();
                }
            );

        }
    };
    var options = $.extend(defaults, options);
    methods.init();
};