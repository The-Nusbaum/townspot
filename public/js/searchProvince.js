(function($){
	$(document).on("change", '#search-province', function(event) 
	{ 	
		//Get based on state
		var currentvalue = $('#search-city').val();
		$('#search-city').html('');	
		$('#search-city').prop( "disabled", true );
		if ($('#search-province').val() == '') {
			$('#search-city').append('<option value="">All</option>');					
		} else {
			$.ajax({
				url: "/admin/lookup",
				type: "POST",
				data: { 
					"lookup": 'cities', 
					"ref_id": $('#search-province').val()
				}
			}).done(function ( data ) {
				$('#search-city').append('<option value="">All</option>');					
				$.each(data.cities, function() {
					$('#search-city').append('<option value="' + this.id + '">' + this.name + '</option>');					
					$('#search-city').prop( "disabled", false );
				});
				$('#search-city').val(currentvalue);
			});
		}
		$('#admin-search').trigger("modified");			
	});	
})(jQuery);
