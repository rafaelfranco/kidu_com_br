//signup page
$(document).ready(function() {
	//signup form submit
	$('#sendSignin').click(jubileu);
	$('#nada').submit(jubileu);
});

jubileu = function(){
errors = 0;
//validade empty fields
errors += validateEmpty('username');
errors += validateEmpty('password');

	if(errors == 0) {
	$('#sendSignin').html('Aguarde');
	$('#sendSignin').css('background','silver');
	$('#sendSignin').css('cursor','auto');
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
						alert('Houve um erro: ' + dados[0] + " " + dados[1]);
		        	}
		        }
		});
	}
}