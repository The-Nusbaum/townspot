(function($){
    $.fn.UserProfile = function( options ) {
        var defaults = {
        }
        var methods =
        {
            init : function()
            {

                $('#userDetails').on('click','.btn-follow',function(){
                    methods.follow();
                });
                $('#userDetails').on('click','.btn-unfollow',function(){
                    methods.unfollow();
                });
                $('#sendEmail').click(function(){
                    $.post(
                        '/users/contactCreator',
                        {
                            user_id: $('#user_id').val(),
                            video_creator: $('#video_creator').val(),
                            subject: $('#subject').val(),
                            message: $('#message').val()
                        }, function () {
                            $('#contactModal').modal('hide')
                        }
                    );
                });
                $('#fans .prev').click(function(e){
                    methods.prevFans(e);
                });
                $('#fans .next').click(function(e){
                    methods.nextFans(e);
                });
                $('#Comments .prev').click(function(e){
                    methods.prevComments(e);
                });
                $('#Comments').on('click','.fa-times',function(e){
                    methods.delComment(e);
                });
                $('#Comments #comment').click(function(e){
                    methods.leaveComment();
                });
                $('#Comments .next').click(function(e){
                    methods.nextComments(e);
                });
                $('#videos .prev, #favorites .prev').click(function(e){
                    methods.prevVideos(e);
                });
                $('#videos .next, #favorites .next').click(function(e){
                    methods.nextVideos(e);
                });
                $('.unfav').click(function(e){
                    methods.unfav(e);
                });
                $('.stats .delete').click(function(e){
                    var $target = $(e.target);
                    var id = $target.parents('ul').attr('data-id');
                    bootbox.confirm("Are you sure?", function(result) {
                        if(result){
                            $.ajax({
                                    type: 'get',
                                    url: '/api/media/delete/'+id
                                }

                            ).done(function(data){
                                    if(data.success) {
                                        $stats = $target.parents('.stats');
                                        $stats.prev().remove();
                                        $stats.remove();
                                        $('.videoCount').text($('#videos .video').length);
                                    }
                                });
                        }
                    });
                });

                $('.stats .edit').click(function(e){
                    var $target = $(e.target);
                    var id = $target.parents('ul').attr('data-id');
                    window.location = "/videos/edit/" + id;
                });

                //calculate the offset
                options.offset = (new Date().getTimezoneOffset() * -1) * 60000 ;
            },
            follow : function(){
                $.ajax({
                    url: "/api/V1/UserFollow",
                    type: "POST",
                    data: {
                        follower_id: options.follower_id,
                        followee_id: options.followee_id
                    }
                }).done(function ( data ) {
                    console.log(data.data.UserFollow.id);
                    $('.btn-follow')
                        .toggleClass('btn-follow')
                        .toggleClass('btn-unfollow')
                        .text('Unfan')
                        .attr('data-id',data.data.UserFollow.id);
                });
            },
            unfollow : function(){
                $.ajax({
                    url: "/api/V1/UserFollow/"+$('.btn-unfollow').attr('data-id'),
                    type: "DELETE"
                }).done(function ( data ) {
                    $('.btn-unfollow')
                        .toggleClass('btn-follow')
                        .toggleClass('btn-unfollow')
                        .text('Become a Fan');
                });
            },
            nextFans : function(e){
                $target = $(e.target).parent().siblings('.list').children(':first');
                $parent = $(e.target).parent().siblings('.list');
                $target.remove().appendTo($parent);
            },
            prevFans : function(e){
                $target = $(e.target).parent().siblings('.list').children(':last');
                $parent = $(e.target).parent().siblings('.list');
                $target.remove().prependTo($parent);
            },
            nextComments : function(e){
                $target = $(e.target).parents('.row:first').siblings('.list').children(':nth-child(-n+5)');
                $parent = $(e.target).parents('.row:first').siblings('.list');
                $target.remove().appendTo($parent);
                $('html, body').animate({
                    scrollTop: $('#Comments').offset().top
                }, 500);
            },
            prevComments : function(e){
                $target = $(e.target).parents('.row:first').siblings('.list').children(':nth-last-of-type(-n+5)');
                $parent = $(e.target).parents('.row:first').siblings('.list');
                $target.remove().prependTo($parent);
                $('html, body').animate({
                    scrollTop: $('#Comments').offset().top
                }, 500);
            },
            leaveComment : function() {
                $target = $('#commentText');
                $.post(
                    '/api/artistComment/create',
                    {
                        target_id: options.follower_id,
                        user_id: options.followee_id,
                        comment: $target.val()
                    },
                    function(data) {
                        var comment = data.data;
                        html = '<div class="row col-xs-12 comment">' +
                            '<a class="col-xs-2" href="/u/'+comment.user_id+'"><img class="img-responsive" src="http://images.townspot.tv/resizer.php?id='+comment.user_id+'&type=profile&w=100&h=100" alt="a"/></a>' +
                            '<p class="col-xs-9 offset-xs-1">'+comment.comment+'<br><a href="/u/'+comment.user_id+'">'+options.username+'</a> - <abbr class="timeago" title="'+new Date().toISOString()+'">'+jQuery.timeago(new Date().toISOString())+'</abbr> <i class="fa fa-times"></i></p>'+
                            '</div>';
                        $('#Comments .list').prepend(html);
                        if($('#Comments .list').children().length > 5) {
                            $('.comment-ctrl').show();
                        }
                    }
                );

            },
            delComment: function(e){
                var $target = $(e.target);
                console.log($target.parents('.comment'));
                var id = $target.parents('.comment').attr('data-id');
                bootbox.confirm("Are you sure?", function(result) {
                    if(result){
                        $.ajax({
                                type: 'get',
                                url: '/api/artistComment/delete/'+id
                            }

                        ).done(function(data){
                                if(data.success) {
                                    $target.parents('.comment').remove();
                                    if($('#Comments .list').children().length < 5) {
                                        $('.comment-ctrl').hide();
                                    }
                                }
                            });
                    }
                });
            },
            nextVideos : function(e){
                $target = $(e.target).parents('#videos,#favorites').find('.list').children('div:nth-child(-n+2)');
                $parent = $(e.target).parents('#videos,#favorites').find('.list');
                $target.remove().appendTo($parent);

            },
            prevVideos : function(e){
                $target = $(e.target).parents('#videos,#favorites').find('.list').children('div:nth-last-of-type(-n+2)');
                $parent = $(e.target).parents('#videos,#favorites').find('.list');
                $target.remove().prependTo($parent);
            },
            unfav : function(e) {
                var $target = $(e.target);
                var id = $target.parents('.video').attr('data-key');
                bootbox.confirm("Are you sure?", function(result) {
                    if(result){
                        $.ajax({
                                type: 'get',
                                url: '/api/user/removeFavorite/'+id
                            }

                        ).done(function(data){
                                if(data.success) {
                                    $stats = $target.parents('.video').remove();
                                }
                            });
                    }
                });
            }
        }
        var options = $.extend(defaults, options);
        methods.init();
    }
})(jQuery);

$(document).ready(function(){
    $('.more-info').popover({html:true});
});