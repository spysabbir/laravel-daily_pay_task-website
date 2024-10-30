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

    // totalTaskProofSubmitChartjsLine start
    if($('#totalTaskProofSubmitChartjsLine').length) {
        new Chart($('#totalTaskProofSubmitChartjsLine'), {
            type: 'line',
            data: {
                labels: totalTaskProofSubmitChartjsLineData.labels,
                datasets: [{
                    data: totalTaskProofSubmitChartjsLineData.datasets[0].data,
                    label: totalTaskProofSubmitChartjsLineData.datasets[0].label,
                    borderColor: colors.info,
                    backgroundColor: "transparent",
                    fill: true,
                    pointBackgroundColor: colors.cardBg,
                    pointBorderWidth: 2,
                    pointHoverBorderWidth: 3,
                    tension: .3
                }, {
                    data: totalTaskProofSubmitChartjsLineData.datasets[1].data,
                    label: totalTaskProofSubmitChartjsLineData.datasets[1].label,
                    borderColor: colors.success,
                    backgroundColor: "transparent",
                    fill: true,
                    pointBackgroundColor: colors.cardBg,
                    pointBorderWidth: 2,
                    pointHoverBorderWidth: 3,
                    tension: .3
                }, {
                    data: totalTaskProofSubmitChartjsLineData.datasets[2].data,
                    label: totalTaskProofSubmitChartjsLineData.datasets[2].label,
                    borderColor: colors.danger,
                    backgroundColor: "transparent",
                    fill: true,
                    pointBackgroundColor: colors.cardBg,
                    pointBorderWidth: 2,
                    pointHoverBorderWidth: 3,
                    tension: .3
                }, {
                    data: totalTaskProofSubmitChartjsLineData.datasets[3].data,
                    label: totalTaskProofSubmitChartjsLineData.datasets[3].label,
                    borderColor: colors.warning,
                    backgroundColor: "transparent",
                    fill: true,
                    pointBackgroundColor: colors.cardBg,
                    pointBorderWidth: 2,
                    pointHoverBorderWidth: 3,
                    tension: .3
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: colors.bodyColor,
                            font: {
                                size: '13px',
                                family: fontFamily
                            }
                        }
                    },
                },
                scales: {
                    x: {
                        display: true,
                        grid: {
                            display: true,
                            color: colors.gridBorder,
                            borderColor: colors.gridBorder,
                        },
                        ticks: {
                            color: colors.bodyColor,
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: true,
                            color: colors.gridBorder,
                            borderColor: colors.gridBorder,
                        },
                        ticks: {
                            color: colors.bodyColor,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    }
    // totalTaskProofSubmitChartjsLine end

    // todayTaskProofSubmitChartjsDoughnut
    if($('#todayTaskProofSubmitChartjsDoughnut').length) {
        new Chart($('#todayTaskProofSubmitChartjsDoughnut'), {
            type: 'doughnut',
            data: {
                labels: today_task_proof_submit_labels,
                datasets: [{
                    label: "Population (millions)",
                    backgroundColor: [colors.primary, colors.success, colors.danger],
                    borderColor: colors.cardBg,
                    data: today_task_proof_submit_series,
                }]
            },
            options: {
                aspectRatio: 2,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                        color: colors.bodyColor,
                            font: {
                                size: '13px',
                                family: fontFamily
                            }
                        }
                    },
                }
            }
        });
    }
    // todayTaskProofSubmitChartjsDoughnut end

    // todayPostedTaskChartjsPie start
    if($('#todayPostedTaskChartjsPie').length) {
        new Chart($('#todayPostedTaskChartjsPie'), {
            type: 'pie',
            data: {
                labels: today_posted_task_labels,
                datasets: [{
                    label: "Population (millions)",
                    backgroundColor: [colors.info, colors.success, colors.warning, colors.danger, colors.primary, colors.secondary],
                    borderColor: colors.cardBg,
                    data: today_posted_task_series,
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: colors.bodyColor,
                            font: {
                                size: '13px',
                                family: fontFamily
                            }
                        }
                    },
                },
                aspectRatio: 2,
            }
        });
    }
    // todayPostedTaskChartjsPie end

    // todayPostedTaskProofChartjsPie start
    if($('#todayPostedTaskProofChartjsPie').length) {
        new Chart($('#todayPostedTaskProofChartjsPie'), {
            type: 'bar',
            data: {
                labels: today_posted_task_proof_labels,
                datasets: [{
                    label: "Population (millions)",
                    backgroundColor: [colors.success, colors.danger],
                    borderColor: colors.cardBg,
                    data: today_posted_task_proof_series,
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: colors.bodyColor,
                            font: {
                                size: '13px',
                                family: fontFamily
                            }
                        }
                    },
                },
                aspectRatio: 2,
            }
        });
    }
    // todayPostedTaskProofChartjsPie end
});
