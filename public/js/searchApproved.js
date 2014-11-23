(function($){
	$(document).on("change", '#search-approved', function(event) 
	{ 	
		$('#admin-search').trigger("modified");			
	});	
})(jQuery);
