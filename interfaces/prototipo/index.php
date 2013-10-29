<?php 
include "host.inc";

// if(isset($_POST['username']) && isset($_POST['password'])){
// http_post_data("http://elasticainterativa.com.br/kidu/services/api/rest/xml/?method=auth.gettoken",array('password' => $_POST['password'], 'username' => $_POST['username']));
// }
?>


<!doctype html>
<html>

<head>
<title>Kidu</title>
<meta charset="utf-8" />
<!-- <meta http-equiv="refresh" content="5"> -->
<link href='http://fonts.googleapis.com/css?family=Finger+Paint' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="css/geral.css">
<script type="text/javascript">
window.onload = function(){
	juca = setTimeout("muda_opacidade()",100);
}

function muda_calca(){
document.getElementById('SVGID_12_').getElementsByTagName('stop')[0].style.stopColor = '#FFCC00';
document.getElementById('SVGID_12_').getElementsByTagName('stop')[1].style.stopColor = '#FFCC00';
}

function muda_opacidade(){
// document.getElementById('marca').style.opacity = 1;
// document.getElementById('texto').style.opacity = 1;
}

function loadXMLDoc(metodo,onde,volta,parametros){
  if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  } else {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }

xmlhttp.onreadystatechange=function(){
    if (xmlhttp.readyState==4 && xmlhttp.status==200){
    eval(volta + '(xmlhttp.responseText)'); //aqui eu chamo a função com o texto de resposta
    }
  }
xmlhttp.open(metodo,onde,true);
    if(metodo == "POST"){xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")};
xmlhttp.send(parametros);
}

function logar(de_onde){
//alert(de_onde.form.password.value);
loadXMLDoc("POST","http://elasticainterativa.com.br/kidu/services/api/rest/xml/?method=auth.gettoken","alerta_me","username=" + de_onde.form.username.value + "&password=" + de_onde.form.password.value);
}

function alerta_me(resp){
alert(resp);
}
</script>
</head>

<body id="home">
<header>
<nav>
<a href="">Para pais</a> | <a href="">Para educadores</a> <a href="cadastro_aluno.php" id="faca_parte">Faça parte</a>
</nav>
<!-- http://elasticainterativa.com.br/kidu/services/api/rest/xml/?method=system.api.list -->
<form action="http://elasticainterativa.com.br/kidu/services/api/rest/xml/?method=auth.gettoken" method="post">
<!-- <form action="" method="post"> -->
<fieldset id="login">
<label>login</label> <input type="text" size="15" name="username"><br>
<label>senha</label> <input type="password" size="15" name="password"><br>
<!-- <button onclick="logar(this); return false;">OK</button> -->
<input type="submit" name="legal" value="vai">
</fieldset>
</form>
</header>

<h1>Kidu</h1>

<section>
<a href="desafio.php" id="desafio">Desafio</a>

<?php
$dados_tema = json_decode(utf8_decode(file_get_contents("{$host}/services/api/rest/json/?method=group.get_groups&username=kidu&limit=3&context=featured&offset=0")));
//echo file_get_contents("http://192.168.0.21:83/kidu/services/api/rest/json/?method=group.get_groups&username=kidu&context=all&offset=0");
//{"status":0,"result":[{"guid":96,"name":"Quais s\u00e3o as suas dicas para arrasar no seu game favorito? Grave seu depoimento e poste aqui!","members":1,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/mod\/groups\/graphics\/defaultsmall.gif"},{"guid":95,"name":"Game ou site que gostaria de inventar: ilustrar a ideia.","members":1,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/mod\/groups\/graphics\/defaultsmall.gif"},{"guid":94,"name":"Game ou site que gostaria de inventar: escrever a ideia.","members":1,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/mod\/groups\/graphics\/defaultsmall.gif"},{"guid":93,"name":"\u00c9 muito bom em algum game? Fotografe ou filme a fase final e poste aqui!","members":1,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/mod\/groups\/graphics\/defaultsmall.gif"},{"guid":92,"name":"Citar at\u00e9 tr\u00eas sites com reviews de 3 games favoritos.","members":1,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/mod\/groups\/graphics\/defaultsmall.gif"},{"guid":83,"name":"Customize Your Board","members":1,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/groupicon\/83\/small\/1379454511.jpg"},{"guid":82,"name":"Skate a Park","members":3,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/groupicon\/82\/small\/1379454045.jpg"},{"guid":62,"name":"Mundo Fashion (sobre moda e estilo)","members":2,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/groupicon\/62\/small\/1379444262.jpg"},{"guid":61,"name":"Zumbis X Unic\u00f3rnios (sobre zumbis e unic\u00f3rnios...)","members":4,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/groupicon\/61\/small\/1379444029.jpg"},{"guid":60,"name":"Geeks & Cia (sobre games e internet)","members":4,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/groupicon\/60\/small\/1379442597.jpg"}]}
?>
<ul>
<li>
<figure onclick="window.location='tema.php?guid=<?php echo $dados_tema->result[0]->guid;?>'"><img src="<?php echo $dados_tema->result[0]->avatar_url; ?>" width="300" height="300" alt="Tema 1">
<figcaption>
<?php echo $dados_tema->result[0]->name; ?>
</figcaption>
</figure>
</li>

<li>
<figure onclick="window.location='tema.php?guid=<?php echo $dados_tema->result[1]->guid;?>'"><img src="<?php echo $dados_tema->result[1]->avatar_url; ?>" width="300" height="300" alt="Tema 2">
<figcaption>
<?php echo $dados_tema->result[1]->name; ?>
</figcaption>
</figure>
</li>

<li>
<figure onclick="window.location='tema.php?guid=<?php echo $dados_tema->result[2]->guid;?>'"><img src="<?php echo $dados_tema->result[2]->avatar_url; ?>" width="300" height="300" alt="Tema 3">
<figcaption>
<?php echo $dados_tema->result[2]->name; ?>
</figcaption>
</figure>
</li>
</ul>
</section>

</body>
</html>