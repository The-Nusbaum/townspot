(function($){
    $.fn.seriesSearch = function( options ) 
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
            _getData : function()   
            {	
				$.ajax({
					url: "/admin/lookupseries",
					type: "POST",
					data: { 
						title: $('#search-title').val(),
						username: $('#search-username').val(),
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
						html = html + '<td class="episodecount-field">' + this.episodecount + '</td>';					
						html = html + '<td class="username-field">' + this.username + '</td>';					
						html = html + '<td class="actions-field">';
						html = html + '<a href="/admin/series/show/' + this.id + '"><i class="fa fa-search"></i></a>';
						html = html + '<a href="/admin/series/edit/' + this.id + '"><i class="fa fa-pencil-square-o"></i></a>';
						html = html + '<i data-type="Series" data-func="delete" data-ref="' + this.id + '" class="icon-remove fa fa-times"></i>';
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
		$(document).on("click", '#admin-sort-episodecount', function(event)	{ 	methods.setSort('episodecount',$(this).hasClass('icon-admin-sort-up'));  	});	
		$(document).on("click", '#admin-sort-username', function(event)		{ 	methods.setSort('username',$(this).hasClass('icon-admin-sort-up'));  	});	
		methods.getResults();
	};	
})(jQuery);

