var SearchResultView = Backbone.View.extend({
	initialize: function(){
		this.template = _.template($('#search-template').html());
    },
    setTemplate: function(template){
		this.template = _.template($(template).html());
        return this;
    },
    resetView: function(){
		this.$el.html('');
    },
    render: function(collection){
		var _self = this;
		var position = "in-row";
		index = 1;
		$.each(collection.models, function() 		{
			if (index == 3) {
				position = 'last';
				index = 0;
			} else if (index == 1) {
				position = 'first';
			} else {
				position = 'in-row';
			}
			index = index + 1;
			html = _self.template({
				id: this.get('id'),
				position: position,
				type: this.get('type'),
				link: this.get('link'),
				image: this.get('image'),
				escaped_title: this.get('escaped_title'),
				title: this.get('title'),
				logline: this.get('logline'),
				user: this.get('user'),
				user_profile: this.get('user_profile'),
				duration: this.get('duration'),
				comment_count: this.get('comment_count'),
				views: this.get('views'),
				escaped_logline: this.get('escaped_logline'),
				escaped_location: this.get('escaped_location'),
				rate_up: this.get('rate_up'),
				rate_down: this.get('rate_down'),
				why_we_choose: this.get('why_we_choose')
			});
			_self.$el.append(html);
		});
		collection.reset();
	}
});