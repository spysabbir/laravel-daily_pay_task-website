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
        var options2 = {
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

        var chart = new ApexCharts(document.querySelector("#totalWorkedTaskApexPie"), options2);
        chart.render();
    }
    // totalWorkedTaskApexPie end


    // totalPostedTasksApexRadialBar start
    if ($('#totalPostedTasksApexRadialBar').length) {

        // Calculate the total sum of all values
        var total = postedTasksStatusStatusesData.reduce((acc, value) => acc + value, 0);

        // Calculate percentage for each value and round it to 2 decimal places
        var percentageData = postedTasksStatusStatusesData.map(value =>
            Math.round((value / total) * 100 * 100) / 100 // Round to 2 decimal places
        );

        var options3 = {
            chart: {
                type: "radialBar",
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
                theme: 'dark',
                y: {
                    formatter: function(value) {
                        // Ensure 2 decimal points in the tooltip
                        return `${Math.round((value + Number.EPSILON) * 100) / 100}%`;
                    }
                }
            },
            colors: [colors.primary, colors.success, colors.warning, colors.danger, colors.info, colors.secondary],
            fill: {},
            grid: {
                padding: {
                    top: 10
                }
            },
            plotOptions: {
                radialBar: {
                    dataLabels: {
                        name: {
                            fontSize: '14px',
                            fontFamily: fontFamily
                        },
                        value: {
                            fontSize: '14px',
                            fontFamily: fontFamily
                        },
                        total: {
                            show: true,
                            label: 'TOTAL',
                            fontSize: '14px',
                            fontFamily: fontFamily,
                            formatter: function () {
                                // Show only the total quantity
                                return `${total}`;
                            }
                        }
                    },
                    track: {
                        background: colors.gridBorder,
                        strokeWidth: '100%',
                        opacity: 1,
                        margin: 5,
                    }
                }
            },
            series: percentageData,
            labels: postedTasksStatusStatuses.map((status, index) => {
                // Show only status and quantity for radial bar labels
                return `${status}: ${postedTasksStatusStatusesData[index]}`;
            }),
            legend: {
                show: true,
                position: "top",
                horizontalAlign: 'center',
                fontFamily: fontFamily,
                itemMargin: {
                    horizontal: 8,
                    vertical: 0
                },
                formatter: function(seriesName, opts) {
                    // Extract the status from the seriesName
                    const statusName = seriesName.split(":")[0];

                    // Find the index of the status in workedTasksStatusStatuses
                    const index = postedTasksStatusStatuses.indexOf(statusName);

                    // Check if the index is valid and percentageData[index] exists
                    if (index !== -1 && percentageData[index] !== undefined) {
                        return `${seriesName} (${percentageData[index].toFixed(2)}%)`; // Force 2 decimal points
                    } else {
                        return `${seriesName} (0.00%)`; // Default to 0.00% if index is invalid
                    }
                }
            },
        };

        var chart = new ApexCharts(document.querySelector("#totalPostedTasksApexRadialBar"), options3);
        chart.render();
    }
    // totalWorkedTasksApexRadialBar end


    // totalWorkedTasksApexRadialBar start
    if ($('#totalWorkedTasksApexRadialBar').length) {

        // Calculate the total sum of all values
        var total = workedTasksStatusStatusesData.reduce((acc, value) => acc + value, 0);

        // Calculate percentage for each value and round it to 2 decimal points
        var percentageData = workedTasksStatusStatusesData.map(value =>
            Math.round((value / total) * 100 * 100) / 100 // Round to 2 decimal places
        );

        var options4 = {
            chart: {
                type: "radialBar",
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
                theme: 'dark',
                y: {
                    formatter: function(value) {
                        return `${Math.round((value + Number.EPSILON) * 100) / 100}%`; // Ensure 2 decimal points in tooltip
                    }
                }
            },
            colors: [colors.primary, colors.success, colors.danger, colors.warning],
            fill: {},
            grid: {
                padding: {
                    top: 10
                }
            },
            plotOptions: {
                radialBar: {
                    dataLabels: {
                        name: {
                            fontSize: '14px',
                            fontFamily: fontFamily
                        },
                        value: {
                            fontSize: '14px',
                            fontFamily: fontFamily
                        },
                        total: {
                            show: true,
                            label: 'TOTAL',
                            fontSize: '14px',
                            fontFamily: fontFamily,
                            formatter: function () {
                                // Show only the total quantity here
                                return `${total}`;
                            }
                        }
                    },
                    track: {
                        background: colors.gridBorder,
                        strokeWidth: '100%',
                        opacity: 1,
                        margin: 5,
                    }
                }
            },
            series: percentageData,
            labels: workedTasksStatusStatuses.map((status, index) => {
                // Show only status and quantity for radial bar labels
                return `${status}: ${workedTasksStatusStatusesData[index]}`;
            }),
            legend: {
                show: true,
                position: "top",
                horizontalAlign: 'center',
                fontFamily: fontFamily,
                itemMargin: {
                    horizontal: 8,
                    vertical: 0
                },
                formatter: function(seriesName, opts) {
                    // Extract the status from the seriesName
                    const statusName = seriesName.split(":")[0];

                    // Find the index of the status in workedTasksStatusStatuses
                    const index = workedTasksStatusStatuses.indexOf(statusName);

                    // Check if the index is valid and percentageData[index] exists
                    if (index !== -1 && percentageData[index] !== undefined) {
                        return `${seriesName} (${percentageData[index].toFixed(2)}%)`; // Force 2 decimal points
                    } else {
                        return `${seriesName} (0.00%)`; // Default to 0.00% if index is invalid
                    }
                }
            },
        };

        var chart = new ApexCharts(document.querySelector("#totalWorkedTasksApexRadialBar"), options4);
        chart.render();
    }
    // totalWorkedTasksApexRadialBar end


    // reportStatusApexLine start
    if ($('#reportStatusApexLine').length) {
        var formattedDates = lastSevenDaysCategories.map(function (dateStr) {
            var date = new Date(dateStr);
            var options = { day: '2-digit', month: 'short' };
            return date.toLocaleDateString('en-GB', options);
        });

        function getCategorySum(seriesData) {
            return seriesData.reduce(function (sum, series) {
                return sum + series.data.reduce(function (subSum, val) {
                    return subSum + (val || 0);
                }, 0);
            }, 0);
        }

        var lineChartOptions = {
            chart: {
                type: "line",
                height: 320,
                parentHeightOffset: 0,
                foreColor: colors.bodyColor,
                background: colors.cardBg,
                toolbar: { show: false },
            },
            theme: { mode: 'dark' },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function (val) {
                        return val;
                    }
                }
            },
            colors: [colors.primary, colors.danger, colors.success],
            grid: {
                padding: { bottom: -4 },
                borderColor: colors.gridBorder,
                xaxis: {
                    lines: { show: true }
                }
            },
            series: formattedStatusWiseReportsDataSeries,
            xaxis: {
                categories: formattedDates,
                lines: { show: true },
                axisBorder: { color: colors.gridBorder },
                axisTicks: { color: colors.gridBorder }
            },
            markers: { size: 0 },
            legend: {
                show: true,
                position: "top",
                horizontalAlign: 'center',
                fontFamily: fontFamily,
                itemMargin: { horizontal: 8, vertical: 0 },
                formatter: function (seriesName, opts) {
                    var sumQty = getCategorySum([opts.w.config.series[opts.seriesIndex]]);

                    return `${seriesName} - Total: ${sumQty}`;
                }
            },
            stroke: {
                width: 3,
                curve: "smooth",
                lineCap: "round"
            }
        };

        var apexLineChart = new ApexCharts(document.querySelector("#reportStatusApexLine"), lineChartOptions);
        apexLineChart.render();
    }
    // reportStatusApexLine end
});
