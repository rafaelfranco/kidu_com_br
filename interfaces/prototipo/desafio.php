<?php include "host.inc" ?>
<!doctype html>
<html>

<head>
<title>Kidu</title>
<meta charset="utf-8" />
<!-- <meta http-equiv="refresh" content="5"> -->
<link href='http://fonts.googleapis.com/css?family=Finger+Paint' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="css/geral.css">
</head>

<body id="desafio">
<?php include "header.inc" ;?>
<section>
<h2>Desafio</h2>

<?php
$dados_desafio = json_decode(utf8_decode(file_get_contents("{$host}/services/api/rest/json/?method=group.get&username=kidu&limit=1&context=all&offset=0&guid=96")));
//echo file_get_contents("http://192.168.0.21:83/kidu/services/api/rest/json/?method=group.get&username=kidu&limit=1&context=all&offset=0&guid=96");
//{"status":0,"result":{"name":"Quais s\u00e3o as suas dicas para arrasar no seu game favorito? Grave seu depoimento e poste aqui!","owner_name":"Kidu Administrador","members_count":1,"fields":{"description":{"label":"About me","type":"longtext","value":""},"briefdescription":{"label":"Brief description","type":"text","value":""},"interests":{"label":"Interests","type":"tags","value":""}},"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/mod\/groups\/graphics\/defaultmedium.gif","enabled_options":[{"name":"bookmarks","label":"Enable group bookmarks","default_on":true},{"name":"file","label":"Enable group files","default_on":true},{"name":"activity","label":"Enable group activity","default_on":true},{"name":"forum","label":"Enable group discussion","default_on":true},{"name":"pages","label":"Enable group pages","default_on":true},{"name":"subgroups","label":"Subgroups: Enable Sub-Groups for this group?","default_on":true},{"name":"subgroups_members_create","label":"Subgroups: Enable any member to create subgroups? (if no, only group admins will be able to create subgroups)","default_on":true}]}} 
?>

<article>
<h3><?php echo $dados_desafio->result->name; ?></h3>
<div id="video">
<iframe width="560" height="315" src="//www.youtube.com/embed/vXb9Mptko2I?rel=0" frameborder="0" allowfullscreen></iframe>
</div>
<form enctype="multipart/form-data" method="post" action="minha_pagina.php">
<figure>
<embed src="imagens/foguete.svg" type="image/svg+xml"> 
<figcaption>Ganhe este selo!</figcaption>
</figure>
<p><?php echo $dados_desafio->result->fields->description->value; ?></p>
<span id="envia_arquivo">
<button>Selecionar arquivo...</button> Nenhum arquivo
<!--input type="file"-->
</span>
</form>
</article>

</section>
<br class="tudo">
<section>
<!-- <h3>Respostas a este desafio</h3>
http://192.168.0.21:83/kidu/services/api/rest/xml/?method=file.get_files&limit=5&context=group&offset=0&group_guid=96 -->

<article>

</article>
</section>

</body>
</html>