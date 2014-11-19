var usernameTypeahead = new Bloodhound({
	datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	remote: '/admin/typeahead?field=user::username&query=%QUERY'
});

usernameTypeahead.initialize();
 
$('#search-username').typeahead(
	{
		hint: true,
		highlight: true,
		minLength: 3
	},
	{
		name: 'username-typeahead',
		displayKey: 'value',
		source: usernameTypeahead.ttAdapter()
	}
);