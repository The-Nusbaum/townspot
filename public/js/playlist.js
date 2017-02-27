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
    }
};
