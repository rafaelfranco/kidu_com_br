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
<h2>Tema</h2>

<?php
$dados_desafio = json_decode(utf8_decode(file_get_contents("{$host}/services/api/rest/json/?method=group.get&username=kidu&limit=1&context=all&offset=0&guid=" . $_GET['guid'])));
//echo file_get_contents("http://192.168.0.21:83/kidu/services/api/rest/json/?method=group.get&username=kidu&limit=1&context=all&offset=0&guid=" . $_GET['guid']);
// {"status":0,"result":{"name":"Zumbis X Unic\u00f3rnios (sobre zumbis e unic\u00f3rnios...)","owner_name":"Kidu Administrador","members_count":4,"fields":{"description":{"label":"About me","type":"longtext","value":"Where others see stairs, handrails, curbs, and roads, Skaters see a stage for adventure and exploration. With style and bravery, Skaters reinvent the possible."},"briefdescription":{"label":"Brief description","type":"text","value":"Skaters find hidden purpose in our built environment."},"interests":{"label":"Interests","type":"tags","value":"Skate"}},"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/groupicon\/61\/medium\/1379444029.jpg","enabled_options":[{"name":"bookmarks","label":"Enable group bookmarks","default_on":true},{"name":"file","label":"Enable group files","default_on":true},{"name":"activity","label":"Enable group activity","default_on":true},{"name":"forum","label":"Enable group discussion","default_on":true},{"name":"pages","label":"Enable group pages","default_on":true},{"name":"subgroups","label":"Subgroups: Enable Sub-Groups for this group?","default_on":true},{"name":"subgroups_members_create","label":"Subgroups: Enable any member to create subgroups? (if no, only group admins will be able to create subgroups)","default_on":true}]}} 
?>

<article>
<h3><?php echo $dados_desafio->result->name; ?></h3>
<div id="video">
<iframe width="560" height="315" src="//www.youtube.com/embed/vXb9Mptko2I?rel=0" frameborder="0" allowfullscreen></iframe>
</div>
<form enctype="multipart/form-data" method="post" action="minha_pagina.php">
<figure>
<!-- <embed src="imagens/foguete.svg" type="image/svg+xml">  -->
<img src="<?php echo $dados_desafio->result->avatar_url; ?>" alt="Tema">
<figcaption>Ganhe este selo!</figcaption>
</figure>
<p><?php echo $dados_desafio->result->fields->description->value; ?></p>
</form>
</article>
</section>
<br class="tudo">
<section>
<!-- <h3>Desafios deste tema</h3>
<article>
</article> -->
</section>

</body>
</html>