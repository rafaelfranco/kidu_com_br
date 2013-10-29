<html>
  <head>
    <!-- Start: ../view/hotsite/global/head.html -->
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css" />
    <link rel="stylesheet" href="css/jquery-ui.css" /> 
    
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
  </head>

<body id="opine">
<?php include "header.inc" ;?>
<br class="tudo">
<section>
<h2>Opine</h2>
<form id="mensagem">
<fieldset>
<label>[!name!]</label><br>
<input class="input" type="text"  id="name">
<span class="erro" id="name-erro">[!name-empty!]</span><br>

<label>[!email!]</label><br>
<input class="input" type="text"  id="email">
<span class="erro" id="email-erro">[!email-empty!]</span><br>

<label>[!message!]</label><br>
<textarea id="message" name="message"></textarea>
<span class="erro" id="father-email-erro">[!father-email-empty!]</span><br>

<span id="sendMessage" class="botao">[!send!]</span><br>
</fieldset>

<article>
<p>Precisa de uma ajuda, quer dizer o que achou? Quer compartilhar sua experiência? Ajuda o Kidu a ser a referência mais inteligente da internet compartilhando com a gente o que você quer.

<p>Se preferir, mande mensagem para <a href="mailto:info@kidu.com.br">info@kidu.com.br</a></p>
</article>

</form>
<br class="tudo">
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