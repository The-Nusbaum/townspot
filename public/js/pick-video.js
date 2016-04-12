$(document).ready(function(){
	$('#pick-videos').on('click','.wrapper:not(.selected)',function(){
		$(this).addClass('selected');
	});
	
	$('#pick-videos').on('click','.wrapper.selected',function(){
		$(this).removeClass('selected');
	});

	$('#video-fbVideos #submit-videos,#video-ytVideos #submit-videos').click(function(e){
		e.preventDefault();
		var data = [];
		$('.wrapper.selected').each(function(){
			data.push($(this).attr('data-id'));
		});

		data = data.reduce(function(o, v, i) {
			o[i] = v;
			return o;
		}, {});

		var i = 0
		$('.wrapper.selected').each(function(){
			var $this = $(this);
			html = "<input type='hidden' name='data[" + i + "]' value='" + $this.attr('data-id') + "'>";
			$('[id*=VideosForm]').append(html);
			i += 1;
		});

		$('[id*=VideosForm]').submit();
	});
});