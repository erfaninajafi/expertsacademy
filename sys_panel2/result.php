<?php
include './includer.php';
$show_per_page = '20';
$pagetitle = $lang[1201];
$Tabpage = '25';
$thisaccess = '1,5,9';
include './header.inc.php';


if (!empty($id)) {
    $this_user_id = $id;
}


if (!empty($_GET['section'])) {
    $section = " tbl_invoice_usercode = '$_GET[section]' and ";
} else {
    $section = '';
}
$select_filter = '';
if (!empty($_GET['filter'])) {

    $filter = $_GET['filter'];
    if ($filter == 'notpay') {
        $select_filter .= " and tbl_invoice_resnum =''  ";
    } elseif ($filter == 'pay') {
        $select_filter .= " and tbl_invoice_resnum !=''  ";
    }
}
if (!empty($_GET['class'])) {
    $select_filter .= " and tbl_invoice_class ='$_GET[class]'  and tbl_invoice_user='$_GET[user]' ";
}
$select_access = '';
if ($access == 2) {
    $select_access = " and tbl_invoice_admin ='$this_user_id'  ";
}
?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12 p-0">
        <div class="card">
            <div class="card-body p-0 position-relative">
                <div class="row g-3">
                    <div class="col-xxl-12 col-lg-12">
                        <div class="card rounded-3 overflow-hidden h-100">
                            <div class="card-body bg-line-chart-gradient d-flex flex-column justify-content-between">
                                <div class="col" data-bs-theme="light">
                                    <h4 class="text-white mb-0"><?=$lang[1722]?></h4>
                                    <?php
                                    $resultMenti = Query("select tbl_memberskill_id from tbl_memberskill where (tbl_memberskill_shayestegi != Null or tbl_memberskill_shayestegi !='' ) and tbl_memberskill_mentor='$this_user_id' ");
                                    ?>
                                    <p class="fs--1 fw-semi-bold text-white"><?=toPersianNum(mysqli_num_rows($resultMenti))?> <span class="opacity-50"><?=$lang[1723]?></span></p>
                                </div>
                                <!-- Find the JS file for the following calendar at: src/js/charts/echarts/line-payment.js-->
                                <!-- If you are not using gulp based workflow, you can find the transpiled code at: public/assets/js/theme.js-->
                                <div class="echart-line-payment" style="height: 200px; user-select: none; position: relative;" data-echart-responsive="true" _echarts_instance_="ec_1691330442265">
                                    <div style="position: relative; width: 855px; height: 200px; padding: 0px; margin: 0px; border-width: 0px; cursor: default;">
                                        <canvas style="position: absolute; left: 0px; top: 0px; width: 855px; height: 200px; user-select: none; padding: 0px; margin: 0px; border-width: 0px;" data-zr-dom-id="zr_0" width="1282" height="300"></canvas>
                                    </div>
                                    <div class="" style="position: absolute; display: block; border-style: solid; white-space: nowrap; z-index: 9999999; box-shadow: rgba(0, 0, 0, 0.2) 1px 2px 10px; background-color: rgb(249, 250, 253); border-width: 1px; border-radius: 4px; color: rgb(11, 23, 39); font: 500 12px / 18px Microsoft YaHei; padding: 7px 10px; top: 0px; left: 0px; transform: translate3d(40px, 86px, 0px); border-color: rgb(216, 226, 239); pointer-events: none; visibility: hidden; opacity: 0;">9:00 AM - 9.42 USD</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row gx-3 mb-3">
    <div class="col-xxl-6 col-xl-6 ">
        <div class="col-xxl-12 col-xl-12 ">
            <div class="card chart-height">
                <div class="card-body p-3 position-relative">
                <div class="tab-pane preview-tab-pane active" role="tabpanel" aria-labelledby="tab-dom-3437c979-0fa6-40a3-af4f-6e942a2b3868" id="dom-3437c979-0fa6-40a3-af4f-6e942a2b3868">
                      <div class="echart-gauge-grade-chart-example" style="min-height: 300px;" data-echart-responsive="true"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-12 col-xl-12 mt-3 ">
            <div class="card chart-height">
                <div class="card-body p-3 position-relative">
                    <?php
                $resultPlanCheck = Query("select  tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and (tbl_memberskill_datereport='' or tbl_memberskill_datereport is NULL) and tbl_memberskill_mentor='$this_user_id'  ");
                ?>
                    <span class="bi-people-fill faragir"></span>
                    <h4 class="text-center"><?=$lang[1724]?></h4>
                    <h3 class="text-center"><?=toPersianNum(mysqli_num_rows($resultPlanCheck)).' '.$lang[1575]?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-6 col-xl-6 ">
        <div class="card chart-height-two">
            <div class="card-body p-3 position-relative">
                        <div class="card-body">
                        <h5 class="text-center mb-3"><?=$lang[1725]?></h5>
                  <div class="tab-content">
                    <div class="tab-pane preview-tab-pane active" role="tabpanel" aria-labelledby="tab-dom-5e1db5d1-2f84-43d0-964b-117fb0c1cd91" id="dom-5e1db5d1-2f84-43d0-964b-117fb0c1cd91">
                      <div class="echart-doughnut-chart-example" style="min-height: 430px;" data-echart-responsive="true"></div>
                    </div>
                    <div class="tab-pane code-tab-pane" role="tabpanel" aria-labelledby="tab-dom-35909859-ba1a-458e-92e0-9ce7231dfd16" id="dom-35909859-ba1a-458e-92e0-9ce7231dfd16">
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
$finalOp = 0;
$resultOpinion = Query("select tbl_opinionend_result from tbl_opinionend where tbl_opinionend_model='1' and tbl_opinionend_mentor ='$this_user_id' ");
$allOp= mysqli_num_rows($resultOpinion);

$resultOpinion = Query("select SUM(tbl_opinionend_result) AS tbl_opinionend_result from tbl_opinionend where tbl_opinionend_model='1' and tbl_opinionend_mentor ='$this_user_id' ");
$readOpinion = mysqli_fetch_array($resultOpinion);
$number = $readOpinion['tbl_opinionend_result'];

$finalOp= ($allOp == 0 ? 0 : round(abs($number / $allOp)) / 100);


$color = array("0"=>"primary","1"=>"danger","2"=>"info","3"=>"success","4"=>"warning","5"=>"dark","6"=>"red","7"=>"orange","8"=>"secondary");
$chartArr = array();

$resultSection = Query("select * from tbl_categories where tbl_categories_type='5' and tbl_categories_lng='$language_default' order by tbl_categories_order DESC , tbl_categories_id DESC ");
$s=0;
while ($readSection = mysqli_fetch_array($resultSection)) {
    $chartArr[$s]["itemStyle"] =   $color[$s];
    $chartArr[$s]["code"] =   strtoupper($readSection['tbl_categories_catcode']);
    $chartArr[$s]["name"] =   ' '.$readSection['tbl_categories_title'].' ';
    $resContract = Query("select * from tbl_contract where tbl_contract_codesection='$readSection[tbl_categories_catcode]' and tbl_contract_mentor like '%$userCode%' ");
    $chartArr[$s]["value"] =   mysqli_num_rows($resContract);
    $s++;
}

//echo json_encode($chartArr , JSON_UNESCAPED_UNICODE);
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
            fontSize : '15px',
            Font : 'tahoma'
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
          data: [
            <?php 
                foreach ($chartArr as $key => $value) {
                    echo " {
                        value: $value[value],
                        name: '$value[name]',
                        itemStyle: {
                        color: utils.getColor('$value[itemStyle]')
                        }
                     }";
                     if($key != 8)
                     {
                        echo ",";
                     }
                     else {
                        
                     }
                }
            ?>
            
            ]
        }],
        tooltip: {
          trigger: 'item',
          padding: [7, 10],
          backgroundColor: utils.getGrays()['100'],
          borderColor: utils.getGrays()['300'],
          textStyle: {
            color: utils.getColors().dark,
            fontSize : '12px'
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
            <?php 
        for ($i=1402; $i <1414 ; $i++) { 
            $resultMenti = Query("select tbl_memberskill_id from tbl_memberskill where (tbl_memberskill_shayestegi != Null or tbl_memberskill_shayestegi !='' ) and tbl_memberskill_startdate like '%$i%' and tbl_memberskill_mentor='$this_user_id' ");
            echo mysqli_num_rows($resultMenti).($i == '1414' ? '' : ',');
        }
    ?>
        ],
    };
    var labels = [
        <?php 
        for ($i=1402; $i <1414 ; $i++) { 
            echo '"'.$i.'",';
        }
    ?>

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
                        value: <?=$finalOp?>,
                        name: 'Grade'
                    }]
                }]
            };
        };
        echartSetOption(chart, userOptions, getDefaultOptions);
    }
};
</script>
<?php
include './footer.inc.php';

?>