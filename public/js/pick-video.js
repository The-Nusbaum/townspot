$(document).ready(function(){
	$('#pick-videos').on('click','.wrapper:not(.selected)',function(){
		$(this).addClass('selected');
	});
	
	$('#pick-videos').on('click','.wrapper.selected',function(){
		$(this).removeClass('selected');
	});

	$('#video-fbVideos #submit-videos').click(function(){
		var data = [];
		$('.wrapper.selected').each(function(){
			$this = $(this);
			data.push($this.attr('data-id'));
		});

		data = data.reduce(function(o, v, i) {
  		o[i] = v;
  		return o;
		}, {});


		$('#fbVideosForm').submit(function(e){
			var i = 0
			$('.wrapper.selected').each(function(){
				var $this = $(this);
				html = "<input type='hidden' name='data[" + i + "]' value='" + $this.attr('data-id') + "'>";
				$('#fbVideosForm').append(html);
				i += 1;
			});
		});
	});
});