/**
 *  Functions.js
 *
 *  This file contains the functions required for visualising the graph using d3js.
 *  Any extra functionality can be sent to rexvalkering@gmail.com
 *
 *  Current functionality: 
 *  Future functionality: autocomplete, show list of names with edges.
 */


// Global arrays.
var nodes = [];
var nodeshelp = [];
var links = [];
var linkedByIndex = [];

// Variables for...
var node;
var svg;
var link;
var texts;
var lastClicked = -1;
var force;

var charge = -600;
var distance = 30;

/**
 *  Get the nodes and edges and visualize them. This is done in sequential order to avoid
 *  errors.
 */
jQuery.getJSON(
    "/app/getnodes.php",
    function(results)
    {
        var offset = 0;
        
        // Loop through all nodes.
        jQuery.each(results, function(key, values)
        {
            // Create node.
            nodes[offset] = {"name": values['name'], "group" : 1, "linkcount" : 1};

            // This array is a helper for adding edges.
            nodeshelp[values['id']] = offset;
            offset++;
        });

        // Get the edges.
        jQuery.getJSON(
            "/app/getedges.php",
            function(results)
            {

                // Loop through all edges and store them in the correct array.
                jQuery.each(results, function(key, values) {
                    var n1 = nodeshelp[values['node_1']];
                    var n2 = nodeshelp[values['node_2']]
                    links.push({"source" : n1, "target": n2, "value": 1, "stroke": values["type"]});
                    linkedByIndex[(n1 + "," + n2)] = true;
                    nodes[n1].linkcount++;
                    nodes[n2].linkcount++;
                });

                // Create the graph.
                startGraph();
            }
        );
    }
);

/**
 *  Function: startGraph
 *
 *  This function constructs and visualizes a graph with d3js, using the force directed layout.
 *  Current functionality includes:     showing nodes and edges.
 *  Future functionality:               clicking on a node centers and recolours it, and shows its neighbours.
 *                                      difference between edge types 
 */
function startGraph() {

    var width = jQuery('#graph-width').val(),
        height = jQuery('#graph-height').val();

    var color = d3.scale.category20();

    // The object containing the graph.
    force = d3.layout.force()
        .charge(charge)
        .linkDistance(distance)
        .size([width, height]);

    svg = d3.select("body").append("svg")
        .attr("width", width)
        .attr("height", height)
        .on("mousedown", click);

    force
        .nodes(nodes)
        .links(links)
        .start();

    link = svg.selectAll(".link")
        .data(links)
        .enter().append("line")
        .attr("class", "link")
        .style("stroke-width", function(d) { return Math.sqrt(d.value); })
        .style("stroke", function(d) { return d.stroke; });

    node = svg.selectAll(".node")
        .data(nodes)
        .enter().append("circle")   
        .attr("class", "node")
        .attr("r", function(d) { return 3 + 2 * Math.sqrt(d.linkcount); })
        .style("fill", function(d) { return color(d.group); })
        .call(force.drag)
        .on("click", fade(.8, .3));

    texts = svg.selectAll("text.label")
        .data(nodes)
        .enter().append("text")
        .attr("class", "label")
        .attr("fill", "black")
        .text(function(d) {  return d.name;  });

    force.on("tick", function() {
        link.attr("x1", function(d) { return d.source.x; })
            .attr("y1", function(d) { return d.source.y; })
            .attr("x2", function(d) { return d.target.x; })
            .attr("y2", function(d) { return d.target.y; });

        node.attr("cx", function(d) { return d.x; })
            .attr("cy", function(d) { return d.y; });

        texts.attr("transform", function(d) {
            return "translate(" + d.x + "," + d.y + ")";
        });
    });

};

function isConnected(a, b) {
    return linkedByIndex[a.index + "," + b.index] || linkedByIndex[b.index + "," + a.index] || a.index == b.index;
}

function click() {
    if (lastClicked > -1)
    {
        node.style("stroke-opacity", 1.0);
        link.style("stroke-opacity", 1.0);
        texts.style("display", "block");
        lastClicked = -1;
    }
};

function fade(opa1, opa2) {
    return function(d) {
        console.log("called");

        if (lastClicked > -1 && (lastClicked == d.index)) {
            lastClicked = -1;


            node.style("stroke-opacity", function() {console.log("gah"); return 1.0;});
            link.style("stroke-opacity", 1.0);
            texts.style("display", "block");
            return;
        }

        node.style("stroke-opacity", function(o) {
            if (o.index == d.index) return 1.0;
            thisOpacity = isConnected(d, o) ? opa1 : opa2;
            this.setAttribute('fill-opacity', thisOpacity);
            return thisOpacity;
        });

        link.style("stroke-opacity", opa2).style("stroke-opacity", function(o) {
            return o.source === d || o.target === d ? opa1 : opa2;
        });

        texts.style("display", function(o) {
            if (isConnected(d, o)) {
                console.log(d.name + " and " + o.name + " connected.");
                return "block";
            }
            return "none";
        });

        lastClicked = d.index;
    };
}