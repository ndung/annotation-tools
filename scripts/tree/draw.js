
function drawTree(bag) {
    var lastScrollX = $('#svg-wrapper').scrollLeft();
    $('#svg-wrapper').empty();
    $('#svg-wrapper').append($('<svg id="svg-canvas" class="display" width="2000" height="600">'));
    // Create the input graph
    var g = new dagreD3.Digraph();

    // Here we're setting nodeclass, which is used by our custom drawNodes function
    // below.        
    for (var i = 1; i < bag.nodes.length; i++) {
        var node = bag.nodes[i];
        g.addNode(node.ID, {label: node.name, nodeclass: node.type, ID: node.ID});
    }
    for (var i = 0; i < bag.links.length; i++) {
        var link = bag.links[i];
        g.addEdge(null, link.source.ID, link.target.ID);
    }

    // Create the renderer
    var renderer = new dagreD3.Renderer();

    // Override drawNodes to add nodeclass as a class to each node in the output
    // graph.
    var oldDrawNodes = renderer.drawNodes();
    renderer.drawNodes(function(graph, root) {
        var svgNodes = oldDrawNodes(graph, root);
        svgNodes.each(function(u) {
            d3.select(this).classed(graph.node(u).nodeclass, true);
            d3.select(this).attr('node-id', graph.node(u).ID);
        });
        return svgNodes;
    });
    // Disable pan and zoom
    renderer.zoom(false);

    // Set up an SVG group so that we can translate the final graph.
    var svg = d3.select('svg.display')
				.call(d3.behavior.zoom().on("zoom", function() {
				 var ev = d3.event;
				 svg.select("g")
					.attr("transform", "translate(" + ev.translate + ") scale(" + ev.scale + ")");
			   })),
            svgGroup = svg.append('g');

    // Run the renderer. This is what draws the final graph.
    var layout = renderer.run(g, d3.select('svg g'));

    // Center the graph
    var layoutWidth = layout.graph().width ? layout.graph().width : 2000;
    var layoutHeight = layout.graph().height ? layout.graph().height : 600;
    svg.attr('width', layoutWidth + 40);
    var xCenterOffset = (svg.attr('width') - layoutWidth) / 2;
    svgGroup.attr('transform', 'translate(' + xCenterOffset + ', 1)');
    svg.attr('height', layoutHeight + 40);
    $('#svg-wrapper').scrollLeft(lastScrollX);
}