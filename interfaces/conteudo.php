<html>
  <head>
    <!-- Start: ../view/hotsite/global/head.html -->
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css" />
    <link rel="stylesheet" href="css/jquery-ui.css" /> 
    <link href='http://fonts.googleapis.com/css?family=Gochi+Hand' rel='stylesheet' type='text/css'>
    <!-- JS -->
    <script type="text/javascript" src="js/jquery.js"></script> 
    <script type="text/javascript" src="js/global.js?v=1"></script> 
    <script type="text/javascript" src="js/jquery-ui.js"></script>
    
    <title>KIDU</title>
    
    <!-- META -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="\">
    <!-- End: ../view/hotsite/global/head.html -->
    <script type="text/javascript" src="js/hotsite/signin.js"></script> 
<script type="text/javascript">
function abre_modal(de_onde){
document.getElementById('modal').style.display = 'block';
//document.getElementById('modal').getElementsByTagName('dl')[0].style.top = ((window.innerHeight - 380)/2) + "px";
document.getElementById('modal').getElementsByTagName('dl')[0].style.left = ((window.innerWidth - 940)/2) + "px";
}

function fecha_modal(de_onde){
document.getElementById('modal').style.display = 'none';
}

</script>


  </head>

<body id="conteudo">
<div id="modal">
<dl>
<dt>
<span onclick="fecha_modal()">Fechar | X</span>
<h4>Tema</h4>
<p>Nome do tema</p>

<h4>Desafio</h4>
<p>Nome do desafio pra onde esta resposta foi postada</p>
<p><br></p>
<p>Postado em<br><time>12.12.2013 - 17h45</time></p>

<div><img src="imagens/ico_curtir.gif" width="36" height="36"> 12</div>
<br class="tudo">
</dt>
<dd>
<img src="images/minininha.jpg" width="700" height="700" alt="Menininha meu amor"><br class="tudo">
</dd>
</dl>
</div>
<?php include "header.inc" ;?>
<section>
<h3>Aderbalzinho</h3>
<br class="tudo">

<dl id="respostas">
<dt>
<div class="aviso amarelo">
<p>
Ainda não recebemos a autorização do seu cadastro pelo seu responsável.<br>
Enquanto você não estiver autorizado, só você vê as respostas aos desafios que você postou!
</p>
</div>
</dt>

<dd>
<figure onclick="abre_modal(this)">
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>

<strong>12.12.2013</strong>
</figcaption>
</figure>

<figure onclick="abre_modal(this)">
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
    
<strong>12.12.2013</strong>
</figcaption>
</figure>

<figure onclick="abre_modal(this)">
<div>
<p><span>Mauris bibendum lectus in neque semper mollis. Donec interdum egestas sem vitae congue. Vestibulum quis quam euismod, mattis dolor.</span></p>
</div>
<figcaption>
<span>
<img src="imagens/ico_curtir.gif" width="36" height="36">
13</span>
    
<strong>12.12.2013</strong>
</figcaption>
</figure>

<figure class="oculto" onclick="abre_modal(this)">
<img src="imagens/minininha.jpg" height="285" width="285" alt="Kidu">
<figcaption>
<strong>Conteúdo oculto</strong>
<a href="">Por quê?</a>
</figcaption>
</figure>
</dd>
</dl>
</section>

<footer id="logado">
<div>
<div>
<strong>Jubileuzinho</strong><span><a href="index.php">ajuda</a> | <a href="index.php">sair</a></span>
</div>
</div>
</footer>

</body>
</html>