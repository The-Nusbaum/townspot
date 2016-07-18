var ArtistCommentView = Backbone.View.extend({
	initialize: function(){
		this.template = _.template($('#artist-comment-template').html());
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
		$.each(collection.models, function() 		{
			html = _self.template({
				id: this.get('id'),
				user_id: this.get('user_id'),
				target_id: this.get('target_id'),
				comment: this.get('comment'),
				created: this.get('created:'),
				updated: this.get('updated:'),
				username: this.get('username')
			});
			_self.$el.append(html);
		});
		collection.reset();
		return this;
	}
});