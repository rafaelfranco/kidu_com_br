<?php include "host.inc" ?>
<!doctype html>
<html>

<head>
<title>Kidu</title>
<meta charset="utf-8" />
<!-- <meta http-equiv="refresh" content="5"> -->
<link href='http://fonts.googleapis.com/css?family=Finger+Paint' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
<script type="text/javascript">
var altura_footer;

window.onload = function(){
window.onscroll = coisa;
altura_footer = document.body.clientHeight - document.documentElement.clientHeight - 50; //50 é a altura do footer de baixo
}

function coisa(){
var top = window.pageYOffset || document.documentElement.scrollTop;

	if(top > altura_footer){
	document.getElementById('avatar').style.bottom = (top - altura_footer) + 'px';
	} else {
	document.getElementById('avatar').style.bottom = 0;
	}
}

</script>
</head>

<body id="tema">
<?php include "header.inc" ;?>

<section id="explica">
<?php
$dados_tema = json_decode(utf8_decode(file_get_contents("{$host}/services/api/rest/json/?method=group.get&username=kidu&limit=1&context=all&offset=0&guid=" . $_GET['guid'])));
//echo file_get_contents("{$host}/services/api/rest/json/?method=group.get&username=kidu&limit=1&context=all&offset=0&guid=" . $_GET['guid']);
?>

<h2>
<?php echo $dados_tema->result->name; ?>
</h2>
<a href="/">Veja todos os temas</a>
<p><?php echo $dados_tema->result->fields->briefdescription->value; ?></p>
<br class="tudo">
<div class="explica">
<ul>
<li>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. In et aliquet nisl. Pellentesque tempus vulputate mauris eu cursus. Proin volutpat.
<br>
<span><img src="imagens/ico_coracao.gif" src="Curtidos" width="27" height="25">12</span>
</li>
<li>
In et aliquet nisl. Pellentesque tempus vulputate mauris eu cursus. Proin volutpat. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
<br>
<span><img src="imagens/ico_medalha.gif" src="Garantidos" width="27" height="25"></span>
</li>
<li>
In et aliquet nisl. Pellentesque tempus vulputate mauris eu cursus. Proin volutpat. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
<br>
<span><img src="imagens/ico_comentado.gif" src="Curtidos" width="27" height="25"></span>
</li>
</ul>
</div>

<div class="video">
<object id="kaltura_player" name="kaltura_player" type="application/x-shockwave-flash" allowFullScreen="true" allowNetworking="all" allowScriptAccess="always" height="360" width="640" data="http://www.kaltura.com/index.php/kwidget/wid/_1598441/uiconf_id/20444342/entry_id/0_64mie35c/video.swf"> <param name="allowFullScreen" value="true" /> <param name="allowNetworking" value="all" /><param name="allowScriptAccess" value="always" /><param name="wmode" value="transparent" /><param name="bgcolor" value="#000000" /><param name="flashVars" value="&autoPlay=&entryId=0_64mie35c" /><param name="movie" value="http://www.kaltura.com/index.php/kwidget/wid/_1598441/uiconf_id/20444342/entry_id/0_64mie35c/video.swf"></object>
</div>

</section>


<br class="tudo">

<dl id="respostas">
<dt>
<h3>O que é moda</h3>

<div>
<span>
<img src="imagens/bot_faca_voce.gif" alt="Faça você!" width="110" height="40">
</span><br>
<a href="vejamais.php">Ver mais respostas a esta questão.</a>
</div>

<p>Alguma coisa bem legal vai escrita aqui. E pode ter até 3 linhas de texto.</p>
</dt>

<dd>
<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>

<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
	
<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
	
<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

</dd>

<dt>
<h3>O que é moda</h3>

<div>
<span>
<img src="imagens/bot_faca_voce.gif" alt="Faça você!" width="110" height="40">
</span><br>
<a href="vejamais.php">Ver mais respostas a esta questão.</a>
</div>

<p>Alguma coisa bem legal vai escrita aqui. E pode ter até 3 linhas de texto.</p>
</dt>

<dd>
<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>

<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
	
<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
	
<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

</dd>

<dt>
<h3>O que é moda</h3>

<div>
<span>
<img src="imagens/bot_faca_voce.gif" alt="Faça você!" width="110" height="40">
</span><br>
<a href="vejamais.php">Ver mais respostas a esta questão.</a>
</div>

<p>Alguma coisa bem legal vai escrita aqui. E pode ter até 3 linhas de texto.</p>
</dt>

<dd>
<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>

<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
	
<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
	
<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

</dd>

<dt>
<h3>O que é moda</h3>

<div>
<span>
<img src="imagens/bot_faca_voce.gif" alt="Faça você!" width="110" height="40">
</span><br>
<a href="vejamais.php">Ver mais respostas a esta questão.</a>
</div>

<p>Alguma coisa bem legal vai escrita aqui. E pode ter até 3 linhas de texto.</p>
</dt>

<dd>
<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>

<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
	
<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
	
<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

</dd>

<dt>
<h3>O que é moda</h3>

<div>
<span>
<img src="imagens/bot_faca_voce.gif" alt="Faça você!" width="110" height="40">
</span><br>
<a href="vejamais.php">Ver mais respostas a esta questão.</a>
</div>

<p>Alguma coisa bem legal vai escrita aqui. E pode ter até 3 linhas de texto.</p>
</dt>

<dd>
<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>

<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
	
<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

<figure>
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
	
<img src="imagens/ico_usuario.gif" width="36" height="36" alt="User">	
<strong>Nome da pessoa</strong>
</figcaption>
</figure>

</dd>
</dl>

<section id="tudo_sobre">
<article>
<h3>Veja tudo sobre o Mundo Fashion</h3>
<nav><a href="">Só vídeo</a> | <a href="">Só texto</a> | <a href="">Só imagens</a> | <a href="">Busca por data</a></nav>
<br class="tudo">
<div>
<figure>
<img src="imagens/foto_desafio.jpg" height="204" width="204" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
</figcaption>
</figure>

<figure>
<img src="imagens/foto_desafio.jpg" height="204" width="204" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
</figcaption>
</figure>

<figure>
<img src="imagens/foto_desafio.jpg" height="204" width="204" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
</figcaption>
</figure>

<figure>
<img src="imagens/foto_desafio.jpg" height="204" width="204" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
</figcaption>
</figure>

</div>

</article>
</section>
<br class="tudo">
<section id="outros_temas">
<span onclick="coisa();"><img src="imagens/bot_ver_temas.gif" width="140" height="36" alt="Ver mais temas"></span>
<h3>Outros temas</h3>
<p>Um pequeno texto que vai explicar o que é isso.</p>
<form>
<input type="text" placeholder="Busque outros temas"><span>Buscar</span>
</form>
<br class="tudo">
<article>
<figure>
<img src="imagens/foto_desafio.jpg" height="204" width="204" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
</figcaption>
</figure>

<figure>
<img src="imagens/foto_desafio.jpg" height="204" width="204" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
</figcaption>
</figure>

<figure>
<img src="imagens/foto_desafio.jpg" height="204" width="204" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
</figcaption>
</figure>

<figure>
<img src="imagens/foto_desafio.jpg" height="204" width="204" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
</figcaption>
</figure>

</article>
</section>
<br class="tudo">
<p>&nbsp;</p>



<!--footer id="avatar">
<dl>
<dt><img src="imagens/avatar.png" width="61" height="199"></dt>
<dd>
Falar <img src="imagens/ico_footer_falar.png" width="22" height="21"><br>
Configurações <img src="imagens/ico_footer_config.png" width="22" height="21"><br>
Lista de amigos <img src="imagens/ico_footer_amigos.png" width="22" height="21">
<h5>Beneditazinha</h5>
</dd>	
</dl>
</footer-->

<footer id="logado">
<div>
<div>
<strong>
	<a href="/profile">bananinha</a>
</strong><span><a href="/help">ajuda</a> | <a href="/logoff">sair</a></span>
</div>
</div>
</footer>

<footer id="info">Quero ver isso aqui! <button>Opa!</button></footer>
</body>
</html>