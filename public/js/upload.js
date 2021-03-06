var upload = {
    categories: [],
    init: function(){
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
        plupInit.video();
        upload.populateCategories();
        upload.clearData();
        $('body').on('click','.category, .category > span',function(e){
            upload.addCategory(e);
        });
        $('body').on('click','.selectedCategories .fa-times',function(e) {
            upload.removeCat(e);
        });
        $('hidden, input, select, textarea').change(function(e){
            upload.setVal(e);
        });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            upload.clearData();
        })
        $('#submitForm').submit(function(e){
            upload.submit(e);
        });
        $('#country_id').change(function(){
            upload.getStates();
            localStorage.setItem('countryId', $(this).val());
        });
        $('#province_id').change(function(){
            upload.getCities();
            localStorage.setItem('provinceId', $(this).val());
        });
        $('#city_id').change(function(){
            localStorage.setItem('cityId', $(this).val());
        });
        $('#allow_contact').click(function(){
            localStorage.setItem('allowContact',$(this).is(':checked'));
        });
        $('#authorised').click(function(){
            localStorage.setItem('authorised',$(this).is(':checked'));
        });

        localStorage.setItem('countryId', $('#country_id').val());
        localStorage.setItem('provinceId', $('#province_id').val());
        localStorage.setItem('cityId', $('#city_id').val());
        localStorage.setItem('authorised',false);
        localStorage.setItem('allowContact',false);

        $('.selectVideos').click(function(e){
            if(!$('#authorised').is(':checked')) {
                error = true;
                $('.noauth').show();
                e.preventDefault();
            }
        });

        $('#uploadTabs:not(#fbtab):not(#youtab):not(#vimtab) a').click(function(){
            $('#footerForm').show();
            $('#submitForm').show();
        });

        $('#fbtab, #youtab, #vimtab').click(function(){
            $('#footerForm').hide();
            $('#submitForm').hide();
        });
    },
    clearData: function(){
        $('#submitForm').children('input').remove();
        $('#headForm select, #headForm input').each(function(){
            upload.setVal($(this));
        });
    },
    setVal: function(param){
        if(param && param.jquery) {
            $this = param;
        } else {
            $this = $(param.target);
        }
        if($('#submitForm [name='+ $this.attr('name') +']').length == 0) {
            $('#submitForm').append('<input type="hidden" name="'+ $this.attr('name') +'">')
        }
        $target = $('#submitForm [name='+ $this.attr('name') +']');
        $target.val($this.val());
    },
    removeCat: function(e){
        $(e.target).parent().remove();
    },
    submit: function(e){
        var i = 0;
        $target = $('#submitForm');
        $('.selCat').each(function(){
            $this = $(this);
            $target.append('<input type="hidden" name="selCat['+ i +']" value="'+ $this.attr('data-id') +'">');
            i++;
        });
        if(!upload.validateForm()) e.preventDefault();
    },
    validateForm: function(){
        $('.alert').hide();
        var error = false
        if($('#youtab').hasClass('active')) {
            if($('#youtube_url').val() == '' ) {
                error = true;
                $('.noyoutube').show();
            }
        } else if($('#vimtab').hasClass('active')) {
            if($('#vimeo_url').val() == '' ) {
                error = true;
                $('.novimeo').show();
            }
        } else if($('#dmtab').hasClass('active')) {
            if($('#dm_url').val() == '' ) {
                error = true;
                $('.nodm').show();
            }
        } else {
            if($('#videofile').val() == '' ) {
                error = true;
                $('.novideo').show();
            }
            if($('#title').val() == '') {
                error = true;
                $('.notitle').show();
            }
            if($('#logline').val() == '') {
                error = true;
                $('.nologline').show();
            }
            if($('#description').val() == '') {
                error = true;
                $('.nodescription').show();
            }
            if($('#preview_url').attr('src') == 'http://images.townspot.tv/resizer.php?id=none') {
                error = true;
                $('.nopreview').show();
            }
            if($('.selCat').length == 0) {
                error = true;
                $('.nocategories').show();
            }
        }

        if($('#state_id').val() == '') {
            error = true;
            $('.nostate').show();
        }
        if($('#city_id').val() == '') {
            error = true;
            $('.nocity').show();
        }
        if(!$('#authorised').is(':checked')) {
            error = true;
            $('.noauth').show();
        }

        return !error;
    },
    addCategory: function(e){
        $target = $(e.target);
        if($target.is('span')) $target = $target.parent();
        $newParent = $target.parents('div').next().find('ul.tree');
        $parent = $target.parent();

        var isParent = $target.is('.parent');
        var isChild = $target.is('.child');
        var id = $target.attr('data-id');

        if(isParent && !isChild) {
            $('.category.active').removeClass('active')
            $('.category.child').remove();
            var children = upload.categories[id].children;
        } else if(isParent && isChild) {
            $newParent.children().remove();
            $target.siblings().removeClass('active');
            var parentId = $('.category.active').attr('data-id');
            var children = upload.categories[parentId].children[id].children;
        } else{
            $target.siblings().removeClass('active');
        }

        $target.addClass('active');

        if(children) {
            html = '';
            for(i in children) {
                var category = children[i];
                var catClass = 'category child list-group-item';
                if(category.children) catClass += ' parent';

                html += '<li class="'+catClass+'" data-id="'+i+'"><span>'+category.name+'</span></li>';
            }
            $newParent.append(html);
        }
        var $selectedContainer = $('.selectedCategories');

        var $selected = $('.category.active');

        $selected.each(function(){
            var $current = $(this);
            var id = $current.attr('data-id');
            $newContainer = $('.selCat[data-id=' + id + ']');
            if ($newContainer.length) {
                $selectedContainer = $newContainer;
            } else {
                var html = '<span class="selCat" data-id="' + id + '">' + $current.text() + ' <i class="fa fa-times"></i> </span>';
                $selectedContainer.append(html);
            }
        })
    },
    populateCategories: function(){
        $.get(
            '/api/category/getTieredCategories',
            function(data){
                var html = '';
                $parent = $('.categories');
                $parent.children().remove();
                categories = data.data;
                upload.categories = categories;
                for(i in categories) {
                    var cat = categories[i];
                    html += '<li class="parent category list-group-item" data-id="'+i+'">';
                    html += '<span>'+cat.name+'</span>';
                    html += '</li>';
                }
                $parent.append(html);
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