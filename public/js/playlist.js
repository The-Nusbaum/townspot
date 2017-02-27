var playlist = {
    uid: 0,
    init: function() {

    },
    populateList: function() {
        $('#playlists').each(function(){
            var uid = $(this).attr('data-uid');
            $.get('/api/playlist/user/' + uid,
                function(response){
                    if(response.success) {
                        var data = response.data;
                        $(data).each(function(){
                            var html = "<option value='" + this.id + "'>" + this.name + "</option>";
                            $('#playlists option:last').before(html);
                        });
                    }
                }
            );
        });
    }
};
