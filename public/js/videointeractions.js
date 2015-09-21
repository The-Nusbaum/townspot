(function($){
    $.fn.Interactions = function( options ) 
    {
        // Default Options
        var defaults = {
            videoid: null,
            userid: null,
            creator: null,
            page: 1,
            pagelimit: 5,
            maxpage: 1,
            favorite: false,
            following: false
		};
        var methods = 
        {
			initialize : function()              
            {
				if (options.userid > 0) {
					methods.favorite();
					methods.fan();
					$('#favorite-link').unbind('click').bind('click', function() { methods.favorite(true) });
					$('#rate-up').unbind('click').bind('click', function() { methods.rate('up') });
					$('#rate-down').unbind('click').bind('click', function() { methods.rate('down') });
					$('#contact-interaction').unbind('click').bind('click', function() { $('#ContactCreatorModal').modal('toggle'); });
					$('#follow-interaction').unbind('click').bind('click', function() { $('#ArtistContactModal').modal('toggle'); });
					$('#report-interaction').unbind('click').bind('click', function() { $('#FlagContentLabel').modal('toggle'); });
					$('#comment-submit').unbind('click').bind('click', function() { methods.comment_submit() });
					$('.comment .deleteme').unbind('click').bind('click',function(){ methods.comment_delete() });
				} else {
					$('#comment-field').css('display','none');
					$('#comment-submit').html('Login to Comment');
					$('#comment-submit').addClass('login-needed');
					$('#comment-submit').unbind('click').bind('click', function() { methods.sendToLogin() });
					$('#favorite-link').unbind('click').bind('click', function() { methods.sendToLogin() });
					$('#rate-up').unbind('click').bind('click', function() { methods.sendToLogin() });
					$('#rate-down').unbind('click').bind('click', function() { methods.sendToLogin() });
					$('#contact-interaction').unbind('click').bind('click', function() { methods.sendToLogin() });
					$('#follow-interaction').unbind('click').bind('click', function() { methods.sendToLogin() });
					$('#report-interaction').unbind('click').bind('click', function() { methods.sendToLogin() });
					$('#report-interaction').unbind('click').bind('click', function() { methods.sendToLogin() });
				}
				$('#artist-contact-yes').unbind('click').bind('click', function() { methods.fan('true') });
				$('#artist-contact-no').unbind('click').bind('click', function() { methods.fan('false') });
				$('#contact-submit').unbind('click').bind('click', function() { methods.contact() });
				$('#flagVideo .reason').unbind('change').bind('change',function(){ methods.flagDetails($(this).val())});
				$('#flagVideo .details > input').unbind('keyup').bind('keyup',function(){ methods.flagCheckDetails()});
				$('#flagVideo .btn-primary').unbind('click').bind('click',function(){ methods.reportVideo()});
		
				methods.rate();
				methods.comments();
                methods.adjust_vimeo();


            },
            adjust_vimeo: function() {
                var vimeoPlayer = $('iframe.vimeoPlayer, iframe.dmPlayer');
                var width = vimeoPlayer.width();
                if(typeof hRatio == 'undefined') {
						    	hRatio = 1.77     
						    }
						    var height = width / hRatio;
                vimeoPlayer.height(height);
            },
            rate : function(rating)              
            {
				$.ajax({
					url: "/videos/ratings/" + options.videoid,
					type: "POST",
					data: { "rating": rating }
				}).done(function ( data ) {
					if (typeof rating !== "undefined") {
						$('#up-ratings').html(data.rate_up);
						$('#down-ratings').html(data.rate_down);
					}
					if (data.my_rating != '') {
						$('#rate-up').removeClass('disabled');
						$('#rate-up').removeAttr("disabled", "disabled");
						$('#rate-down').removeClass('disabled');
						$('#rate-down').removeAttr("disabled", "disabled");
						$('#rate-' + data.my_rating).addClass('disabled');
						$('#rate-' + data.my_rating).attr("disabled", "disabled");
						$('#rate-up').removeClass('fa-thumbs-up').removeClass('fa-thumbs-o-up').addClass('fa-thumbs-o-up');
						$('#rate-down').removeClass('fa-thumbs-down').removeClass('fa-thumbs-o-down').addClass('fa-thumbs-o-down');	
						if (data.my_rating == 'up') {
							$('#rate-up').removeClass('fa-thumbs-o-up').addClass('fa-thumbs-up');
							$('#rate-down').removeClass('fa-thumbs-down').addClass('fa-thumbs-o-down');	
						} else if (data.my_rating == 'down') {
							$('#rate-down').removeClass('fa-thumbs-o-down').addClass('fa-thumbs-down');	
							$('#rate-up').removeClass('fa-thumbs-up').addClass('fa-thumbs-o-up');
						} 
					}
				});
            },
			favorite : function(toggle)
			{
				$.ajax({
					url: "/videos/favorite/" + options.videoid,
					type: "POST",
					data: { "toggle": toggle }
				}).done(function ( data ) {
					if (data.favorite == true) {
						$('#favorite-link').removeClass('fa-star-o').addClass('fa-star').addClass('disabled');
						$('#favorite-link').css('color','#FCAD1C');
					} else {
						$('#favorite-link').removeClass('fa-star').removeClass('disabled').addClass('fa-star-o');
						$('#favorite-link').css('color','#FFFFFF');
					}
				});
            },
			fan : function(follow)
			{
				$.ajax({
					url: "/videos/followartist/" + options.videoid,
					type: "POST",
					data: { 
						"follow": follow 
					}
				}).done(function ( data ) {
					if (data.following == true) {
						$('#follow-interaction').html('Un-fan User');
						$('#follow-interaction').addClass('townspot-btn');
						$('#follow-interaction').unbind('click').bind('click', function() { methods.fan('false') });
					} else {
						$('#follow-interaction').html('Become a Fan');
						$('#follow-interaction').removeClass('townspot-btn');
						$('#follow-interaction').unbind('click').bind('click', function() { $('#ArtistContactModal').modal('toggle'); });
					}
				});
				$('#ArtistContactModal').modal('hide');
			},
            comments : function()              
            {
				$.ajax({
					url: "/videos/comments/" + options.videoid,
					type: "POST",
					data: { "pagelimit": options.pagelimit,
							"page": options.page
					}
				}).done(function ( data ) {
					if (options.page == 1) {
						$('#comment-list').html('');
					}
					$(data).each(function() {
						_html = '<li id="comment-' + this.id + '" class="comment" data-id="'+this.id+'">';
						if(this.candelete) _html += "<i class='icon-remove-sign deleteme'></i>"
						_html = _html + '<img src="' + this.profileImage + '" class="img-responsive">';
						_html = _html + '<p class="comment-comment">' + this.comment + '</p>';
						_html = _html + '<a href="' + this.profileLink + '">' + this.username + '</a>';
						_html = _html + ' - ' + jQuery.timeago(this.created);
						_html = _html + '</li>';
						$('#comment-list').append(_html);
					});
					if (data.length == options.pagelimit) {
						options.page = options.page + 1;
						$('#comment-navigation-down').css('display','block');
						$('#comment-navigation-down .next').unbind('click').bind('click', function() { methods.comments() });
					} else {
						$('#comment-navigation-down').css('display','none');
					}
				});
            },
            comment_submit : function()              
            {
				var comment = $('#comment-field').val();
				$('#comment-field').val('');
				if (comment == '') {
					$('#comment-error').show();
					$('#comment-error h4').html('Please enter a comment');
					return;
				}
				$.ajax({
					url: "/videos/comments/" + options.videoid,
					type: "POST",
					data: { "comment": comment	}
				}).done(function ( data ) {
					$('#video-comment-list').html('');
					options.page = 1;
					methods.comments();
				});
			},
			comment_delete : function()
			{	
				target = $(this);
				$.ajax({
					url: "/videos/comments/" + options.videoid,
					type: "POST",
					data: { "delete": target.parent().attr('data-id')	}
				}).done(function ( data ) {
					target.parent().remove();
				});
			},
            comment_prev : function()              
            {
				options.page = options.page - 1;
				methods.comments();
			},
            comment_next : function()              
            {
				options.page = options.page + 1;
				methods.comments();
			},
            contact : function()              
			{
				$('#message-success-message h4').html('');
				$('#message-success-message p').html('');
				var subject = $('#contact-subject').val();
				var message = $('#contact-message').val();
				if (subject == '') {
					$('#contact-error-subject').show();
					$('#contact-error-subject h4').html('Please enter a subject for your message');
					return;
				}
				if (message == '') {
					$('#contact-error').show();
					$('#contact-error h4').html('Please enter a message');
					return;
				}
				$.ajax({
					url: "/videos/contactartist/" + options.videoid,
					type: "POST",
					data: { "subject": $('#contact-subject').val(),
							"message": $('#contact-message').val(),
					}
				}).done(function ( data ) {
					$('#message-success-message').show();
					$('#message-success-message h4').html('Your message has been sent!');
					setTimeout(function(){ $('#ContactCreatorModal').modal('hide'); },8000);
					return;
				});
			},
            sendToLogin : function()              
            {
				window.location = "/login?redirect=" + encodeURIComponent(document.URL);
            },
      flagDetails: function(value){
        if(value.length) $('#flagVideo .details').show();
		    else {
		      $('#flagVideo .details').hide();
		      $('#flagVideo .submitFlag').attr('disabled','disabled');
		    }
      },
      flagCheckDetails: function(){
      	if($('#flagVideo .detailsText').val().length && $('#flagVideo .email').val().length) {
		      $('#flagVideo .btn-primary').removeAttr('disabled');
		    } else {
		      $('#flagVideo .btn-primary').removeAttr('disabled','disabled');
		    }
      },
      reportVideo: function(){
      	var data = {
      		email: $('#flagVideo .email').val(),
      		reason: $('#flagVideo .reason').val(),
      		details: $('#flagVideo .detailsText').val(),
      	};
      	$.post(
      		'/videos/report/' + options.videoid,
      		data,
      		function(response){
      			console.log(response);
      		}
      	);
      }
		}
        var options = $.extend(defaults, options);
		methods.initialize();
    };
})(jQuery);
