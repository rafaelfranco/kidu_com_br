//home page after login
$(document).ready(function() {
	$('#uploadFile').change(function(){
		if($('#uploadFile').val() != '') {
			$('#acoes .botao').html('Enviando... aguarde');
			$('#postFile').submit();
		}
	})
});