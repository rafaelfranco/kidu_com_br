$(document).ready( function() {
var canvas = document.getElementById('canvas'),
paper = new Raphael(canvas, 500, 500),
colour = '#000000',
mousedown = false,
width = 1,
lastX, lastY, path, pathString;

	$(canvas).mousedown(function (e) {
	mousedown = true;
	var offset = $(canvas).offset(),
    x = e.pageX - Math.round(offset.left),
	y = e.pageY - Math.round(offset.top);
	pathString = 'M' + x + ' ' + y + 'l0 0';
	path = paper.path(pathString);
		path.attr({
		'stroke': colour,
		'stroke-linecap': 'round',
		'stroke-linejoin': 'round',
		'stroke-width': width
		});

	lastX = x;
	lastY = y;
	});

		$(document).mouseup(function () {
		mousedown = false;
		});

		$(canvas).mousemove(function (e) {
			if (!mousedown) {
			return;
			}

	var offset = $(canvas).offset(),
    x = e.pageX - Math.round(offset.left),
	y = e.pageY - Math.round(offset.top);

	pathString += 'l' + (x - lastX) + ' ' + (y - lastY);
	path.attr('path', pathString);
	lastX = x;
	lastY = y;
	});


	/** save our image */
	$('#salvar_imagem').click( function(event) {
	// note that this is a 2 step process
	// due to IE stupidity
	// parse the paper object and send the attributes to our php script
	// it will return the full svg via ajax, which we can then convert
	// to png, and send that back to php for saving to the filesystem
	event.preventDefault();
	var json_svg = buildJSON(paper);

		$.ajax({
			url: '/action/converteSVG',
			type: 'POST',
			async:false,
			data: { imgdata: json_svg },

			success: function(response) {
			canvg(document.getElementById('realcanvas'), response, {ignoreDimensions: true});
			var realCanvas = document.getElementById('realcanvas');
			var img = realCanvas.toDataURL("image/png");
			$('#draw-image-result').val(img);
			$('#salva_avatar').submit();
			},

			fail: function() {
			alert('Não deu certo...');
			}
		});
	});

	/** Initiate the range picker **/

    $("#draw-slider").slider({
	value:1,
	min: 1,
	max: 100,
	step: 1,
		slide: function( event, ui ) {
        width = ui.value;
        $('#lineWidth-sample').text(ui.value);
		}
    });

	/**  Initiate a colorpicker */

	Raphael.colorwheel($("#colorSelector")[0],120,250).color("#000000").onchange(function(c){
	colour = c.hex;
	$('#colorSelector-sample').css('backgroundColor', c.hex);
	});

	/** reset the page */

	$("#draw-reset").click( function(event) {
	event.preventDefault();
	paper.clear();
	});
});