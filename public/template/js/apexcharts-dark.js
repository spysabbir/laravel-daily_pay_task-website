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

    // totalWorkedTaskApexLine start
    if ($('#totalWorkedTaskApexLine').length) {
        var lineChartOptions = {
            chart: {
                type: "line",
                height: '320',
                parentHeightOffset: 0,
                foreColor: colors.bodyColor,
                background: colors.cardBg,
                toolbar: {
                    show: false
                },
            },
            theme: {
                mode: 'dark'
            },
            tooltip: {
                theme: 'dark'
            },
            colors: [colors.info, colors.success, colors.danger, colors.warning],
            grid: {
                padding: {
                    bottom: -4
                },
                borderColor: colors.gridBorder,
                xaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            series: [
                {
                    name: totalWorkedTaskApexLineData.series[0].name,
                    data: totalWorkedTaskApexLineData.series[0].data
                },
                {
                    name: totalWorkedTaskApexLineData.series[1].name,
                    data: totalWorkedTaskApexLineData.series[1].data
                },
                {
                    name: totalWorkedTaskApexLineData.series[2].name,
                    data: totalWorkedTaskApexLineData.series[2].data
                },
                {
                    name: totalWorkedTaskApexLineData.series[3].name,
                    data: totalWorkedTaskApexLineData.series[3].data
                }
            ],
            xaxis: {
                categories: totalWorkedTaskApexLineData.categories,
                lines: {
                    show: true
                },
                axisBorder: {
                    color: colors.gridBorder,
                },
                axisTicks: {
                    color: colors.gridBorder,
                },
            },
            markers: {
                size: 0,
            },
            legend: {
                show: true,
                position: "top",
                horizontalAlign: 'center',
                fontFamily: fontFamily,
                itemMargin: {
                    horizontal: 8,
                    vertical: 0
                },
            },
            stroke: {
                width: 3,
                curve: "smooth",
                lineCap: "round"
            },
        };
        var apexLineChart = new ApexCharts(document.querySelector("#totalWorkedTaskApexLine"), lineChartOptions);
        apexLineChart.render();
    }
    // totalWorkedTaskApexLine end


    // totalApprovedPostedTaskChargeApexDonut start
    if ($('#totalApprovedPostedTaskChargeApexDonut').length) {
        var options = {
            chart: {
                height: 300,
                type: "donut",
                foreColor: colors.bodyColor,
                background: colors.cardBg,
                toolbar: {
                    show: false
                },
            },
            theme: {
                mode: 'dark'
            },
            tooltip: {
                theme: 'dark'
            },
            stroke: {
                colors: ['rgba(0,0,0,0)']
            },
            labels: todayReportLabels,
            colors: [colors.primary, colors.success, colors.danger, colors.warning],
            legend: {
                show: true,
                position: "top",
                horizontalAlign: 'center',
                fontFamily: fontFamily,
                itemMargin: {
                    horizontal: 8,
                    vertical: 0
                },
            },
            dataLabels: {
                enabled: false
            },
            series: todayReportSeries
        };

        var chart = new ApexCharts(document.querySelector("#totalApprovedPostedTaskChargeApexDonut"), options);
        chart.render();
    }
    // totalApprovedPostedTaskChargeApexDonut end


    // todayWorkedTaskApexPie start
    if ($('#todayWorkedTaskApexPie').length) {
        var options = {
            chart: {
                height: 300,
                type: "pie",
                foreColor: colors.bodyColor,
                background: colors.cardBg,
                toolbar: {
                    show: false
                },
            },
            theme: {
                mode: 'dark'
            },
            tooltip: {
                theme: 'dark'
            },
            labels: today_worked_task_labels,
            colors: [colors.info, colors.success, colors.danger, colors.warning],
            legend: {
                show: true,
                position: "top",
                horizontalAlign: 'center',
                fontFamily: fontFamily,
                itemMargin: {
                    horizontal: 8,
                    vertical: 0
                },
            },
            stroke: {
                colors: ['rgba(0,0,0,0)']
            },
            dataLabels: {
                enabled: false
            },
            series: today_worked_task_series,
        };

        var chart = new ApexCharts(document.querySelector("#todayWorkedTaskApexPie"), options);
        chart.render();
    }
    // todayWorkedTaskApexPie end
});
