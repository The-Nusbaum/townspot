$('document').ready(function(){
	//handle the damned country/state/city logic

    jQuery("abbr.timeago").timeago();
    $('[data-toggle="popover"]').popover({
        html: true
    });

    $('body').on('click','[data-track=click]',function(e){
    	var type =  $(this).data('type');
    	var value = $(this).data('value');

    	var url = "/api/tracking/record-click/" + 
    	type + "/" +
    	tracked_user;

    	if(value) url += "/" + value;

    	$.get(
    		url,
    		function(response){
    			console.log(response);
    		}
    	);
    })

    $('body').on('submit','[data-track=submit]',function(e){
    	var type =  $(this).data('type');
    	var value = $($(this).data('value')).val();

    	var url = "/api/tracking/record-click/" + 
    	type + "/" +
    	tracked_user + "/" +
    	value;

    	$.get(
    		url,
    		function(response){
    			console.log(response);
    		}
    	);
    });    
});
