(function($){
	$(document).on("change", '#search-status', function(event) 
	{ 	
		$('#admin-search').trigger("modified");			
	});	
})(jQuery);
