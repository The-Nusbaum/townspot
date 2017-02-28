var playlist = {
    uid: 0,
    mid: 0,
    init: function() {
        $(".addToList").click(function(){
            if($('#playlists').val() != 'create' && $('#playlists').val() != '') {
                $('.playlistAddon').toggle();
                $('select#playlists').attr('disabled', 1);
                $.get('/api/playlist/add/' + $('#playlists').val() + '?mid=' + playlist.mid,
                    function () {
                        $('.playlistAddon').toggle();
                        $('select#playlists').removeAttr('disabled');
                    }
                );
            } else {
                $('#createPlaylist').modal('show');
            }
        });

        $('#createPlaylist .submit').click(function(){
            $.post('/api/playlist/create/' + playlist.uid,
                {
                    name: $('#createPlaylist #playlistName').val(),
                    desc: $('#createPlaylist #playlistDescription').val()
                }, function(response) {
                    var data = response.data;
                    var p = data[0];
                    var html = "<option value='" + p.id + "'>" + p.name + "</option>";
                    $('#playlists option:last').before(html);
                    $.get('/api/playlist/add/' + p.id + '?mid=' + playlist.mid);
                    $('#createPlaylist').modal('hide');
                    $('#createPlaylist input, #createPlaylist textarea, #createPlaylist select').val('');
                }
            );
        });

    },
    populateList: function() {
        $('#playlists').each(function(){
            var uid = $(this).attr('data-uid');
            $.get('/api/playlist/user/' + uid,
                function(response){
                    if(response.success) {
                        var data = response.data;
                        $(data).each(function(){
			                var p = this[0];
                            var html = "<option value='" + p.id + "'>" + p.name + "</option>";
                            $('#playlists option:last').before(html);
                        });
                    }
                }
            );
        });
    },
    populateProfile: function() {
        $('#playlists').each(function(){
            var uid = $(this).attr('data-uid');
            $.get('/api/playlist/user/' + playlist.uid,
                function(response){
                    if(response.success) {
                        var data = response.data;
                        $(data).each(function(){
                            var p = this[0];
                            var ohtml = '' +
                            '<div class="playlist">' +
                                '<header class="row">' +
                                    '<div class="name col-xs-8">' +
                                        p.name +
                                    '</div>' +
                                    '<div class="name col-xs-1 pull-right"><i class="fa fa-minus-circle delete" data-id="'+ p.id +'"></i></div>' +
                                '</header>' +
                                '<div class="row">' +
                                    '<p class="col-xs-12">' +
                                        p.description +
                                    '</p>' +
                                '</div>' +
                                '<div class="row playlist-media-list">' +


                            console.log(p);
                            $(p.media).each(function(){
                                var ihtml =  '<div class="col-sm-4 col-xs-6 col-wide">' +
                                                '<div class="video-preview first" data-id="[id]">' +
                                                    '<a href="[video_link]" style="background-image: url([thumb])"></a>' +
                                                    '<div class="carousel-caption small">' +
                                                        '<div class="video-title">' +
                                                            '<h3 title="[title]" class="dot-text">' +
                                                                '<a href="[video_link]">' +
                                                                    '<div class="dot-text">[Title]</div>' +
                                                                '</a>' +
                                                            '</h3>' +

                                                            '<h3 class="dot-text">' +
                                                                '<a href="[profile_link]">' +
                                                                    '<div class="dot-text">by [display_name]</div>' +
                                                                '</a>' +
                                                            '</h3>' +

                                                            '<h3 title="[location]" class="dot-text">[location]</h3>' +
                                                        '</div>' +
                                                    '</div>' +
                                                '</div>' +
                                            '</div>';
                                ihtml = ihtml.replace(/\[id\]/g, this.id);
                                ihtml = ihtml.replace(/\[location\]/g, this.location);
                                ihtml = ihtml.replace(/\[profile_link\]/g, this.author.profileLink);
                                ihtml = ihtml.replace(/\[display_name\]/g, this.author.displayName);
                                ihtml = ihtml.replace(/\[video_link\]/g, this.mediaLink);
                                ihtml = ihtml.replace(/\[thumb\]/g, this.previewImage);
                                ihtml = ihtml.replace(/\[title\]/g, this.title);
                                ohtml += ihtml
                            });
                            ohtml += '</div>' +
                            '</div>';
                            $('#playlists').prepend(ohtml);
                        });
                    }
                }
            );
        });
    }
};
