(function($){
	$(document).on("change", '#search-city', function(event) 
	{ 	
		$('#admin-search').trigger("modified");			
	});	
})(jQuery);
