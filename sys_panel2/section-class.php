<?php

include './includer.php';
$show_per_page = '100';

if (!isset($do)) {
    $pagetitle = $lang[1704] .' / '.$lang[1761] ;
    $t = 5;
}
else if (isset($do) && $do=='skill') {
    $pagetitle = $lang[1704] .' / '.$lang[1337] ;
    $t = 4;
}
else if (isset($do) && $do=='course') {
    $pagetitle = $lang[1704] .' / '.$lang[1762] ;
}
else if (isset($do) && $do=='class') {
    $pagetitle = $lang[1704] .' / '.$lang[1764] ;
}

$thisaccess = '1,5';

include './header.inc.php';

if (!isset($do)) {
}
else if (isset($do) && $do=='skill') {
    $result_bakhsh = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$code' and tbl_categories_lng='$language_default'");
    $read_bakhsh = mysqli_fetch_array($result_bakhsh);
    echo '<div class="col-md-12 mb-3">
        <div class="box box-body ">
            <div class="card mb-3">
                <div class="card-body custom-h5">
                <div class="row justify-content-between align-items-center">
                    <div class="col">
                    <div class="d-flex">
                        <div class="flex-1 fs--1">
                        <h5 class="fs-0">'.$lang[1761].'  : ' .$read_bakhsh['tbl_categories_title']  . '</h5>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        </div>'; 

}
else if (isset($do) && $do=='course') {
    $result_skill = Query("SELECT tbl_categories_title,tbl_categories_bakhsh,tbl_categories_code From tbl_categories Where tbl_categories_code = '$getskill' and tbl_categories_lng='$language_default'");
    $read_skill = mysqli_fetch_array($result_skill);
    $bakhsh = $read_skill['tbl_categories_bakhsh'];
    $result_bakhsh = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$bakhsh' and tbl_categories_lng='$language_default'");
    $read_bakhsh = mysqli_fetch_array($result_bakhsh);
    echo '<div class="col-md-12 mb-3">
        <div class="box box-body ">
            <div class="card mb-3">
                <div class="card-body custom-h5">
                <div class="row justify-content-between align-items-center">
                    <div class="col">
                    <div class="d-flex">
                        <div class="flex-1 fs--1">
                        <h5 class="fs-0">'.$lang[1761].'  : ' .$read_bakhsh['tbl_categories_title']  . '</h5>
                        <h5 class="fs-0">'.$lang[1337].'  : <a href="section-class.php?do=skill&code=' .$read_skill['tbl_categories_bakhsh']  . '">' .$read_skill['tbl_categories_title']  . '</a></h5>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        </div>'; 
}

else if (isset($do) && $do=='class') {
    $result_skill = Query("SELECT tbl_categories_code,tbl_categories_title,tbl_categories_bakhsh,tbl_categories_code From tbl_categories Where tbl_categories_code = '$getskill' and tbl_categories_lng='$language_default'");
    $read_skill = mysqli_fetch_array($result_skill);
    $bakhsh = $read_skill['tbl_categories_bakhsh'];
    $result_bakhsh = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$bakhsh' and tbl_categories_lng='$language_default'");
    $read_bakhsh = mysqli_fetch_array($result_bakhsh);

    $result_class = Query("SELECT * From tbl_class where tbl_class_lng='$language_default' and tbl_class_code='$getclass'");
    $read_class = mysqli_fetch_array($result_class);
    echo '<div class="col-md-12 mb-3">
        <div class="box box-body ">
            <div class="card mb-3">
                <div class="card-body custom-h5">
                <div class="row justify-content-between align-items-center">
                    <div class="col">
                    <div class="d-flex">
                        <div class="flex-1 fs--1">
                        <h5 class="fs-0">'.$lang[1761].'  : ' .$read_bakhsh['tbl_categories_title']  . '</h5>
                        <h5 class="fs-0">'.$lang[1337].'  : <a href="section-class.php?do=skill&code=' .$read_skill['tbl_categories_bakhsh']  . '">' .$read_skill['tbl_categories_title']  . '</a></h5>
                        <h5 class="fs-0">'.$lang[470].'  : <a href="?do=course&skill=' .$read_skill['tbl_categories_code']  . '">' .$read_class['tbl_class_title']  . '</a></h5>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        </div>'; 
}

// end include
?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle?></h4>
                            
                        </div>
                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
           
            <div class="card-body p-0 position-relative">

                <?php
                    

                        
                        if(!isset($do))
                    {
                        $result = Query("SELECT * From tbl_categories Where tbl_categories_type = '$t' and tbl_categories_parentid = '0' and tbl_categories_lng='$language_default' order by tbl_categories_order ASC   ");
                    }
                    else if(isset($do) && $do =='skill')
                    {
                        $result = Query("SELECT * From tbl_categories Where tbl_categories_bakhsh = '$code' and tbl_categories_lng='$language_default' order by tbl_categories_order ASC  ");
                    }
                    else if(isset($do) && $do =='course')
                    {
                        $result = Query("SELECT * From tbl_class where tbl_class_lng='$language_default' and tbl_class_skill like '%$getskill%' ORDER BY tbl_class_order ASC ");
                    }
                    else if(isset($do) && $do =='class')
                    {
                        $result = Query("SELECT * From tbl_courseparent where tbl_courseparent_class = '$getclass' and tbl_courseparent_lng='$language_default' ORDER BY tbl_courseparent_order ASC ");
                    }
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                        }
                        else {
                        $allfield = '';
                        ?>
                    <div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th><?=$lang[60] ?></th>
                                    <th><?= $lang[1763]?></th>
                                    <th><?= $lang[50] ?> </th>
                                    <th><?=(isset($do) && $do =='class' ? $lang[1521]: $lang[1749])?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                    if(isset($do) && $do =='course')
                    {

                            
                            ?>
                                <tr>
                                    <td class="align-middle"><?= $i ?></td>
                                    <td class="align-middle"><?= $read['tbl_class_classcode'] ?></td>
                                    <td class="align-middle"><?= $read['tbl_class_title']?></td>
                                    <td class="align-middle  pe-1"><a href="?do=class&class=<?= $read['tbl_class_code'] ?>&skill=<?=$getskill?>"  class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$lang[1366]?>"><span class="bi-list"></span></a>
                                    </td>
                                </tr>
                                <?php
                            $i++;

                    }
                    else if(isset($do) && $do =='class')
                    {
                        $result_cat = Query("SELECT tbl_course_title FROM tbl_course Where tbl_course_lng='$language_default' and tbl_course_class =  '$read[tbl_courseparent_code]'");
                            ?>
                                <tr>
                                    <td class="align-middle"><?= $i ?></td>
                                    <td class="align-middle"><?= $read['tbl_courseparent_classcode'] ?></td>
                                    <td class="align-middle"><?= $read['tbl_courseparent_title']?></td>
                                    <td class="align-middle  pe-1">
                                        <?php
                                        $k=1;
                                        while ($readCourse = mysqli_fetch_array($result_cat)) {
                                            echo '<label>'.$k.' - '.$readCourse['tbl_course_title'].'</label><br>';
                                            $k++;
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            $i++;

                    }
                    else {
                            $code = $read['tbl_categories_code'];
                            $resultkill = Query("SELECT * From tbl_bankfile Where tbl_bankfile_skill='$code' and tbl_bankfile_lng='$language_default' ");

                            if ($t == '4') {
                                $bakhsh = $read['tbl_categories_bakhsh'];
                                $result_bakhsh = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$bakhsh' and tbl_categories_lng='$language_default'");
                                $read_bakhsh = mysqli_fetch_array($result_bakhsh);
                                $karbord = $read['tbl_categories_karbord'];
                                $result_karbord = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$karbord' and tbl_categories_lng='$language_default'");
                                $read_karbord = mysqli_fetch_array($result_karbord);

                                $result_skill = Query("SELECT * From tbl_class where tbl_class_lng='$language_default' and tbl_class_skill like '%$code%'");
                                $result_question = Query("SELECT * From tbl_question where tbl_question_lng='$language_default' and tbl_question_disable='0' and tbl_question_skill = '$code' ");
                            }
                            if ($t == '5') {
                                $result_bakhsh = Query("SELECT tbl_categories_title,tbl_categories_code From tbl_categories Where tbl_categories_type='4' and tbl_categories_bakhsh = '$code' and tbl_categories_lng='$language_default'");
                                $read_bakhsh = mysqli_fetch_array($result_bakhsh);
                            }
                            ?>
                                <tr>
                                    <td class="align-middle"><?= $i ?></td>
                                    <td class="align-middle"><?= strtoupper($read['tbl_categories_catcode']) ?></td>
                                    <td class="align-middle"><?= $read['tbl_categories_title']?></td>
                                    <td class="align-middle  pe-1">
                                    <?= ($t == 4 ? '<a href="?do=course&skill=' . $read['tbl_categories_code'] . '"  class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="'.$lang[1305].' (' . toPersianNum(mysqli_num_rows($result_skill)) . ')"><span class="bi-list"></span></a>
                                ' : ''); ?><?= ($t == '5' ?  '<a href="?do=skill&code=' . $code . '" class="btn btn-dark me-1 mb-1 btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="'.$lang[1358].' (' . toPersianNum(mysqli_num_rows($result_bakhsh)) . ')"><span class="bi-list-check"></span></a>' : ''); ?>
                                    </td>
                                </tr>
                                <?php
                            $i++;
                        }
                        }
                        ?>
                                </tr>
                        </table>
                <?php
                    }
                    ?>
            </div>
        </div>
    </div>
</div>
</div>
<?php
include './footer.inc.php';

?>