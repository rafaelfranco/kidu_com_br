//home page after login
$(document).ready(function() {
	$.ajax({
		url: '/action/getgroups/',
		type: 'POST',
		data: { 
				text: $('#searchInput').val()
				},
	        success: function(json) {
	        	$('#themeList').html(json);
	        }
	});
});