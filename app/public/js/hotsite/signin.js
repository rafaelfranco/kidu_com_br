//signup page
$(document).ready(function() {
	//signup form submit
	$('#sendSignin').click(function(){
		errors = 0;
		//validade empty fields
		errors += validateEmpty('username');
		errors += validateEmpty('password');

		if(errors == 0) {
			$.ajax({
				url: '/action/signin/',
				type: 'POST',
				data: { 
						username: $('#username').val(),
						password: $('#password').val()
     				},
			        success: function(json) {
			        	dados = json.split(";");
			        	//cadastre realizado com sucesso
			        	if(dados[0] == 'success') {
			        		window.location = '/profile';
			        	} else {
							alert(dados[1]);
			        	}
			        }
			});
		}
	});
});