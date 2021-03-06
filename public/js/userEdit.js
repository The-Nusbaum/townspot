var userEdit = {
    data: {},
    init: function() {
        tinyMCE.init({
//            mode: "textareas",
            selector: "textarea",
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
        plupInit.image();

        $("#userEdit input[type!=checkbox], #userEdit select, #userEdit textarea").change(function(e){
            $this = $(this);
            var val = $this.val();
            userEdit.data[$this.attr('name')] = val;
        });

        $("#userEdit input[type=checkbox]").click(function(){
            $this = $(this);
            l = $this.is(':checked');
            userEdit.data[$this.attr('name')] = val;
        });

        $('#link_twitter').click(function(e){
            e.preventDefault();
            window.location = "/custom/login/twitter";
        });

        $('#province_id').change(function(){
            pId = $(this).val();
            $target = $('#city_id');
            $target.children().remove();
            $target.append("<option>loading...</option>");
            $.get(
                '/api/city/getList/'+pId,
                function(data){
                    $target = $('#city_id');
                    if(data.success) {
                        $target.children().remove();
                        $target.append("<option>Please select a City</option>");
                        var cities = data.data;
                        $(cities).each(function(){
                            $target.append('<option value="'+this.id+'">'+this.name+'</option>');
                        })
                    }
                }
            );
        });
        $('#username').blur(function(){
            userEdit.checkUsername();
        });
        $('#email').blur(function(){
            userEdit.checkEmail();
        });

        $('#link_facebook').click(function(e){
            e.preventDefault();
            window.location = "/custom/login/facebook";
        });

        $('#unlink_twitter').click(function(e){
            e.preventDefault();
            window.location = "/user/unlink/twitter";
        });

        $('#unlink_facebook').click(function(e){
            e.preventDefault();
            window.location = "/user/unlink/facebook";
        });
        $("#userEdit").submit(function(e){
            e.preventDefault();
            var editors = tinyMCE.editors;

            if(!userEdit.validate()) return false;

            $.ajax({
                method: "POST",
                url: "/api/user/update/" + parseInt($("#userEdit #user_id").val()),
                data: userEdit.data,
                success: function(data){
                    if(data.success) {
                        window.location = '/dashboard';
                    }
                }
            });
        });

    },
    validate: function() {
        $('.alert').hide();
        var error = false;
        if($('#username').val() == ''){
            error = true;
            $('.nousername').show();
        }

        if($('#display_name').val() == ''){
            error = true;
            $('.nodisplayname').show();
        }

        if($('#email').val() == ''){
            error = true;
            $('.noemail').show();
        }

        /*
        if($('#firstName').val() == ''){
            error = true;
            $('.nofirstname').show();
        }

        if($('#lastName').val() == ''){
            error = true;
            $('.nolastname').show();
        }
        */

        if($('#password').val() != ''){
             if($('#password').val() != $('#password2').val()){
                error = true;
                $('.noconfirmpass').show();
            }

            if(!$('#password').val().match(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,15}$/)) {
                error = true;
                $('.invalidpass').show();
            }
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

        /*
        if($('#plupPicVal').val() == "http://images.townspot.tv/resizer.php?id=none&type=profile") {
            error = true;
            $('.noimage').show();
        }
        */
        return !error;
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
    }
}


