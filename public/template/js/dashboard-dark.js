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

    // Date Picker
    if ($('.datePickerExample').length) {
        var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

        $('.datePickerExample').datepicker({
            format: "dd MM, yyyy",
            todayHighlight: true,
            autoclose: true,
            endDate: today
        });

        $('.datePickerExample').datepicker('setDate', today);
    }
    // Date Picker - END


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

        var totalVerifiedUsers = formattedVerifiedUsersDataInt.reduce(function(a, b) {
            return a + b;
        }, 0);

        var options2 = {
            chart: {
                type: "bar",
                height: 200,
                toolbar: {
                    show: false,
                },
            },
            plotOptions: {
                bar: {
                    borderRadius: 2,
                    columnWidth: "50%"
                }
            },
            colors: [colors.primary],
            series: [{
                name: '',
                data: formattedVerifiedUsersDataInt,
            }],
            xaxis: {
                categories: lastSevenDaysCategories,
                labels: {
                    formatter: function(value) {
                        const date = new Date(value);
                        return date.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                        });
                    },
                    style: {
                        colors: colors.primary,
                        fontSize: '10px',
                    },
                },
                title: {
                    text: 'Total Verified Users: ' + totalVerifiedUsers,
                    style: {
                        color: colors.primary,
                        fontSize: '10px',
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function(value) {
                        return parseInt(value);
                    },
                    style: {
                        colors: colors.primary,
                        fontSize: '10px',
                    },
                    offsetX: -15,
                },
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
                style: {
                    fontSize: '10px',
                    colors: ['#fff']
                }
            },
            grid: {
                show: true,
                padding: {
                    left: 0,
                    right: 0
                },
            },
        };

        new ApexCharts(document.querySelector("#verifiedUsersChart"),options2).render();
    }
    // verifiedUsersChart - END

    // postedTasksDataChart start
    if ($('#postedTasksDataChart').length) {
        var formattedPostedTasksDataInt = formattedPostedTasksData.map(function(value) {
            return Math.round(value);
        });

        var totalPostedTasks = formattedPostedTasksDataInt.reduce(function(a, b) {
            return a + b;
        }, 0);

        var options1 = {
            chart: {
                type: "line",
                height: 200,
                toolbar: {
                    show: false,
                },
            },
            series: [{
                name: '',
                data: formattedPostedTasksDataInt,
            }],
            xaxis: {
                categories: lastSevenDaysCategories,
                labels: {
                    formatter: function(value) {
                        const date = new Date(value);
                        return date.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                        });
                    },
                    style: {
                        colors: colors.primary,
                        fontSize: '10px',
                    },
                },
                title: {
                    text: 'Total Posted Tasks: ' + totalPostedTasks,
                    style: {
                        color: colors.primary,
                        fontSize: '10px',
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function(value) {
                        return parseInt(value);
                    },
                    style: {
                        colors: colors.primary,
                        fontSize: '10px',
                    },
                    offsetX: -15,
                },
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
                style: {
                    fontSize: '10px',
                    colors: [colors.primary]
                }
            },
            grid: {
                show: true,
                padding: {
                    left: 0,
                    right: 0
                },
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

        var totalWorkedTasks = formattedWorkedTasksDataInt.reduce(function(a, b) {
            return a + b;
        }, 0);

        var options3 = {
            chart: {
                type: "line",
                height: 200,
                toolbar: {
                    show: false,
                },
            },
            series: [{
                name: '',
                data: formattedWorkedTasksDataInt,
            }],
            xaxis: {
                categories: lastSevenDaysCategories,
                labels: {
                    formatter: function(value) {
                        const date = new Date(value);
                        return date.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                        });
                    },
                    style: {
                        colors: colors.primary,
                        fontSize: '10px',
                    },
                },
                title: {
                    text: 'Total Worked Tasks: ' + totalWorkedTasks,
                    style: {
                        color: colors.primary,
                        fontSize: '10px',
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: colors.primary,
                        fontSize: '10px',
                    },
                    offsetX: -15,
                },
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
                style: {
                    fontSize: '10px',
                    colors: [colors.primary]
                }
            },
            grid: {
                show: true,
                padding: {
                    left: 0,
                    right: 0
                },
            },
        };

        new ApexCharts(document.querySelector("#workedTasksDataChart"), options3).render();
    }
    // workedTasksDataChart - END

    // currentlyOnlineUserChart Chart
    if ($('#currentlyOnlineUserChart').length) {
        var currentlyOnlineUserPercentage = (currentlyOnlineUserCount / totalActiveUserCount) * 100;

        var options = {
        chart: {
            height: 400,
            type: "radialBar"
        },
        series: [currentlyOnlineUserPercentage],
        colors: [colors.success],
        plotOptions: {
            radialBar: {
            hollow: {
                margin: 15,
                size: "75%"
            },
            track: {
                show: true,
                background: colors.dark,
                strokeWidth: '100%',
                opacity: 1,
                margin: 5,
            },
            dataLabels: {
                showOn: "always",
                name: {
                offsetY: -11,
                show: true,
                color: colors.muted,
                fontSize: "13px"
                },
                value: {
                color: colors.bodyColor,
                fontSize: "30px",
                show: true
                }
            }
            }
        },
        fill: {
            opacity: 1
        },
        stroke: {
            lineCap: "round",
        },
        labels: ["Currently Online User"],
        };

        var chart = new ApexCharts(document.querySelector("#currentlyOnlineUserChart"), options);
        chart.render();
    }
    // currentlyOnlineUserChart - END
});
