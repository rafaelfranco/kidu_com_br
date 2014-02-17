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
	$('#sendSignin').css('margin','30px 230px 10px');
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
			        	$('#sendSignin').html('Entrar');
						$('#sendSignin').css('background','url("../images/fundo_botoes.jpg") repeat-x scroll 0px 0px transparent');
						$('#sendSignin').css('cursor','pointer');
						$('#sendSignin').css('margin','30px 245px 10px');
						alert('Houve um erro: ' + dados[0] + " " + dados[1]);
		        	}
		        }
		});
	}
}