(function($){
	$('.fa-toggle-o').mouseover(function()  {	
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
	$('.fa-toggle-o').mouseout(function()  {	
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
//fa-bell-o fa-bell
//fa-bell-slash fa-bell-slash-o
//fa-bar-chart-o (alias)fa-bar-chart
//fa-arrow-circle-o-down fa-arrow-circle-down
//fa-arrow-circle-o-left fa-arrow-circle-left
//fa-arrow-circle-o-right fa-arrow-circle-right
//fa-arrow-circle-o-up fa-arrow-circle-up
//fa-bookmark-o fa-bookmark
//fa-building-o fa-building
//fa-calendar-o fa-calendar
//fa-check-circle-o fa-check-circle
//fa-check-square fa-check-square-o
//fa-circle-o fa-circle
//fa-close fa-clock-o
//fa-comment-o fa-comment
//fa-comments-o fa-comments
//fa-envelope-o fa-envelope
//fa-file-o fa-file
//fa-file-text-o fa-file-text
//fa-flag-o fa-flag
//fa-folder-o fa-folder
//fa-folder-open-o fa-folder-open
//fa-heart-o fa-heart
//fa-minus-square-o fa-minus-square
//fa-paper-plane-o fa-paper-plane
//fa-pencil-square-o fa-pencil-square
//fa-play-circle-o fa-play-circle
//fa-plus-square-o fa-plus-square
//fa-square-o fa-square
//fa-star-half-o fa-star-half
//fa-star-o fa-star
//fa-thumbs-o-down fa-thumbs-down
//fa-thumbs-o-up fa-thumbs-up
//fa-times-circle-o fa-times-circle
//fa-trash-o fa-trash	
//fa-bell-slash-o
//fa-futbol-o
//fa-soccer-ball-o
//fa-newspaper-o
//fa-bar-chart-o
//fa-bell-o
//fa-bookmark-o
//fa-building-o
//fa-calendar-o
//fa-caret-square-o-up
//fa-caret-square-o-down
//fa-caret-square-o-left
//fa-caret-square-o-right
