(function($){
    $.fn.Discover = function( options ) 
    {
		var searchResultsCollection 	= new SearchResultCollection();		
		var searchResultsView 			= new SearchResultView({el: 		'#search-results'});		
        var defaults = {
			searchId: 	'',
			province: 	'',
			city: 		'',
			categoryId: 0,
			sortTerm: 	'created:asc',
			page: 		1,
		};
        var methods = 
        {
            buildUrl : function(newcategory)              
            {
				var url = '/discover';
				if (options.province != '') {
					url = url + '/' + methods.encodeStr(options.province);	
				}
				if (options.city != '') {
					url = url + '/' + methods.encodeStr(options.city);	
				}
				$('.selected-category').each( function() {
					if (typeof  $(this).data('name') != 'undefined') {
						url = url + '/' + methods.encodeStr($(this).data('name'));	
					}
				});
				if (typeof newcategory != 'undefined') {
					url = url + '/' + methods.encodeStr(newcategory);	
				}
				if (options.sortTerm != 'created:asc') {
					url = url + '?sort=' + options.sortTerm;
				}
				window.location.href = url.toLowerCase();
			},
			encodeStr : function(string)              
            {
				string = encodeURI(string);
				string.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
					return '&#'+i.charCodeAt(0)+';';
				});
				string.replace('&', '&amp;');
				return string;
			},
            updateOrder : function(element)              
            {
				options.sortTerm = $(element).val();
				methods.buildUrl();
			},
            removeState : function()              
            {
				options.province = '';
				options.city = '';
				methods.buildUrl()
			},
            removeCity : function()              
            {
				options.city = '';
				methods.buildUrl()
			},
            updateState : function(element)              
            {
				options.province = $(element).data('id');
				options.city = '';
				methods.buildUrl()
			},
            updateCity : function(element)              
            {
				options.city = $(element).data('id');
				methods.buildUrl()
			},
            updateCategory : function(element)              
            {
			},
            removeCategory0 : function()              
            {
				for (i = 0; i < 6; i++) {
					$('.remove-category-' + i).parent().remove();
				}
				methods.buildUrl()
			},
            removeCategory1 : function()              
            {
				for (i = 1; i < 6; i++) {
					$('.remove-category-' + i).parent().remove();
				}
				methods.buildUrl()
			},
            removeCategory2 : function()              
            {
				for (i = 2; i < 6; i++) {
					$('.remove-category-' + i).parent().remove();
				}
				methods.buildUrl()
			},
            removeCategory3 : function()              
            {
				for (i = 3; i < 6; i++) {
					$('.remove-category-' + i).parent().remove();
				}
				methods.buildUrl()
			},
            removeCategory4 : function()              
            {
				for (i = 4; i < 6; i++) {
					$('.remove-category-' + i).parent().remove();
				}
				methods.buildUrl()
			},
            removeCategory5 : function()              
            {
				$('.remove-category-5').parent().remove();
				methods.buildUrl()
			},
            updateSubCategory : function(element)              
            {
				methods.buildUrl($(element).data('name'));
			},
            getResults : function(category)              
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
			}
		}
        var options = $.extend(defaults, options);
		$('#sort').change(function() { methods.updateOrder(this) });
		$('.state-selector').click(function() { methods.updateState(this) });
		$('.city-selector').click(function() { methods.updateCity(this) });
		$('.remove-state').click(function() { methods.removeState() });
		$('.remove-city').click(function() { methods.removeCity() });
		$('.update-subcategory').click(function() { methods.updateSubCategory(this) });
		$('.remove-category-0').click(function() { methods.removeCategory0() });
		$('.remove-category-1').click(function() { methods.removeCategory1() });
		$('.remove-category-2').click(function() { methods.removeCategory2() });
		$('.remove-category-3').click(function() { methods.removeCategory3() });
		$('.remove-category-4').click(function() { methods.removeCategory4() });
		$('.remove-category-5').click(function() { methods.removeCategory5() });
		methods.getResults();
    };
})(jQuery);
