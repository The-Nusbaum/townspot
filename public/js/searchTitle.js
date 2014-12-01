(function($){
	$(document).on("change", '#search-title', function(event) 
	{ 	
		$('#admin-search').trigger("modified");			
	});	
})(jQuery);
