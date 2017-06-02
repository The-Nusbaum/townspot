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
				allowContact: this.get('allowContact'),
				approved: this.get('approved'),
				authorised: this.get('authorised'),
				city_id: this.get('city_id'),
				commentsAbout: this.get('commentsAbout'),
				comment_count: this.get('comment_count'),
				country_id: this.get('country_id'),
				created: this.get('created'),
				debutTime: this.get('debutTime'),
				description: this.get('description'),
				duration: this.get('duration'),
				embedLink: this.get('embedLink'),
				id: this.get('id'),
				latitude: this.get('latitude'),
				location: this.get('location'),
				logline: this.get('logline'),
				longitude: this.get('longitude'),
				mediaLink: this.get('mediaLink'),
				mediaType: this.get('mediaType'),
				mediaUrl: this.get('mediaUrl'),
				neighborhood: this.get('neighborhood'),
				onMediaServer: this.get('onMediaServer'),
				previewImage: this.get('previewImage'),
				province_id: this.get('province_id'),
				requestDebutTime: this.get('requestDebutTime'),
				resizerCdnLink: this.get('resizerCdnLink'),
				resizerLink: this.get('resizerLink'),
				source: this.get('source'),
				title: this.get('title'),
				updated: this.get('updated'),
				url: this.get('url'),
				user_id: this.get('user_id'),
				username: this.get('username'),
				displayName: this.get('displayName'),
				profileLink: this.get('profileLink'),
				views: this.get('views'),
				whyWeChose: this.get('whyWeChose'),
				ytAuthor: this.get('ytAuthor'),
				ytSubscriberChannelId: this.get('ytSubscriberChannelId'),
				ytSubscriberChannelTitle: this.get('ytSubscriberChannelTitle'),
				ytVideoId: this.get('ytVideoId'),
				rate_up: this.get('rate_up'),
				rate_down: this.get('rate_down')
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
					allowContact: this.get('allowContact'),
					approved: this.get('approved'),
					authorised: this.get('authorised'),
					city_id: this.get('city_id'),
					commentsAbout: this.get('commentsAbout'),
					comment_count: this.get('comment_count'),
					country_id: this.get('country_id'),
					created: this.get('created'),
					debutTime: this.get('debutTime'),
					description: this.get('description'),
					duration: this.get('duration'),
					embedLink: this.get('embedLink'),
					id: this.get('id'),
					latitude: this.get('latitude'),
					location: this.get('location'),
					logline: this.get('logline'),
					longitude: this.get('longitude'),
					mediaLink: this.get('mediaLink'),
					mediaType: this.get('mediaType'),
					mediaUrl: this.get('mediaUrl'),
					neighborhood: this.get('neighborhood'),
					onMediaServer: this.get('onMediaServer'),
					previewImage: this.get('previewImage'),
					province_id: this.get('province_id'),
					requestDebutTime: this.get('requestDebutTime'),
					resizerCdnLink: this.get('resizerCdnLink'),
					resizerLink: this.get('resizerLink'),
					source: this.get('source'),
					title: this.get('title'),
					updated: this.get('updated'),
					url: this.get('url'),
					user_id: this.get('user_id'),
					username: this.get('username'),
					displayName: this.get('displayName'),
					profileLink: this.get('profileLink'),
					views: this.get('views'),
					whyWeChose: this.get('whyWeChose'),
					ytAuthor: this.get('ytAuthor'),
					ytSubscriberChannelId: this.get('ytSubscriberChannelId'),
					ytSubscriberChannelTitle: this.get('ytSubscriberChannelTitle'),
					ytVideoId: this.get('ytVideoId'),
					rate_up: this.get('rate_up'),
					rate_down: this.get('rate_down')
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
			} else if (index == 2) {
				_self.$el.append('<div class="google-ad" data-position="rectangle" ></div>');
			} else {
				position = 'in-row';
			}
			index = index + 1;
			html = _self.template({
				allowContact: this.get('allowContact'),
				approved: this.get('approved'),
				authorised: this.get('authorised'),
				city_id: this.get('city_id'),
				commentsAbout: this.get('commentsAbout'),
				comment_count: this.get('comment_count'),
				country_id: this.get('country_id'),
				created: this.get('created'),
				debutTime: this.get('debutTime'),
				description: this.get('description'),
				duration: this.get('duration'),
				embedLink: this.get('embedLink'),
				id: this.get('id'),
				latitude: this.get('latitude'),
				location: this.get('location'),
				logline: this.get('logline'),
				longitude: this.get('longitude'),
				mediaLink: this.get('mediaLink'),
				mediaType: this.get('mediaType'),
				mediaUrl: this.get('mediaUrl'),
				neighborhood: this.get('neighborhood'),
				onMediaServer: this.get('onMediaServer'),
				previewImage: this.get('previewImage'),
				province_id: this.get('province_id'),
				requestDebutTime: this.get('requestDebutTime'),
				resizerCdnLink: this.get('resizerCdnLink'),
				resizerLink: this.get('resizerLink'),
				source: this.get('source'),
				title: this.get('title'),
				updated: this.get('updated'),
				url: this.get('url'),
				user_id: this.get('user_id'),
				username: this.get('username'),
				displayName: this.get('displayName'),
				profileLink: this.get('profileLink'),
				views: this.get('views'),
				whyWeChose: this.get('whyWeChose'),
				ytAuthor: this.get('ytAuthor'),
				ytSubscriberChannelId: this.get('ytSubscriberChannelId'),
				ytSubscriberChannelTitle: this.get('ytSubscriberChannelTitle'),
				ytVideoId: this.get('ytVideoId'),
				rate_up: this.get('rate_up'),
				rate_down: this.get('rate_down')
			});
			_self.$el.append(html);
		});
		collection.reset();
		return this;
	}
});