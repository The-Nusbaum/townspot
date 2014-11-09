var userRegister = {
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
                });
            }
        });
        userRegister.pluploadImage();

        $('#username').blur(function(){
            userRegister.checkUsername();
        });

        $('#username').focus(function(){
            $('.userexists').remove();
        });

        $('#email').blur(function(){
            userRegister.checkEmail();
        });

        $('#email').focus(function(){
            $('.emailexists').remove();
        });

        $("#register input, #register select, #register textarea").change(function(e){
            $this = $(this);
            userRegister.data[$this.attr('name')] = $this.val();
        });
        $("#register").submit(function(e){
            e.preventDefault();
            if(userRegister.validate()) {
                $(this).submit();
            }
        });

    },
    checkUsername: function(){
        $.get(
            '/api/User/checkUsername/'+$('#username').val(),
            function(data){
                console.log(data.success);
                if(!data.success) {
                    html = "<div class='alert alert-warning userexists' style='display:block'>"+data.message+"</div>"
                    $(html).insertAfter('.nousername');
                }
            }

        )
    },
    checkEmail: function() {
        $.get(
                '/api/User/checkEmail/'+$('#email').val(),
            function(data){
                if(!data.success) {
                    html = "<div class='alert alert-warning emailexists' style='display:block'>"+data.message+"</div>"
                    $(html).insertAfter('.noemail');
                }
            }

        )
    },
    validate: function() {
        $('.alert').hide();
        var error = false;
        if($('#username').val() == ''){
            error = true;
            $('.nousername').show();
        }

        if($('#email').val() == ''){
            error = true;
            $('.noemail').show();
        }

        if($('#firstName').val() == ''){
            error = true;
            $('.nofirstname').show();
        }

        if($('#lastName').val() == ''){
            error = true;
            $('.nolastname').show();
        }

        if($('#password').val() == ''){
            error = true;
            $('.nopassword').show();
        }

        if($('#password').val() != $('#password2').val()){
            error = true;
            $('.noconfirmpass').show();
        }

        if(!$('#password').val().match(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,15}$/)) {
            error = true;
            $('.invalidpass').show();
        }

        if($('#email').val() == ''){
            error = true;
            $('.noemail').show();
        }

        if($('#province_id').val() == ''){
            error = true;
            $('.nostate').show();
        }

        if($('#city_id').val() == ''){
            error = true;
            $('.nocity').show();
        }

        if($('#email').val() == ''){
            error = true;
            $('.noemail').show();
        }

        if(!$('#terms_agreement').is(':checked')){
            error = true;
            $('.noterms').show();
        }

        if($('#plupPicVal').val() == "http://images.townspot.tv/resizer.php?id=none&type=profile") {
            error = true;
            $('.noimage').show();
        }
        if($('.userexists, .emailexists').length) {
            error = true;
        }
        return !error;
    },
    pluploadImage: function() {
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
                    $('#image_url').attr('src',"/files/"+files.target_name);
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

        photo_uploader.init();
    }
}

$(document).ready(function(){
    userRegister.init();
});
