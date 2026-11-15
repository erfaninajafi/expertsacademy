<?php
include './includer.php';
$show_per_page = '30';
$pagetitle = $lang[1435];
$Tabpage = '91';
$thisaccess = '1,5';
include './header.inc.php';
if ($adminQuestion == '0') {
    header("Location: index.php");
}

if (!empty($_GET['skill'])) {
    $skill = " tbl_question_skill = '$_GET[skill]' and ";
    $resultSkill = Query("SELECT tbl_categories_bakhsh,tbl_categories_title From tbl_categories Where tbl_categories_code = '$_GET[skill]' and tbl_categories_lng='$language_default' ");
    $readSkill = mysqli_fetch_array($resultSkill);
    $bakhsh = $readSkill['tbl_categories_bakhsh'];
    $result_bakhsh = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$bakhsh' and tbl_categories_lng='$language_default'");
    $read_bakhsh = mysqli_fetch_array($result_bakhsh);

    echo '<div class="card mb-3">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6 col-lg-4 mb-4 mb-lg-0">
          <h5 class="mb-3 fs-0"><a href="category_manage.php?t=4">' . $lang[1304] . '</a></h5>
          <p class="mb-1 fs--1">' . $lang[1337] . ' : ' . $readSkill['tbl_categories_title'] . '</p>
          <p class="mb-1 fs--1">' . $lang[1339] . ' : ' . $read_bakhsh['tbl_categories_title'] . '</p>
        </div>
      </div>
    </div>
  </div>';

    $skillTitle = $readSkill['tbl_categories_title'];
}

?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle ?></h4>
                            <?= (!isset($do) ? '<div class="plus">
                                <a href="?do=add' . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . '"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?= (!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <?= (!isset($do) ? '<div class="bg-holder d-none d-lg-block bg-card" >
            </div>' : '') ?>
            <div class="card-body p-0 position-relative">


                <?php
                if (!empty($_GET['orderby'])) {
                    if ($_GET['orderby'] == 'order') {
                        $orderby = ' ORDER BY tbl_question_order ASC ';
                    }
                    if ($_GET['orderby'] == 'title') {
                        $orderby = ' ORDER BY tbl_question_title ASC ';
                    }
                } else {
                    $orderby = ' ORDER BY tbl_question_order ASC ';
                }

                if (!isset($do)) { ?>
                    <table>
                        </tr>
                        <td class="col-sm-6 p-3">
                            <form action="levelquestion.php?do=search" method="POST" class="search-form">
                                <input type="text" class="form-control" value="<?= $keyword ?>" name="keyword">
                                <input type="hidden" class="form-control" value="<?= $_GET['skill'] ?>" name="skill">
                                <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                            </form>
                        </td>
                        </tr>
                    </table>
                    <?php
                    // start count db result for pagination               
                    $result_for_count = Query("SELECT * From tbl_question where $skill tbl_question_lng='$language_default' and tbl_question_title like '%$keyword%' and tbl_question_skill !=''  and tbl_question_disable='0'  ");
                    $count_result = mysqli_num_rows($result_for_count);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);
                    // end count db result for pagination               
                    $result = Query("SELECT * From tbl_question where $skill tbl_question_lng='$language_default' and tbl_question_title like '%$keyword%' and tbl_question_skill !=''  and tbl_question_disable='0'   $orderby  LIMIT $start_contnet,$show_per_page ");
                    // error : if not find record show no item for view
                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    // start show list
                    echo '<form action="?do=runupdate" method="POST">
                        <input type="hidden" name="skill" value="' . $_GET['skill'] . '">                        
                        <div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th><a href="?orderby=order&skill=' . $_GET['skill'] . '">' . $lang[1336] . ' <span class="' . (!empty($_GET['orderby']) && $_GET['orderby'] == 'order' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>
                                    <th><a href="?orderby=title&skill=' . $_GET['skill'] . '">' . $lang[1372] . ' <span class="' . (!empty($_GET['orderby']) && $_GET['orderby'] == 'title' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>
                                    <th>' . $lang[1359] . '</th>
                                    <th>' . $lang[1337] . '</th>
                                    <th>' . $lang[1436] . '</th>
                                    <th>' . $lang[1437] . '</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $i = 1;
                    while ($read = mysqli_fetch_array($result)) {
                        $mentor = $read['tbl_question_mentor'];
                        $resultMentor = Query("SELECT * From tbl_user Where tbl_user_id = '$mentor' ");
                        $readMentor = mysqli_fetch_array($resultMentor);
                        $cat = $read['tbl_question_skill'];
                        $result_cat = Query("Select * from tbl_categories where tbl_categories_lng='$language_default' and tbl_categories_code='$cat' ");
                        $read_cat = mysqli_fetch_array($result_cat);

                        echo '<tr>
                            <td class="align-middle p-2">' . $i . '</td>
                            <td class="align-middle p-2">
                            <input type="hidden" name="code[]" value="' . $read['tbl_question_code'] . '">
                            <input type="text" name="o' . $read['tbl_question_code'] . '" value="' . $read['tbl_question_order'] . '" maxlength="5" style="width: 26px;text-align: center;height: 23px;margin-left: 2px;">
                            </td>
                            <td class="align-middle p-2"><a href="?do=edit&code=' . $read['tbl_question_code'] . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . '" >' . $read['tbl_question_title'] . '</a></td>
                            <td class="align-middle p-2">' . ($read['tbl_question_level'] == 0 ? $lang[1361] : ($read['tbl_question_level'] == 1 ? $lang[1362] : $lang[1363])) . '</td>
                            <td class="align-middle p-2">' . $read_cat['tbl_categories_title'] . '</td>
                            <td class="align-middle p-2">' . ($read['tbl_question_model'] == '1' ? $lang[1295] : ($read['tbl_question_model'] == '2' ? $lang[2062] : $lang[1373]))  . '</div>
                            <td class="align-middle p-2">' . @$readMentor['tbl_user_name'] . '</div>
                            <td class="align-middle p-2">
                            <a href="?do=edit&code=' . $read['tbl_question_code'] . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                            <a href="?do=delete&code=' . $read['tbl_question_code'] . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a></td>
                            </tr>';
                        $i++;
                    }
                    echo '</tbody></table><input type="submit" name="runupdate" value="' . $lang[1342] . '" class="btn btn-warning update-btn m-3" ></form>';
                    // start pagination                
                    if ($page > $page_number) {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    if ($count_result > $show_per_page) {
                        if ($page == 1) {
                            $back_page_number = 2;
                        } else {
                            $back_page_number = $page;
                        }
                        // $other_page_keyword = '';
                        if (isset($keyword) && $keyword == TRUE) {
                            $other_page_keyword = "&keyword=";
                        } else {
                            $other_page_keyword = '';
                        }
                    ?>
                        <table class="table">
                            <tr>
                                <td class="col-lg-8">
                                    <ul class="pagination">
                                        <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . (!empty($_GET['orderby']) ? '&orderby=' . $_GET['orderby'] : '') . $other_page_keyword . $keyword . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . (!empty($code) ? '&code=' . $_GET['code'] : '') ?>"><?= $lang[75] ?></a>
                                        </li>
                                        <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                        </li>
                                        <li><a class="page-link" href="<?= '?page=' . ($page + 1) . (!empty($_GET['orderby']) ? '&orderby=' . $_GET['orderby'] : '') .  $other_page_keyword . $keyword . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . (!empty($code) ? '&code=' . $_GET['code'] : '') ?>"><?= $lang[74] ?></a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                <?php

                    }
                }
                if (isset($do) && $do == 'runupdate') {
                    $levelcode = $_POST['levelcode'];
                    $code = $_POST['code'];
                    $skill = $_POST['skill'];
                    foreach ($code as $key => $value) {
                        $val = $_POST['o' . $value];
                        Query("update tbl_question set tbl_question_order='$val' Where tbl_question_code = '$value' ");
                    }
                    header("Location: levelquestion.php?skill=$skill");
                }
                if (isset($do) && $do == 'gotopage') {
                    if (isset($_POST['gotopage'])) {
                        $page_number = $_POST['page_number'];
                        header("Location: levelquestion.php?page=$page_number");
                    }
                }
                // end = pagination
                // start = do add
                if (isset($do) && $do == 'add') {
                    echo '<div class="col-lg-8 col-12 p-3">
                            <form action="?do=additem" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="skill" value="' . $_GET['skill'] . '">';

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group ">
                                  <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                                  <input type="text" name="title_' . $language[$i] . '" class="form-control">
                                  </div>';
                    }
                    echo '<div class="form-group mb-3 mt-3">
                        <label>' . $lang[53] . '</label>
                        <input type="file" name="img" class="form-control">
                        </div><div class="form-group check-style">
                            <label><input type="radio" name="level" value="0" checked >' . $lang[1361] . '</label>
                            <label><input type="radio" name="level" value="1"  >' . $lang[1362] . '</label>
                            <label><input type="radio" name="level" value="2"  >' . $lang[1363] . '</label>
                            </div>
                            <hr><div class="form-group check-style" >
                            <label>' . $lang[1379] . '</label>
                            <label>
                                <input type="radio" name="model" value="2" checked> ' . $lang[2062] . '
                            </label>
                            <label>
                                <input type="radio" name="model" value="1" checked> ' . $lang[1295] . '
                            </label>
                            <label>
                                <input type="radio" name="model" value="0"> ' . $lang[1374] . ' 
                            </label>
                        </div>
                        <hr>';
                    echo '<div class="col-12 col-lg-12"><div class="plus-add">' . $lang[1375] . '</div></div><div class="price_input">
                            <div class="col-12 col-lg-4 col-md-12">
                            <div class="form-group">
                            <label>' . $lang[1376] . '</label>
                            <input type="text" name="answerfa[]" class="form-control" >
                            </div>
                            </div>
                            <div class="col-12 col-lg-4 col-md-12">
                            <div class="form-group">
                            <label>' . $lang[1377] . '</label>
                            <input type="text" name="answeren[]" class="form-control" >
                            </div>
                            </div>
                            <div class="col-12 col-lg-2 col-md-12">
                            <div class="form-group">
                            <label>' . $lang[1378] . '</label>
                            <input type="text" name="answervalue[]" class="form-control" data-type="number" >
                            </div>
                            </div>
                            </div>
                            <div class="result row"></div>';

                    echo '<div class="col-12 col-md-12"><div class="form-group">
        <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary mt-3"></div>
        </div></form></div>';
                }
                // start = add record to db
                if (isset($do) && $do == 'additem') {
                    if (isset($_POST['additem'])) {
                        $code = Code("", "no", 7);
                        $date = jdate("Y/m/d : a:h:s");
                        $model = $_POST['model'];
                        $skill = $_POST['skill'];
                        $level = $_POST['level'];
                        $answer_valuefa = $_POST['answerfa'];
                        $answer_valueen = $_POST['answeren'];
                        $answervalue_value = $_POST['answervalue'];
                        $all_value = array();
                        foreach ($answer_valuefa as $key => $value) {
                            if (!empty($value)) {
                                $all_value[$key]['answervalue'] = imjection($answervalue_value[$key]);
                                $all_value[$key]['answerfa'] = $value;
                                $all_value[$key]['answeren'] = $answer_valueen[$key];
                            }
                        }
                        $all_value = json_encode($all_value, JSON_UNESCAPED_UNICODE);


                        $img = $_FILES['img']['name'];
                        $order = $_POST['order'];
                        $read_file_name = explode(".", $img);
                        $extn = end($read_file_name);
                        // upload images after rename 
                        if (empty($img) && $extn == 'docx' || $extn == 'DOCX' || $extn == 'doc' || $extn == 'DOC' || $extn == 'pdf' || $extn == 'PDF' ||  $extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' || $extn == 'swf' || $extn == 'SWF') {
                            $img_name = 'question_' . time() . '.' . $extn;
                            move_uploaded_file($_FILES['img']["tmp_name"], "../upload/question/" . $img_name);
                        } else {
                            $img_name = '';
                        }

                        for ($i = 1; $i <= $language_count; $i++) {
                            $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                            $language_name = $language[$i];
                            Query("insert into tbl_question (
                                tbl_question_levelquestion,
                                tbl_question_title,
                                tbl_question_answer,
                                tbl_question_dateadd,
                                tbl_question_disable,
                                tbl_question_level,
                                tbl_question_mentor,
                                tbl_question_code,
                                tbl_question_lng,
                                tbl_question_skill,
                                tbl_question_parent,
                                tbl_question_model,
                                tbl_question_img) 
                                values('1','$title_[$i]','$all_value','$date','0','$level','$this_user_id','$code','$language_name','$skill','','$model','$img_name')");
                        }
                        $_SESSION['error'] = $lang[32];
                        header("Location: levelquestion.php?skill=$skill");
                        exit();
                    }
                }
                // end  = add record  
                if (isset($do) && $do == 'edit') {
                    function read_fild($language_id = NULL, $field = NULL)
                    {
                        if ($language_id == NULL) {
                            $check_language_id = "";
                        } else {
                            $check_language_id = "and tbl_question_lng = '$language_id'";
                        }
                        $code = $_GET['code'];
                        $result = Query("SELECT * FROM tbl_question Where tbl_question_code =  '$code'  $check_language_id ");
                        $read = mysqli_fetch_array($result);
                        $read = $read[$field];
                        return $read;
                    }
                    // form for edit
                    echo '<div class="col-lg-8 col-12 p-3">
            <form action="?do=edititem" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="code" value="' . $code . '" >
            <input type="hidden" name="skill" value="' . $_GET['skill'] . '" >
            <input type="hidden" name="old_img" value="' . exjection(read_fild(NULL, 'tbl_question_img')) . '" >';


                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                  <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                  <input type="text" name="title_' . $language[$i] . '" value="' . exjection(read_fild($language[$i], 'tbl_question_title')) . '" class="form-control">
                  </div><div class="form-group mb-3 mt-3">';
                    }

                    $img = read_fild(NULL, 'tbl_question_img');

                    if ($img) {
                        echo '<div class="form-group"><table ><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[53] . '</label></td>
                                      <td><input type="file" name="img"></td><td><a href="../upload/question/' . $img . '" target="_blank" ><span class="btn btn-xs btn-success"><span class="bx bxs-show"></span></span></a>
                                      <a href="?code=' . $code . '&do=deleteimage&file=' . read_fild(NULL, 'tbl_question_img') . '&skill=' . $_GET['skill'] . '"  ><span class="btn btn-xs btn-danger"><span class="bx bxs-trash"></span></span></a>
                                      </td></tr></table></div>';
                    } else {
                        echo '<div class="form-group"><table ><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[53] . '</label></td>
                                      <td><input type="file" name="img">
                                      </td></tr></table></div>';
                    }

                    echo '</div><div class="form-group check-style">
                            <label>
                            <input type="radio" name="level" value="0" ' . (read_fild(NULL, 'tbl_question_level') == 0 ? 'checked' : '') . '  > ' . $lang[1361] . '
                            </label>
                            <label>
                            <input type="radio" name="level" value="1"  ' . (read_fild(NULL, 'tbl_question_level') == 1 ? 'checked' : '') . '> ' . $lang[1362] . '
                            </label>
                            <label>
                            <input type="radio" name="level" value="2"  ' . (read_fild(NULL, 'tbl_question_level') == 2 ? 'checked' : '') . '> ' . $lang[1363] . '
                            </label>
                            </div>
                            <hr>
                            <div class="form-group check-style" >
                            <label>' . $lang[1379] . '</label>
                            <label>
                                <input type="radio" name="model" value="2" ' . (read_fild(NULL, 'tbl_question_model') == 2 ? 'checked' : '') . '> ' . $lang[2062] . '
                            </label>
                            <label>
                                <input type="radio" name="model" value="1" ' . (read_fild(NULL, 'tbl_question_model') == 1 ? 'checked' : '') . ' > ' . $lang[1295] . '
                            </label>
                            <label>
                                <input type="radio" name="model" value="0" ' . (read_fild(NULL, 'tbl_question_model') == 0 ? 'checked' : '') . '> ' . $lang[1374] . ' 
                            </label>
                        </div>';
                    $all_value = json_decode(read_fild(NULL, 'tbl_question_answer'));
                    foreach ($all_value as $key => $value) {
                        echo '<div id="d' . $key . '" style="display: flex;">
            <div class="col-12 col-lg-1 col-md-12">
            <div class="delete-item" data="' . $key . '">' . $lang[24] . '</div>
            </div>
                <div class="col-12 col-lg-4 col-md-12">
                <div class="form-group">
                <label>' . $lang[1376] . '</label>
                <input type="text" name="answerfa[]" class="form-control" value="' . $value->answerfa . '" required>
                </div>
                </div>
                <div class="col-12 col-lg-4 col-md-12">
                <div class="form-group">
                <label>' . $lang[1377] . '</label>
                <input type="text" name="answeren[]" class="form-control" value="' . $value->answeren . '" required>
                </div>
                </div>
                <div class="col-12 col-lg-2 col-md-12">
                <div class="form-group">
                <label>' . $lang[1378] . '</label>
                <input type="text" name="answervalue[]" class="form-control" value="' . $value->answervalue . '" data-type="number" required>
                </div>
                </div>
              </div>';
                    }
                    echo '<hr><div class="col-12 col-lg-12 col-md-12"><div class="plus-add">' . $lang[1375] . '</div></div>
                            <div class="price_input">
                            <div class="col-12 col-lg-4 col-md-12">
                            <div class="form-group">
                            <label>' . $lang[1376] . '</label>
                            <input type="text" name="answerfa[]" class="form-control">
                            </div>
                            </div>
                            <div class="col-12 col-lg-4 col-md-12">
                            <div class="form-group">
                            <label>' . $lang[1377] . '</label>
                            <input type="text" name="answeren[]" class="form-control">
                            </div>
                            </div>
                            <div class="col-12 col-lg-4 col-md-12">
                            <div class="form-group">
                            <label>' . $lang[1378] . '</label>
                            <input type="text" name="answervalue[]" class="form-control" data-type="number" >
                            </div>
                            </div>
                            </div>
                            <div class="result row"></div>
                            <div class="form-group mt-3">
                            <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary" >
                            </div>
                    </form></div>';
                }

                if (isset($do) && $do == 'edititem') {
                    if (isset($_POST['updateitem'])) {
                        $code = $_POST['code'];
                        $skill = $_POST['skill'];
                        $level = $_POST['level'];
                        $model = $_POST['model'];
                        $date = jdate("Y/m/d : a:h:s");
                        $answer_valuefa = $_POST['answerfa'];
                        $answer_valueen = $_POST['answeren'];
                        $answervalue_value = $_POST['answervalue'];
                        $all_value = array();
                        foreach ($answer_valuefa as $key => $value) {
                            if (!empty($value)) {
                                $all_value[$key]['answervalue'] = imjection($answervalue_value[$key]);
                                $all_value[$key]['answerfa'] = $value;
                                $all_value[$key]['answeren'] = imjection($answer_valueen[$key]);
                            }
                        }
                        $all_value = json_encode($all_value, JSON_UNESCAPED_UNICODE);
                        $old_img = $_POST['old_img'];
                        $img = $_FILES['img']['name'];
                        $read_file_name = explode(".", $img);
                        $extn = end($read_file_name);

                        if (!empty($img)) {

                            if ($extn == 'docx' || $extn == 'DOCX' || $extn == 'doc' || $extn == 'DOC' || $extn == 'pdf' || $extn == 'PDF' || $extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                $img_name = 'question_' . time() . '.' . $extn;
                                move_uploaded_file($_FILES['img']["tmp_name"], "../upload/question/" . $img_name);
                            } else {
                                $img_name = '';
                            }
                        } else {
                            $img_name = $old_img;
                        }

                        for ($i = 1; $i <= $language_count; $i++) {
                            $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                            $language_record = $language[$i];
                            Query("Update tbl_question SET 
                            tbl_question_level='$level',
                            tbl_question_dateupdate='$date',
                            tbl_question_title='$title_[$i]', 
                            tbl_question_answer = '$all_value',
                            tbl_question_model = '$model',
                            tbl_question_img = '$img_name',
                            tbl_question_mentor='$this_user_id'  Where tbl_question_code= '$code' and tbl_question_lng='$language_record' ");
                        }
                        $_SESSION['error'] = $lang[21];
                        header("Location: levelquestion.php?skill=$skill");
                    }
                }

                if (isset($do) && $do == 'deleteimage') {

                    $result = Query("SELECT * from tbl_question Where tbl_question_code = '$code' ");
                    $read = mysqli_fetch_array($result);

                    echo '<div class="col-lg-8 p-3"><p>' . $lang[25] . '</p><img style="width:35%; height:35%;" src="../upload/question/'  . $_GET['file'] . '" ><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" name="file" value="' . $_GET['file']  . '" >
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="hidden" value="' . $_GET['skill'] . '" name="skill" >
                         <input type="submit" name="submit" value="' . $lang[26] . '"  class="btn btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
                    if (isset($_POST['submit'])) {
                        $file = $_POST['file'];
                        $code = $_POST['code'];
                        $skill = $_POST['skill'];
                        $external_file = '../upload/question/' . $file;
                        unlink("$external_file");
                        Query("Update tbl_question SET tbl_question_img = '' Where tbl_question_code = '$code' ");
                        header("Location: levelquestion.php?code=$code&skill=$skill&do=edit");
                    } elseif (isset($_POST['nodelete'])) {
                        $code = $_POST['code'];
                        $skill = $_POST['skill'];
                        header("Location: levelquestion.php?code=$code&skill=$skill&do=edit");
                    }
                }

                // start = delete record
                if (isset($do) && $do == 'delete') {
                    $result = Query("SELECT * from tbl_question Where tbl_question_code = '$code' ");
                    $read = mysqli_fetch_array($result);
                    echo '<div class="col-lg-12 p-3"><p>' . $lang[25] . '</p><h2>' . $read['tbl_question_title'] . '</h2>
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $code . '" name="id" >
                         <input type="hidden" value="' . $_GET['skill'] . '" name="skill" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
                    if (isset($_POST['submit'])) {
                        $id = $_POST['id'];
                        $skill = $_POST['skill'];
                        Query("update tbl_question set  tbl_question_disable='1',tbl_question_mentor='$now_login' Where tbl_question_code = '$id' ");
                        $_SESSION['error'] = $lang[28];
                        header("Location: levelquestion.php?skill=$skill");
                        exit();
                    }
                    // no delete
                    elseif (isset($_POST['nodelete'])) {
                        $skill = $_POST['skill'];
                        header("Location: levelquestion.php?skill=$skill");
                        exit();
                    }
                }
                if (isset($do) && $do == 'search') {
                    if (isset($_POST['go'])) {
                        $keyword_search = $_POST['keyword'];
                        $skill = $_POST['skill'];
                        header("Location: ?keyword=$keyword_search&skill=$skill");
                    }
                }
                // end = delete item

                ?>
            </div>
        </div>
    </div>
</div>
</div>
<?php
include './footer.inc.php';

?>