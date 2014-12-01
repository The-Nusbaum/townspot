(function($){
    $.fn.Admin = function( options ) 
    {
        var methods = 
        {
			lookupStates : function()              
            {	
				//Get based on name			
			},
            lookupCities : function()              
            {	
				//Get based on state and name
				var currentvalue = $('#search-city').val();
				$('#search-city').html('');	
				$('#search-city').prop( "disabled", true );
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
		};
		$(document).on("change", '#search-province', function(event)			{ 	methods.lookupCities(this);  			});	

		//Triggers
		
	}
})(jQuery);

$().Admin({});
//Lookup State Change
//City Change
//Typeahead change
