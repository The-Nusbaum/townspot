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

        $('#country_id').change(function(){
            userRegister.getStates();
        });
        $('#province_id').change(function(){
            userRegister.getCities();
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
        if(window.location.pathname.match(/admin/)) {
            return userRegister.adminValidate();
        }
        var error = false;
        if($('#username').val() == ''){
            error = true;
            $('.nousername').show();
        }

        if($('#displayName').val() == ''){
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

        if(!$('#terms_agreement').is(':checked')){
            error = true;
            $('.noterms').show();
        }

        /*
        if($('#plupPicVal').val() == "http://images.townspot.tv/resizer.php?id=none&type=profile") {
            error = true;
            $('.noimage').show();
        }
        if($('.userexists, .emailexists').length) {
            error = true;
        }
        */
        return !error;
    },
    adminValidate: function() {
        var error = false;
        if($('#username').val() == ''){
            error = true;
            $('.nousername').show();
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

        if($('#province_id').val() == ''){
            error = true;
            $('.nostate').show();
        }

        if($('#city_id').val() == ''){
            error = true;
            $('.nocity').show();
        }
        return !error;
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


