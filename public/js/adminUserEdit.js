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
        $('#country_id').change(function(){
            userEdit.getStates();
        });
        $('#province_id').change(function(){
            userEdit.getCities();
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
    },
    getStates: function(){
        $('#province_id option:not(:first)').remove();
        $('#province_id option:first').text('loading...');
        $('#city_id option:not(:first)').remove();
        $('#city_id option:first').text('loading...');
        $.get(
            '/api/province/getList/' + $('#country_id').val(),
            function(response){
                var data = response.data;
                if(data.length) {
                    $('#province_id option:first').text('Please Select a State');
                    $(data).each(function(){
                        $('#province_id').append("<option value='"+ this.id +"'>"+ this.name +"</option>");
                    });
                    $('#city_id option:first').text('Please Select a State');
                    $('#province_id, #city_id, [for=province_id], [for=city_id]').show();
                } else {
                    $('#province_id, #city_id, [for=province_id], [for=city_id]').hide();
                }
            }
        );
    },
    getCities: function(){
        $('#city_id option:not(:first)').remove();
        $('#city_id option:first').text('loading...');
        $.get(
            '/api/city/getList/' + $('#province_id').val(),
            function(response){
                var data = response.data;
                if(data.length) {
                    $('#city_id option:first').text('Please Select a City');
                    $(data).each(function(){
                        $('#city_id').append("<option value='"+ this.id +"'>"+ this.name +"</option>");
                    });
                    $('#city_id option:first').text('Please Select a City');
                    $('#province_id, #city_id').show();
                } else {
                    $('#city_id option:first').text('Not Applicable');
                }
            }
        );
    }
}


