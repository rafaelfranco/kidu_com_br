function chama_ajax(metodo,onde,volta,parametros){
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