$(function() {
    'use strict';


    var colors = {
        primary        : "#6571ff",
        secondary      : "#7987a1",
        success        : "#05a34a",
        info           : "#66d1d1",
        warning        : "#fbbc06",
        danger         : "#ff3366",
        light          : "#e9ecef",
        dark           : "#060c17",
        muted          : "#7987a1",
        gridBorder     : "rgba(77, 138, 240, .15)",
        bodyColor      : "#b8c3d9",
        cardBg         : "#0c1427"
    }

    var fontFamily = "'Roboto', Helvetica, sans-serif"

    // Pie Chart
    $.plot($('#userStatusFlotPie'), formattedUserStatusData, {
        series: {
            shadowSize: 0,
            pie: {
                show: true,
                radius: 1,
                innerRadius: 0.5,
                stroke: {
                    color: colors.cardBg,
                    width: 3
                },
                label: {
                    show: true,
                    radius: 3 / 4,
                    background: { opacity: 0.5 },
                    formatter: function(label, series) {
                        const count = series.data[0][1]; // Extract count
                        const percent = Math.round(series.percent); // Extract percent
                        return `<div style="font-size:11px;text-align:center;color:white;">
                                    ${label}<br>${count} (${percent}%)
                                </div>`;
                    }
                }
            }
        },

        grid: {
            color: colors.bodyColor,
            borderColor: colors.gridBorder,
            borderWidth: 1,
            hoverable: true,
            clickable: true
        },

        xaxis: { tickColor: colors.gridBorder },
        yaxis: { tickColor: colors.gridBorder },
        legend: { backgroundColor: colors.cardBg },
        colors: [colors.primary, colors.success, colors.warning, colors.danger]
    });

});
