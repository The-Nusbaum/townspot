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
        plupInit.image();

        $("#userEdit input, #userEdit select, #userEdit textarea").change(function(e){
            $this = $(this);
            userEdit.data[$this.attr('name')] = $this.val();
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

        if($('#plupPicVal').val() == "http://images.townspot.tv/resizer.php?id=none&type=profile") {
            error = true;
            $('.noimage').show();
        }
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


