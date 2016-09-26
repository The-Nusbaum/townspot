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

    $('body').on('mouseenter','.video-preview img',function() {
        $(this).attr('data-src', $(this).find('img').attr('src'));
        $.get('http://images.townspot.tv/getThumbs.php',
            {
                id: $(this).parents('.video-preview').attr('data-id')
            },
            function (resp) {
                if (resp) {
                    var $target = $('.video-preview[data-id=' + resp.id + '] img');
                    cycle = true;
                    cycleThumbTimer = setTimeout(function () {
                        cycleThumb(0, resp.images, $target)
                    }, 1000);
                }
            }
        );
    });
    $('body').on('mouseleave','.video-preview img',function(){
        cycle = false;
        $(this).attr('src',$(this).attr('data-src'));
    });
});
var cycleThumbTimer = null;
var cycle = true;
var x;
function cycleThumb(i,images,$target) {
    if(cycle) {
        x = i
        $target.attr('src', images[x++]);
        if (x < 6) cycleThumbTimer = setTimeout(function () {
            cycleThumb(x, images, $target)
        }, 1000);
        else cycleThumbTimer = setTimeout(function () {
            cycleThumb(0, images, $target)
        }, 1000);
    } else {
        clearTimeout(cycleThumbTimer);
    }
}


function getThumbs() {

}