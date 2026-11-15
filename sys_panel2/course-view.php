<?php

include './includer.php';
$show_per_page = '500';
$pagetitle = $lang[1658];
$Tabpage = '90';
$thisaccess = '1,5';
include './header.inc.php';
$lng = 'fa';

$result_parent = Query("SELECT * From tbl_course where tbl_course_delete='0' and tbl_course_status='1' and tbl_course_code ='$code' and tbl_course_lng='$lng' ");
$read_parent = mysqli_fetch_array($result_parent);
$thisParent = $read_parent['tbl_course_class'];

$result_class = Query("SELECT * From tbl_courseparent where  tbl_courseparent_lng='$lng' and tbl_courseparent_code ='$thisParent' ");
$read_class = mysqli_fetch_array($result_class);
$thisParentClass = $read_class['tbl_courseparent_class'];

?>

<div class="row gx-3 mb-3">

    <div class="col-12 col-lg-9 col-md-12">
        <div class="card theme-wizard mb-5">
            <div class="card-body table-custom">



                <?php
                $resultcourse = Query("SELECT * From tbl_course where tbl_course_status='1' and  tbl_course_delete='0' and tbl_course_class='$thisParent' and tbl_course_lng='$lng'  ORDER BY tbl_course_order ASC , tbl_course_id DESC ");
                $i = 0;
                if (mysqli_num_rows($resultcourse) > 1) {
                    echo '<div class="card-header bg-light pt-3 pb-2"><ul class="nav justify-content-between nav-wizard" role="tablist">';
                    while ($readcourse = mysqli_fetch_array($resultcourse)) {
                        $result_view = Query("SELECT * From tbl_view where tbl_view_class='$readcourse[tbl_course_code]' ");

                        echo '<li class="nav-item" role="presentation">
            <a class="nav-link fw-semi-bold ' . ($code == $readcourse['tbl_course_code'] ? 'active' : '') . '" href="course-view.php?code=' . $readcourse['tbl_course_code'] . '" >
                <span class="nav-item-circle-parent">
                    <span class="nav-item-circle">';
                        if ($readcourse['tbl_course_icon'] == 'film') {
                            echo '<span class="fas fa-film"></span>';
                        } else if ($readcourse['tbl_course_icon'] == 'file-text2') {
                            echo '<span class="fab fa-stack-exchange"></span>';
                        } else if ($readcourse['tbl_course_icon'] == 'book') {
                            echo '<span class="fas fa-book"></span>';
                        } else if ($readcourse['tbl_course_icon'] == 'file') {
                            echo '<span class="fas fa-file-archive"></span>';
                        } else if ($readcourse['tbl_course_icon'] == 'mic') {
                            echo '<span class="fas fa-microphone-alt"></span>';
                        }
                        echo '</span></span>
                <span class="d-none d-md-block mt-1 fs--1">' . $readcourse['tbl_course_title'] . '</span>
            </a>
        </li>';

                        $i++;
                    }
                    echo '</ul></div>';
                }
                ?>


                <div class="class_detail_info">
                    <?php
                    $resultcourse = Query("SELECT * From tbl_course where tbl_course_mohtava !='' and tbl_course_delete='0' and tbl_course_status='1' and tbl_course_code='$code' and tbl_course_lng='$lng'  ORDER BY tbl_course_order DESC , tbl_course_id DESC limit 0,1");
                    $readcourse = mysqli_fetch_array($resultcourse);
                    ?>
                    <h4 class="mt-3"><?= $readcourse['tbl_course_title'] ?></h4>
                    <?php
                    $mohatava = str_replace(",", "", $readcourse['tbl_course_mohtava']);
                    $result = Query("SELECT * From tbl_mohtava Where  tbl_mohtava_lng='$lng' and tbl_mohtava_code ='$mohatava' ");
                    $read = mysqli_fetch_array($result);
                    @$pagetitle .= ' - ' . $title;
                    $file = explode(",", $read['tbl_mohtava_file']);
                    $ext = explode(".", $file[0]);
                    $ext = end($ext);

                    echo  $read['tbl_mohtava_text'];


                    if ($ext == 'mp4' || $ext == 'MP4') {
                        echo '<video controls crossorigin playsinline poster="' . (!empty($file[1]) ? '../upload/course/' . $file[1] : 'assets/img/no-image.jpg') . '" class="player">
                                <source src="../' . (!empty($read['tbl_mohtava_foldername']) ? 'uploadcourse/' . $read['tbl_mohtava_foldername'] . '/' . $file[0] : 'upload/course/' . $file[0])  . '" type="video/mp4" size="576">                             
                            </video>';
                    } else if ($ext == 'mp3' || $ext == 'MP3') {
                        echo '<audio crossorigin playsinline>
                                    <source src="../'  . (!empty($read['tbl_mohtava_foldername']) ? 'uploadcourse/' . $read['tbl_mohtava_foldername'] . '/' . $file[0] : 'upload/course/' . $file[0])  . '" type="audio/mp3">
                                </audio>';
                    } else {
                        echo '<div class="download"><a href="../' . (!empty($read['tbl_mohtava_foldername']) ? 'uploadcourse/' . $read['tbl_mohtava_foldername'] . '/' . $file[0] : 'upload/course/' . $file[0])  . '" target="_blank">' . $lang[1410] . '</a></div>';
                    }
                    ?>
                    <hr>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-3 col-md-12">


        <div class="card mb-3 ">
            <div class="card-header bg-light">
                <h5><?= $lang[2024] ?></h5>
            </div>
            <div class="card-body fs--1 text-center">
                <?php
                $resultcourse = Query("SELECT tbl_course_mohtava From tbl_course where tbl_course_mohtava !='' and tbl_course_delete='0' and tbl_course_code='$code' and tbl_course_lng ='$lng' ");
                $readcourse = mysqli_fetch_array($resultcourse);

                $mohatava = str_replace(",", "", $readcourse['tbl_course_mohtava']);
                $resultTeacher = Query("SELECT * From tbl_mohtava Where  tbl_mohtava_lng='$lng' and tbl_mohtava_code ='$mohatava' ");
                $readTeacher = mysqli_fetch_array($resultTeacher);
                $teacher = $readTeacher['tbl_mohtava_producer'];

                $resultUser = Query("select tbl_user_img,tbl_user_name from tbl_user where tbl_user_id = '$teacher' ");
                $readUser = mysqli_fetch_array($resultUser);
                if (mysqli_num_rows($resultUser) > 0) {
                    echo '<div class="avatar avatar-5xl">
                        <img class="rounded-circle" src="' . (empty($readUser['tbl_user_img']) ? 'asset/images/no-image.jpg' :  '../upload/' . $readUser['tbl_user_img']) . '" alt="">
                      </div>';
                    echo '<h4>' . $readUser['tbl_user_name'] . '</h4>';
                }
                // $result_class = Query("select * from tbl_class where tbl_class_lng='$lng' and tbl_class_code='$thisParentClass'");
                // $read_class = mysqli_fetch_array($result_class);
                // if ($read_class['tbl_class_question'] != 0) {
                // }

                ?>
            </div>
        </div>


        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5><?= $lang[2025] ?></h5>
            </div>
            <?php
            $result = Query("SELECT * From tbl_courseparent where tbl_courseparent_class='$thisParentClass' and tbl_courseparent_lng ='$lng' ORDER BY tbl_courseparent_order ASC , tbl_courseparent_id DESC  ");

            $k = 1;
            while ($read = mysqli_fetch_array($result)) {
                $resultcourse = Query("SELECT * From tbl_course where tbl_course_mohtava !='' and  tbl_course_delete='0' and tbl_course_code='$code' and tbl_course_lng ='$lng'  ORDER BY tbl_course_order DESC , tbl_course_id DESC limit 0,1");
                $readcourse = mysqli_fetch_array($resultcourse);
                //read current code for link
                $resultcourseclass_count = Query("SELECT * From tbl_course where tbl_course_mohtava !='' and tbl_course_delete ='0' and  tbl_course_class='$read[tbl_courseparent_code]' and tbl_course_lng ='$lng'  ");

                $resultcourseclass = Query("SELECT * From tbl_course where tbl_course_mohtava !='' and tbl_course_delete ='0' and tbl_course_class='$read[tbl_courseparent_code]' and tbl_course_lng ='$lng'  ORDER BY tbl_course_order DESC , tbl_course_id DESC limit 0,1");
                $readcourseclass = mysqli_fetch_array($resultcourseclass);


                echo '<br>';
                echo '<div class="card-body d-flex align-items-center ' . ($k != 1 ? 'border-top' : '') . ' p-2">
                                <div class="flex-1">
                                    <div class="row g-0 align-items-center pb-1 pt-1">
                                        <div class="col pe-4">
                                            <h6 class="fs--2 text-600"><a href="course-view.php?code=' . $readcourseclass['tbl_course_code'] . '" >
                                            ' . $read['tbl_courseparent_title'] . '</a></h6>
                                            </div></div>
                                </div>
                            </div>';

                $k++;
            }
            ?>

        </div>
    </div>
</div>
</div>
<?php
include './footer.inc.php';
?>