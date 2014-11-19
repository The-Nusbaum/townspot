var usernameTypeahead = new Bloodhound({
	datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	remote: 'admin/typeahead?field=user::name&query=%QUERY'
});

usernameTypeahead.initialize();
 
$('.typeahead').typeahead(
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