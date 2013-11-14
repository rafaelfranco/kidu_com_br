//home page after login
jubileu = function(){
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
}

$(document).ready(function() {
jubileu();

	$('#searchBottom').click(function() {
		if($('#inputSearch').val() == ''){
			alert('Preencha a pesquisa!');
		} else {
			//window.location = '/home/'+$('#inputSearch').val();
			jubileu();
		}
	});

});