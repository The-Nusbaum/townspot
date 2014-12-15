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
        upload.pluploadImage();
        upload.pluploadVideo();
        upload.populateCategories();
        $('body').on('click','.category, .category > span',function(e){
            upload.addCategory(e);
        });
        $('body').on('click','.selectedCategories .fa-times',function(e) {
            upload.removeCat(e);
        });
        $('hidden, input, select, textarea').change(function(e){
            upload.setVal(e);
        });
        $('#youtube_url').change(function(){
            $('#submitForm [name=url]').val($(this.val()));
        });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            upload.clearData();
        })
        $('#submitForm').submit(function(e){
            upload.submit(e);
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
                    $('#previewImage').attr('src',"/files/"+files.target_name);
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
    },
    pluploadVideo: function() {
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
                    $('#youtube_url').val('');
                },

                FilesAdded: function(up, files) {
                    $.each(files, function(i, file) {
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
        return true;
        $('.alert').hide();
        var error = false
        if($('#youtab').hasClass('active')) {
            if($('#youtube_url').val() == '' ) {
                error = true;
                $('.noyoutube').show();
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
        $newParent = $target.parents('div').next().find('ul');
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
                var html = '<span class="selCat" data-id="' + id + '">' + $current.text() + '<i class="fa fa-times"></i> </span>';
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
    }
}