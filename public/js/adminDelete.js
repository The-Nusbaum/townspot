(function($){
	$(document).on("click", '.icon-remove', function(event) 
	{ 	
		var type = $(this).attr('data-type');
		var id = $(this).attr('data-ref');
		var verify = confirm("Warning this action cannot be undone, are you sure you want to delete this record");
		if (!verify) {	return;	}
		$.ajax({
			url: "/admin/delete",
			type: "POST",
			data: { 
				type: type,
				id: id,
			}
		}).done(function ( data ) {
			$('#row-' + id).remove();
		});
console.log(this);	
	});	
})(jQuery);
