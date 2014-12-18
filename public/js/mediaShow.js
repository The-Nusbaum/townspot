(function($){
    $.fn.mediaShow = function( options ) 
    {
        var defaults = {
			id: '',
		};
	
        var methods = 
        {
            deleteMedia : function(element)              
            {
				var verify = confirm("Warning this action cannot be undone, are you sure you want to delete this record");
				if (!verify) {	return;	}
				$.ajax({
					url: "/admin/delete",
					type: "POST",
					data: { 
						type: 'Media',
						id: options.id,
					}
				}).done(function ( data ) {
					window.location.href = 'http://' + window.location.hostname + '/admin/users';
				});
			},
            approveMedia : function(element)              
            {
				$.ajax({
					url: "/admin/mediaapprove/" + options.id,
					type: "POST",
					data: { } 
				}).done(function ( data ) {
					window.location.reload();
				});
			},
            unapproveMedia : function(element)              
            {
				$.ajax({
					url: "/admin/mediaunapprove/" + options.id,
					type: "POST",
					data: { } 
				}).done(function ( data ) {
					window.location.reload();
				});
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
		
		$(document).on("click", '#delete-media', function(event)			{ 	methods.deleteMedia(this); 			});	
		$(document).on("click", '.delete-request', function(event)			{ 	methods.deleteRequest(this); 			});	
		$(document).on("click", '#approve', function(event)					{ 	methods.approveMedia(this); 			});	
		$(document).on("click", '#unapprove', function(event)				{ 	methods.unapproveMedia(this); 			});	
	};	
})(jQuery);

