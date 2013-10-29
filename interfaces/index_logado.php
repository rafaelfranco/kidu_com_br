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
  <body id="bodyHome" >
    <nav>
      <ul>
        <li><a href="/fathers">Para Pais</a></li>
        <li><a href="/teachers">Para Educadores</a></li>
      </ul>
    </nav>
    <div id="homeLogo"><img src="images/marca_kidu_home.gif" alt="Kidu" width="463" height="168">
<img src="images/selo_beta.png" alt="Kidu" width="173" height="175" class="selo">
    </div>

<form id="busca_home">
<input type="text">
<span class="botao2">Buscar</span>
</form>

<section id="temas">

<?php
$host = 'http://kidu.com.br/engine';
$dados_tema = json_decode(utf8_decode(file_get_contents("{$host}/services/api/rest/json/?method=group.get_groups&username=kidu&limit=3&context=featured&offset=0")));
//echo file_get_contents("http://192.168.0.21:83/kidu/services/api/rest/json/?method=group.get_groups&username=kidu&context=all&offset=0");
//{"status":0,"result":[{"guid":96,"name":"Quais s\u00e3o as suas dicas para arrasar no seu game favorito? Grave seu depoimento e poste aqui!","members":1,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/mod\/groups\/graphics\/defaultsmall.gif"},{"guid":95,"name":"Game ou site que gostaria de inventar: ilustrar a ideia.","members":1,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/mod\/groups\/graphics\/defaultsmall.gif"},{"guid":94,"name":"Game ou site que gostaria de inventar: escrever a ideia.","members":1,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/mod\/groups\/graphics\/defaultsmall.gif"},{"guid":93,"name":"\u00c9 muito bom em algum game? Fotografe ou filme a fase final e poste aqui!","members":1,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/mod\/groups\/graphics\/defaultsmall.gif"},{"guid":92,"name":"Citar at\u00e9 tr\u00eas sites com reviews de 3 games favoritos.","members":1,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/mod\/groups\/graphics\/defaultsmall.gif"},{"guid":83,"name":"Customize Your Board","members":1,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/groupicon\/83\/small\/1379454511.jpg"},{"guid":82,"name":"Skate a Park","members":3,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/groupicon\/82\/small\/1379454045.jpg"},{"guid":62,"name":"Mundo Fashion (sobre moda e estilo)","members":2,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/groupicon\/62\/small\/1379444262.jpg"},{"guid":61,"name":"Zumbis X Unic\u00f3rnios (sobre zumbis e unic\u00f3rnios...)","members":4,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/groupicon\/61\/small\/1379444029.jpg"},{"guid":60,"name":"Geeks & Cia (sobre games e internet)","members":4,"avatar_url":"http:\/\/192.168.0.21:83\/kidu\/groupicon\/60\/small\/1379442597.jpg"}]}
?>
<ul>
<li>
<figure onclick="window.location='tema.php?guid=<?php echo $dados_tema->result[0]->guid;?>'"><img src="<?php echo $dados_tema->result[0]->avatar_url; ?>" width="200" height="200" alt="Tema 1">
<figcaption>
<?php echo $dados_tema->result[0]->name; ?>
</figcaption>
</figure>
</li>

<li>
<figure onclick="window.location='tema.php?guid=<?php echo $dados_tema->result[1]->guid;?>'"><img src="<?php echo $dados_tema->result[1]->avatar_url; ?>" width="200" height="200" alt="Tema 2">
<figcaption>
<?php echo $dados_tema->result[1]->name; ?>
</figcaption>
</figure>
</li>

<li>
<figure onclick="window.location='tema.php?guid=<?php echo $dados_tema->result[2]->guid;?>'"><img src="<?php echo $dados_tema->result[2]->avatar_url; ?>" width="200" height="200" alt="Tema 3">
<figcaption>
<?php echo $dados_tema->result[2]->name; ?>
</figcaption>
</figure>
</li>
</ul>
</section>
    <!-- Start: ../view/hotsite/global/footer.html -->
<footer id="logado">
<div>
<div>
<strong>Jubileuzinho</strong> <a href="index.php">Sair</a>
</div>
</div>
</footer>
    <!-- End: ../view/hotsite/global/footer.html -->
  </body>
</html>