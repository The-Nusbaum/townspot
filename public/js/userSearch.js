(function($){
    $.fn.userSearch = function( options ) 
    {
        var defaults = {
			type: '',
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
            _getData : function()   
            {	
				$.ajax({
					url: "/admin/lookupusers",
					type: "POST",
					data: { 
						username: $('#search-username').val(),
						province: $('#search-province').val(),
						city: $('#search-city').val(),
						after: $('#search-created-after').val(),
						before: $('#search-created-before').val(),
						status: $('#search-status').val(),
						page: options.page,
						sort_field: options.sort_field,
						sort_order: options.sort_order,
						type: options.type
					}
				}).done(function ( data ) {
					$.each(data, function() {
						var html = '<tr id="row-' + this.id + '">';
						html = html + '<td class="check-field"></td>';					
						html = html + '<td class="id-field">' + this.id + '</td>';					
						html = html + '<td class="username-field">' + this.username + '</td>';					
						html = html + '<td class="name-field">' + this.name + '</td>';					
						html = html + '<td class="email-field">' + this.email + '</td>';					
						html = html + '<td class="location-field">' + this.location + '</td>';					
						html = html + '<td class="joined-field">' + this.joined + '</td>';			
						html = html + '<td class="status-field">' + this.status + '</td>';					
						html = html + '<td class="actions-field">';
						html = html + '<a href="/admin/user/show/' + this.id + '"><i class="fa fa-search"></i></a>';
						html = html + '<a href="/admin/user/edit/' + this.id + '"><i class="fa fa-pencil-square-o"></i></a>';
						html = html + '<i data-type="User" data-func="delete" data-ref="' + this.id + '" class="icon-remove fa fa-times"></i>';
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
		$(document).on("click", '#admin-sort-username', function(event)		{ 	methods.setSort('username',$(this).hasClass('icon-admin-sort-up'));  	});	
		$(document).on("click", '#admin-sort-name', function(event)			{ 	methods.setSort('name',$(this).hasClass('icon-admin-sort-up'));  	});	
		$(document).on("click", '#admin-sort-email', function(event)		{ 	methods.setSort('email',$(this).hasClass('icon-admin-sort-up'));  	});	
		$(document).on("click", '#admin-sort-location', function(event)		{ 	methods.setSort('location',$(this).hasClass('icon-admin-sort-up'));  	});	
		$(document).on("click", '#admin-sort-joined', function(event)		{ 	methods.setSort('joined',$(this).hasClass('icon-admin-sort-up'));  	});	
		$(document).on("click", '#admin-sort-status', function(event)		{ 	methods.setSort('status',$(this).hasClass('icon-admin-sort-up'));  	});	
		methods.getResults();
	};	
})(jQuery);

