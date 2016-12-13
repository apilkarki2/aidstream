var w = 1200;
var h = 600;

d3.json("/data/countries.geo.json", function (countries) {
    worldMap(recipientCountries);
    function worldMap(countryNames) {
        var divNode = d3.select("#map").node();
        var canvas = d3.select("#map")
            .attr("preserveAspectRatio", "xMinYMin meet")
            .append("svg")
            .attr("width", w)
            .attr("height", h)
            .attr("viewBox", "0 0 804 621")
            .classed("svg-content-resonsive", true);

        var group = canvas.selectAll("g")
            .data(countries.features)
            .enter()
            .append("g");

        var projection = d3.geo.mercator()
            .scale(130)
            .translate([450, 450]);

        var geoPath = d3.geo.path().projection(projection);

        var plotMap = group.append("path")
            .attr("d", geoPath)
            .style("fill", function (d) {
                if (countryNames[d.id2] != undefined)
                    return "#00A8FF";
                else
                    return "#D9E5EB";

            })
            .attr("stroke", "#fff")

            .attr("stroke-width", "0.5px")
            .attr("countries", function (d) {
                return d.id2;
            });

        plotMap.on('mouseover', function (d) {
            console.log('Mouse overed');
            // var districtName = d.properties.DISTRICT.charAt(0).toUpperCase() + d.properties.DISTRICT.slice(1).toLowerCase();
            // var districtData = valueById[d.properties.DISTRICT.toLowerCase()];
            var absoluteMousePos = d3.mouse(divNode);
            d3.select("#tooltip")
                .style("left", absoluteMousePos[0] + "px")
                .style("top", absoluteMousePos[1] + 80 + "px")
                .style("position", "absolute")
                .attr("text-anchor", "middle")
                .attr("font-size", "14px")
                .html("TOol tip show");

            d3.select("#tooltip").classed("tooltips", false);
        })
            .on('mouseout', function (d) {
                d3.select("#tooltip").classed("tooltips", true);
            });

    }
});

