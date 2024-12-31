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

    // totalBalanceTransferChartjsLine start
    if($('#totalBalanceTransferChartjsLine').length) {
        new Chart($('#totalBalanceTransferChartjsLine'), {
            type: 'bar',
            data: {
                labels: totalBalanceTransferChartjsLineData.labels,
                datasets: [{
                    data: totalBalanceTransferChartjsLineData.datasets[0].data,
                    label: totalBalanceTransferChartjsLineData.datasets[0].label,
                    borderColor: colors.info,
                    backgroundColor: colors.info,
                    fill: true,
                    pointBackgroundColor: colors.cardBg,
                    pointBorderWidth: 2,
                    pointHoverBorderWidth: 3,
                    tension: .3
                }, {
                    data: totalBalanceTransferChartjsLineData.datasets[1].data,
                    label: totalBalanceTransferChartjsLineData.datasets[1].label,
                    borderColor: colors.primary,
                    backgroundColor: colors.primary,
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
    // totalBalanceTransferChartjsLine end

    // totalPostedTaskProofSubmitChartjsDoughnut
    if($('#totalPostedTaskProofSubmitChartjsDoughnut').length) {
        new Chart($('#totalPostedTaskProofSubmitChartjsDoughnut'), {
            type: 'doughnut',
            data: {
                labels: total_posted_task_proof_submit_labels,
                datasets: [{
                    label: "Population (millions)",
                    backgroundColor: [colors.primary, colors.success, colors.danger, colors.warning],
                    borderColor: colors.cardBg,
                    data: total_posted_task_proof_submit_series,
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
    // totalPostedTaskProofSubmitChartjsDoughnut end

    // totalPostedTaskChartjsPie start
    if($('#totalPostedTaskChartjsPie').length) {
        new Chart($('#totalPostedTaskChartjsPie'), {
            type: 'pie',
            data: {
                labels: total_posted_task_labels,
                datasets: [{
                    label: "Population (millions)",
                    backgroundColor: [colors.info, colors.success, colors.primary, colors.warning, colors.danger, colors.secondary],
                    borderColor: colors.cardBg,
                    data: total_posted_task_series,
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
    // totalPostedTaskChartjsPie end

    // totalPostedTaskProofChartjsPie start
    if($('#totalPostedTaskProofChartjsPie').length) {
        new Chart($('#totalPostedTaskProofChartjsPie'), {
            type: 'bar',
            data: {
                labels: total_posted_task_proof_labels,
                datasets: [{
                    label: "Population (millions)",
                    backgroundColor: [colors.success, colors.danger],
                    borderColor: colors.cardBg,
                    data: total_posted_task_proof_series,
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
    // totalPostedTaskProofChartjsPie end
});
