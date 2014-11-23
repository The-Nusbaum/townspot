(function($){
	$(document).on("change", '#search-create-before', function(event) 
	{ 	
		$('#admin-search').trigger("modified");			
	});	
	$(document).on("change", '#search-create-after', function(event) 
	{ 	
		$('#admin-search').trigger("modified");			
	});		
})(jQuery);
