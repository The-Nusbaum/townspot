(function($){
	$(document).on("change", '#search-verified', function(event) 
	{ 	
		$('#admin-search').trigger("modified");			
	});	
})(jQuery);
