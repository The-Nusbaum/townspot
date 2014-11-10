(function($){
    $.fn.Search = function( options ) 
    {
		var searchResultsCollection 	= new SearchResultCollection();		
		var searchResultsView 			= new SearchResultView({el: 		'#search-results'});		
        var defaults = {
			searchId: '',
			page: 1,
			searchTerm: '',
			sortTerm: 'created:asc',
			data: []
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
						page: options.page,
						searchTerm: options.searchTerm,
						sortTerm: options.sortTerm,
					}
				}).done(function ( data ) {
					options.data = data.data;
					methods.renderPage();
				});
			},
            updateOrder : function(element)              
            {
				options.sort = $(element).val();
				window.location.href = "/videos/search?q=" + options.searchTerm + "&sort=" + options.sort;
			},
            renderPage : function()              
            {
				$.each(options.data, function() {
					searchResultsCollection.add(new Video(this));
				});
				searchResultsView.render(searchResultsCollection);
				setInfoButtons();
				loadAds();
				setTimeout( function() { 
					$('#Loading').modal('hide');
					$('.loading').removeClass('loading'); 
					$('.loading-spinner').css('display','none');
				}, 100 );
				options.page = options.page + 1;
					$(document).endlessScroll({
					inflowPixels: 50,
					fireDelay: 10,
					callback: function(i) {
						methods.getResults();
					},
					ceaseFire: function() {
						if (options.data.length < 12) {
							return true;
						}
						if (options.data.length == 0) {
							return true;
						}
						return false;
					}
				});
			}
		}
        var options = $.extend(defaults, options);
		$('#sort').change(function() { methods.updateOrder(this) });
		methods.renderPage();
    };
})(jQuery);
