var Video = Backbone.Model.extend({
	defaults: {
        id: '',
        type: '',
        link: '',
        image: '',
        escaped_title: '',
        title: '',
        logline: '',
        user: '',
        user_profile: '',
        duration: '',
        comment_count: '',
        views: '',
        escaped_logline: '',
        location: '',
        escaped_location: '',
        rate_up: '',
        rate_down: '',
        why_we_choose: '',
		series_name: '',
		series_link: '',
		image_source: ''
	},
	initialize: function(){
    }
});

