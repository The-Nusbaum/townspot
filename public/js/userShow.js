(function($){
    $.fn.userShow = function( options ) 
    {
        var defaults = {
			id: '',
		};
	
        var methods = 
        {
            deleteUser : function(element)              
            {
				var verify = confirm("Warning this action cannot be undone, are you sure you want to delete this record");
				if (!verify) {	return;	}
				$.ajax({
					url: "/admin/delete",
					type: "POST",
					data: { 
						type: 'User',
						id: options.id,
					}
				}).done(function ( data ) {
					window.location.href = 'http://' + window.location.hostname + '/admin/users';
				});
			},
            contactUser : function(element)              
            {
				$('#send-message').modal('toggle');				
			},
            sendMessage : function(element)              
            {
				$.ajax({
					url: "/admin/sendmessage",
					type: "POST",
					data: { 
						user: options.id,
						subject: $('#input-email-subject').val(),
						password: $('#input-email-password-reset').val(),
						body: $('#input-email-body').val(),
					}
				}).done(function ( data ) {
					$('#send-message').modal('toggle');				
				});
			},
            createEvent : function(element)              
            {
			},
            saveEvent : function(element)              
            {
				$.ajax({
					url: "/admin/saveevent",
					type: "POST",
					data: { 
						id: $('#input-event-id').val(),
						title: $('#input-event-title').val(),
						url: $('#input-event-url').val(),
						start_date: $('#input-event-start-date').val(),
						start_time: $('#input-event-start-time').val(),
						description: $('#input-event-description').val(),
					}
				}).done(function ( data ) {
				});
				$('#event-edit').modal('toggle');				
			},
            viewRequest : function(element)              
            {
				if ($(element).data('type') == 'Media') {
					var targeturl = '/admin/video/show/' + $(element).data('ref');
					targeturl = targeturl + '?referrer=/admin/user/show/' + options.id;
					window.location.href = 'http://' + window.location.hostname + targeturl;
				}
				if ($(element).data('type') == 'Series') {
					var targeturl = '/admin/series/show/' + $(element).data('ref');
					targeturl = targeturl + '?referrer=/admin/user/show/' + options.id;
					window.location.href = 'http://' + window.location.hostname + targeturl;
				}
				if ($(element).data('type') == 'UserEvent') {
					$.ajax({
						url: "/admin/lookupevent",
						type: "POST",
						data: { 
							id: $(element).data('ref')
						}
					}).done(function ( data ) {
						$('#event-title').html(data.title);
						$('#event-url').html(data.url);
						$('#event-start').html(data.start);
						$('#event-description').html(data.description);
						$('#event-view').modal('toggle');				
					});
				}
			},
            editRequest : function(element)              
            {
				if ($(element).data('type') == 'Media') {
					var targeturl = '/admin/video/edit/' + $(element).data('ref');
					targeturl = targeturl + '?referrer=/admin/user/show/' + options.id;
					window.location.href = 'http://' + window.location.hostname + targeturl;
				}
				if ($(element).data('type') == 'Series') {
					var targeturl = '/admin/series/edit/' + $(element).data('ref');
					targeturl = targeturl + '?referrer=/admin/user/show/' + options.id;
					window.location.href = 'http://' + window.location.hostname + targeturl;
				}
				if ($(element).data('type') == 'UserEvent') {
					$.ajax({
						url: "/admin/lookupevent",
						type: "POST",
						data: { 
							id: $(element).data('ref')
						}
					}).done(function ( data ) {
						$('#input-event-id').val($(element).data('ref'));
						$('#input-event-title').val(data.title);
						$('#input-event-url').val(data.url);
						$('#input-event-start-date').val(data.start_date);
						$('#input-event-start-time').val(data.start_time);
						$('#input-event-description').val(data.description);
						$('#event-edit').modal('toggle');				
					});
				}
			},
            deleteRequest : function(element)              
            {
				var verify = confirm("Warning this action cannot be undone, are you sure you want to delete this record(s)");
				if (!verify) {	return;	}
				var type = $(element).data('type');
				var ref = $(element).data('ref');
				if ($(element).data('type') == 'Favorite') {
					$.ajax({
						url: "/admin/delete",
						type: "POST",
						data: { 
							type: $(element).data('type'),
							id: options.id,
							ref: ref,
						}
					}).done(function ( data ) {
						$('#' + type + '-' + ref).remove();
					});
				} else {
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
			}
		};
		
        var options = $.extend(defaults, options);
		
		$(document).on("click", '#contact-user', function(event)			{ 	methods.contactUser(this);  		});	
		$(document).on("click", '#delete-user', function(event)				{ 	methods.deleteUser(this); 			});	
		$(document).on("click", '.view-request', function(event)			{ 	methods.viewRequest(this); 			});	
		$(document).on("click", '.edit-request', function(event)			{ 	methods.editRequest(this); 			});	
		$(document).on("click", '.delete-request', function(event)			{ 	methods.deleteRequest(this); 		});	
		$(document).on("click", '#input-event-save', function(event)		{ 	methods.saveEvent(this); 			});	
	};	
})(jQuery);

