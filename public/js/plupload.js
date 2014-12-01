var plupInit = {
    image: function() {
        var photo_uploader = new plupload.Uploader({
            runtimes : 'html5',
            browse_button : 'plupload-image', // you can pass in id...
            url : "/file/upload",
            unique_names: true,
            filters : {
                max_file_size : '10mb',
                mime_types: [
                    {title : "Image files", extensions : "jpg,gif,png"},
                ]
            },
            init: {
                FileUploaded: function(up, files) {
                    $('.picPreview').attr('src',"/files/"+files.target_name);
                    $('#plupPicVal').val("/files/"+files.target_name);
                    $('#plupPicVal').change();
                },

                FilesAdded: function(up, files) {
                    $.each(files, function(i, file) {
                        photo_uploader.start();
                    });
                }
            }
        });

        $('.picPreview').click(function(){
            $('#plupload-image').click();
        });

        photo_uploader.init();
    },
    video: function() {
        var video_uploader = new plupload.Uploader({
            runtimes : 'html5',
            browse_button : 'plupload-video', // you can pass in id...
            url : "/file/upload",
            unique_names: true,
            filters : {
                max_file_size : '100gb',
                mime_types: [
                    {title : "Video files", extensions : "mp4,mov,qt,flv,f4v,wmv,asf,mpg,vob,m2v,mp2,m4v,avi,webm,ogv,ogg,mxf,mts,mkv,r3d,rm,ram,flac,mj2,mpeg,3gp"},
                ]
            },
            multi_selection : false,
            init: {
                FileUploaded: function(up, files) {
                    $('#videofile').val("/files/"+files.target_name);
                    $('#videofile').change();
                },

                FilesAdded: function(up, files) {
                    $.each(files, function(i, file) {
                        $('.progress').removeClass('hidden');
                        video_uploader.start();
                    });
                },

                UploadProgress: function (up, file) {
                    $('.progress .progress-bar').attr('style','width:'+up.total.percent+'%;');
                    $('.progress .progress-bar').attr('aria-valuenow',up.total.percent)
                }
            }
        });

        video_uploader.init();
    }
}