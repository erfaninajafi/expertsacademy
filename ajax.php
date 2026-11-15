<?php
include './includer.php';

if (empty($_SESSION['userid'])) {
    // header("location: $site_url/$lng/register/login/");
    // exit();
} else {
    $user_id = $_SESSION['userid'];
}

if (isset($do) && $do == 'libraryfilter') {
    $price = sqi($_POST['price']);
    $lng = sqi($_POST['lng']);
    $catall = '';
    if (!empty($_POST['cat'])) {
        $cat = $_POST['cat'];
        foreach ($cat as $key => $value) {
            if (!empty($value)) {
                $catall .= $value . ',';
            }
        }
    }
    $catall = rtrim($catall, ",");
    header("Location: $site_url/$lng/mypanel/library-filter/?price=$price&cat=$catall");
}

if (isset($do) && $do == 'rating') {
    // Ratings are submitted as whole numbers by the five-star widget.
    $memberId = $_SESSION['userid'] ?? null;
    $id_user = is_string($memberId) || is_int($memberId) ? (string) $memberId : '';
    $postID = filter_var($_POST['postID'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $ratingPoints = filter_var($_POST['ratingPoints'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]]);
    if (!preg_match('/^[A-Za-z0-9_-]{1,10}$/D', $id_user) || $postID === false || $ratingPoints === false) {
        echo 'err';
        exit();
    }

    $date = date('Y-m-d H:i:s');
    $rating = Query("SELECT rating_id FROM tbl_rating WHERE post_id='$postID' LIMIT 1");
    $previousVote = Query("SELECT tbl_rateuser_id FROM tbl_rateuser WHERE tbl_rateuser_product='$postID' AND tbl_rateuser_user='$id_user' LIMIT 1");
    if (!$rating || !$previousVote || mysqli_num_rows($previousVote) > 0) {
        echo 'err';
        exit();
    }

    if (mysqli_num_rows($rating) > 0) {
        // Increment in SQL so a later vote cannot overwrite the existing total.
        $saved = Query("UPDATE tbl_rating SET rating_number=COALESCE(rating_number, 0)+1, total_points=COALESCE(total_points, 0)+$ratingPoints, modified='$date' WHERE post_id='$postID'");
    } else {
        $saved = Query("INSERT INTO tbl_rating (post_id, rating_number, total_points, created, modified) VALUES ('$postID', 1, '$ratingPoints', '$date', '$date')");
    }

    if (!$saved) {
        echo 'err';
        exit();
    }

    $voteSaved = Query("INSERT INTO tbl_rateuser (tbl_rateuser_product, tbl_rateuser_user, tbl_rateuser_date) VALUES ('$postID', '$id_user', '$date')");
    echo $voteSaved ? 'ok' : 'err';
    exit();
}


if (isset($do) && $do == 'libraryfiltersite') {
    $price = sqi($_POST['price']);
    $lng = sqi($_POST['lng']);
    $catall = '';
    if (!empty($_POST['cat'])) {
        $cat = $_POST['cat'];
        foreach ($cat as $key => $value) {
            if (!empty($value)) {
                $catall .= $value . ',';
            }
        }
    }
    $catall = rtrim($catall, ",");
    header("Location: $site_url/$lng/library-filter/?price=$price&cat=$catall");
}

if (isset($do) && $do == 'mohtava') {
    $code = sqi($_GET['code']);
    $lng = sqi($_GET['lng']);
    $date = jdate("Y/m/d : A:h:s");
    $dateen = jdate("Y/m/d");
    $codetrack = Code("", "no", 7);



    $resultMohtavaView = Query("select * from tbl_mohtava where tbl_mohtava_code='$code' ");
    if (mysqli_num_rows($resultMohtavaView) <= 0) {
        header("Location: $site_url/$lng/mypanel/");
        exit();
    }
    $readMohtavaView = mysqli_fetch_array($resultMohtavaView);

    $price = $readMohtavaView['tbl_mohtava_price'];
    $sellprice = $readMohtavaView['tbl_mohtava_sellprice'];
    $percentmalek = $readMohtavaView['tbl_mohtava_percentmalek'];
    $percentdepartement = $readMohtavaView['tbl_mohtava_percentdepartement'];
    $percentacademy = $readMohtavaView['tbl_mohtava_percentacademy'];
    $pricedate = $readMohtavaView['tbl_mohtava_pricedate'];

    $result = Query("select * from tbl_mohtavaregister where tbl_mohtavaregister_mohtava='$code' and tbl_mohtavaregister_user='$user_id' ");
    if (mysqli_num_rows($result) <= 0) {
        Query("insert into tbl_mohtavaregister (
        tbl_mohtavaregister_mohtava,
        tbl_mohtavaregister_user,
        tbl_mohtavaregister_dateregister,
        tbl_mohtavaregister_code,
        tbl_mohtavaregister_dateen,
        tbl_mohtavaregister_model,
        tbl_mohtavaregister_price,
        tbl_mohtavaregister_sellprice,
        tbl_mohtavaregister_percentmalek,
        tbl_mohtavaregister_percentdepartement,
        tbl_mohtavaregister_percentacademy,
        tbl_mohtavaregister_pricedate,
        tbl_mohtavaregister_section) values(
        '$code',
        '$user_id',
        '$date',
        '$codetrack',
        '$dateen',
        '0',
        '$price',
        '$sellprice',
        '$percentmalek',
        '$percentdepartement',
        '$percentacademy',
        '$pricedate',
        '0')");
        header("Location: $site_url/$lng/mypanel/mycontentview/$code/");
    }
}
if (isset($do) && $do == 'interview') {

    $contract = sqi($_POST['contract']);
    $code = sqi($_POST['code']);
    $lng = ($_POST['lng'] == 'fa' || $_POST['lng'] == 'en' ? sqi($_POST['lng']) : 'fa');
    include_once './include/lang/site_' . $lng . '.php';
?>
    <script>
        $(".circle_percent").each(function() {
            var $this = $(this),
                $dataV = $this.data("percent"),
                $dataDeg = $dataV * 3.6,
                $round = $this.find(".round_per");
            $round.css("transform", "rotate(" + parseInt($dataDeg + 180) + "deg)");
            $this.append(
                '<div class="circle_inbox"><span class="percent_text"></span></div>'
            );
            $this.prop("Counter", 0).animate({
                Counter: $dataV
            }, {
                duration: 2000,
                easing: "swing",
                step: function(now) {
                    $this.find(".percent_text").text(Math.ceil(now) + "");
                },
            });
            if ($dataV >= 51) {
                $round.css("transform", "rotate(" + 360 + "deg)");
                setTimeout(function() {
                    $this.addClass("percent_more");
                }, 1000);
                setTimeout(function() {
                    $round.css("transform", "rotate(" + parseInt($dataDeg + 180) + "deg)");
                }, 1000);
            }
        });
    </script>

    <?php

    $resultQuestion = Query("select * from tbl_memberquestion where tbl_memberquestion_contract='$contract' and tbl_memberquestion_id='$code' and tbl_memberquestion_userid='$user_id' ");
    if (mysqli_num_rows($resultQuestion) > 0) {
        $readQuestion =  mysqli_fetch_array($resultQuestion);
        $contract = $readQuestion['tbl_memberquestion_contract'];
        $result_contract = Query("SELECT tbl_contract_namesection From tbl_contract where tbl_contract_id='$contract' ");
        $read_contract = mysqli_fetch_array($result_contract);
        $titleFirst = $read_contract['tbl_contract_namesection'];
        $result = 0;
        $allQuestion = 0;
        if ($readQuestion['tbl_memberquestion_check'] != '0') {
            $data = json_decode($readQuestion['tbl_memberquestion_dataanswer'], true);
            $k = 1;
            foreach ($data as $keyexam => $valueexam) {
                if ($keyexam != 'submit' && $keyexam != 'code') {
                    $questionId = $keyexam;
                    $result_question = Query("select * from tbl_question Where tbl_question_disable='0' and tbl_question_lng='$lng' and tbl_question_code = '$questionId' ");
                    $read_question = mysqli_fetch_array($result_question);
                    $all_value = json_decode($read_question['tbl_question_answer'], true);
                    $allQuestion++;
                    foreach ($all_value as $key => $value) {
                        $answervalue = $value["answervalue"];
                        //var_dump($answervalue);
                        if ($answervalue == 1 && $valueexam == $key) {
                            $result++;
                        }
                    }
                    $k++;
                }
            }
        }

    ?>
        <div class="row gx-3 mb-3 ">
            <div class="col-lg-12 col-xl-12 col-xxl-12">
                <div class="card h-lg-100">
                    <div class="card-body d-flex align-items-center">
                        <h4 class="mb-0"><?= $titleFirst ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gx-3 mb-3 ">
            <div class="col-lg-6 col-12">
                <div class="card height-three">
                    <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-1.png);">
                    </div>
                    <div class="card-body p-0 position-relative">
                        <div class="row g-0 justify-content-center">
                            <?php
                            echo '<h4 class="p-3">' . $lang[1392] . '</h4>
                        <div class=" h-100">
                            <div class="d-flex flex-center flex-column">
                            <div class="circle_percent" data-percent="' . (!empty($allQuestion) && $allQuestion != 0 ? round(abs(((($allQuestion - $result) / $allQuestion) * 100) - 100)) : '0') . '"> 
                            <div class="circle_inner">
                                <div class="round_per"></div>
                            </div>
                        </div>
                                <div class="text-center">
                                    <h6 class="fs-0 mb-1">
                                        <span class="fas fa-check text-primary me-1" data-fa-transform="shrink-2"></span>' . (!empty($allQuestion) && $allQuestion != 0 ? round(abs(((($allQuestion - $result) / $allQuestion) * 100) - 100)) : '0') . ' 
                                    </h6>
                                    <p class="fs--1 mb-0">' . ($readQuestion['tbl_memberquestion_check'] == '0' ? $lang[1351] : $lang[1352]) . '</p>
                                </div>
                            </div>
                        </div>';
                            ?>
                            <div class="col-sm-6  mt-2 text-center">
                                <?= ($readQuestion['tbl_memberquestion_check'] != '0' ? '<a href="' . $site_url . $lng . '/mypanel/showanswer/' . $readQuestion['tbl_memberquestion_id'] . '/" class="text-center">
                    <button class="btn btn-primary me-3 " type="button">' . $lang[1353] . '</button>
                    </a>' : '') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-6 col-12">
                <div class="card height-three">
                    <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-1.png);">
                    </div>
                    <div class="p-0 position-relative">
                        <div class="row g-0 justify-content-center p-3">
                            <h4><?= $lang[1391] ?></h4>
                            <?php
                            //echo "SELECT * From tbl_interview where tbl_interview_contract='$contract' and (tbl_interview_model='0' or tbl_interview_model is NULL ) and tbl_interview_menti='$user_id' and tbl_interview_lng='$lng' and tbl_interview_delete='0'  ";
                            $resultInterview = Query("SELECT * From tbl_interview where tbl_interview_contract='$contract' and (tbl_interview_model='0' or tbl_interview_model is NULL ) and tbl_interview_menti='$user_id' and tbl_interview_lng='$lng' and tbl_interview_delete='0'  ");
                            if (mysqli_num_rows($resultInterview) > 0) {
                                $readInterview =  mysqli_fetch_array($resultInterview);
                                echo '<div class="scroll">' . $readInterview['tbl_interview_reporttext'] . '</div>';
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
}


if (isset($do) && $do == 'skill') {

    $contract = sqi($_POST['contract']);
    $title = sqi($_POST['title']);
    $lng = ($_POST['lng'] == 'fa' || $_POST['lng'] == 'en' ? sqi($_POST['lng']) : 'fa');
    $skill[0] = sqi($_POST['skill']);
    include_once './include/lang/site_' . $lng . '.php';
    ?>
    <script>
        $(".circle_percent").each(function() {
            var $this = $(this),
                $dataV = $this.data("percent"),
                $dataDeg = $dataV * 3.6,
                $round = $this.find(".round_per");
            $round.css("transform", "rotate(" + parseInt($dataDeg + 180) + "deg)");
            $this.append(
                '<div class="circle_inbox"><span class="percent_text"></span></div>'
            );
            $this.prop("Counter", 0).animate({
                Counter: $dataV
            }, {
                duration: 2000,
                easing: "swing",
                step: function(now) {
                    $this.find(".percent_text").text(Math.ceil(now) + "");
                },
            });
            if ($dataV >= 51) {
                $round.css("transform", "rotate(" + 360 + "deg)");
                setTimeout(function() {
                    $this.addClass("percent_more");
                }, 1000);
                setTimeout(function() {
                    $round.css("transform", "rotate(" + parseInt($dataDeg + 180) + "deg)");
                }, 1000);
            }
        });
    </script>
    <?php
    $titleFirst = '';
    $resultContract = Query("SELECT tbl_contract_customer From tbl_contract where tbl_contract_id ='$contract' limit 0,1 ");
    $readContract = mysqli_fetch_array($resultContract);
    $resultPlan = Query("SELECT tbl_plan_title FROM tbl_plan where tbl_plan_delete='0' and tbl_plan_lng='$lng' and tbl_plan_menti = '$user_id' and tbl_plan_contract ='$contract' and tbl_plan_skill='$skill[0]'");
    $resultPlanConfirm = Query("SELECT tbl_plan_title FROM tbl_plan where tbl_plan_status='1' and tbl_plan_delete='0' and tbl_plan_lng='$lng' and tbl_plan_menti = '$user_id' and tbl_plan_contract ='$contract' and tbl_plan_skill='$skill[0]'");
    $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$skill[0]' and tbl_categories_lng='$lng' ");
    $readSkill = mysqli_fetch_array($resultSkill);
    $titleFirst = $readSkill['tbl_categories_title'];
    ?>
    <div class="row">

        <div class="col-lg-12 col-xl-12 col-xxl-12 mb-0 p-lg-2">
            <div class="card h-lg-100">
                <div class="card-body d-flex align-items-center">
                    <h4 class="mb-0"><?= $titleFirst ?></h4>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-xl-6 col-xxl-6 mb-0 p-lg-2">
            <div class="card height-one">
                <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-1.png);">
                </div>
                <div class="card-body p-0 position-relative">
                    <div class="row g-0 justify-content-center">
                        <?php

                        $resultNumberModel = Query("SELECT tbl_plan_rank,tbl_plan_vazn From tbl_plan where tbl_plan_model='1236' and tbl_plan_skill='$skill[0]' and tbl_plan_contract='$contract' and tbl_plan_menti='$user_id' and tbl_plan_delete='0'   ORDER BY tbl_plan_id DESC,tbl_plan_order DESC LIMIT 0,1 ");
                        $readNumberModel = mysqli_fetch_array($resultNumberModel);
                        @$firstRank = $readNumberModel['tbl_plan_rank'];
                        @$firstVazn = $readNumberModel['tbl_plan_vazn'];
                        $resultNumberModel = Query("SELECT tbl_plan_rank,tbl_plan_vazn From tbl_plan where tbl_plan_model='1237' and tbl_plan_skill='$skill[0]' and tbl_plan_contract='$contract' and tbl_plan_menti='$user_id' and tbl_plan_delete='0'   ORDER BY tbl_plan_id DESC,tbl_plan_order DESC LIMIT 0,1 ");
                        $readNumberModel = mysqli_fetch_array($resultNumberModel);
                        @$secondRank = $readNumberModel['tbl_plan_rank'];
                        $resultAll = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$contract' and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ");
                        $rowAll = mysqli_num_rows($resultAll);

                        $resultAllNazari = Query("SELECT tbl_plan_id From tbl_plan where (tbl_plan_model ='1230' or tbl_plan_model='1231' or tbl_plan_model ='1232' or tbl_plan_model='1233') and tbl_plan_contract='$contract' and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ");
                        $rowAllNazari = (mysqli_num_rows($resultAllNazari) == 0 ? '0' : mysqli_num_rows($resultAllNazari));

                        $resultConfirmNazari = Query("SELECT tbl_plan_id From tbl_plan where (tbl_plan_model ='1230' or tbl_plan_model='1231' or tbl_plan_model ='1232' or tbl_plan_model='1233') and tbl_plan_contract='$contract' and tbl_plan_status='1' and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ");
                        $rowConfirmNazari = (mysqli_num_rows($resultConfirmNazari) == 0 ? '0' : mysqli_num_rows($resultConfirmNazari));


                        $resultAllAmali = Query("SELECT tbl_plan_id From tbl_plan where (tbl_plan_model ='1234' or tbl_plan_model='1235') and tbl_plan_contract='$contract' and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ");
                        $rowUnAllAmali = (mysqli_num_rows($resultAllAmali) == 0 ? '0' : mysqli_num_rows($resultAllAmali));

                        $resultConfirmAmali = Query("SELECT tbl_plan_id From tbl_plan where (tbl_plan_model ='1234' or tbl_plan_model='1235') and tbl_plan_contract='$contract' and tbl_plan_status='1' and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ");
                        $rowUnConfirmAmali = (mysqli_num_rows($resultConfirmAmali) == 0 ? '0' : mysqli_num_rows($resultConfirmAmali));




                        $resultAzmoonAmali = Query("SELECT tbl_plan_rank From tbl_plan where tbl_plan_model ='1236' and tbl_plan_contract='$contract'  and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' order by tbl_plan_expire DESC limit 0,1 ");
                        if (mysqli_num_rows($resultAzmoonAmali) == 0) {
                            $readAzmoonAmali = 0;
                        } else {
                            $readAzmoonAmaliRes = mysqli_fetch_array($resultAzmoonAmali);
                            $readAzmoonAmali = $readAzmoonAmaliRes['tbl_plan_rank'];
                        }

                        $resultAzmoonKatbi = Query("SELECT tbl_plan_rank From tbl_plan where tbl_plan_model ='1237' and tbl_plan_contract='$contract'  and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' order by tbl_plan_expire DESC limit 0,1 ");
                        $readAzmoonKatbi = mysqli_fetch_array($resultAzmoonKatbi);


                        @$secondVazn = (!empty($firstVazn) ? 100 - $firstVazn : 0);



                        echo ' <div class="col-sm-6  pe-lg-2 mb-2">
                <div class=" h-100">
                    <div class="card-body d-flex flex-center flex-column">
                    <div class="circle_percent" data-percent="' . (!empty($rowAllNazari) && $rowAllNazari != 0 ? round(abs(((($rowAllNazari - $rowConfirmNazari) / $rowAllNazari) * 100) - 100)) : '0') . '"> 
                    <div class="circle_inner">
                        <div class="round_per"></div>
                    </div>
                </div>
                        <div class="text-center mt-3">
                            <h6 class="fs-0 mb-1">
                                <span class="fas fa-check text-primary me-1" data-fa-transform="shrink-2"></span>' . (!empty($rowAllNazari) && $rowAllNazari != 0 ? round(abs(((($rowAllNazari - $rowConfirmNazari) / $rowAllNazari) * 100) - 100)) : '0') . ' 
                            </h6>
                            <p class="fs--1 mb-0">' . $lang[1325] . '</p>
                        </div>
                    </div>
                </div>
            </div>';

                        echo ' <div class="col-sm-6  pe-lg-2 mb-2">
                <div class=" h-100">
                    <div class="card-body d-flex flex-center flex-column">
                    <div class="circle_percent" data-percent="' . (!empty($rowUnAllAmali) && $rowUnAllAmali != 0 ? round(abs(((($rowUnAllAmali - $rowUnConfirmAmali) / $rowUnAllAmali) * 100) - 100)) : '0') . '"> 
                    <div class="circle_inner">
                        <div class="round_per"></div>
                    </div>
                </div>
                        <div class="text-center mt-3">
                            <h6 class="fs-0 mb-1">
                                <span class="fas fa-check text-primary me-1" data-fa-transform="shrink-2"></span>' . (!empty($rowUnAllAmali) && $rowUnAllAmali != 0 ? round(abs(((($rowUnAllAmali - $rowUnConfirmAmali) / $rowUnAllAmali) * 100) - 100)) : '0') . ' 
                            </h6>
                            <p class="fs--1 mb-0">' . $lang[1326] . '</p>
                        </div>
                    </div>
                </div>
            </div>';

                        $shayestegi = 0;
                        $emtehan = 0;
                        $resultCheckNumber = Query("select tbl_number_shayestegi,tbl_number_emtehan from tbl_number where tbl_number_user='$user_id' and tbl_number_contract='$contract' and tbl_number_skill='$skill[0]' ");
                        if (mysqli_num_rows($resultCheckNumber) > 0) {
                            $readCheckNumber = mysqli_fetch_array($resultCheckNumber);
                            $shayestegi = $readCheckNumber['tbl_number_shayestegi'];
                            $emtehan = $readCheckNumber['tbl_number_emtehan'];
                        }


                        $emtehanNumber = (($firstRank * $firstVazn) / 100) + (($secondRank * $secondVazn) / 100);


                        $finalNumber = abs($emtehan - 100);
                        $finalEmtehanNumber = ($emtehanNumber * $emtehan) / 100;
                        $finalShayestegiNumber = ($shayestegi * $finalNumber) / 100;
                        $finalRank = $finalEmtehanNumber + $finalShayestegiNumber;


                        // echo '<hr>
                        // <a href="' . $site_url . $lng . '/mypanel/skilldetail/'.$contract.'/'. $skill[0] . '/" class="text-center"><button class="btn btn-primary me-3 mb-3" type="button">'.$lang[1315].'
                        // </button></a>';
                        ?>

                    </div>
                </div>
            </div>
        </div>
        <?php
        $now = date("Y/m/d");
        $next_days = strtotime('+10 days');
        $nextTenDays = date('Y/m/d', $next_days);
        $resultPlan = Query("select * from tbl_plan where tbl_plan_lng='$lng' and tbl_plan_contract='$contract' and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_dateenglish BETWEEN '$now' and '$nextTenDays' order by tbl_plan_dateenglish ASC ");

        ?>

        <div class="col-lg-6 col-xl-6 col-xxl-6 mb-0 p-lg-2">
            <div class="row g-0 justify-content-center">
                <div class="col-xxl-12 col-xl-12 mb-0 pe-0">
                    <div class="card height-one">
                        <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-1.png);">
                        </div>
                        <div class="card-body p-3 position-relative">
                            <div class="row g-0 justify-content-center">
                                <div class="col-lg-12  p-3 mb-2">
                                    <h5><?= $lang[1365] ?></h5>
                                    <div class="box-content-two mt-2">
                                        <?php
                                        if (mysqli_num_rows($resultPlan) <= 0) {
                                            echo '<p>' . $lang[1366] . '</p>';
                                        } else {
                                            while ($readPlan = mysqli_fetch_array($resultPlan)) {
                                                $expire = explode("/", $readPlan['tbl_plan_expire']);
                                                $contractId = $readPlan['tbl_plan_contract'];
                                                $faragir = $readPlan['tbl_plan_menti'];

                                                $resultContract = Query("select * from tbl_contract where tbl_contract_id = '$contractId'  ");
                                                $readContract =  mysqli_fetch_array($resultContract);

                                                $resultMember = Query("select * from tbl_member where tbl_member_id ='$faragir' ");
                                                $readMember =  mysqli_fetch_array($resultMember);
                                                $a = jdate_words(array('mm' => $expire[1]));
                                                echo '<div class="d-flex btn-reveal-trigger mb-2 mt-2">
                                        <div class="calendar"><span class="calendar-month">' . $a['mm'] . '</span><span class="calendar-day">' . toPersianNum($expire[2]) . '</span></div>
                                        <div class="flex-1 position-relative ps-3">
                                            <h6 class="fs-0 mb-2"><a class="item-info"  href="#" data-value="' . $readPlan['tbl_plan_code'] . '-' . $lng . '">' . ($readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233' || $readPlan['tbl_plan_model'] == '1234' ? PlanModel($readPlan['tbl_plan_model']) : PlanModel($readPlan['tbl_plan_model'])) . '</a></h6>';
                                                echo '<p class="fs-0  mb-1">' . ($readPlan['tbl_plan_model'] == '1230' || $readPlan['tbl_plan_model'] == '1231' || $readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233'  ? $lang[1367] . ' : ' : '') .
                                                    ($readPlan['tbl_plan_model'] == '1234' ? $lang[62] . ' : ' : '') .
                                                    ($readPlan['tbl_plan_model'] == '1235' ? $lang[62] . ' : ' : '') .
                                                    ($readPlan['tbl_plan_model'] == '1236' || $readPlan['tbl_plan_model'] == '1237' ? $lang[1416] . ' : ' : '') .
                                                    toPersianNum($readPlan['tbl_plan_expire']) . '</p>
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
        </div>

        <div class="col-lg-12 col-xl-12 col-xxl-12 mb-1 p-lg-2">
            <div class="row g-0 justify-content-center">
                <div class="col-xxl-4 col-xl-4 mb-2 pe-2">
                    <div class="card height-two-color color-one">
                        <div class="card-body p-3 position-relative">
                            <span class="bi-card-checklist"></span>
                            <h4><?= $lang[1322] ?></h4>
                            <h3 class="number"><?= (!empty($readAzmoonKatbi['tbl_plan_rank']) ? $readAzmoonKatbi['tbl_plan_rank']  : '0') ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-4 mb-2 pe-2">
                    <div class="card height-two-color color-two">
                        <div class="card-body p-3 position-relative">
                            <span class="bi-pencil-square"></span>
                            <h4><?= $lang[1323] ?></h4>
                            <h3 class="number"><?= (!empty($readAzmoonAmali) ? $readAzmoonAmali : '0') ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-4 mb-2 pe-0">
                    <div class="card height-two-color color-three">
                        <div class="card-body p-3 position-relative">
                            <span class="bi-bookmark-star"></span>
                            <h4><?= $lang[1324] ?></h4>
                            <h3 class="number"><?= ($shayestegi != '0' ? $finalRank : '0') ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php

}

if (isset($do) && $do == 'descriptionindex') {
    $val = sqi($_POST['val']);
    $val = explode('-', $val);

    $result = Query("SELECT * From tbl_plan where tbl_plan_code ='$val[0]' and tbl_plan_delete='0' and tbl_plan_lng='$val[1]'");
    if (mysqli_num_rows($result) > 0) {
        $readPlan = mysqli_fetch_array($result);

        echo '<h6 class="fs-0 mb-2">' . $lang[1119] . ' : ' . ($readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233' || $readPlan['tbl_plan_model'] == '1234' ? PlanModel($readPlan['tbl_plan_model']) : PlanModel($readPlan['tbl_plan_model'])) . '</h6>';
        echo '<p class="mb-1">' . ($readPlan['tbl_plan_model'] == '1230' || $readPlan['tbl_plan_model'] == '1231' || $readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233'  ? $lang[1367] . ' : ' : '') .
            ($readPlan['tbl_plan_model'] == '1234' ? $lang[62] . ' : ' : '') .
            ($readPlan['tbl_plan_model'] == '1235' ? $lang[62] . ' : ' : '') .
            ($readPlan['tbl_plan_model'] == '1236' || $readPlan['tbl_plan_model'] == '1237' ? $lang[1637] . ' : ' : '') .
            toPersianNum($readPlan['tbl_plan_expire']) . '</p>';
        echo '<p>' . $readPlan['tbl_plan_text'] . '</p>';
    }
}

if (isset($do) && $do == 'timeline') {
    $contract = sqi($_POST['contract']);
    $skill = sqi($_POST['skill']);
    $lng = sqi($_POST['lng']);

    ?>
        <div class="mb-3">
            <div>
                <h5 class="mb-0"><?= $lang[1316] ?></h5>
            </div>
            <div class="timeline-vertical">
                <?php
                $result = Query("SELECT * From tbl_plan where tbl_plan_contract='$contract' and tbl_plan_skill ='$skill' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ORDER BY tbl_plan_expire ASC ");
                if (mysqli_num_rows($result) > 0) {
                    $i = 1;
                    while ($read =  mysqli_fetch_array($result)) {
                        $now = strtotime(date("Y/m/d"));
                        $endate = strtotime($read['tbl_plan_dateenglish']);
                        // echo '<tr '.(($endate - $now) <= 259200 &&  $read['tbl_plan_status'] == '0' ? 'class="expire-soon"' : '').'>
                        // <td class="align-middle subject py-2 pe-4">
                        //                 ' . PlanModel($read['tbl_plan_model']).'</td>';
                        //     echo '</tr>';

                        echo ' <div class="timeline-item timeline-item-' . ($i % 2 == 1 ? 'start' : 'end') . '">
                            <div class="timeline-icon icon-item icon-item-lg text-' . ($now > $endate && $read['tbl_plan_status'] == '0'  ? 'warning' : ($now > $endate  ? 'secondary' : 'primary')) . ' border-300"><span class="fs-1 fas fa-flag"></span></div>
                            <div class="row">
                              <div class="col-lg-6 timeline-item-time">
                                <div>
                                  <p class="fs--2 text-600 pt-2">' . $lang[398] . ' : ' . $read['tbl_plan_expire'] . '</p>
                                </div>
                              </div>
                              <div class="col-lg-6">
                                <div class="timeline-item-content">
                                  <div class="timeline-item-card" >
                                    <h6 class="mb-2">' . PlanModel($read['tbl_plan_model']) . '</h6>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>';
                        $i++;
                    }
                } ?>

            </div>
        </div>

    <?php
}


if (isset($_GET['do']) && $_GET['do'] == 'description') {
    $val = sqi($_POST['val']);
    $val = explode('-', $val);
    $result = Query("SELECT tbl_plan_text From tbl_plan where tbl_plan_code ='$val[0]' and tbl_plan_delete='0' and tbl_plan_lng='$val[1]'");
    if (mysqli_num_rows($result) > 0) {
        $read = mysqli_fetch_array($result);
        echo '<p>' . $read['tbl_plan_text'] . '</p>';
    }
}
if (isset($do) && $do == 'lesson') {
    $code = sqi($_POST['code']);
    $codeFinal = explode("-", $code);
    $code = $codeFinal[0];
    $codeContract = $codeFinal[1];
    $date = jdate("Y/m/d : A:h:s");
    if (empty($_SESSION['userid'])) {
        header("location: $site_url/$lng/mypanel/");
        exit();
    } else if ($code == '') {
        header("location: $site_url/$lng/mypanel/");
        exit();
    } else {
        $result = Query("select * from tbl_course where tbl_course_code='$code'");
        if (mysqli_num_rows($result) == 0) {
            header("location: $site_url/$lng/mypanel/");
            exit();
        } else {

            $read = mysqli_fetch_array($result);
            Query("Insert into tbl_view (
            tbl_view_user,
            tbl_view_class,
            tbl_view_date,
            tbl_view_course,
            tbl_view_contract
        ) values('$_SESSION[userid]','$code','$date','$read[tbl_course_class]','$codeContract')");

            $resultMohtava = Query("SELECT tbl_course_mohtava From tbl_course where tbl_course_code='$code'  ");
            $readMohtava = mysqli_fetch_array($resultMohtava);

            $resultMohtavaView = Query("select * from tbl_mohtava where tbl_mohtava_code='$readMohtava[tbl_course_mohtava]' ");
            $readMohtavaView = mysqli_fetch_array($resultMohtavaView);
            $view = $readMohtavaView['tbl_mohtava_view'] + 1;

            $price = $readMohtavaView['tbl_mohtava_price'];
            $sellprice = $readMohtavaView['tbl_mohtava_sellprice'];
            $percentmalek = $readMohtavaView['tbl_mohtava_percentmalek'];
            $percentdepartement = $readMohtavaView['tbl_mohtava_percentdepartement'];
            $percentacademy = $readMohtavaView['tbl_mohtava_percentacademy'];
            $pricedate = $readMohtavaView['tbl_mohtava_pricedate'];

            Query("update tbl_mohtava set tbl_mohtava_view='$vi' where tbl_mohtava_code='$readMohtava[tbl_course_mohtava]' ");

            $dateen = jdate("Y/m/d");
            $result = Query("select * from tbl_mohtavaregister where tbl_mohtavaregister_contract='$codeContract' and tbl_mohtavaregister_mohtava='$readMohtava[tbl_course_mohtava]' and tbl_mohtavaregister_user='$_SESSION[userid]' ");
            if (mysqli_num_rows($result) <= 0) {
                Query("insert into tbl_mohtavaregister (
            tbl_mohtavaregister_mohtava,
            tbl_mohtavaregister_user,
            tbl_mohtavaregister_dateregister,
            tbl_mohtavaregister_dateen,
            tbl_mohtavaregister_model,
            tbl_mohtavaregister_contract,
            tbl_mohtavaregister_price,
            tbl_mohtavaregister_sellprice,
            tbl_mohtavaregister_percentmalek,
            tbl_mohtavaregister_percentdepartement,
            tbl_mohtavaregister_percentacademy,
            tbl_mohtavaregister_pricedate,
            tbl_mohtavaregister_section
            ) values(
            '$readMohtava[tbl_course_mohtava]',
            '$_SESSION[userid]',
            '$date',
            '$dateen',
            '1',
            '$codeContract',
            '$price',
            '$sellprice',
            '$percentmalek',
            '$percentdepartement',
            '$percentacademy',
            '$pricedate','0')");
            }

            header("location: $site_url/$lng/mypanel/course/$code-$codeContract/");
            exit();
        }
    }
}

if (isset($do) && $do == 'setting') {
    if (empty($_SESSION['userid'])) {
        header("location: $site_url/$lng/register/login/");
        exit();
    } else {
        $user_id = $_SESSION['userid'];
    }
    $color = sqi($_POST['color']);
    $fluid = sqi($_POST['fluid']);
    $navbarStyle = sqi($_POST['navbarStyle']);

    // $result = Query("select * from tbl_member where tbl_member_id= '$user_id' ");
    // $read = mysqli_fetch_array($result);

    // if($read['tbl_member_fluid'] =='on')
    // {

    // }

    Query("update tbl_member SET 
    tbl_member_color='$color',
    tbl_member_fluid='$fluid',
    tbl_member_navbarStyle='$navbarStyle'
    where tbl_member_id= '$user_id' ");
}

if (isset($do) && $do == 'exam') {
    if (!empty($_SESSION['userid'])) {
        $user_id = $_SESSION['userid'];
    } else {
        $user_id = '';
    }
    if (isset($_POST['submit'])) {
        $all_value = array();
        foreach ($_POST as $key => $value) {
            $all_value[$key] = sqi($value);
            if ($key == 'code') {
                $code = sqi($value);
            }
        }
        $thisCode = explode("-", $code);
        $date = jdate("Y/m/d : a:h:s");
        $all_value = json_encode($all_value, JSON_UNESCAPED_UNICODE);
        Query("insert into tbl_exam (tbl_exam_user,tbl_exam_data,tbl_exam_date,tbl_exam_course,tbl_exam_contract) values('$user_id','$all_value','$date','$thisCode[0]','$thisCode[1]')");
        header("location: $site_url/$lng/mypanel/examcourse/$code/");
        exit();
    }
}

if (isset($do) && $do == 'updateresume') {
    $user_id = $_SESSION['userid'];
    $date = jdate("Y/m/d : a:h:s");
    Query("update tbl_member set tbl_member_resumeupdatedate='$date' , tbl_member_resumeupdate='1' where tbl_member_id = '$user_id' ");
    echo $lang[21];
}
