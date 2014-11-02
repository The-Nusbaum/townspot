(function($){
    $.fn.Search = function( options ) 
    {
		var searchResultsCollection 	= new SearchResultCollection();		
		var searchResultsView 			= new SearchResultView({el: 		'#search-results'});		
        var defaults = {
			searchId: '',
			searchTerm: '',
			sortTerm: 'date:asc',
			page: 1
		};
        var methods = 
        {
            clearResults : function()              
            {
				$('#search-results').html('');
			},
            getResults : function()              
            {
				if (options.page == 1) {
					$('#Loading').modal('show');
				} else {
					$('.loading-spinner').css('display','block');
				}
				$.ajax({
					url: "/videos/searchresults",
					type: "POST",
					data: { 
						searchId: options.searchId,
						searchTerm: options.searchTerm,
						sortTerm: options.sortTerm,
						page: options.page,
					}
				}).done(function ( data ) {
					if (data.reload) {
						//Redirect
					} else {
						$.each(data.data, function() {
							searchResultsCollection.add(new Video(this));
						});
						searchResultsView.render(searchResultsCollection);
						options.page = options.page + 1;
						$(document).endlessScroll({
							inflowPixels: 50,
							fireDelay: 10,
							callback: function(i) {
								methods.getResults(false);
							},
							ceaseFire: function() {
								if (data.data.length < 12) {
									return true;
								}
								if (data.data.length == 0) {
									return true;
								}
								return false;
							}
						});
						setTimeout( function() { 
							$('#Loading').modal('hide');
							$('.loading').removeClass('loading'); 
							$('.loading-spinner').css('display','none');
						}, 500 );
						setInfoButtons();
						loadAds();
					}
				});
			},
            updateOrder : function(element)              
            {
			}
		}
        var options = $.extend(defaults, options);
		$('#sort').change(function() { methods.updateOrder(this) });
		methods.getResults();
    };
})(jQuery);
