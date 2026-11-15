<?php
$Tabpage = '16';
$thisaccess = '1,2,3,4,5,6,7,8,9';
include './header.inc.php';

echo '<div class="modal fade" id="result" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . $lang[1760] . '</button>
        </div>
      </div>
    </div>
  </div>';


?>

<?php if (!empty($_SESSION['user_access']) && $_SESSION['user_access'] == 'admin') { ?>

    <div class="row">
        <div class="col-lg-6 col-xl-6 col-xxl-6  p-lg-2 ">
            <div class="row">
                <div class="col-xxl-12 col-xl-12 ">
                    <div class="card chart-height" style="height: 260px;">
                        <div class="card-body p-3 position-relative">
                            <h4><?= $lang[1788] ?></h4>
                            <div class="tab-pane preview-tab-pane active index-pos" role="tabpanel" aria-labelledby="tab-dom-3437c979-0fa6-40a3-af4f-6e942a2b3868" id="dom-3437c979-0fa6-40a3-af4f-6e942a2b3868">
                                <div class="echart-gauge-grade-chart-example" style="min-height: 300px;" data-echart-responsive="true"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $finalOp = 0;
                $resultOpinion = Query("select tbl_opinionend_result from tbl_opinionend where tbl_opinionend_model='1'  ");
                $allOp = mysqli_num_rows($resultOpinion);

                $resultOpinion = Query("select SUM(tbl_opinionend_result) AS tbl_opinionend_result from tbl_opinionend where tbl_opinionend_model='1'  ");
                $readOpinion = mysqli_fetch_array($resultOpinion);
                $number = $readOpinion['tbl_opinionend_result'];

                $finalOp = ($allOp == 0 ? 0 : round(abs($number / $allOp)) / 100);
                ?>
                <script>
                    // const canvas = document.getElementById("myCanvas");
                    // const ctx = canvas.getContext("2d");

                    // ctx.font = "30px Arial";


                    var echartsDoughnutChartInit = function echartsDoughnutChartInit() {
                        var $doughnutChartEl = document.querySelector('.echart-doughnut-chart-example');
                        if ($doughnutChartEl) {
                            // Get options from data attribute
                            var userOptions = utils.getData($doughnutChartEl, 'options');
                            var chart = window.echarts.init($doughnutChartEl);
                            var getDefaultOptions = function getDefaultOptions() {
                                return {
                                    legend: {
                                        orient: 'vertical',
                                        left: 'left',
                                        textStyle: {
                                            color: utils.getGrays()['1000'],
                                            fontSize: '15px',
                                            Font: 'tahoma'
                                        }
                                    },
                                    series: [{
                                        type: 'pie',
                                        radius: ['50%', '70%'],
                                        center: ['70%', '50%'],
                                        avoidLabelOverlap: false,
                                        label: {
                                            show: false,
                                            position: 'center'
                                        },
                                        labelLine: {
                                            show: false
                                        },
                                        data: [{
                                            value: 0,
                                            name: ' مهندسی ایمنی دریایی ',
                                            itemStyle: {
                                                color: utils.getColor('primary')
                                            }
                                        }, {
                                            value: 0,
                                            name: ' توسعه کیفیت و استانداردسازی ',
                                            itemStyle: {
                                                color: utils.getColor('danger')
                                            }
                                        }, {
                                            value: 0,
                                            name: ' مدیریت کیفیت و استانداردها ',
                                            itemStyle: {
                                                color: utils.getColor('info')
                                            }
                                        }, {
                                            value: 1,
                                            name: '  مدیریت بهره وری ',
                                            itemStyle: {
                                                color: utils.getColor('success')
                                            }
                                        }, {
                                            value: 0,
                                            name: '  مدیریت راهبردی کیفیت ',
                                            itemStyle: {
                                                color: utils.getColor('warning')
                                            }
                                        }, {
                                            value: 0,
                                            name: ' بازرسی و کنترل کیفیت ',
                                            itemStyle: {
                                                color: utils.getColor('dark')
                                            }
                                        }, {
                                            value: 0,
                                            name: '  مهندسی کیفیت و ابزارها ',
                                            itemStyle: {
                                                color: utils.getColor('red')
                                            }
                                        }, {
                                            value: 1,
                                            name: ' مدیریت دارایی ها  ',
                                            itemStyle: {
                                                color: utils.getColor('orange')
                                            }
                                        }]
                                    }],
                                    tooltip: {
                                        trigger: 'item',
                                        padding: [7, 10],
                                        backgroundColor: utils.getGrays()['100'],
                                        borderColor: utils.getGrays()['300'],
                                        textStyle: {
                                            color: utils.getColors().dark,
                                            fontSize: '12px'
                                        },
                                        borderWidth: 1,
                                        transitionDuration: 0,
                                        axisPointer: {
                                            type: 'none'
                                        }
                                    }
                                };
                            };
                            echartSetOption(chart, userOptions, getDefaultOptions);
                        }
                    };


                    var linePaymentChartInit = function linePaymentChartInit() {
                        var $echartsLinePaymentChart = document.querySelector(".echart-line-payment");
                        var dataset = {
                            successful: [
                                0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
                            ],
                        };
                        var labels = [
                            "1402", "1403", "1404", "1405", "1406", "1407", "1408", "1409", "1410", "1411", "1412", "1413",
                        ];
                        if ($echartsLinePaymentChart) {
                            var userOptions = utils.getData($echartsLinePaymentChart, "options");
                            var chart = window.echarts.init($echartsLinePaymentChart);
                            var getDefaultOptions = function getDefaultOptions() {
                                return {
                                    tooltip: {
                                        trigger: "axis",
                                        axisPointer: {
                                            type: "none",
                                        },
                                        padding: [7, 10],
                                        backgroundColor: utils.getGrays()["100"],
                                        borderColor: utils.getGrays()["300"],
                                        borderWidth: 1,
                                        transitionDuration: 0,
                                        formatter: function formatter(params) {
                                            console.log(params);
                                            return ""
                                                //.concat(params[0].axisValue, " - ")
                                                .concat(params[0].value, "");
                                        },
                                        textStyle: {
                                            fontWeight: 500,
                                            fontSize: 12,
                                            color: utils.getColors().dark,
                                        },
                                    },
                                    xAxis: {
                                        type: "category",
                                        data: labels,
                                        splitLine: {
                                            show: true,
                                            lineStyle: {
                                                color: utils.rgbaColor("#fff", 0.1),
                                            },
                                            interval: 0,
                                        },
                                        axisLine: {
                                            lineStyle: {
                                                color: utils.rgbaColor("#fff", 0.1),
                                            },
                                        },
                                        axisTick: {
                                            show: true,
                                            length: 10,
                                            lineStyle: {
                                                color: utils.rgbaColor("#fff", 0.1),
                                            },
                                        },
                                        axisLabel: {
                                            color: utils.getGrays()["400"],
                                            fontWeight: 600,
                                            formatter: function formatter(value) {
                                                return value.substring(0, value.length);
                                            },
                                            fontSize: 12,
                                            interval: window.innerWidth < 768 ? "auto" : 0,
                                            margin: 15,
                                        },
                                        boundaryGap: false,
                                    },
                                    yAxis: {
                                        type: "value",
                                        axisPointer: {
                                            show: false,
                                        },
                                        splitLine: {
                                            show: false,
                                        },
                                        axisLabel: {
                                            show: false,
                                        },
                                        axisTick: {
                                            show: false,
                                        },
                                        axisLine: {
                                            show: false,
                                        },
                                    },
                                    series: [{
                                        type: "line",
                                        smooth: true,
                                        data: dataset.successful.map(function(d) {
                                            return (d);
                                        }),
                                        symbol: "emptyCircle",
                                        itemStyle: {
                                            color: localStorage.getItem("theme") === "light" ?
                                                utils.getGrays().white : utils.getColors().primary,
                                        },
                                        lineStyle: {
                                            color: localStorage.getItem("theme") === "light" ?
                                                utils.rgbaColor(utils.getGrays().white, 0.8) : utils.getColors().primary,
                                        },
                                        areaStyle: {
                                            color: {
                                                type: "linear",
                                                x: 0,
                                                y: 0,
                                                x2: 0,
                                                y2: 1,
                                                colorStops: [{
                                                        offset: 0,
                                                        color: localStorage.getItem("theme") === "light" ?
                                                            "rgba(255, 255, 255, 0.5)" : utils.rgbaColor(utils.getColors().primary, 0.5),
                                                    },
                                                    {
                                                        offset: 1,
                                                        color: localStorage.getItem("theme") === "light" ?
                                                            "rgba(255, 255, 255, 0)" : utils.rgbaColor(utils.getColors().primary, 0),
                                                    },
                                                ],
                                            },
                                        },
                                        emphasis: {
                                            lineStyle: {
                                                width: 2,
                                            },
                                        },
                                    }, ],
                                    grid: {
                                        right: 15,
                                        left: 15,
                                        bottom: "15%",
                                        top: 0,
                                    },
                                };
                            };
                            echartSetOption(chart, userOptions, getDefaultOptions);
                            utils.resize(function() {
                                if (window.innerWidth < 768) {
                                    chart.setOption({
                                        xAxis: {
                                            axisLabel: {
                                                interval: "auto",
                                            },
                                        },
                                    });
                                }
                            });
                            var selectMenu = document.querySelector("#dashboard-chart-select");
                            if (selectMenu) {
                                selectMenu.addEventListener("change", function(e) {
                                    var value = e.currentTarget.value;
                                    chart.setOption({
                                        series: [{
                                            data: dataset[value].map(function(d) {
                                                return (d * 3.14).toFixed(2);
                                            }),
                                        }, ],
                                    });
                                });
                            }
                        }
                    };

                    //tbl_opinionend_model  2



                    var echartsGaugeGradeChartInit = function echartsGaugeGradeChartInit() {
                        var $gaugeGradeChartEl = document.querySelector('.echart-gauge-grade-chart-example');
                        if ($gaugeGradeChartEl) {
                            // Get options from data attribute
                            var userOptions = utils.getData($gaugeGradeChartEl, 'options');
                            var chart = window.echarts.init($gaugeGradeChartEl);
                            var getDefaultOptions = function getDefaultOptions() {
                                return {
                                    series: [{
                                        radius: '100%',
                                        type: 'gauge',
                                        center: ['50%', '70%'],
                                        startAngle: 180,
                                        endAngle: 0,
                                        min: 0,
                                        max: 1,
                                        splitNumber: 8,
                                        axisLine: {
                                            lineStyle: {
                                                width: 6,
                                                color: [
                                                    [0.25, utils.getColor('danger')],
                                                    [0.5, utils.getColor('warning')],
                                                    [0.75, utils.getColor('info')],
                                                    [1, utils.getColor('success')]
                                                ]
                                            }
                                        },
                                        pointer: {
                                            icon: 'path://M12.8,0.7l12,40.1H0.7L12.8,0.7z',
                                            length: '12%',
                                            width: 20,
                                            offsetCenter: [0, '-60%'],
                                            itemStyle: {
                                                color: 'auto'
                                            }
                                        },
                                        axisTick: {
                                            length: 12,
                                            lineStyle: {
                                                color: 'auto',
                                                width: 2
                                            }
                                        },
                                        splitLine: {
                                            length: 20,
                                            lineStyle: {
                                                color: 'auto',
                                                width: 5
                                            }
                                        },
                                        axisLabel: {
                                            color: utils.getGrays()['600'],
                                            distance: -60,
                                            formatter: function formatter(value) {
                                                if (value === 0.875) {
                                                    return 'Excellent';
                                                }
                                                if (value === 0.625) {
                                                    return 'Good';
                                                }
                                                if (value === 0.375) {
                                                    return 'Well';
                                                }
                                                if (value === 0.125) {
                                                    return 'Bad';
                                                }
                                                return '';
                                            }
                                        },
                                        title: {
                                            offsetCenter: [0, '-20%'],
                                            color: utils.getGrays()['600']
                                        },
                                        detail: {
                                            offsetCenter: [0, '0%'],
                                            valueAnimation: true,
                                            formatter: function formatter(value) {
                                                return Math.round(value * 100);
                                            },
                                            color: 'auto'
                                        },
                                        data: [{
                                            value: <?= $finalOp ?>,
                                            name: 'Grade'
                                        }]
                                    }]
                                };
                            };
                            echartSetOption(chart, userOptions, getDefaultOptions);
                        }
                    };
                </script>
            </div>
        </div>
        <div class="col-lg-6 col-xl-6 col-xxl-6  p-lg-2 ">
            <div class="card py-3 ">
                <div class="card-body py-3 " style="height: 230px;">
                    <div class="row g-0">
                        <div class="col-6 col-md-4 border-200 border-bottom border-end pb-4">
                            <h6 class="pb-1 text-700"><?= $lang[1789] ?></h6>
                            <?php
                            $res = Query("select tbl_member_id from tbl_member where tbl_member_delete='0' ");
                            ?>
                            <p class="font-sans-serif lh-1 mb-1 fs-2"><?= toPersianNum(mysqli_num_rows($res)) ?></p>

                        </div>
                        <div class="col-6 col-md-4 border-200 border-bottom border-end-md pb-4 ps-3">
                            <h6 class="pb-1 text-700"><?= $lang[1790] ?></h6>
                            <?php
                            $res = Query("select tbl_user_id from tbl_user where tbl_user_access='5' ");
                            ?>
                            <p class="font-sans-serif lh-1 mb-1 fs-2"><?= toPersianNum(mysqli_num_rows($res)) ?></p>

                        </div>
                        <div class="col-6 col-md-4 border-200 border-bottom border-end border-end-md-0 pb-4 pt-4 pt-md-0 ps-md-3">
                            <h6 class="pb-1 text-700"><?= $lang[1791] ?></h6>
                            <?php
                            $res = Query("select tbl_user_id from tbl_user where tbl_user_access='6' ");
                            ?>
                            <p class="font-sans-serif lh-1 mb-1 fs-2"><?= toPersianNum(mysqli_num_rows($res)) ?></p>

                        </div>
                        <div class="col-6 col-md-4 border-200 border-bottom border-bottom-md-0 border-end-md pt-4 pb-md-0 ps-3 ps-md-0">
                            <h6 class="pb-1 text-700"><?= $lang[1792] ?></h6>
                            <?php
                            $res = Query("select tbl_contract_id from tbl_contract where tbl_contract_status='0' ");
                            ?>
                            <p class="font-sans-serif lh-1 mb-1 fs-2"><?= toPersianNum(mysqli_num_rows($res)) ?></p>

                        </div>
                        <div class="col-6 col-md-4 border-200 border-bottom-md-0 border-end pt-4 pb-md-0 ps-md-3">
                            <h6 class="pb-1 text-700"><?= $lang[1793] ?></h6>
                            <?php
                            $res = Query("select tbl_contract_id from tbl_contract where tbl_contract_status='1' ");
                            ?>
                            <p class="font-sans-serif lh-1 mb-1 fs-2"><?= toPersianNum(mysqli_num_rows($res)) ?></p>

                        </div>
                        <?php
                        $res = Query("select tbl_categories_id from tbl_categories where tbl_categories_lng='$language_default' and tbl_categories_type='5' ");
                        ?>
                        <div class="col-6 col-md-4 pb-0 pt-4 ps-3">
                            <h6 class="pb-1 text-700"><?= $lang[1794] ?></h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2"><?= toPersianNum(mysqli_num_rows($res)) ?></p>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2 ">
            <div class="card h-100">
                <div class="card-header d-flex flex-between-center py-2">
                    <h4 class="mb-0 p-3"><?= $lang[1795] ?></h4>
                </div>
                <div class="card-body px-0 py-0">
                    <div class="table-responsive scrollbar">
                        <table class="table fs--1 mb-0">
                            <thead class="bg-200 text-800">
                                <tr>
                                    <th class="sort align-middle"><?= $lang[1537] ?></th>
                                    <th class="sort align-middle"><?= $lang[61] ?></th>
                                </tr>
                            </thead>
                            <tbody class="list" id="table-recent-leads-body">
                                <?php
                                $result = Query("select * from tbl_message where tbl_message_userid !='' order by tbl_message_id DESC limit 0,5");
                                while ($read = mysqli_fetch_array($result)) {
                                ?>
                                    <tr class="hover-actions-trigger btn-reveal-trigger hover-bg-100">
                                        <td class="align-middle white-space-nowrap"><a class="text-800" href="message.php?do=view&code=<?= $read['tbl_message_code'] ?>">
                                                <h6 class="mb-0 ps-2 name"><?= $read['tbl_message_code'] ?></h6>
                                            </a></td>
                                        <td class="align-middle white-space-nowrap"><?= $read['tbl_message_title'] ?></td>
                                    </tr>
                                <?php
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light p-0">
                    <div class="pagination d-none">
                        <li class="active"><button class="page" type="button" data-i="1" data-page="8">1</button></li>
                    </div><a class="btn btn-sm btn-link d-block py-2" href="message.php"><?= $lang[1699] ?></a>
                </div>
            </div>
        </div>



        <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2 ">
            <div class="card">
                <div class="card-header d-flex flex-between-center py-2">
                    <h4 class="mb-0 p-3"><?= $lang[1786] ?></h4>
                </div>
                <div class="card-body px-0 py-0">
                    <div class="table-responsive scrollbar">
                        <table class="table fs--1 mb-0">
                            <thead class="bg-200 text-800">
                                <tr>
                                    <th class="sort align-middle" data-sort="name"><?= $lang[127] ?></th>
                                    <th class="sort align-middle" data-sort="email"><?= $lang[378] ?></th>
                                    <th class="sort align-middle" data-sort="status"><?= $lang[385] ?></th>
                                    <th class="sort align-middle"><?= $lang[1787] ?></th>
                                </tr>
                            </thead>
                            <tbody class="list" id="table-recent-leads-body">
                                <?php
                                $result = Query("SELECT * From tbl_member Where tbl_member_delete ='0' ORDER BY tbl_member_id DESC LIMIT 0,5 ");
                                while ($read = mysqli_fetch_array($result)) {
                                ?>
                                    <tr class="hover-actions-trigger btn-reveal-trigger hover-bg-100">
                                        <td class="align-middle white-space-nowrap"><a class="text-800" href="member.php?do=edit&id=<?= $read['tbl_member_systemcode'] ?>">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-xl">
                                                        <img class="rounded-circle" src="<?= (!empty($read['tbl_member_profile']) ? '../upload/profile/' . $read['tbl_member_profile'] : 'assets/img/no-image.jpg') ?>" alt="">

                                                    </div>
                                                    <h6 class="mb-0 ps-2 name"><?= $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] ?></h6>
                                                </div>
                                            </a></td>
                                        <td class="align-middle white-space-nowrap "><?= toPersianNum($read['tbl_member_mobile']) ?></td>
                                        <td class="align-middle white-space-nowrap"><?= toPersianNum($read['tbl_member_nationalcode']) ?></td>
                                        <td class="align-middle white-space-nowrap"><?= toPersianNum($read['tbl_member_lastlogin']) ?></td>
                                    </tr>
                                <?php
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light p-0">
                    <div class="pagination d-none">
                        <li class="active"><button class="page" type="button" data-i="1" data-page="8">1</button></li>
                    </div><a class="btn btn-sm btn-link d-block py-2" href="member.php"><?= $lang[1451] ?></a>
                </div>
            </div>
        </div>
    </div>

<?php } ?>
<?php if (!empty($_SESSION['user_access']) && $_SESSION['user_access'] == 'coach') { ?>


    <div class="row">
        <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2 ">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card h-180">
                        <?php
                        $result_profile = Query("select * from tbl_user where tbl_user_id = '$this_user_id' ");
                        $read_profile = mysqli_fetch_array($result_profile);
                        $lastLogin = explode(" ", $read_profile['tbl_user_lastlogin']);
                        ?>
                        <div class="card-body d-flex gap-3 flex-column flex-sm-row align-items-center index-profile">
                            <img class="rounded-3" src="<?= (!empty($read_profile['tbl_user_img']) ? '../upload/' . $read_profile['tbl_user_img'] : 'assets/img/no-image.jpg') ?>" alt="" width="112">
                            <table class="table table-borderless fs--1 fw-medium mb-0">

                                <tbody>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[92] ?></td>
                                        <td class="p-1"><a class="text-600 text-decoration-none"><?= $read_profile['tbl_user_name'] ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[378] ?></td>
                                        <td class="p-1"><a class="text-600 text-decoration-none"><?= $read_profile['tbl_user_mobile'] ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[1707] ?></td>
                                        <td class="p-1"><a class="text-600 text-decoration-none"><?= toPersianNum($read_profile['tbl_user_username']) ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[1706] ?></td>
                                        <td class="p-1 text-600"><?= toPersianNum($lastLogin[0]) ?></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2">
            <div class="card h-180">
                <div class="bg-holder bg-card" style="background-image:url(assets/img/icons/spot-illustrations/corner-1.png);">
                </div>
                <?php
                $resultPages = Query("Select * from tbl_pages Where tbl_pages_code = '0134EDE' and tbl_pages_lng='$language_default'  ");
                $readPages = mysqli_fetch_array($resultPages);
                $textPages = $readPages['tbl_pages_text'];
                ?>
                <div class="card-body position-relative pages">
                    <h5 class="text-warning"><?= $readPages['tbl_pages_title'] ?></h5>
                    <p class="fs--1 mb-0"><?= $textPages ?></p>
                </div>
            </div>
        </div>
    </div>



    <div class="row gx-3 mb-2">
        <div class="col-xxl-12 col-xl-12 p-0">
            <div class="card">
                <div class="card-header border-bottom border-200 px-0">
                    <div class="d-lg-flex justify-content-between">
                        <div class="row flex-between-center gy-2 px-x1">
                            <div class="col-auto pe-0">
                                <h4 class="mb-0"><?= $lang[1776] ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-4.png);">
                </div>

                <div class="card-body p-0 position-relative">
                    <div class="table-responsive scrollbar">
                        <?php
                        ?>
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th><?= $lang[53] ?></th>
                                    <th><?= $lang[92] ?></th>
                                    <th><?= $lang[385] ?></th>
                                    <th><?= $lang[1770] ?></th>
                                    <th><?= $lang[1785] ?></th>
                                    <th><?= $lang[1774] ?></th>
                                    <th><?= $lang[1775] ?></th>
                                    <th><?= $lang[1447] ?></th>
                                    <th><?= $lang[1772] ?></th>
                                    <th><?= $lang[1709] ?></th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                <?php

                                $result = Query("SELECT * From tbl_plan where tbl_plan_coach='$this_user_id' and tbl_plan_lng='$language_default' and tbl_plan_delete='0' and tbl_plan_status='0' ORDER BY tbl_plan_order DESC , tbl_plan_id DESC ");
                                if (mysqli_num_rows($result) > 0) {
                                    while ($read =  mysqli_fetch_array($result)) {
                                        $contractId = $read['tbl_plan_contract'];
                                        $faragir = $read['tbl_plan_menti'];
                                        $skill = $read['tbl_plan_skill'];
                                        $planCode = $read['tbl_plan_code'];
                                        $mentor = $read['tbl_plan_mentor'];

                                        $resultSkill = Query("select * from tbl_categories where tbl_categories_code = '$skill'  ");
                                        $readSkill =  mysqli_fetch_array($resultSkill);
                                        $parent = $readSkill['tbl_categories_bakhsh'];

                                        $resultBakhsh = Query("select * from tbl_categories where tbl_categories_code = '$parent'  ");
                                        $readBakhsh =  mysqli_fetch_array($resultBakhsh);


                                        $resultContract = Query("select * from tbl_contract where tbl_contract_id = '$contractId'  ");
                                        $readContract =  mysqli_fetch_array($resultContract);

                                        $resultMember = Query("select * from tbl_member where tbl_member_delete='0' and tbl_member_id ='$faragir' ");
                                        $readMember =  mysqli_fetch_array($resultMember);

                                        $resultUser = Query("select tbl_user_name from tbl_user where tbl_user_id ='$mentor' ");
                                        $readUser =  mysqli_fetch_array($resultUser);

                                        $resultMessage = Query("SELECT * From tbl_coachmessage Where tbl_coachmessage_reply='0' and tbl_coachmessage_plan='$planCode' and tbl_coachmessage_contract='$contractId' and tbl_coachmessage_skill='$skill' and tbl_coachmessage_user='$faragir' and tbl_coachmessage_delete ='0'");

                                ?>
                                        <tr>
                                            <td class="align-middle pe-3 py-2">
                                                <div class="notification-avatar">
                                                    <div class="avatar avatar-xl me-3">
                                                        <img class="rounded-circle" src="<?= (!empty($readMember['tbl_member_profile']) ? '../upload/profile/' . $readMember['tbl_member_profile'] : 'assets/img/no-image.jpg') ?>" alt="">
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle pe-3 py-2"><?= $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] ?></td>
                                            <td class="align-middle pe-3 py-2"><?= toPersianNum($readMember['tbl_member_nationalcode']) ?></td>
                                            <td class="align-middle pe-3 py-2 "><?= $readSkill['tbl_categories_title'] ?></td>
                                            <td class="align-middle subject py-2 pe-2"><?= strtoupper($readBakhsh['tbl_categories_catcode']) ?></td>
                                            <td class="align-middle subject py-2 pe-2"><?= strtoupper($readContract['tbl_contract_code']) ?></td>
                                            <td class="align-middle status py-2 pe-2"><?= $readContract['tbl_contract_customer'] ?></td>
                                            <td class="align-middle status py-2 pe-2"><?= $readUser['tbl_user_name'] ?></td>
                                            <?php
                                            $resultPlan = Query("SELECT tbl_plan_title FROM tbl_plan where tbl_plan_delete='0' and tbl_plan_lng='$language_default' and tbl_plan_menti = '$faragir' and tbl_plan_contract ='$contractId' and tbl_plan_skill='$skill'");
                                            $resultPlanConfirm = Query("SELECT tbl_plan_title FROM tbl_plan where tbl_plan_status='1' and tbl_plan_delete='0' and tbl_plan_lng='$language_default' and tbl_plan_menti = '$faragir' and tbl_plan_contract ='$contractId' and tbl_plan_skill='$skill'");
                                            $count = mysqli_num_rows($resultPlan);
                                            $confirm = mysqli_num_rows($resultPlanConfirm);
                                            $persent = ($confirm == 0 ? $confirm : round(abs(((($count - $confirm) / $count) * 100) - 100)));
                                            echo '<td class="align-middle priority pe-4">
                                                <div class="d-flex align-items-center gap-2">
                                                  <div style="--falcon-circle-progress-bar: ' . $persent . '">
                                                    <svg
                                                      class="circle-progress-svg"
                                                      width="26"
                                                      height="26"
                                                      viewBox="0 0 120 120"
                                                    >
                                                      <circle
                                                        class="progress-bar-rail"
                                                        cx="60"
                                                        cy="60"
                                                        r="54"
                                                        fill="none"
                                                        stroke-linecap="round"
                                                        stroke-width="12"
                                                      ></circle>
                                                      <circle
                                                        class="progress-bar-top"
                                                        cx="60"
                                                        cy="60"
                                                        r="54"
                                                        fill="none"
                                                        stroke-linecap="round"
                                                        stroke="' . ($persent <= 30 ? '#e63757' : ($persent <= 50 ? '#F68F57' : ($persent <= 75 ? '#2A7BE4' : '#00D27B'))) . '"
                                                        stroke-width="12"
                                                      ></circle>
                                                    </svg>
                                                  </div>
                                                  <h6 class="mb-0 text-700">' . toPersianNum($persent) . '%</h6>
                                                </div>
                                              </td>';
                                            ?>

                                            </td>
                                            <td class="align-middle subject py-2 pe-2">
                                                <a href="interviewcoach.php?user=<?= $faragir ?>&skill=<?= $skill ?>&contract=<?= $contractId ?>&plan=<?= $planCode ?>" class="btn me-1 mb-1 btn-sm  btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1777] ?>"><span class="bi-book"></span></a>
                                                <a href="arzyabi.php?do=answer&id=<?= $faragir ?>&contract=<?= $contractId ?>" class="btn me-1 mb-1 btn-sm  btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1751] ?>"><span class="bi-pie-chart-fill"></span></a>
                                                <a href="#" data-user="<?= $faragir ?>" data-contract="<?= $contractId ?>" data-skill="<?= $skill ?>" data-lng="<?= $language_default ?>" class=" timelinecontent btn me-1 mb-1 btn-sm  btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1708] ?>"><span class="bi-bar-chart-steps"></span></a>
                                                <a href="#" data-value="<?= $read['tbl_plan_code'] . '-' . $language_default ?>" class="item-info btn me-1 mb-1 btn-sm  btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1784] ?>"><span class="bi-pencil-square"></span></a>
                                                <a href="coachmessage.php?user=<?= $faragir ?>&skill=<?= $skill ?>&contract=<?= $contractId ?>&plan=<?= $planCode ?>" class="btn me-1 mb-1 btn-sm  btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[822] . '(' . toPersianNum(mysqli_num_rows($resultMessage)) . ')' ?>"><span class="bi-chat-dots-fill"></span></a>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6">
                                                <?php

                                                $resultDis = Query("SELECT * From tbl_plan where tbl_plan_code ='$read[tbl_plan_code]' and tbl_plan_delete='0' and tbl_plan_lng='$language_default'");
                                                if (mysqli_num_rows($resultDis) > 0) {
                                                    $readPlan = mysqli_fetch_array($resultDis);
                                                    echo '<h6 class="fs-0 mb-2">' . $lang[1436] . ' : ' . ($readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233' || $readPlan['tbl_plan_model'] == '1234' ? PlanModel($readPlan['tbl_plan_model']) : PlanModel($readPlan['tbl_plan_model'])) . '</h6>';
                                                    echo '<p class="mb-1">' . ($readPlan['tbl_plan_model'] == '1230' || $readPlan['tbl_plan_model'] == '1231' || $readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233'  ? $lang[1636] . ' : ' : '') .
                                                        ($readPlan['tbl_plan_model'] == '1234' ? $lang[62] . ' : ' : '') .
                                                        ($readPlan['tbl_plan_model'] == '1235' ? $lang[62] . ' : ' : '') .
                                                        ($readPlan['tbl_plan_model'] == '1236' || $readPlan['tbl_plan_model'] == '1237' ? $lang[1637] . ' : ' : '') .
                                                        toPersianNum($readPlan['tbl_plan_expire']) . '<br>';
                                                    echo $readPlan['tbl_plan_text'] . '</p>';
                                                }

                                                ?>
                                            </td>
                                        </tr>
                                <?php

                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>




    <div class="row">

        <?php
        if (!empty($contractId)) {
            $now = date("Y/m/d");
            $next_days = strtotime('+10 days');
            $nextTenDays = date('Y/m/d', $next_days);
            $resultPlan = Query("select * from tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$contractId' and tbl_plan_status='0' and tbl_plan_coach='$this_user_id' and  tbl_plan_dateenglish BETWEEN '$now' and '$nextTenDays' order by tbl_plan_dateenglish ASC ");
            if (mysqli_num_rows($resultPlan) > 0) {
        ?>
                <div class="col-lg-12 col-xl-12 col-xxl-12 mb-2 p-lg-2 ">
                    <div class="card ">
                        <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(assets/img/icons/spot-illustrations/corner-1.png);">
                        </div>
                        <div class="card-body p-0 position-relative height-380">
                            <div class="row g-0 justify-content-center">
                                <div class="col-lg-12  p-3 mb-2">
                                    <h4><?= $lang[1800] ?></h4>
                                    <div class="box-content-two mt-2">
                                        <?php
                                        if (mysqli_num_rows($resultPlan) <= 0) {
                                            echo '<p>' . $lang[1768] . '</p>';
                                        } else {
                                            while ($readPlan = mysqli_fetch_array($resultPlan)) {
                                                $expire = explode("/", $readPlan['tbl_plan_expire']);
                                                $contractId = $readPlan['tbl_plan_contract'];
                                                $faragir = $readPlan['tbl_plan_menti'];

                                                $resultContract = Query("select * from tbl_contract where tbl_contract_id = '$contractId'  ");
                                                $readContract =  mysqli_fetch_array($resultContract);

                                                $resultMember = Query("select * from tbl_member where tbl_member_delete='0' and tbl_member_id ='$faragir' ");
                                                $readMember =  mysqli_fetch_array($resultMember);


                                                $a = jdate_words(array('mm' => $expire[1]));
                                                echo '<div class="d-flex btn-reveal-trigger mb-2 mt-2">
                                        <div class="calendar"><span class="calendar-month">' . $a['mm'] . '</span><span class="calendar-day">' . toPersianNum($expire[2]) . '</span></div>
                                        <div class="flex-1 position-relative ps-3">
                                            <h6 class="fs-0 mb-2"><a class="item-info"  href="#" data-value="' . $readPlan['tbl_plan_code'] . '-' . $language_default . '">' . ($readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233' || $readPlan['tbl_plan_model'] == '1234' ? PlanModel($readPlan['tbl_plan_model']) : PlanModel($readPlan['tbl_plan_model'])) . '</a></h6>';
                                                echo '<p class="fs-0  mb-1">' . ($readPlan['tbl_plan_model'] == '1230' || $readPlan['tbl_plan_model'] == '1231' || $readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233'  ? $lang[1636] . ' : ' : '') .
                                                    ($readPlan['tbl_plan_model'] == '1234' ? $lang[62] . ' : ' : '') .
                                                    ($readPlan['tbl_plan_model'] == '1235' ? $lang[62] . ' : ' : '') .
                                                    ($readPlan['tbl_plan_model'] == '1236' || $readPlan['tbl_plan_model'] == '1237' ? $lang[1637] . ' : ' : '') .
                                                    toPersianNum($readPlan['tbl_plan_expire']) . ' ، ' . $lang[1438] . ' : ' . $readContract['tbl_contract_customer'] . ' ، ' . $lang[1780] . ' : ' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '</p>
                                        </div>
                                    </div><hr>';
                                            }
                                        }
                                        ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php

                $now = date("Y/m/d");
                $next_days = strtotime('+10 days');
                $nextTenDays = date('Y/m/d', $next_days);
                $resultPlan = Query("select * from tbl_interview where tbl_interview_lng='$language_default' and tbl_interview_contract='$contractId' and tbl_interview_delete='0' and tbl_interview_mentor='$this_user_id' and  tbl_interview_dateenglish BETWEEN '$now' and '$nextTenDays' order by tbl_interview_dateenglish ASC ");

                ?>

                <div class="col-lg-12 col-xl-12 col-xxl-12 mb-2 p-lg-2 ">
                    <div class="card ">
                        <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(assets/img/icons/spot-illustrations/corner-1.png);">
                        </div>
                        <div class="card-body p-0 position-relative height-380">
                            <div class="row g-0 justify-content-center">
                                <div class="col-lg-12  p-3 mb-2">
                                    <h4><?= $lang[1801] ?></h4>
                                    <div class="box-content-two mt-2">
                                        <?php
                                        if (mysqli_num_rows($resultPlan) <= 0) {
                                            echo '<p>' . $lang[1768] . '</p>';
                                        } else {
                                            while ($readPlan = mysqli_fetch_array($resultPlan)) {
                                                $expire = explode("/", $readPlan['tbl_interview_date']);
                                                $contractId = $readPlan['tbl_interview_contract'];
                                                $faragir = $readPlan['tbl_interview_menti'];

                                                $resultContract = Query("select * from tbl_contract where tbl_contract_id = '$contractId'  ");
                                                $readContract =  mysqli_fetch_array($resultContract);

                                                $resultMember = Query("select * from tbl_member where tbl_member_delete='0' and tbl_member_id ='$faragir' ");
                                                $readMember =  mysqli_fetch_array($resultMember);


                                                $a = jdate_words(array('mm' => $expire[1]));
                                                echo '<div class="d-flex btn-reveal-trigger mb-2 mt-2">
                                        <div class="calendar"><span class="calendar-month">' . $a['mm'] . '</span><span class="calendar-day">' . toPersianNum($expire[2]) . '</span></div>
                                        <div class="flex-1 position-relative ps-3">
                                            <h6 class="fs-0 mb-2"><a class="item-info-interview"  href="#" data-value="' . $readPlan['tbl_interview_code'] . '-' . $language_default . '">' . $readPlan['tbl_interview_title'] . '</a></h6>';
                                                echo '<p class="fs-0  mb-1">' . $lang[1802] . ' : ' .
                                                    toPersianNum($readPlan['tbl_interview_date']) . ' ، ' . $lang[1438] . ' : ' . $readContract['tbl_contract_customer'] . ' ، ' . $lang[1780] . ' : ' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '</p>
                                        </div>
                                    </div><hr>';
                                            }
                                        }
                                        ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

    </div>


<?php     }
        }
    } ?>















<?php if (!empty($_SESSION['user_access']) && $_SESSION['user_access'] == 'karfarma') { ?>


    <div class="row">
        <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2 ">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card h-180">
                        <?php
                        $result_profile = Query("select * from tbl_user where tbl_user_id = '$this_user_id' ");
                        $read_profile = mysqli_fetch_array($result_profile);
                        $lastLogin = explode(" ", $read_profile['tbl_user_lastlogin']);
                        ?>
                        <div class="card-body d-flex gap-3 flex-column flex-sm-row align-items-center index-profile">
                            <img class="rounded-3" src="<?= (!empty($read_profile['tbl_user_img']) ? '../upload/' . $read_profile['tbl_user_img'] : 'assets/img/no-image.jpg') ?>" alt="" width="112">
                            <table class="table table-borderless fs--1 fw-medium mb-0">

                                <tbody>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[92] ?></td>
                                        <td class="p-1"><a class="text-600 text-decoration-none"><?= $read_profile['tbl_user_name'] ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[378] ?></td>
                                        <td class="p-1"><a class="text-600 text-decoration-none"><?= $read_profile['tbl_user_mobile'] ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[1707] ?></td>
                                        <td class="p-1"><a class="text-600 text-decoration-none"><?= toPersianNum($read_profile['tbl_user_username']) ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[1706] ?></td>
                                        <td class="p-1 text-600"><?= toPersianNum($lastLogin[0]) ?></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2">
            <div class="card h-180">
                <div class="bg-holder bg-card" style="background-image:url(assets/img/icons/spot-illustrations/corner-1.png);">
                </div>
                <?php
                $resultPages = Query("Select * from tbl_pages Where tbl_pages_code = '0134EDE' and tbl_pages_lng='$language_default'  ");
                $readPages = mysqli_fetch_array($resultPages);
                $textPages = $readPages['tbl_pages_text'];
                ?>
                <div class="card-body position-relative pages">
                    <h5 class="text-warning"><?= $readPages['tbl_pages_title'] ?></h5>
                    <p class="fs--1 mb-0"><?= $textPages ?></p>
                </div>
            </div>
        </div>
    </div>







    <div class="row gx-3 mb-2">
        <div class="col-xxl-12 col-xl-12 p-0">
            <div class="card">
                <div class="card-header border-bottom border-200 px-0">
                    <div class="d-lg-flex justify-content-between">
                        <div class="row flex-between-center gy-2 px-x1">
                            <div class="col-auto pe-0">
                                <h4 class="mb-0"><?= $lang[1769] ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-4.png);">
                </div>
                <div class="card-body p-0 position-relative">
                    <div class="table-responsive scrollbar">
                        <?php
                        ?>
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th><?= $lang[1567] ?></th>
                                    <th><?= $lang[1440] ?></th>
                                    <th><?= $lang[1806] ?></th>
                                    <th><?= $lang[1611] ?></th>
                                    <th><?= $lang[1458] ?></th>
                                    <th><?= $lang[1459] ?></th>
                                    <th><?= $lang[1748] ?></th>
                                    <th><?= $lang[1709] ?></th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                <?php

                                foreach ($myContract as $key => $value) {
                                    $thisId = str_replace("[", "", str_replace("]", "", $value));
                                    $result = Query("SELECT * From tbl_contract where tbl_contract_id='$thisId' and (tbl_contract_status is null or tbl_contract_status='0') ORDER BY tbl_contract_id DESC ");
                                    if (mysqli_num_rows($result) > 0) {

                                        $read =  mysqli_fetch_array($result);

                                        $contractId = $read['tbl_contract_id'];
                                        $result_attach = Query("select * from tbl_attach where tbl_attach_contract = '$contractId'  ");
                                        $contractIdStr = '[' . $contractId . '],';
                                        $result_member = Query("select tbl_member_id from tbl_member where tbl_member_delete='0' and tbl_member_lastcontract like '%$contractIdStr%' ");
                                        $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_catcode = '$read[tbl_contract_codesection]' and tbl_categories_lng='$language_default' ");
                                        $readSkill = mysqli_fetch_array($resultSkill);
                                ?>
                                        <tr>
                                            <td class="align-middle pe-3 py-2"><?= $read['tbl_contract_number'] ?></td>
                                            <td class="align-middle pe-3 py-2  cat-code"><?= $read['tbl_contract_code'] ?></td>
                                            <td class="align-middle pe-3 py-2"><?= $readSkill['tbl_categories_title'] ?></td>
                                            <td class="align-middle pe-3 py-2 ">
                                                <?= $read['tbl_contract_customer'] ?>
                                            </td>
                                            <td class="align-middle subject py-2 pe-2"><?= $read['tbl_contract_startdate'] ?></td>
                                            <td class="align-middle status py-2 pe-2"><?= $read['tbl_contract_enddate'] ?></td>
                                            <td class="align-middle status py-2 pe-2">

                                                <?php
                                                if (mysqli_num_rows($result_member) > '0') {
                                                    $h = 0;
                                                    echo '<div class="avatar-group avatar-group-dense mb-3">';
                                                    $resultMember = Query("select * from tbl_member where tbl_member_lastcontract like '%$contractIdStr%' order by tbl_member_id DESC ");
                                                    while ($readMember = mysqli_fetch_array($resultMember)) {
                                                        if ($h <= 3) {
                                                            echo '<div class="avatar avatar-2xl border border-3 border-light rounded-circle">
                                            <img class="rounded-circle" src="' . (!empty($readMember['tbl_member_profile']) ? '../upload/profile/' . $readMember['tbl_member_profile'] : 'assets/img/no-image.jpg') . '" alt="' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '">
                                            </div>';
                                                            $h++;
                                                        } else {
                                                            break;
                                                        }
                                                    }
                                                    if (mysqli_num_rows($result_member) > $h) {
                                                        echo '<div class="avatar avatar-2xl border border-3 border-light rounded-circle">
                                        <div class="avatar-name rounded-circle "><span>+' . abs(mysqli_num_rows($result_member) - 4) . '</span></div>
                                        </div>';
                                                    }
                                                    echo '</div>';
                                                ?>
                                                <?php } else {
                                                    echo toPersianNum('0');
                                                } ?>

                                            </td>
                                            <td class="align-middle subject py-2 pe-2">

                                                <?php
                                                $checkSkill = Query("select * from tbl_memberskill where tbl_memberskill_contract='$read[tbl_contract_id]' ");
                                                if (mysqli_num_rows($checkSkill) > 0) {
                                                ?>
                                                    <a href="skill-list.php?contract=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm  btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1304] ?>"><span class="bi-bar-chart-steps"></span></a>
                                                <?php } ?>
                                                <a href="faragir-list.php?contract=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm btn-success " data-contract="<?= $read['tbl_contract_id'] ?>" data-lng="<?= $language_default ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1451] ?>"><span class="bi-people-fill"></span></a>
                                                <a href="mentorlist.php?contract=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm btn-primary " data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1929] ?>"><span class="bi-eye-fill"></span></a>
                                                <a href="endreport.php?contract=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm btn-default" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1947] ?>"><span class="bi-calendar-check-fill"></span></a>
                                                <a href="#" class="btn me-1 mb-1 btn-sm btn-warning ajaxkarfarma" data-contract="<?= $read['tbl_contract_id'] ?>" data-lng="<?= $language_default ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1749] ?>"><span class="bi-card-heading"></span></a>
                                            </td>
                                        </tr>

                                <?php

                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>




    <div class="ajaxresult">

        <?php


        $thisId = str_replace("[", "", str_replace("]", "", $myContract[0]));
        $result = Query("SELECT * From tbl_contract where tbl_contract_id='$thisId' ORDER BY tbl_contract_id DESC limit 0,1");
        if (mysqli_num_rows($result) > 0) {

            $read =  mysqli_fetch_array($result);
            $contractId = $read['tbl_contract_id'];
            $contractIdStr = '[' . $contractId . '],';
            $result_member = Query("select tbl_member_id from tbl_member where tbl_member_delete='0' and tbl_member_lastcontract like '%$contractIdStr%' ");
            $result_member_question = Query("select tbl_memberquestion_id from tbl_memberquestion where tbl_memberquestion_contract='$contractId' ");
            $result_member_question_check = Query("select tbl_memberquestion_id from tbl_memberquestion where tbl_memberquestion_admincheck='1' and tbl_memberquestion_contract='$contractId' and tbl_memberquestion_mentor='$this_user_id' ");
            $resultPlan = Query("SELECT DISTINCT  tbl_plan_menti From tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$contractId' and tbl_plan_delete='0'");
            $resultPlanCheck = Query("select tbl_memberskill_id,tbl_memberskill_data from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_contract='$contractId' ");
            $resultCheckEndPlan = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_contract='$contractId' ");

            $allFaragir = mysqli_num_rows($result_member);
            $allFaragirHaveQuestion = mysqli_num_rows($result_member_question);
            $allFaragirHaveQuestionCheck = mysqli_num_rows($result_member_question_check);
            $noPlan = mysqli_num_rows($resultPlan);
            $eblaghNashode = $allFaragir - mysqli_num_rows($resultPlanCheck);
            $endPlan =  mysqli_num_rows($resultCheckEndPlan);
            $thisCustomer = $read['tbl_contract_customer'];



            $resultShayeste = Query("select DISTINCT tbl_number_user from tbl_number where tbl_number_shayestegi !='0' and tbl_number_contract='$contractId' ");

            $allSkill = array();
            while ($readSkill = mysqli_fetch_array($resultPlanCheck)) {
                $skillData = explode(",", $readSkill['tbl_memberskill_data']);
                foreach ($skillData as $key => $value) {
                    if (!empty($value)) {
                        $allSkill[$value] = $value;
                    }
                }
            }

            // echo 'A = arzyabi shodan'. $itemOne = $allFaragir-$allFaragirHaveQuestion;
            // echo '<br>B = plan daran'. mysqli_num_rows($resultPlanCheck);
            // echo '<br>c = plan tamom shode'. mysqli_num_rows($resultCheckEndPlan);
            // echo '<br>d = shayestegi'. mysqli_num_rows($resultShayeste);
            if ($allFaragir > 0) {
        ?>

                <div class="row">
                    <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2 ">
                        <div class="card h-100">

                            <div class="card-body pt-0 height-380">
                                <div class="row mb-2 p-3">
                                    <div class="col-6 border-end border-200">
                                        <h4 class="mb-0"><?= toPersianNum(mysqli_num_rows($result_member)) ?></h4>
                                        <p class="fs--1 text-600 mb-0"><?= $lang[1752] ?></p>
                                    </div>
                                    <div class="col-3 border-end text-center border-200">
                                        <h4 class="mb-0"><?= toPersianNum(count($allSkill)) ?></h4>
                                        <p class="fs--1 text-600 mb-0"><?= $lang[1753] ?></p>
                                    </div>
                                    <div class="col-3 text-center">
                                        <h4 class="mb-0"><?= toPersianNum(abs(mysqli_num_rows($resultShayeste))) ?></h4>
                                        <p class="fs--1 text-600 mb-0"><?= $lang[1754] ?></p>
                                    </div>
                                </div>
                                <!-- <div class="progress-stacked overflow-visible mt-4 rounded-3" style="height: 6px;">
                    <div class="progress" role="progressbar" style="width: 25%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                      <div class="overflow-visible progress-bar bg-progress-gradient border-end border-white border-2 rounded-end rounded-pill"><span class="mt-n4 text-900 fw-bold">25%</span></div>
                    </div>
                    <div class="progress" role="progressbar" style="width: 45%; height: 6px;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
                      <div class="overflow-visible progress-bar bg-secondary border-end border-white border-2"><span class="mt-n4 text-900 fw-bold">45%</span></div>
                    </div>
                    <div class="progress" role="progressbar" style="width: 15%; height: 6px;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">
                      <div class="overflow-visible progress-bar bg-warning border-end border-white border-2"><span class="mt-n4 text-900 fw-bold">15%</span></div>
                    </div>
                    <div class="progress" role="progressbar" style="width: 15%; height: 6px;" aria-valuenow="15" aria-valuemin="15" aria-valuemax="100">
                      <div class="overflow-visible progress-bar bg-info rounded-start rounded-pill"><span class="mt-n4 text-900 fw-bold">15%</span></div>
                    </div>
                  </div> -->
                                <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2 mt-3"><a href="#!">
                                        <h6 class="mb-0"><svg class="svg-inline--fa fa-circle fa-w-16 fs--1 me-3 text-primary" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                                                <path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z"></path>
                                            </svg><?= $lang[1756] ?></h6>
                                    </a><a class="fs--2 text-600 mb-0" href="#!">
                                        <?php
                                        if ($allFaragir != 0) {
                                            //$darsad = round(abs(($allFaragir - $allFaragirHaveQuestion) / $allFaragir) * 100);
                                            $darsad = ($allFaragirHaveQuestion * 100) / ($allFaragirHaveQuestion - $allFaragir == 0 ? $allFaragirHaveQuestion : abs($allFaragirHaveQuestion - $allFaragir));
                                        } else {
                                            $darsad = 0;
                                        }
                                        ?>
                                        <div class="progress progress-index" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar rounded-3 <?= ($darsad <= '50' ? 'text-primary' : ($darsad <= '75' ? 'text-primary' : 'text-primary')) ?>" style="width: <?= $darsad ?>%"></div>
                                        </div>
                                        <p class="mt-1 p-0 mb-0 f2-11"><?= $darsad ?>%</p>
                                    </a>
                                </div>

                                <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2"><a href="#!">
                                        <h6 class="mb-0"><svg class="svg-inline--fa fa-circle fa-w-16 fs--1 me-3 text-warning" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                                                <path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z"></path>
                                            </svg><?= $lang[1757] ?></h6>
                                    </a>
                                    <a class="fs--2 text-600 mb-0" href="#!">

                                        <?php
                                        if ($allFaragir != 0) {
                                            $darsad = (mysqli_num_rows($resultPlanCheck) * 100) / abs(mysqli_num_rows($resultPlanCheck) - $allFaragir);
                                            //$darsad = round(abs((($allFaragir - mysqli_num_rows($resultPlanCheck)) / $allFaragir) * 100));
                                        } else {
                                            $darsad = 0;
                                        }
                                        ?>
                                        <div class="progress progress-index" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar rounded-3 <?= ($darsad <= '50' ? 'bg-warning' : ($darsad <= '75' ? 'bg-warning' : 'bg-warning')) ?>" style="width: <?= $darsad ?>%"></div>
                                        </div>
                                        <p class="mt-1 p-0 mb-0 f2-11"><?= $darsad ?>%</p>
                                    </a>

                                </div>


                                <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2"><a href="#!">
                                        <h6 class="mb-0"><svg class="svg-inline--fa fa-circle fa-w-16 fs--1 me-3 text-secondary" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                                                <path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z"></path>
                                            </svg><?= $lang[1758] ?></h6>
                                    </a>
                                    <a class="fs--2 text-600 mb-0" href="#!">

                                        <?php
                                        if ($allFaragir != 0) {
                                            $darsad = round(abs(100 - (($allFaragir - mysqli_num_rows($resultCheckEndPlan)) / $allFaragir) * 100));
                                        } else {
                                            $darsad = 0;
                                        }
                                        ?>
                                        <div class="progress progress-index" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar rounded-3 <?= ($darsad <= '50' ? 'bg-secondary' : ($darsad <= '75' ? 'bg-secondary' : 'bg-secondary')) ?>" style="width: <?= $darsad ?>%"></div>
                                        </div>
                                        <p class="mt-1 p-0 mb-0 f2-11"><?= $darsad ?>%</p>
                                    </a>

                                </div>
                                <div class="d-flex flex-between-center rounded-3 bg-light p-3"><a href="#!">
                                        <h6 class="mb-0"><svg class="svg-inline--fa fa-circle fa-w-16 fs--1 me-3 text-info" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                                                <path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z"></path>
                                            </svg><?= $lang[1759] ?></h6>
                                    </a>
                                    <a class="fs--2 text-600 mb-0" href="#!">

                                        <?php
                                        if ($allFaragir != 0) {
                                            $darsad = round(abs(100 - (($allFaragir - mysqli_num_rows($resultShayeste)) / $allFaragir) * 100));
                                        } else {
                                            $darsad = 0;
                                        }
                                        ?>
                                        <div class="progress progress-index" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar rounded-3 <?= ($darsad <= '50' ? 'bg-info' : ($darsad <= '75' ? 'bg-info' : 'bg-info')) ?>" style="width: <?= $darsad ?>%"></div>
                                        </div>
                                        <p class="mt-1 p-0 mb-0 f2-11"><?= $darsad ?>%</p>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    $now = date("Y/m/d");
                    $next_days = strtotime('+10 days');
                    $nextTenDays = date('Y/m/d', $next_days);
                    $resultPlan = Query("select * from tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$contractId' and tbl_plan_status='0' and tbl_plan_namayandeh='1' and tbl_plan_dateenglish BETWEEN '$now' and '$nextTenDays' order by tbl_plan_dateenglish ASC ");

                    ?>
                    <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2 ">
                        <div class="card ">
                            <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(assets/img/icons/spot-illustrations/corner-1.png);">
                            </div>
                            <div class="card-body p-0 position-relative height-380">
                                <div class="row g-0 justify-content-center">
                                    <div class="col-lg-12  p-3 mb-2">
                                        <h4><?= $lang[1720] ?></h4>
                                        <div class="box-content-two mt-2">
                                            <?php
                                            if (mysqli_num_rows($resultPlan) <= 0) {
                                                echo '<p>' . $lang[1768] . '</p>';
                                            } else {
                                                while ($readPlan = mysqli_fetch_array($resultPlan)) {
                                                    $expire = explode("/", $readPlan['tbl_plan_expire']);
                                                    $contractId = $readPlan['tbl_plan_contract'];
                                                    $faragir = $readPlan['tbl_plan_menti'];

                                                    $resultContract = Query("select * from tbl_contract where tbl_contract_id = '$contractId'  ");
                                                    $readContract =  mysqli_fetch_array($resultContract);

                                                    $resultMember = Query("select * from tbl_member where tbl_member_delete='0' and tbl_member_id ='$faragir' ");
                                                    $readMember =  mysqli_fetch_array($resultMember);
                                                    $a = jdate_words(array('mm' => $expire[1]));
                                                    echo '<div class="d-flex btn-reveal-trigger mb-2 mt-2">
                                        <div class="calendar"><span class="calendar-month">' . $a['mm'] . '</span><span class="calendar-day">' . toPersianNum($expire[2]) . '</span></div>
                                        <div class="flex-1 position-relative ps-3">
                                            <h6 class="fs-0 mb-2"><a class="item-info"  href="#" data-value="' . $readPlan['tbl_plan_code'] . '-' . $language_default . '">' . ($readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233' || $readPlan['tbl_plan_model'] == '1234' ? PlanModel($readPlan['tbl_plan_model']) : PlanModel($readPlan['tbl_plan_model'])) . '</a></h6>';
                                                    echo '<p class="fs-0  mb-1">' . ($readPlan['tbl_plan_model'] == '1230' || $readPlan['tbl_plan_model'] == '1231' || $readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233'  ? $lang[1636] . ' : ' : '') .
                                                        ($readPlan['tbl_plan_model'] == '1234' ? $lang[62] . ' : ' : '') .
                                                        ($readPlan['tbl_plan_model'] == '1235' ? $lang[62] . ' : ' : '') .
                                                        ($readPlan['tbl_plan_model'] == '1236' || $readPlan['tbl_plan_model'] == '1237' ? $lang[1637] . ' : ' : '') .
                                                        toPersianNum($readPlan['tbl_plan_expire']) . ' ، ' . $lang[1438] . ' : ' . $readContract['tbl_contract_customer'] . ' ، ' . $lang[1780] . ' : ' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '</p>
                                        </div>
                                    </div><hr>';
                                                }
                                            }
                                            ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


            <?php }
        } ?>


                </div>
    </div>

<?php } ?>












<?php if (!empty($_SESSION['user_access']) && $_SESSION['user_access'] == 'project') { ?>


    <div class="row">
        <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2 ">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card h-180">
                        <?php
                        $result_profile = Query("select * from tbl_user where tbl_user_id = '$this_user_id' ");
                        $read_profile = mysqli_fetch_array($result_profile);
                        $lastLogin = explode(" ", $read_profile['tbl_user_lastlogin']);
                        ?>
                        <div class="card-body d-flex gap-3 flex-column flex-sm-row align-items-center index-profile">
                            <img class="rounded-3" src="<?= (!empty($read_profile['tbl_user_img']) ? '../upload/' . $read_profile['tbl_user_img'] : 'assets/img/no-image.jpg') ?>" alt="" width="112">
                            <table class="table table-borderless fs--1 fw-medium mb-0">

                                <tbody>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[92] ?></td>
                                        <td class="p-1"><a class="text-600 text-decoration-none"><?= $read_profile['tbl_user_name'] ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[378] ?></td>
                                        <td class="p-1"><a class="text-600 text-decoration-none"><?= $read_profile['tbl_user_mobile'] ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[1707] ?></td>
                                        <td class="p-1"><a class="text-600 text-decoration-none"><?= toPersianNum($read_profile['tbl_user_username']) ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[1706] ?></td>
                                        <td class="p-1 text-600"><?= toPersianNum($lastLogin[0]) ?></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2">
            <div class="card h-180">
                <div class="bg-holder bg-card" style="background-image:url(assets/img/icons/spot-illustrations/corner-1.png);">
                </div>
                <?php
                $resultPages = Query("Select * from tbl_pages Where tbl_pages_code = '023574C' and tbl_pages_lng='$language_default'  ");
                $readPages = mysqli_fetch_array($resultPages);
                $textPages = $readPages['tbl_pages_text'];
                ?>
                <div class="card-body position-relative pages">
                    <h5 class="text-warning"><?= $readPages['tbl_pages_title'] ?></h5>
                    <p class="fs--1 mb-0"><?= $textPages ?></p>
                </div>
            </div>
        </div>
    </div>



    <?php
    $result = Query("SELECT * From tbl_contract where tbl_contract_project='$userCode' ORDER BY tbl_contract_id DESC ");
    if (mysqli_num_rows($result) > 0) {
    ?>

        <div class="row gx-3 mb-3">
            <div class="col-xxl-12 col-xl-12 p-0">
                <div class="card">
                    <div class="card-header border-bottom border-200 px-0">
                        <div class="d-lg-flex justify-content-between">
                            <div class="row flex-between-center gy-2 px-x1">
                                <div class="col-auto pe-0">
                                    <h4 class="mb-0"><?= $lang[1708] ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-4.png);">
                    </div>
                    <div class="card-body p-0 position-relative">
                        <div class="table-responsive scrollbar">
                            <?php
                            ?>
                            <table class="table table-sm mb-0 fs--1 table-view-tickets">
                                <thead class="text-800 bg-light">
                                    <tr>
                                        <th><?= $lang[1440] ?></th>
                                        <th><?= $lang[1352] ?></th>
                                        <th><?= $lang[1611] ?></th>
                                        <th><?= $lang[1458] ?></th>
                                        <th><?= $lang[1459] ?></th>
                                        <th><?= $lang[1955] ?></th>
                                        <th><?= $lang[714] ?></th>
                                        <th><?= $lang[1709] ?></th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    <?php
                                    while ($read =  mysqli_fetch_array($result)) {
                                        $contractId = $read['tbl_contract_id'];
                                        $resultChecklistLast = Query("SELECT tbl_checklist_persent From tbl_checklist where tbl_checklist_confirm='1' and tbl_checklist_contract ='$contractId' and  tbl_checklist_admin='$userCode' and tbl_checklist_delete='0' order by tbl_checklist_order ASC , tbl_checklist_order DESC limit 0,1 ");
                                        $readChecklistLast = mysqli_fetch_array($resultChecklistLast);


                                        $result_attach = Query("select * from tbl_attach where tbl_attach_contract = '$contractId'  ");
                                        $contractIdStr = '[' . $contractId . '],';
                                        $result_member = Query("select tbl_member_id from tbl_member where tbl_member_delete='0' and tbl_member_lastcontract like '%$contractIdStr%' ");

                                        $resultMentor = Query("SELECT * From tbl_user Where tbl_user_usercode !='' and tbl_user_usercode = '$read[tbl_contract_mentor]' ");
                                        $readMentor = mysqli_fetch_array($resultMentor);


                                        $resultChecklist = Query("SELECT tbl_checklist_confirm FROM tbl_checklist where tbl_checklist_delete='0' and tbl_checklist_lng='$language_default'  and tbl_checklist_contract ='$contractId' ");
                                        $resultChecklistConfirm = Query("SELECT tbl_checklist_confirm FROM tbl_checklist where tbl_checklist_confirm='1' and tbl_checklist_delete='0' and tbl_checklist_lng='$language_default'  and tbl_checklist_contract ='$contractId' ");
                                        $count = mysqli_num_rows($resultChecklist);
                                        $confirm = mysqli_num_rows($resultChecklistConfirm);
                                        $persent = ($confirm == 0 ? $confirm : round(abs(((($count - $confirm) / $count) * 100) - 100)));

                                    ?>
                                        <tr>
                                            <td class="align-middle pe-3 py-2  cat-code"><?= $read['tbl_contract_code'] ?></td>
                                            <td class="align-middle pe-3 py-2"><?= strtoupper($read['tbl_contract_codesection']) ?></td>
                                            <td class="align-middle pe-3 py-2 ">
                                                <h6 class="mb-0 cat-title"><a href="#" data-contract="<?= $contractId ?>" class="ajaxchecklist" data-lng="<?= $language_default ?>"><?= $read['tbl_contract_customer'] ?></a></h6>
                                            </td>
                                            <td class="align-middle subject py-2 pe-2"><?= $read['tbl_contract_startdate'] ?></td>
                                            <td class="align-middle status py-2 pe-2"><?= $read['tbl_contract_enddate'] ?></td>
                                            <td class="align-middle status py-2 pe-2"><?= (mysqli_num_rows($resultChecklistLast) > 0 ? $readChecklistLast['tbl_checklist_persent'] . '%' : '0') ?>
                                                <!-- <div class="d-flex align-items-center gap-2">
                                                  <div style="--falcon-circle-progress-bar: <?= $persent ?>">
                                                    <svg
                                                      class="circle-progress-svg"
                                                      width="26"
                                                      height="26"
                                                      viewBox="0 0 120 120"
                                                    >
                                                      <circle
                                                        class="progress-bar-rail"
                                                        cx="60"
                                                        cy="60"
                                                        r="54"
                                                        fill="none"
                                                        stroke-linecap="round"
                                                        stroke-width="12"
                                                      ></circle>
                                                      <circle
                                                        class="progress-bar-top"
                                                        cx="60"
                                                        cy="60"
                                                        r="54"
                                                        fill="none"
                                                        stroke-linecap="round"
                                                        stroke="<?= ($persent <= 30 ? '#e63757' : ($persent <= 50 ? '#F68F57' : ($persent <= 75 ? '#2A7BE4' : '#00D27B'))) ?>"
                                                        stroke-width="12"
                                                      ></circle>
                                                    </svg>
                                                  </div>
                                                  <h6 class="mb-0 text-700"><?= toPersianNum($persent) ?>%</h6>
                                                </div> -->
                                            </td>
                                            <td class="align-middle status py-2 pe-2"><?= (mysqli_num_rows($result_member) > '0' ? toPersianNum(mysqli_num_rows($result_member)) : '0') ?></td>
                                            <td class="align-middle subject py-2 pe-2">
                                                <a href="contract.php?do=view&id=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm  btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1710] ?>"><span class="bi-info-circle-fill"></span></a>
                                                <a href="faragir.php?contract=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm  btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1451] ?>"><span class="bi-people-fill"></span></a>
                                                <a href="contract.php?do=import&id=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1609] ?>"><span class="bi-person-lines-fill"></span></a>
                                                <a href="contract.php?do=importother&id=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1610] ?>"><span class="bi-person-plus-fill"></span></a>
                                                <a href="contract.php?do=mentor&id=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1923] ?>"><span class="bi-person-plus-fill"></span></a>
                                                <a href="checklist.php?contract=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1927] ?>"><span class="bi-card-checklist"></span></a>
                                                <a href="endreport.php?contract=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm btn-default" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1947] ?>"><span class="bi-calendar-check-fill"></span></a>
                                            </td>
                                        </tr>

                                    <?php

                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php }

    ?>

    <div class="ajaxresult">

    </div>

<?php } ?>


<?php if (!empty($_SESSION['user_access']) && $_SESSION['user_access'] == 'mentor') { ?>


    <div class="row">
        <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2 ">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card h-180">
                        <?php
                        $result_profile = Query("select * from tbl_user where tbl_user_id = '$this_user_id' ");
                        $read_profile = mysqli_fetch_array($result_profile);
                        $lastLogin = explode(" ", $read_profile['tbl_user_lastlogin']);
                        ?>
                        <div class="card-body d-flex gap-3 flex-column flex-sm-row align-items-center index-profile">
                            <img class="rounded-3" src="<?= (!empty($read_profile['tbl_user_img']) ? '../upload/' . $read_profile['tbl_user_img'] : 'assets/img/no-image.jpg') ?>" alt="" width="112">
                            <table class="table table-borderless fs--1 fw-medium mb-0">

                                <tbody>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[92] ?></td>
                                        <td class="p-1"><a class="text-600 text-decoration-none"><?= $read_profile['tbl_user_name'] ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[378] ?></td>
                                        <td class="p-1"><a class="text-600 text-decoration-none"><?= $read_profile['tbl_user_mobile'] ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[1707] ?></td>
                                        <td class="p-1"><a class="text-600 text-decoration-none"><?= toPersianNum($read_profile['tbl_user_username']) ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[1706] ?></td>
                                        <td class="p-1 text-600"><?= toPersianNum($lastLogin[0]) ?></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2">
            <div class="card h-180">
                <div class="bg-holder bg-card" style="background-image:url(assets/img/icons/spot-illustrations/corner-1.png);">
                </div>
                <?php
                $resultPages = Query("Select * from tbl_pages Where tbl_pages_code = '023574C' and tbl_pages_lng='$language_default'  ");
                $readPages = mysqli_fetch_array($resultPages);
                $textPages = $readPages['tbl_pages_text'];
                ?>
                <div class="card-body position-relative pages">
                    <h5 class="text-warning"><?= $readPages['tbl_pages_title'] ?></h5>
                    <p class="fs--1 mb-0"><?= $textPages ?></p>
                </div>
            </div>
        </div>
    </div>



    <?php
    $result = Query("SELECT * From tbl_contract where tbl_contract_mentor like '%$userCode%' ORDER BY tbl_contract_id DESC ");
    if (mysqli_num_rows($result) > 0) {
    ?>

        <div class="row gx-3 mb-3">
            <div class="col-xxl-12 col-xl-12 p-0">
                <div class="card">
                    <div class="card-header border-bottom border-200 px-0">
                        <div class="d-lg-flex justify-content-between">
                            <div class="row flex-between-center gy-2 px-x1">
                                <div class="col-auto pe-0">
                                    <h4 class="mb-0"><?= $lang[1708] ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-4.png);">
                    </div>
                    <div class="card-body p-0 position-relative">
                        <div class="table-responsive scrollbar">
                            <?php
                            ?>
                            <table class="table table-sm mb-0 fs--1 table-view-tickets">
                                <thead class="text-800 bg-light">
                                    <tr>
                                        <th><?= $lang[1440] ?></th>
                                        <th><?= $lang[1352] ?></th>
                                        <th><?= $lang[1611] ?></th>
                                        <th><?= $lang[1458] ?></th>
                                        <th><?= $lang[1459] ?></th>
                                        <th><?= $lang[714] ?></th>
                                        <th><?= $lang[1709] ?></th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    <?php
                                    while ($read =  mysqli_fetch_array($result)) {

                                        $contractId = $read['tbl_contract_id'];
                                        $result_attach = Query("select * from tbl_attach where tbl_attach_contract = '$contractId'  ");
                                        $contractIdStr = '[' . $contractId . '],';
                                        // $result_member = Query("select tbl_projectmember_mentor from tbl_projectmember where tbl_projectmember_contract = '$contractId' and tbl_projectmember_mentor='$userCode' ");
                                        $result_member = Query("SELECT 
                                                                p.tbl_projectmember_mentor, 
                                                                p.tbl_projectmember_contract,
                                                                p.tbl_projectmember_mentor ,
                                                                p.tbl_projectmember_user AS tbl_projectmember, 
                                                                m.tbl_member_lastcontract, 
                                                                m.tbl_member_id,
                                                                m.tbl_member_delete AS tbl_member
                                                            FROM
                                                                tbl_member m
                                                            INNER JOIN tbl_projectmember p 
                                                            ON p.tbl_projectmember_contract = '$contractId' 
                                                            and p.tbl_projectmember_mentor='$userCode'  
                                                            and p.tbl_projectmember_user= m.tbl_member_id 
                                                            and m.tbl_member_lastcontract like CONCAT ('%', p.tbl_projectmember_contract ,'%') 
                                                            and m.tbl_member_delete='0' ");

                                    ?>
                                        <tr>
                                            <td class="align-middle pe-3 py-2  cat-code"><?= $read['tbl_contract_code'] ?></td>
                                            <td class="align-middle pe-3 py-2"><?= strtoupper($read['tbl_contract_codesection']) ?></td>
                                            <td class="align-middle pe-3 py-2 ">
                                                <h6 class="mb-0 cat-title"><a href="#" data-contract="<?= $contractId ?>" class="ajaxlink" data-lng="<?= $language_default ?>"><?= $read['tbl_contract_customer'] ?></a></h6>
                                            </td>
                                            <td class="align-middle subject py-2 pe-2"><?= $read['tbl_contract_startdate'] ?></td>
                                            <td class="align-middle status py-2 pe-2"><?= $read['tbl_contract_enddate'] ?></td>
                                            <td class="align-middle status py-2 pe-2"><?= (mysqli_num_rows($result_member) > '0' ? toPersianNum(mysqli_num_rows($result_member)) : '0') ?></td>
                                            <td class="align-middle subject py-2 pe-2">
                                                <a href="contract.php?do=view&id=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm  btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1710] ?>"><span class="bi-info-circle-fill"></span></a>
                                                <a href="faragir.php?do=listall&contract=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm  btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1451] ?>"><span class="bi-people-fill"></span></a>
                                                <a href="checklist.php?contract=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1927] ?>"><span class="bi-card-checklist"></span></a>
                                            </td>
                                        </tr>

                                    <?php

                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php }
    ?>

    <div class="ajaxresult">

        <?php


        $result = Query("SELECT * From tbl_contract where tbl_contract_mentor like '%$userCode%' ORDER BY tbl_contract_id DESC limit 0,1");
        if (mysqli_num_rows($result) > 0) {

            $read =  mysqli_fetch_array($result);
            $contractId = $read['tbl_contract_id'];
            $contractIdStr = '[' . $contractId . '],';

            $result_member = Query("SELECT 
                                                                p.tbl_projectmember_mentor, 
                                                                p.tbl_projectmember_contract,
                                                                p.tbl_projectmember_user AS tbl_projectmember, 
                                                                m.tbl_member_lastcontract, 
                                                                m.tbl_member_id,
                                                                m.tbl_member_delete AS tbl_member
                                                            FROM
                                                                tbl_member m
                                                            INNER JOIN tbl_projectmember p 
                                                            ON p.tbl_projectmember_contract = '$contractId' 
                                                            and p.tbl_projectmember_user= m.tbl_member_id 
                                                            and m.tbl_member_lastcontract like CONCAT ('%', p.tbl_projectmember_contract ,'%') 
                                                            and m.tbl_member_delete='0' ");

            $result_member_question = Query("select tbl_memberquestion_id from tbl_memberquestion where tbl_memberquestion_contract='$contractId' and tbl_memberquestion_mentor='$this_user_id' ");
            $result_member_question_check = Query("select tbl_memberquestion_id from tbl_memberquestion where tbl_memberquestion_admincheck='1' and tbl_memberquestion_contract='$contractId' and tbl_memberquestion_mentor='$this_user_id' ");
            $resultPlan = Query("SELECT DISTINCT  tbl_plan_menti From tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$contractId' and tbl_plan_delete='0'");
            $resultPlanCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_mentor='$this_user_id' and tbl_memberskill_contract='$contractId' ");

            $resultCheckEndPlan = Query("SELECT COUNT(*) AS total
                        FROM tbl_memberskill ms
                        JOIN tbl_member m ON m.tbl_member_id = ms.tbl_memberskill_user
                        WHERE ms.tbl_memberskill_contract = '$contractId' and tbl_memberskill_datereport !=''
                        AND m.tbl_member_delete = '0'");

            //$resultCheckEndPlan = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_contract='$contractId' ");
            $allFaragir = mysqli_num_rows($result_member);
            $allFaragirHaveQuestion = mysqli_num_rows($result_member_question);
            $allFaragirHaveQuestionCheck = mysqli_num_rows($result_member_question_check);
            $noPlan = mysqli_num_rows($resultPlan);
            $eblaghNashode = $allFaragir - mysqli_num_rows($resultPlanCheck);
            $endPlan =  mysqli_num_rows($resultCheckEndPlan);
            $thisCustomer = $read['tbl_contract_customer'];
            if ($allFaragir > 0) {
        ?>

                <div class="row gx-3 mb-3">
                    <div class="col-xxl-12 col-xl-12 p-0">
                        <div class="card">
                            <div class="card-header border-bottom border-200 px-0">
                                <div class="d-lg-flex justify-content-between">
                                    <div class="row flex-between-center gy-2 px-x1">
                                        <div class="col-auto pe-0">
                                            <h4 class="mb-0"><?= $lang[1711] . ' ' . $thisCustomer ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-4.png);">
                            </div>
                            <div class="card-body p-0 position-relative">
                                <div class="table-responsive scrollbar">
                                    <?php
                                    ?>
                                    <table class="table table-sm mb-0 fs--1 table-view-tickets">
                                        <thead class="text-800 bg-light">
                                            <tr>
                                                <th><?= $lang[1711] ?></th>
                                                <th><?= $lang[714] ?></th>
                                                <th><?= $lang[1712] ?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="list">
                                            <tr>
                                                <td class="align-middle pe-2 py-2  "><?= $lang[1713] ?></td>
                                                <td class="align-middle pe-2 py-2 "><?= abs($allFaragir - $allFaragirHaveQuestion) ?></td>
                                                <td class="align-middle subject py-2 ">
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-3 rounded-3 bg-200" style="height: 5px; width:80px;" role="progressbar" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar rounded-pill" style="width: <?= (abs($allFaragir - $allFaragirHaveQuestion) == '0' ? '0' : round(abs((($allFaragir - ($allFaragirHaveQuestion - $allFaragir)) / $allFaragir * 100) - 100))) ?>%;"></div>
                                                        </div>
                                                        <div class="fw-semi-bold ms-2"><?= (abs($allFaragir - $allFaragirHaveQuestion) == '0' ? '0' : round(abs((($allFaragir - ($allFaragirHaveQuestion - $allFaragir)) / $allFaragir * 100) - 100))) ?>%</div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle pe-2 py-2  "><?= $lang[1714] ?></td>
                                                <td class="align-middle pe-2 py-2 "><?= abs($allFaragir - $allFaragirHaveQuestionCheck) ?></td>
                                                <td class="align-middle subject py-2 ">
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-3 rounded-3 bg-200" style="height: 5px; width:80px;" role="progressbar" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar rounded-pill" style="width: <?= (abs($allFaragir - $allFaragirHaveQuestionCheck) == '0' ? '0' : round(abs((($allFaragirHaveQuestionCheck * 100) / $allFaragir) - 100))) ?>%;"></div>
                                                        </div>
                                                        <div class="fw-semi-bold ms-2"><?= (abs($allFaragir - $allFaragirHaveQuestionCheck) == '0' ? '0' : round(abs((($allFaragirHaveQuestionCheck * 100) / $allFaragir) - 100)))  ?>%</div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle pe-2 py-2  "><?= $lang[1715] ?></td>
                                                <td class="align-middle pe-2 py-2 "><?= abs($eblaghNashode) ?></td>
                                                <td class="align-middle subject py-2 ">
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-3 rounded-3 bg-200" style="height: 5px; width:80px;" role="progressbar" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar rounded-pill" style="width: <?= ($eblaghNashode == '0' ? '0' : abs((($eblaghNashode) / $allFaragir * 100) - 100)) ?>%;"></div>
                                                        </div>
                                                        <div class="fw-semi-bold ms-2"><?= ($eblaghNashode == '0' ? '0' : round(abs((($eblaghNashode) / $allFaragir * 100) - 100))) ?>%</div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle pe-2 py-2  "><?= $lang[1717] ?></td>
                                                <td class="align-middle pe-2 py-2 "><?= abs($allFaragir - $endPlan) ?></td>
                                                <td class="align-middle subject py-2 ">
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-3 rounded-3 bg-200" style="height: 5px; width:80px;" role="progressbar" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar rounded-pill" style="width: <?= ($allFaragir - $endPlan == '0' ? '0' : round(abs((($allFaragir - $endPlan) / $allFaragir * 100) - 100))) ?>%;"></div>
                                                        </div>
                                                        <div class="fw-semi-bold ms-2"><?= ($allFaragir - $endPlan == '0' ? '0' : round(abs((($allFaragir - $endPlan) / $allFaragir * 100) - 100))) ?>%</div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    $now = date("Y/m/d");
                    $next_days = strtotime('+10 days');
                    $nextTenDays = date('Y/m/d', $next_days);
                    $resultPlan = Query("select * from tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$contractId' and tbl_plan_mentor ='$this_user_id' and tbl_plan_dateenglish BETWEEN '$now' and '$nextTenDays' order by tbl_plan_dateenglish ASC ");

                    ?>
                    <div class="col-lg-12 col-12 mt-3 p-0">
                        <div class="card height-one">
                            <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(assets/img/icons/spot-illustrations/corner-1.png);">
                            </div>
                            <div class="card-body p-0 position-relative">
                                <div class="row g-0 justify-content-center">
                                    <div class="col-lg-12  p-3 mb-2">
                                        <h4><?= $lang[1720] . ' ' . $thisCustomer ?></h4>
                                        <div class="box-content mt-2">
                                            <?php
                                            if (mysqli_num_rows($resultPlan) <= 0) {
                                                echo '<p>' . $lang[1768] . '</p>';
                                            } else {
                                                while ($readPlan = mysqli_fetch_array($resultPlan)) {
                                                    $expire = explode("/", $readPlan['tbl_plan_expire']);
                                                    $contractId = $readPlan['tbl_plan_contract'];
                                                    $faragir = $readPlan['tbl_plan_menti'];

                                                    $resultContract = Query("select * from tbl_contract where tbl_contract_id = '$contractId'  ");
                                                    $readContract =  mysqli_fetch_array($resultContract);

                                                    $resultMember = Query("select * from tbl_member where tbl_member_delete='0' and tbl_member_id ='$faragir' ");
                                                    $readMember =  mysqli_fetch_array($resultMember);
                                                    $a = jdate_words(array('mm' => $expire[1]));
                                                    echo '<div class="d-flex btn-reveal-trigger mb-2 mt-2">
                                        <div class="calendar"><span class="calendar-month">' . $a['mm'] . '</span><span class="calendar-day">' . toPersianNum($expire[2]) . '</span></div>
                                        <div class="flex-1 position-relative ps-3">
                                            <h6 class="fs-0 mb-2"><a href="plan.php?user=' . $readPlan['tbl_plan_menti'] . '&contract=' . $contractId . '" target="_blank">' . ($readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233' || $readPlan['tbl_plan_model'] == '1234' ? PlanModel($readPlan['tbl_plan_model']) : PlanModel($readPlan['tbl_plan_model'])) . '</a></h6>';
                                                    echo '<p class="fs-0 mb-1">' . ($readPlan['tbl_plan_model'] == '1230' || $readPlan['tbl_plan_model'] == '1231' || $readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233'  ? $lang[1636] . ' : ' : '') .
                                                        ($readPlan['tbl_plan_model'] == '1234' ? $lang[62] . ' : ' : '') .
                                                        ($readPlan['tbl_plan_model'] == '1235' ? $lang[62] . ' : ' : '') .
                                                        ($readPlan['tbl_plan_model'] == '1236' || $readPlan['tbl_plan_model'] == '1237' ? $lang[1637] . ' : ' : '') .
                                                        toPersianNum($readPlan['tbl_plan_expire']) . ' ، ' . $lang[1438] . ' : ' . $readContract['tbl_contract_customer'] . ' ، ' . $lang[1780] . ' : ' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '</p>
                                        </div>
                                    </div><hr>';
                                                }
                                            }
                                            ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


            <?php }
        } ?>


                </div>
    </div>

<?php } ?>

<?php
include './footer.inc.php';
?>