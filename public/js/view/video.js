var VideoView = Backbone.View.extend({
	initialize: function(){
		this.template = _.template($('#video-template').html());
    },
    setTemplate: function(template){
		this.template = _.template($(template).html());
        return this;
    },
    resetView: function(){
		this.$el.html('');
    },
    renderCarouselCollection: function(collection){
		var _self = this;
		_self.resetView();		
		var html = '';
		var classname = 'active';
		var index = 0;
		$.each(collection.models, function() 		{
			var random_id = _self.stringGen(16);
			html = html + _self.template({
				random_id: random_id,
				classname: classname,
				id: this.get('id'),
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
			index = index + 1;
			classname = '';
		});
		_self.$el.append(html);
	},
    renderCarouselRow: function(collection,perRow){
		var _self = this;
		_self.resetView();		
		var row = 12 / perRow;
		var index = 0;
		for(i=0; i < (collection.models.length/perRow); i++){
			var group = collection.models.slice(i * perRow, (i * perRow) + perRow);
			var html = '';
			index = 1;
			$.each(group, function() 		{	
				var random_id = _self.stringGen(16);
				var position = '';
				if (index == 1) {
					position = 'first';
				} else if (index == (row - 1)) {
					position = 'last';
				} else {
					position = 'in-row';
				}
				index = index + 1;
				html = html + _self.template({
					random_id: random_id,
					row: row,
					position: position,
					id: this.get('id'),
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
					why_we_choose: this.get('why_we_choose'),
					series_name: this.get('series_name'),
					series_link: this.get('series_link')
				});
			});
			if (i == 0) {
				_self.$el.append('	<div class="item active"><div class="row">' + html + '</div></div>');
			} else {
				_self.$el.append('	<div class="item"><div class="row">' + html + '</div></div>');
			}
		};
	},
    renderCarouselRowIndicators: function(panel,collection,perRow){
		var _self = this;
		_self.resetView();		
		var html = '';
		var classname = 'active';
		var index = 0;
		for(i=0; i < (collection.models.length/perRow); i++){
			var group = collection.models.slice(i * perRow, (i * perRow) + perRow);
			html = html + _self.template({
							panel: panel,
							index: index,
							classname: classname,
			});
			index = index + 1;
			classname = '';
		};
		_self.$el.append(html);
	},
    stringGen: function(len){
		var text = "";
		var charset = "abcdefghijklmnopqrstuvwxyz0123456789";
		for( var i=0; i < len; i++ )
			text += charset.charAt(Math.floor(Math.random() * charset.length));
		return text;
	}	
});