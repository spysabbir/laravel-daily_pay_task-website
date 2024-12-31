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

    // totalReportSendApexLine start
    if ($('#totalReportSendApexLine').length) {
        var lineChartOptions = {
            chart: {
                type: "bar",
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
            colors: [colors.info, colors.danger, colors.success],
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
                    name: totalReportSendApexLineData.series[0].name,
                    data: totalReportSendApexLineData.series[0].data
                },
                {
                    name: totalReportSendApexLineData.series[1].name,
                    data: totalReportSendApexLineData.series[1].data
                },
                {
                    name: totalReportSendApexLineData.series[2].name,
                    data: totalReportSendApexLineData.series[2].data
                },
            ],
            xaxis: {
                categories: totalReportSendApexLineData.categories,
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
        var apexLineChart = new ApexCharts(document.querySelector("#totalReportSendApexLine"), lineChartOptions);
        apexLineChart.render();
    }
    // totalReportSendApexLine end


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
            labels: totalPostTaskChargeLabels,
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
            series: totalPostTaskChargeSeries
        };

        var chart = new ApexCharts(document.querySelector("#totalApprovedPostedTaskChargeApexDonut"), options);
        chart.render();
    }
    // totalApprovedPostedTaskChargeApexDonut end


    // totalWorkedTaskApexPie start
    if ($('#totalWorkedTaskApexPie').length) {
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
            labels: total_worked_task_labels,
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
            series: total_worked_task_series,
        };

        var chart = new ApexCharts(document.querySelector("#totalWorkedTaskApexPie"), options);
        chart.render();
    }
    // totalWorkedTaskApexPie end
});
