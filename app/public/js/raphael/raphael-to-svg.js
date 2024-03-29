// loop through the 'paper' variable from Raphael JS and build up the JSON object describing all images and paths within it.
loadJSON = function(paper, json) {
  var set = paper.set();
  $.each(json, function(index, node) {
    try {
      var el = paper[node.type]().attr(node);
      set.push(el);
    } catch(e) {}
  });
  return set;
};

buildJSON = function(paper) {
    var svgdata = [];
    svgdata.push({
		width: paper.width,
		height: paper.height
	});

    for(var node = paper.bottom; node != null; node = node.next) {
      if (node && node.type) {
        switch(node.type) {
          case "image":
            var object = {
              type: node.type,
              width: node.attrs['width'],
              height: node.attrs['height'],
              x: node.attrs['x'],
              y: node.attrs['y'],
              src: node.attrs['src'],
              transform: node.transformations ? node.transformations.join(' ') : ''
            }
            break;
          case "ellipse":
            var object = {
              type: node.type,
              rx: node.attrs['rx'],
              ry: node.attrs['ry'],
              cx: node.attrs['cx'],
              cy: node.attrs['cy'],
              stroke: node.attrs['stroke'] === 0 ? 'none': node.attrs['stroke'],
              'stroke-width': node.attrs['stroke-width'],
              fill: node.attrs['fill']
            }
            break;
          case "rect":
            var object = {
              type: node.type,
              x: node.attrs['x'],
              y: node.attrs['y'],
              width: node.attrs['width'],
              height: node.attrs['height'],
              stroke: node.attrs['stroke'] === 0 ? 'none': node.attrs['stroke'],
              'stroke-width': node.attrs['stroke-width'],
              fill: node.attrs['fill']
            }
            break;
          case "text":
            var object = {
              type: node.type,
              font: node.attrs['font'],
              'font-family': node.attrs['font-family'],
              'font-size': node.attrs['font-size'],
              stroke: node.attrs['stroke'] === 0 ? 'none': node.attrs['stroke'],
              fill: node.attrs['fill'] === 0 ? 'none' : node.attrs['fill'],
              'stroke-width': node.attrs['stroke-width'],
              x: node.attrs['x'],
              y: node.attrs['y'],
              text: node.attrs['text'],
              'text-anchor': node.attrs['text-anchor']
            }
            break;

          case "path":
            var path = "";

            $.each(node.attrs['path'], function(i, group) {
              $.each(group,
                function(index, value) {
                  if (index < 1) {
                      path += value;
                  } else {
                    if (index == (group.length - 1)) {
                      path += value;
                    } else {
                     path += value + ',';
                    }
                  }
                });
            });

            var object = {
              type: node.type,
              fill: node.attrs['fill'],
              opacity: node.attrs['opacity'],
              translation: node.attrs['translation'],
              scale: node.attrs['scale'],
              path: path,
              stroke: node.attrs['stroke'] === 0 ? 'none': node.attrs['stroke'],
              'stroke-width': node.attrs['stroke-width'],
              transform: node.transformations ? node.transformations.join(' ') : ''
            }
        }

        if (object) {
          svgdata.push(object);
        }
      }
    }

    return(JSON.stringify(svgdata));
};