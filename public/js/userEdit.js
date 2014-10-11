var userEdit = {
    data: {},
    init: function() {
        tinyMCE.init({
            mode: "textareas",
            theme_url: "/js/tinymceTheme.js",
            skin_url: "/css/tinymce",
            content_css: "/css/tinymce",
            menubar:false,
            statusbar: false,
            setup: function(editor) {
                editor.on('change', function(e) {
                    var editor = tinyMCE.activeEditor
                    editor.save();
                    $(editor.getElement()).change();
                    console.log(userEdit.data);
                });
            }
        });
        //userEdit.picInit();

        $("#userEdit input, #userEdit select, #userEdit textarea").change(function(e){
            $this = $(this);
            userEdit.data[$this.attr('name')] = $this.val();
        });
        $("#userEdit").submit(function(e){
            e.preventDefault();
            var editors = tinyMCE.editors;

            $.ajax({
                method: "PUT",
                url: "/api/user/" + parseInt($("#userEdit #user_id").val()),
                data: userEdit.data,
                success: function(data){
                    if(data.success) {
                        window.location = '/dashboard';
                    }
                }
            });
        });

    },
    picInit: function(){
        var uploader = new plupload.Uploader({
            runtimes : 'html5,flash',
            browse_button : 'picPrevfiew',
            max_file_size : '10mb',
            url : '/user/process-profile-pic',
            flash_swf_url : '/plupload/js/plupload.flash.swf',
            silverlight_xap_url : '/plupload/js/plupload.silverlight.xap',
            filters : [
                {title : "Image files", extensions : "jpg,gif,png"}
            ],
            resize : {width : 700, height : 400, quality : 90},
            multi_selection : false,
            unique_names : true
        });

        uploader.bind('Init', function(up, params) {
        });

        $('#uploadfiles').click(function(e) {
            uploader.start();
            e.preventDefault();
        });

        uploader.init();

        uploader.bind('FilesAdded', function(up, files) {
            $.each(files, function(i, file) {
                uploader.start();
            });

            up.refresh(); // Reposition Flash/Silverlight
        });

        uploader.bind('Error', function(up, err) {
            up.refresh(); // Reposition Flash/Silverlight
        });

        uploader.bind('FileUploaded', function(up, file, r) {
            var url = r.response;
            if (url != 'error') {
                $('.picPreview').attr('src','/'+url);
                $('#plupPicVal').val("/" + url).change();
            } else {
                $('#plupPic').html('There was an error uploading your profile picture');
                $('#plupPicVal').val("");
            }
        });
    }
}

$(document).ready(function(){
    userEdit.init();
});
