var FollowView = Backbone.View.extend({
	initialize: function(){
		this.template = _.template($('#follow-template').html());
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
		var $container = $("<div class='followContainer'></div>");
		var $group = $("<div class='followGroup'></div>");
		var i = 0;
		var x = 1;
		var y = collection.models.length - 1;
		$.each(collection.models, function() {
			html = _self.template({
				id: this.get('id'),
				username: this.get('username'),
				thumb: this.get('thumb'),
				profile: this.get('profile')
			});
			$group.append(html);
			i++;
			x++;
			if(i == 10 || x == y) {
				$container.append($group);
				$group = $("<div class='followGroup'></div>");
				i = 0;
			}
		});
		_self.$el.append($container);
		collection.reset();
		return this;
	}
});