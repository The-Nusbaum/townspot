(function($){
	$(document).on("change", '#search-username', function(event) 
	{ 	
		$('#admin-search').trigger("modified");			
	});	
})(jQuery);
