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
        plupInit.image();

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

        $("#register input, #register select, #register textarea").change(function(e){
            $this = $(this);
            userRegister.data[$this.attr('name')] = $this.val();
        });
        $("#register").submit(function(e){
            if(!userRegister.validate()) {
                return false;
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
    }
}


