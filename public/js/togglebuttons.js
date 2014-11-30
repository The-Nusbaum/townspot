(function($){
	$(document).on("mouseover", '.fa-toggle-o', function(event)	{
		if ($(this).hasClass('disabled') == false) {
			if ($(this).hasClass('fa-bell-o')) 					{	$(this).removeClass('fa-bell-o').addClass('fa-bell');								}
			if ($(this).hasClass('fa-arrow-circle-o-down')) 	{	$(this).removeClass('fa-arrow-circle-o-down').addClass('fa-arrow-circle-down');		} 
			if ($(this).hasClass('fa-arrow-circle-o-left')) 	{	$(this).removeClass('fa-arrow-circle-o-left').addClass('fa-arrow-circle-left');		}
			if ($(this).hasClass('fa-arrow-circle-o-right')) 	{	$(this).removeClass('fa-arrow-circle-o-right').addClass('fa-arrow-circle-right');	}
			if ($(this).hasClass('fa-arrow-circle-o-up')) 		{	$(this).removeClass('fa-arrow-circle-o-up').addClass('fa-arrow-circle-up');			}
			if ($(this).hasClass('fa-bookmark-o')) 				{	$(this).removeClass('fa-bookmark-o').addClass('fa-bookmark');						}
			if ($(this).hasClass('fa-building-o')) 				{	$(this).removeClass('fa-building-o').addClass('fa-building');						}
			if ($(this).hasClass('fa-calendar-o')) 				{	$(this).removeClass('fa-calendar-o').addClass('fa-calendar');						}
			if ($(this).hasClass('fa-check-circle-o')) 			{	$(this).removeClass('fa-check-circle-o').addClass('fa-check-circle');				}
			if ($(this).hasClass('fa-check-square-o')) 			{	$(this).removeClass('fa-check-square-o').addClass('fa-check-square');				}
			if ($(this).hasClass('fa-circle-o')) 				{	$(this).removeClass('fa-circle-o').addClass('fa-circle');							}
			if ($(this).hasClass('fa-comment-o')) 				{	$(this).removeClass('fa-comment-o').addClass('fa-comment');							}
			if ($(this).hasClass('fa-comments-o')) 				{	$(this).removeClass('fa-comments-o').addClass('fa-comments');						}
			if ($(this).hasClass('fa-envelope-o')) 				{	$(this).removeClass('fa-envelope-o').addClass('fa-envelope');						}
			if ($(this).hasClass('fa-file-o')) 					{	$(this).removeClass('fa-file-o').addClass('fa-file');								}
			if ($(this).hasClass('fa-file-text-o')) 			{	$(this).removeClass('fa-file-text-o').addClass('fa-file-text');						}
			if ($(this).hasClass('fa-flag-o')) 					{	$(this).removeClass('fa-flag-o').addClass('fa-flag');								}
			if ($(this).hasClass('fa-folder-o')) 				{	$(this).removeClass('fa-folder-o').addClass('fa-folder');							}
			if ($(this).hasClass('fa-folder-open-o')) 			{	$(this).removeClass('fa-folder-open-o').addClass('fa-folder-open');					}
			if ($(this).hasClass('fa-heart-o')) 				{	$(this).removeClass('fa-heart-o').addClass('fa-heart');								}
			if ($(this).hasClass('fa-minus-square-o')) 			{	$(this).removeClass('fa-minus-square-o').addClass('fa-minus-square');				}		
			if ($(this).hasClass('fa-paper-plane-o')) 			{	$(this).removeClass('fa-paper-plane-o').addClass('fa-paper-plane');					}
			if ($(this).hasClass('fa-pencil-square-o')) 		{	$(this).removeClass('fa-bell-o').addClass('fa-bell');								}
			if ($(this).hasClass('fa-play-circle-o')) 			{	$(this).removeClass('fa-pencil-square-o').addClass('fa-pencil-square');				}
			if ($(this).hasClass('fa-plus-square-o')) 			{	$(this).removeClass('fa-plus-square-o').addClass('fa-plus-square');					}
			if ($(this).hasClass('fa-square-o')) 				{	$(this).removeClass('fa-square-o').addClass('fa-square');							}
			if ($(this).hasClass('fa-star-half-o')) 			{	$(this).removeClass('fa-star-half-o').addClass('fa-star-half');						}
			if ($(this).hasClass('fa-star-o')) 					{	$(this).removeClass('fa-star-o').addClass('fa-star');								}
			if ($(this).hasClass('fa-thumbs-o-down')) 			{	$(this).removeClass('fa-thumbs-o-down').addClass('fa-thumbs-down');					}
			if ($(this).hasClass('fa-thumbs-o-up')) 			{	$(this).removeClass('fa-thumbs-o-up').addClass('fa-thumbs-up');						}
			if ($(this).hasClass('fa-times-circle-o')) 			{	$(this).removeClass('fa-times-circle-o').addClass('fa-times-circle');				}
			if ($(this).hasClass('fa-trash-o')) 				{	$(this).removeClass('fa-trash-o').addClass('fa-trash');								}
			if ($(this).hasClass('fa-bell-slash-o')) 			{	$(this).removeClass('fa-bell-slash-o').addClass('fa-bell-slash');					}
		}
	});
	$(document).on("mouseout", '.fa-toggle-o', function(event)	{
		if ($(this).hasClass('disabled') == false) {
			if ($(this).hasClass('fa-bell')) 					{	$(this).removeClass('fa-bell').addClass('fa-bell-o');								}
			if ($(this).hasClass('fa-arrow-circle-down')) 		{	$(this).removeClass('fa-arrow-circle-down').addClass('fa-arrow-circle-o-down');		} 
			if ($(this).hasClass('fa-arrow-circle-left')) 		{	$(this).removeClass('fa-arrow-circle-left').addClass('fa-arrow-circle-o-left');		}
			if ($(this).hasClass('fa-arrow-circle-right')) 		{	$(this).removeClass('fa-arrow-circle-right').addClass('fa-arrow-circle-o-right');	}
			if ($(this).hasClass('fa-arrow-circle-up')) 		{	$(this).removeClass('fa-arrow-circle-up').addClass('fa-arrow-circle-o-up');			}
			if ($(this).hasClass('fa-bookmark')) 				{	$(this).removeClass('fa-bookmark').addClass('fa-bookmark-o');						}
			if ($(this).hasClass('fa-building')) 				{	$(this).removeClass('fa-building').addClass('fa-building-o');						}
			if ($(this).hasClass('fa-calendar')) 				{	$(this).removeClass('fa-calendar').addClass('fa-calendar-o');						}
			if ($(this).hasClass('fa-check-circle')) 			{	$(this).removeClass('fa-check-circle').addClass('fa-check-circle-o');				}
			if ($(this).hasClass('fa-check-square')) 			{	$(this).removeClass('fa-check-square').addClass('fa-check-square-o');				}
			if ($(this).hasClass('fa-circle')) 					{	$(this).removeClass('fa-circle').addClass('fa-circle-o');							}
			if ($(this).hasClass('fa-comment')) 				{	$(this).removeClass('fa-comment').addClass('fa-comment-o');							}
			if ($(this).hasClass('fa-comments')) 				{	$(this).removeClass('fa-comments').addClass('fa-comments-o');						}
			if ($(this).hasClass('fa-envelope')) 				{	$(this).removeClass('fa-envelope').addClass('fa-envelope-o');						}
			if ($(this).hasClass('fa-file')) 					{	$(this).removeClass('fa-file').addClass('fa-file-o');								}
			if ($(this).hasClass('fa-file-text')) 				{	$(this).removeClass('fa-file-text').addClass('fa-file-text-o');						}
			if ($(this).hasClass('fa-flag')) 					{	$(this).removeClass('fa-flag').addClass('fa-flag-o');								}
			if ($(this).hasClass('fa-folder')) 					{	$(this).removeClass('fa-folder').addClass('fa-folder-o');							}
			if ($(this).hasClass('fa-folder-open')) 			{	$(this).removeClass('fa-folder-open').addClass('fa-folder-open-o');					}
			if ($(this).hasClass('fa-heart')) 					{	$(this).removeClass('fa-heart').addClass('fa-heart-o');								}
			if ($(this).hasClass('fa-minus-square')) 			{	$(this).removeClass('fa-minus-square').addClass('fa-minus-square-o');				}		
			if ($(this).hasClass('fa-paper-plane')) 			{	$(this).removeClass('fa-paper-plane').addClass('fa-paper-plane-o');					}
			if ($(this).hasClass('fa-pencil-square')) 			{	$(this).removeClass('fa-bell').addClass('fa-bell-o');								}
			if ($(this).hasClass('fa-play-circle')) 			{	$(this).removeClass('fa-pencil-square').addClass('fa-pencil-square-o');				}
			if ($(this).hasClass('fa-plus-square')) 			{	$(this).removeClass('fa-plus-square').addClass('fa-plus-square-o');					}
			if ($(this).hasClass('fa-square')) 					{	$(this).removeClass('fa-square').addClass('fa-square-o');							}
			if ($(this).hasClass('fa-star-half')) 				{	$(this).removeClass('fa-star-half').addClass('fa-star-half-o');						}
			if ($(this).hasClass('fa-star')) 					{	$(this).removeClass('fa-star').addClass('fa-star-o');								}
			if ($(this).hasClass('fa-thumbs-down')) 			{	$(this).removeClass('fa-thumbs-down').addClass('fa-thumbs-o-down');					}
			if ($(this).hasClass('fa-thumbs-up')) 				{	$(this).removeClass('fa-thumbs-up').addClass('fa-thumbs-o-up');						}
			if ($(this).hasClass('fa-times-circle')) 			{	$(this).removeClass('fa-times-circle').addClass('fa-times-circle-o');				}
			if ($(this).hasClass('fa-trash')) 					{	$(this).removeClass('fa-trash').addClass('fa-trash-o');								}
			if ($(this).hasClass('fa-bell-slash')) 				{	$(this).removeClass('fa-bell-slash').addClass('fa-bell-slash-o');					}
		}
	});
})(jQuery);
