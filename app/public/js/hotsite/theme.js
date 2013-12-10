//home page after login
jubileu = function(){
	if($('#searchInput').val() == ''){
	$.ajax({
		url: '/action/getgroups/',
		type: 'POST',
		data: { 
				text: $('#searchInput').val()
				},
	        success: function(json) {
	        	$('#themeList').html(json);
	        }
	});
	} else {
	busca_tudo();
	}
}

busca_tudo = function(){
$('#themeList').html('Buscando temas e desafios...');
$.ajax({
		url: '/action/busca_tudo/',
		type: 'POST',
		data: { 
				text: $('#searchInput').val()
				},
	        success: function(json) {
	        resposta = JSON.parse(json);
	        	if(resposta.status != -1){
		        html = "<dl>";
		        	for (i=0; i<resposta.result.length; i++) {
		        	html += "<dt>\n";
		        	html += "<a href='/theme/view/" + resposta.result[i].guid + "'><img src='" + resposta.result[i].avatar_url + "' width='200' height='200' border='0'></a><br>\n";
		        	html += "<a href='/theme/view/" + resposta.result[i].guid + "'>" + resposta.result[i].name;
		        	html += "</dt><dd><ul>\n";
		        		for(y=0;y<resposta.result[i].subgrupos.result.length;y++){
		        		html += "<li><strong>▶</strong> <a href='/theme/challenge/" + resposta.result[i].guid + "/" + resposta.result[i].subgrupos.result[y].guid + "'>" + resposta.result[i].subgrupos.result[y].name + "</a></li>\n";
		        		}
		        	html += "</ul></dd>\n";
		        	};
		        html += "</dl>\n";
		        } else {
		        html = "<h4>Não houve resultado...</h4>\n";
		        }
	        $('#themeList').html(html);
	        }
	});
}

$(document).ready(function() {
jubileu();

	$('#searchBottom').click(function() {
		if($('#inputSearch').val() == ''){
			alert('Preencha a pesquisa!');
		} else {
			//window.location = '/home/'+$('#inputSearch').val();
			busca_tudo();
		}
	});

});