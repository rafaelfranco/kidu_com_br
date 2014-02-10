function setValid(element) {
	$('#'+element).css('border','solid 1px green');
	$('#'+element+'-erro').css('display','none');
}
function setInvalid(element,msg) {
	$('#'+element).css('border','solid 1px red');
	$('#'+element+'-erro').html(msg);
	$('#'+element+'-erro').fadeIn();
}
//Funcoes executadas no load da aplicação
$(document).ready(function() {
	$('#sendMessage').click(function(){
		errors = 0;
		//validade empty fields
		errors += validateEmpty('name');
		errors += validateEmpty('email');
		errors += validateEmpty('message');

		$.ajax({
			url: '/action/sendMessage/',
			type: 'POST',
			async:false,
			data: { 
  					name: $('#name').attr('value'),  
  					email: $('#email').attr('value'),  
  					message: $('#message').attr('value')
 				},
		        success: function(json) {
		        	dados = json.split(";");
		        	//cadastre realizado com sucesso
		        	if(dados[0] == 'sucesso') {
		        		alert('Mensagem enviada com sucesso!');
		        	}
		        }
		});
		

	});


	//formulario de login
	$('#sendSignIn').click(function(){
		
		//testa erros
		errors = 0;
		errors += validateEmpty('login');
		errors += validateEmpty('pass');
		
		if(errors == 0) {
			$.ajax({
				url: '/action/login/',
				type: 'POST',
				async:false,
				data: { 
      					login: $('#login').attr('value'),  
      					senha: $('#pass').attr('value')
     				},
			        success: function(json) {
			        	dados = json.split(";");
			        	//cadastre realizado com sucesso
			        	if(dados[0] == 'sucesso') {
			        		window.location = '/perfil';
			        	} else {
							setInvalid('login',dados[1]);
							setInvalid('pass',dados[1]);
			        	}
			        }
			});
		}
	});

	
	$('#usernameBox').click(function(){
		$('#floatProfileBox').show();
	});
	$('#content').mouseover(function(){
		$('#floatProfileBox').hide();
	});
	

	$('#sendSignUp').click(function(){
		errors = 0;
		//validade empty fields
		errors += validateEmpty('nome');
		errors += validateEmpty('email');
		errors += validateEmpty('reemail');
		errors += validateEmpty('senha');
		errors += validateEmpty('resenha');
		errors += validateChecked('privacy');
		errors += validateEmail('email');
		
		

		//if havent empty fields test equals
		if(errors ==0) {
			errors += validateEqual('senha','resenha');
			errors += validateEqual('email','reemail');
		}

		if(errors == 0) {
			$.ajax({
				url: '/action/signup/',
				type: 'POST',
				data: { 
						nome: $('#nome').attr('value'),
						email: $('#email').attr('value'),
						senha: $('#senha').attr('value'),
						dia_nascimento: $('#dia_nascimento').attr('value'),
						mes_nascimento: $('#mes_nascimento').attr('value'),
						ano_nascimento: $('#ano_nascimento').attr('value'),
						sexo: $('#sexo').attr('value'),
						facebook_id: $('#facebook_id').attr('value'),
						foto: $('#foto').attr('value'),
						AccessToken : $('#AccessToken').attr('value')
						
     				},
			        success: function(json) {
			        	dados = json.split(";");
			        	//cadastre realizado com sucesso
			        	if(dados[0] == 'sucesso') {
			        		window.location = '/perfil';
			        	} else {
							alert('Ocorreu um erro');
			        	}
			        }
			});
		}



	});
	

$(function() {
    var cache = {};
    $( "#local" ).autocomplete({
      minLength: 2,
      source: function( request, response ) {
        var term = request.term;
        if ( term in cache ) {
          response( cache[ term ] );
          return;
        }
        $.getJSON( "/hotsite/locais/", request, function( data, status, xhr ) {
          cache[ term ] = data;
          response( data );
        });
      }
    });
  });

	$('#sendTextChallenge').click(function() {
		if($('#textAnswer').val() == '') {
			alert('Preencha a resposta!');
		} else {
			$('form').submit();
		}
	});


	$('#homeSearch').click(function() {
		if($('#searchInput').val() == '') {
			alert('Preencha a pesquisa!');
		} else {
			jubileu();
		}
	});
	
	$('#topSearch').click(function() {
		if($('#topInputsearch').val() == ''){
			alert('Preencha a pesquisa!');
		} else {
			window.location = '/home/'+$('#topInputsearch').val();
		}
	});

});
function closeModal() {
	$('#fullBlack').hide();
	$('#modalBox').hide();
}

function validateEmpty(element) {
	//testa erros
	errors = 0;
	if($('#'+element).attr('value') == '') {
		setInvalid(element);
		errors++;
	} else {
		setValid(element);
	}
	return errors;
}

function validateChecked(element) {
	//testa erros
	errors = 0;
	if($('#'+element).attr('checked') != 'checked') {
		
		setInvalid(element);
		errors++;
	} else {
		
		setValid(element);
	}
	return errors;
}

function validateFutureDate(date) {
	dt = date.split('/');
	errors = 0;
	$.ajax({
		url: '/action/isFutureDate/',
		type: 'POST',
		async: false,
		data: { 
				d : dt[0],
				m : dt[1],
				y : dt[2]
			},
	        success: function(json) {
	        	if(json == 'not') {
	        		errors++;
	        		$('#datepicker').css('border','solid 1px red');
	        	} else {
	        		$('#datepicker').css('border','solid 1px green');
	        	}
	        }
	});
	return errors;
	
} 

function validateEqual(element1,element2) {
	//testa erros
	errors = 0;
	if($('#'+element1).attr('value') == $('#'+element2).attr('value')) {
		setValid(element1);
	} else {
		setInvalid(element2);
		errors++;
	}
	return errors;
}

function loadWall(user,start) {
	$.ajax({
		url: '/action/loadWall/',
		type: 'POST',
		data: { 
				usuario_id: user,
				inicio: start,
			},
	        success: function(json) {
	        	$('#messageList').html(json);
	        }
	});
}

function loadModal(url,parameter) {
	$('#fullBlack').show();
	$('#modalBox').show();
	$.ajax({
		url: url,
		type: 'POST',
		data: {param1: parameter},
	        success: function(json) {
	        	$('#modal').html('<div id="closeModal" onclick="closeModal();" >X</div>'+json);
	        }
	});
}


function formatar_mascara(src, mascara) {
	var campo = src.value.length;
	var saida = mascara.substring(0,1);
	var texto = mascara.substring(campo);
	if(texto.substring(0,1) != saida) {
		src.value += texto.substring(0,1);
	}
}

function onlyNumbers(evt)
{
	var e = event || evt; // for trans-browser compatibility
	var charCode = e.which || e.keyCode;
	
	if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;

	return true;

}

function onlyNumbersPoints(evt)
{
	var e = event || evt; // for trans-browser compatibility
	var charCode = e.which || e.keyCode;
	
	if (charCode > 31 && (charCode < 45 || charCode > 57))
		return false;

	return true;
}

function buscaCep() {
	window.open('/cep/busca','Busca de cep','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=365,height=310, left=30, top=30');
}

function preencheEndereco() {

	$('#endereco').attr('onclick','setActive(\'endereco\');');
	$('#bairro').attr('onclick','setActive(\'endereco\');');
	$('#cidade').attr('onclick','setActive(\'endereco\');');

	//busca endereco e preenche
	 $.ajax({
		url: '/cep/pesquisa/'+$('#cep').attr('value'),
		type: 'GET',
		async: 'false',
            success: function(json) {
				dados = json.split(";");
				if(dados[0] == 1) {
					$('#endereco').val(dados[1]);
                	$('#bairro').val(dados[2]);
                	$('#cidade').val(dados[3]);
                	$("#estado option[value='"+dados[4]+"']").attr("selected", true);              
				} else {
					setInvalid('cep','Cep inválido');
				}
             }
        });
}

function alerta(mensagem) {
	$('#alerta').html(mensagem);
	$('#alerta').fadeIn();
	setTimeout(function() {fechaAlerta() },4000);
}
function fechaAlerta() {
	$('#alerta').fadeOut();
	//setTimeout(function() {$('#alerta').html('');},2000);
}
function validateEmail(element) {
	errors = 0;  
	var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
     if ($('#'+element).val().search(emailRegEx) == -1) {
     	  setInvalid(element);
          errors++;
     }
     return errors;
}

function showModal(file) {
	$('#modal').show();

	 $.ajax({
		url: '/action/getFile/'+file,
		type: 'GET',
		async: 'false',
            success: function(json) {
				if(json.indexOf('erro_deslogado') != -1){
				alert("Você está deslogado!");
				document.location = '/logoff';
				} else {
				$('#modal').html(json);
             	document.getElementById('modal').getElementsByTagName('dl')[0].style.left = ((window.innerWidth - 900)/2) + "px";
             	document.getElementById('modal').getElementsByTagName('dl')[0].style.top = ((window.innerHeight - 600)/2) + "px";
	             }
             }
        });
}
function fecha_modal() {
	$('#modal').html('');
	$('#modal').hide();
}

function likeItem(item_id) {
mudar_likes = document.getElementsByClassName('curtir curtir_item_' + item_id);
	for(i=0;i<mudar_likes.length;i++){
	mudar_likes[i].getElementsByTagName('b')[0].innerHTML = parseInt(mudar_likes[i].getElementsByTagName('b')[0].innerHTML) + 1;
	mudar_likes[i].setAttribute('onclick','unlikeItem(' + item_id + ')');
	mudar_likes[i].getElementsByTagName('img')[0].setAttribute('src','/images/ico_curtir.gif');
	}
	
	$.ajax({
		url: '/action/addlike/',
		type: 'POST',
		data: {item_id: item_id},
	}).done(function(data) {
		if(data != 'You now like this item') {
		mudar_likes = document.getElementsByClassName('curtir curtir_item_' + item_id);
			for(i=0;i<mudar_likes.length;i++){
			mudar_likes[i].getElementsByTagName('b')[0].innerHTML = parseInt(mudar_likes[i].getElementsByTagName('b')[0].innerHTML) - 1;
			mudar_likes[i].setAttribute('onclick','likeItem(' + item_id + ')');
			mudar_likes[i].getElementsByTagName('img')[0].setAttribute('src','/images/ico_curtir_cz.gif');
			}
		}
	});
}

function unlikeItem(item_id) {
mudar_likes = document.getElementsByClassName('curtir curtir_item_' + item_id);
	for(i=0;i<mudar_likes.length;i++){
	mudar_likes[i].getElementsByTagName('b')[0].innerHTML = parseInt(mudar_likes[i].getElementsByTagName('b')[0].innerHTML) - 1;
	mudar_likes[i].setAttribute('onclick','likeItem(' + item_id + ')');
	mudar_likes[i].getElementsByTagName('img')[0].setAttribute('src','/images/ico_curtir_cz.gif');
	}	

	$.ajax({
		url: '/action/unlike/',
		type: 'POST',
		data: {item_id: item_id},
	}).done(function(data) {
		if(data != "Your like has been removed") {
		mudar_likes = document.getElementsByClassName('curtir curtir_item_' + item_id);
			for(i=0;i<mudar_likes.length;i++){
			mudar_likes[i].getElementsByTagName('b')[0].innerHTML = parseInt(mudar_likes[i].getElementsByTagName('b')[0].innerHTML) + 1;
			mudar_likes[i].setAttribute('onclick','unlikeItem(' + item_id + ')');
			mudar_likes[i].getElementsByTagName('img')[0].setAttribute('src','/images/ico_curtir.gif');
			}	
		}
	});
}

var este_dd;

function achar_posicao_dd(de_onde){
quantos_dd = de_onde.parentNode.getElementsByTagName('dd').length;
	
	for(i=0;i<quantos_dd;i++){
		if(de_onde.parentNode.getElementsByTagName('dd')[i] == de_onde){
		este_dd = i;
		break;
		}
	}
}

function aciona_sombra(de_onde){
//de_onde.className = 'sombra';
//alert(de_onde.scrollWidth - de_onde.offsetWidth);
achar_posicao_dd(de_onde);

	if(de_onde.scrollLeft > 50){
	de_onde.parentNode.getElementsByTagName('dd')[este_dd - 1].style.opacity = 1;
	} else {
	de_onde.parentNode.getElementsByTagName('dd')[este_dd - 1].style.opacity = 0;
	}

	if(de_onde.scrollLeft >= (de_onde.scrollWidth - de_onde.offsetWidth - 50)){
	de_onde.parentNode.getElementsByTagName('dd')[este_dd + 1].style.opacity = 0;
	} else {
	de_onde.parentNode.getElementsByTagName('dd')[este_dd + 1].style.opacity = 1;
	}
}

function rola_esquerda(de_onde){
achar_posicao_dd(de_onde.parentNode);
de_onde.parentNode.parentNode.getElementsByTagName('dd')[este_dd + 1].scrollLeft = de_onde.parentNode.parentNode.getElementsByTagName('dd')[este_dd + 1].scrollLeft - 800;
}

function rola_direita(de_onde){
achar_posicao_dd(de_onde.parentNode);
de_onde.parentNode.parentNode.getElementsByTagName('dd')[este_dd - 1].scrollLeft = de_onde.parentNode.parentNode.getElementsByTagName('dd')[este_dd - 1].scrollLeft + 800;
}

function apaga_arquivo(tipo, dado){
	if(tipo == "confirmar"){
	dado.parentNode.getElementsByTagName('div')[0].style.display = 'block';
	} else if(tipo == "cancelar"){
	dado.parentNode.style.display = 'none';
	} else if(tipo == "mesmo"){
		$.ajax({
			url: '/action/apagaArquivo/',
			type: 'POST',
			data: {id_arquivo: dado},
			success: function(res){
				fecha_modal();
				document.getElementById('figura_' + dado).style.backgroundColor = '#E8E5E1';
				document.getElementById('figura_' + dado).getElementsByTagName('figcaption')[0].innerHTML = "<strong class='removido'>Conteúdo removido</strong>";
					if(document.getElementById('figura_' + dado).getElementsByTagName('img')[0]){
					document.getElementById('figura_' + dado).getElementsByTagName('img')[0].setAttribute('onclick','');
					document.getElementById('figura_' + dado).getElementsByTagName('img')[0].style.cursor = 'default';
					}
					if(document.getElementById('figura_' + dado).getElementsByTagName('div')[0]){
					document.getElementById('figura_' + dado).getElementsByTagName('div')[0].setAttribute('onclick','');
					document.getElementById('figura_' + dado).getElementsByTagName('div')[0].style.cursor = 'default';
					}
			}
		})
		// .done(function(res) {
		// alert(res)
		// });
	}
}