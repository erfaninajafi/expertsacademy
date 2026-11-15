<?php

include './includer.php';
$show_per_page = '500';
$pagetitle = $lang[1658];
$Tabpage = '90';
$thisaccess = '1,5';
include './header.inc.php';
$lng = 'fa';
?>

<div class="row gx-3 mb-3">

    <div class="col-12 col-lg-9 col-md-12">
        <div class="card theme-wizard mb-5">
            <div class="card-body table-custom">

                <?php
       $resultClass = Query("SELECT * From tbl_class where tbl_class_lng='$lng' and tbl_class_code ='$code' ");
       $readClass = mysqli_fetch_array($resultClass);

        ?>

                <div class="card-header bg-light pt-3 pb-2 mb-3">
                    <h4><?= $readClass['tbl_class_title'] ?></h4>
                </div>
                <?php
                $img = explode(",", $readClass['tbl_class_img']);
                $ext = explode(".", $img[2]);
                $ext = end($ext);

                foreach ($img as $key => $value) {
                    if (!empty($value)) {
                        $ext = explode(".", $value);
                        $ext = end($ext);
                        if ($ext == 'mp4' || $ext == 'MP4') {
                            echo '<video controls crossorigin playsinline poster="' . (!empty($img[1]) ? '../upload/' . $img[1] : 'asset/images/defualt.jpg') . '" class="player">
                                <source src="../upload/' . $img[$key]  . '" type="video/mp4" size="576">                             
                            </video>';
                        } else if ($ext == 'mp3' || $ext == 'MP3') {
                            echo '<audio crossorigin playsinline>
                            <source src="../upload/' . $img[$key]  . '" type="audio/mp3">
                        </audio>';
                        } else {
                            if ($key != 9) {
                            }
                        }
                    }
                }

                        echo  '<br>'.$readClass['tbl_class_content'];

                        ?>
            </div>
        </div>
    </div>


    <div class="col-12 col-lg-3 col-md-12">

        <div class="card mb-3 ">
            <div class="card-header bg-light">
                <h5><?=$lang[2024]?></h5>
            </div>
            <div class="card-body fs--1 text-center">
                <?php
                        $teacher = explode(",",$readClass['tbl_class_teacher']);
                        //str_replace("[","",str_replace("],","",$readClass['tbl_class_teacher']));

                        foreach ($teacher as $key => $value) {
                            if(!empty($value))
                            {
                                $thisVal = str_replace("[","",str_replace("]","",$value));
                            
                        $result_teacher = Query("select tbl_teacher_text,tbl_teacher_fname,tbl_teacher_lname,tbl_teacher_id,tbl_teacher_info,tbl_teacher_profile from tbl_teacher where tbl_teacher_id = '$thisVal' ");
                        $read_teacher = mysqli_fetch_array($result_teacher);
                        $text = explode("[short]", $read_teacher['tbl_teacher_text']);

                        echo '<div class="avatar avatar-5xl">
                        <img class="rounded-circle" src="'.(empty($read_teacher['tbl_teacher_profile']) ? 'asset/images/no-image.jpg' : '../upload/profile/' . $read_teacher['tbl_teacher_profile'] ).'" alt="">

                      </div>';
                        echo '<h4>' . $read_teacher['tbl_teacher_fname'] . ' ' . $read_teacher['tbl_teacher_lname'] . '</h4>
                                <p>' . $read_teacher['tbl_teacher_info'] . '</p>
                                <p>' . strip_tags($text[0]) . '</p>';
                            }
                        }
                        ?>
            </div>
        </div>

        <div class="card shopping-cart-bar-min-height mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0"><?=$lang[2025]?></h5>
            </div>
            <?php
                            $result = Query("SELECT * From tbl_courseparent where tbl_courseparent_class='$code' and tbl_courseparent_lng ='$lng' ORDER BY tbl_courseparent_order ASC , tbl_courseparent_id DESC ");
                            $k=1;
                            while ($read = mysqli_fetch_array($result)) {
                                //echo $read['tbl_courseparent_code'] . '<br>';
                                //read current code for style
                                $resultcourse = Query("SELECT * From tbl_course where tbl_course_delete='0' and tbl_course_status='1' and tbl_course_code='$code' and tbl_course_lng ='$lng'  ORDER BY tbl_course_order DESC , tbl_course_id ASC limit 0,1");
                                $readcourse = mysqli_fetch_array($resultcourse);
                                //read current code for link
                                //$resultcourseclass_count = Query("SELECT * From tbl_course where tbl_course_delete='0' and tbl_course_status='1' and tbl_course_class='$read[tbl_courseparent_code]' and tbl_course_lng ='$lng'");

                                $resultcourseclass = Query("SELECT * From tbl_course where tbl_course_delete='0' and tbl_course_status='1' and tbl_course_class='$read[tbl_courseparent_code]' and tbl_course_lng ='$lng'  ORDER BY tbl_course_order ASC , tbl_course_id DESC limit 0,1");
                                $readcourseclass = mysqli_fetch_array($resultcourseclass);
                             
                               
                                echo '<div class="card-body d-flex align-items-center '.($k !=1 ? 'border-top' : '').' p-2">
                                <div class="flex-1">
                                    <div class="row g-0 align-items-center pb-1 pt-1">
                                        <div class="col pe-4">
                                            <h6 class="fs--2 text-600"><a href="course-view.php?code=' . $readcourseclass['tbl_course_code'] .'" >
                                            ' . $read['tbl_courseparent_title'] . '</a></h6>';
                                    echo '</div></div>
                                </div>
                            </div>';

                                $k++;
                            }
                            ?>
        </div>
    </div>
</div>
                        
<?php
include './footer.inc.php';
?>
