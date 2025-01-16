$(function() {
    'use strict'

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

    // monthlyDepositAndWithdrawChart start
    if($('#monthlyDepositAndWithdrawChart').length) {
        var options = {
            chart: {
                type: 'bar',
                height: '318',
                parentHeightOffset: 0,
                foreColor: colors.bodyColor,
                background: colors.cardBg,
                toolbar: {
                    show: false
                },
            },
            theme: {
                mode: 'light'
            },
            tooltip: {
                theme: 'light'
            },
            colors: [colors.primary, colors.secondary],
            fill: {
                opacity: .9
            },
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
                    name: 'Deposit',
                    data: monthlyDepositeSeries,
                },
                {
                    name: 'Withdraw',
                    data: monthlyWithdrawSeries,
                }
            ],
            xaxis: {
                categories: monthlyDepositAndWithdrawCategories,
                axisBorder: {
                    color: colors.gridBorder,
                },
                axisTicks: {
                    color: colors.gridBorder,
                },
            },
            yaxis: {
                title: {
                    text: 'Ammount',
                    style:{
                        size: 9,
                        color: colors.muted
                    }
                },
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
                width: 0
            },
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '10px',
                    fontFamily: fontFamily,
                },
                offsetY: -27
            },
            plotOptions: {
                bar: {
                    columnWidth: "50%",
                    borderRadius: 4,
                    dataLabels: {
                        position: 'top',
                        orientation: 'vertical',
                    }
                },
            },
        }

        var apexBarChart = new ApexCharts(document.querySelector("#monthlyDepositAndWithdrawChart"), options);
        apexBarChart.render();
    }
    // monthlyDepositAndWithdrawChart end

    // verifiedUsersChart - START
    if($('#verifiedUsersChart').length) {
        var formattedVerifiedUsersDataInt = formattedVerifiedUsersData.map(function(value) {
            return Math.round(value);
        });

        var options2 = {
            chart: {
                type: "bar",
                height: 200,
                sparkline: {
                    enabled: true
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 2,
                    columnWidth: "50%"
                }
            },
            colors: [colors.primary],
            series: [{
                name: 'Verified Users Data',
                data: formattedVerifiedUsersDataInt,
            }],
            xaxis: {
                type: 'datetime',
                categories: lastTenDaysCategories,
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return parseInt(value);
                    }
                }
            },
            stroke: {
                width: 2,
                curve: "smooth"
            },
            markers: {
                size: 3,
            },
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: {
                    fontSize: '10px',
                    colors: ["#fff"]
                }
            }
        };

        new ApexCharts(document.querySelector("#verifiedUsersChart"),options2).render();
    }
    // verifiedUsersChart - END

    // postedTasksDataChart start
    if ($('#postedTasksDataChart').length) {
        var formattedPostedTasksDataInt = formattedPostedTasksData.map(function(value) {
            return Math.round(value);
        });

        var options1 = {
            chart: {
                type: "line",
                height: 200,
                sparkline: {
                    enabled: true
                }
            },
            series: [{
                name: 'Posted Tasks Data',
                data: formattedPostedTasksDataInt,
            }],
            xaxis: {
                type: 'datetime',
                categories: lastTenDaysCategories,
            },
            stroke: {
                width: 2,
                curve: "smooth"
            },
            markers: {
                size: 3,
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return parseInt(value);
                    }
                }
            },
            colors: [colors.primary],
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: {
                    fontSize: '10px',
                    colors: [colors.primary]
                }
            },
        };

        new ApexCharts(document.querySelector("#postedTasksDataChart"), options1).render();
    }
    // formattedPostedTasksData end

    // workedTasksDataChart - START
    if ($('#workedTasksDataChart').length) {
        var formattedWorkedTasksDataInt = formattedWorkedTasksData.map(function(value) {
            return Math.round(value);
        });

        var options3 = {
            chart: {
                type: "line",
                height: 200,
                sparkline: {
                    enabled: true
                }
            },
            series: [{
                name: 'Worked Tasks Data',
                data: formattedWorkedTasksDataInt,
            }],
            xaxis: {
                type: 'datetime',
                categories: lastTenDaysCategories,
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return parseInt(value);
                    }
                }
            },
            stroke: {
                width: 2,
                curve: "smooth"
            },
            markers: {
                size: 3,
            },
            colors: [colors.primary],
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: {
                    fontSize: '10px',
                    colors: [colors.primary]
                }
            }
        };

        new ApexCharts(document.querySelector("#workedTasksDataChart"), options3).render();
    }
    // workedTasksDataChart - END
});
