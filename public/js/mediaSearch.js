(function($){
    $.fn.mediaSearch = function( options ) 
    {
        var defaults = {
			page: 1,
			recordcount: 0,
			sort_field: 'id',
			sort_order: 'ASC',
		};
        var methods = 
        {
            setSort : function(field,sortUp)              
            {
				options.sort_field = field;
				$(".icon-admin-sort").removeClass('icon-admin-sort-up').removeClass('icon-admin-sort-down').addClass('icon-admin-sort-undefined');
				if (sortUp == true) {
					options.sort_field = field;
					options.sort_order = 'DESC';
					$("#admin-sort-" + field).removeClass('icon-admin-sort-undefined').addClass('icon-admin-sort-down');
				} else {
					options.sort_field = field;
					options.sort_order = 'ASC';
					$("#admin-sort-" + field).removeClass('icon-admin-sort-undefined').addClass('icon-admin-sort-up');
				}
				methods.getResults(); 
			},
            getResults : function()              
            {
				$('#record-list-results tbody').html('');	
				methods._getData();
			},
            nextPage : function()              
            {
				options.page = options.page + 1;
				methods.getResults();
			},
            previousPage : function()              
            {
				options.page = options.page - 1;
				methods.getResults();
			},
            unapprove : function(element)              
            {
				var id = $(element).parent().attr('id');
				id = id.replace('row-','');
				$.ajax({
					url: "/admin/mediaunapprove/" + id,
					type: "POST",
					data: { } 
				}).done(function ( data ) {
				});
				$(element).removeClass('unapprove').addClass('approve');
				$(element).css('color','#ff0000');
				$(element).html('<i class="fa fa-square-o"></i>');
			},
            approve : function(element)              
            {
				var id = $(element).parent().attr('id');
				id = id.replace('row-','');
				$.ajax({
					url: "/admin/mediaapprove/" + id,
					type: "POST",
					data: { } 
				}).done(function ( data ) {
				});
				$(element).removeClass('approve').addClass('unapprove');
				$(element).css('color','#00ff00');
				$(element).html('<i class="fa fa-check-square"></i>');
			},
            _getData : function()   
            {	
				$.ajax({
					url: "/admin/lookupmedia",
					type: "POST",
					data: { 
						title: $('#search-title').val(),
						username: $('#search-username').val(),
						province: $('#search-province').val(),
						city: $('#search-city').val(),
						after: $('#search-created-after').val(),
						before: $('#search-created-before').val(),
						status: $('#search-status').val(),
						page: options.page,
						sort_field: options.sort_field,
						sort_order: options.sort_order
					}
				}).done(function ( data ) {
					$.each(data, function() {
						var html = '<tr id="row-' + this.id + '">';
						html = html + '<td class="check-field"></td>';					
						html = html + '<td class="id-field">' + this.id + '</td>';					
						html = html + '<td class="title-field">' + this.title + '</td>';					
						html = html + '<td class="series-field">' + this.series + '</td>';					
						html = html + '<td class="username-field">' + this.username + '</td>';					
						html = html + '<td class="location-field">' + this.location + '</td>';					
						html = html + '<td class="views-field">' + this.views + '</td>';					
						html = html + '<td class="added-field">' + this.added + '</td>';	
						if (this.status == 1) {
							html = html + '<td class="status-field unapprove" style="text-align: center; color: #00ff00;"><i class="fa fa-check-square"></i></td>';			
						} else {
							html = html + '<td class="status-field approve" style="text-align: center; color: #ff0000;"><i class="fa fa-square-o"></i></td>';			
						}
						html = html + '<td class="actions-field">';
						html = html + '<a href="/admin/video/show/' + this.id + '"><i class="fa fa-search"></i></a>';
						html = html + '<a href="/admin/video/edit/' + this.id + '"><i class="fa fa-pencil-square-o"></i></a>';
						html = html + '<i data-type="Media" data-func="delete" data-ref="' + this.id + '" class="icon-remove fa fa-times"></i>';
						html = html + '</td>';					
						html = html + '</tr>';
						$('#record-list-results tbody').append(html);
					});
				});
			}
		};
		
        var options = $.extend(defaults, options);
		$(document).on("modified", '#admin-search', function(event)			{ 	methods.getResults();  											});	
		$(document).on("click", '#admin-sort-id', function(event)			{ 	methods.setSort('id',$(this).hasClass('icon-admin-sort-up'));  	});	
		$(document).on("click", '#admin-sort-title', function(event)		{ 	methods.setSort('title',$(this).hasClass('icon-admin-sort-up'));  	});	
		$(document).on("click", '#admin-sort-series', function(event)		{ 	methods.setSort('series',$(this).hasClass('icon-admin-sort-up'));  	});	
		$(document).on("click", '#admin-sort-username', function(event)		{ 	methods.setSort('username',$(this).hasClass('icon-admin-sort-up'));  	});	
		$(document).on("click", '#admin-sort-location', function(event)		{ 	methods.setSort('location',$(this).hasClass('icon-admin-sort-up'));  	});	
		$(document).on("click", '#admin-sort-views', function(event)		{ 	methods.setSort('views',$(this).hasClass('icon-admin-sort-up'));  	});	
		$(document).on("click", '#admin-sort-added', function(event)		{ 	methods.setSort('added',$(this).hasClass('icon-admin-sort-up'));  	});	
		$(document).on("click", '#admin-sort-status', function(event)		{ 	methods.setSort('status',$(this).hasClass('icon-admin-sort-up'));  	});	
		$(document).on("click", '.unapprove', function(event)				{ 	methods.unapprove(this);  	});	
		$(document).on("click", '.approve', function(event)					{ 	methods.approve(this);  	});	
		methods.getResults();
	};	
})(jQuery);

