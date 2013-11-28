//signup page
$(document).ready(function() {
	//signup form submit
	$('#sendSignup').click(function(){
		errors = 0;
		//validade empty fields
		errors += validateEmpty('username');
		//errors += validateEmpty('email');
		errors += validateEmpty('name');
		errors += validateEmpty('password');
		errors += validateEmpty('repassword');
		//errors += validateEmail('father-email');

		//if havent empty fields test equals
		if(errors ==0) {
			errors += validateEqual('password','repassword');
		}

		if(errors == 0) {

			$.ajax({
				url: '/action/signup/',
				type: 'POST',
				data: { 
						username: $('#username').val(),
					//	email: $('#email').val(),
						name: $('#name').val(),
						password: $('#password').val(),
					//	father_email: $('#father-email').val()
     				},
			        success: function(json) {
			        	dados = json.split(";");
			        	//cadastro realizado com sucesso
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