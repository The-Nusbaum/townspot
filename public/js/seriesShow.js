(function($){
    $.fn.seriesShow = function( options ) 
    {
        var defaults = {
			id: '',
		};
	
        var methods = 
        {
            deleteSeries : function(element)              
            {
				var verify = confirm("Warning this action cannot be undone, are you sure you want to delete this record(s)");
				if (!verify) {	return;	}
				$.ajax({
					url: "/admin/delete",
					type: "POST",
					data: { 
						type: 'Series',
						id: options.id,
					}
				}).done(function ( data ) {
					window.location.href = 'http://' + window.location.hostname + '/admin/series';
				});
			},
            viewRequest : function(element)              
            {
				if ($(element).data('type') == 'Media') {
					var targeturl = '/admin/video/show/' + $(element).data('ref');
					targeturl = targeturl + '?referrer=/admin/series/show/' + options.id;
					window.location.href = 'http://' + window.location.hostname + targeturl;
				}
			},
            editRequest : function(element)              
            {
				if ($(element).data('type') == 'Media') {
					var targeturl = '/admin/video/edit/' + $(element).data('ref');
					targeturl = targeturl + '?referrer=/admin/series/show/' + options.id;
					window.location.href = 'http://' + window.location.hostname + targeturl;
				}
			},
            deleteRequest : function(element)              
            {
				var verify = confirm("Warning this action cannot be undone, are you sure you want to delete this record(s)");
				if (!verify) {	return;	}
				var type = $(element).data('type');
				var ref = $(element).data('ref');
				$.ajax({
					url: "/admin/delete",
					type: "POST",
					data: { 
						type: $(element).data('type'),
						id: ref,
					}
				}).done(function ( data ) {
					$('#' + type + '-' + ref).remove();
				});
			}
		};
		
        var options = $.extend(defaults, options);
		
		$(document).on("click", '#delete-series', function(event)			{ 	methods.deleteSeries(this); 			});	
		$(document).on("click", '.view-request', function(event)			{ 	methods.viewRequest(this); 			});	
		$(document).on("click", '.edit-request', function(event)			{ 	methods.editRequest(this); 			});	
		$(document).on("click", '.delete-request', function(event)			{ 	methods.deleteRequest(this); 			});	
	};	
})(jQuery);

