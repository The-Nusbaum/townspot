$('.img-responsive').load(function(){
	if ($(this).width() > 0) {
		console.log('test');
		console.log($(this).width());	
	}
})
