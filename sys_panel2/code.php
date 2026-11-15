<?php

// include
include './includer.php';
$show_per_page = '20';
$Tabpage = '6';
$check_level = '0,2';
$pagetitle = 'کدتخفیف';
$thisaccess = '1';
include './header.inc.php';
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
                            <?=(!isset($do) ? '<div class="plus">
                                <a href="?do=add"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <?=(!isset($do) ? '<div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(assets/img/icons/spot-illustrations/corner-4.png);">
            </div>' : '') ?>
            <div class="card-body p-0 position-relative">
                    <?php
                    if (!isset($do)) { ?>
                <table>
                    </tr>
                    <td class="col-sm-6 p-3">
                        <form action="?do=search" method="POST" class="search-form">
                            <input type="text" class="form-control" value="<?= $keyword ?>" name="keyword">
                            <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                        </form>
                    </td>
                    </tr>
                </table>
                <?php

                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_code Where tbl_code_admin='0' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_code Where tbl_code_admin='0' and tbl_code_title LIKE '%$keyword%' ORDER BY tbl_code_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                        }
                        // start show list
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[50] . '</th>
                                    <th>'.$lang[1328].'</th>
                                    <th>'.$lang[1442].'</th>
                                    <th>'.$lang[1443].'</th>
                                    <th>'.$lang[1452].'</th>
                                    <th>'.$lang[1453].'</th>
                                    <th>'.$lang[1436].'</th>
                                    <th>'.$lang[1454].'</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {

                            echo '<tr>
                                <td class="align-middle p-2">' . $i . '</td>
                                <td class="align-middle p-2"><a href="?do=edit&id=' . $read['tbl_code_id'] . '" >' . $read['tbl_code_title'] . ($read['tbl_code_workshop'] == 1 ? ' [برای کارگاه ها]' : '') . '</a></td>
                                <td class="align-middle p-2">' . $read['tbl_code_code'] . '</td>
                                <td class="align-middle p-2">' . $read['tbl_code_start'] . '</td>
                                <td class="align-middle p-2">' . $read['tbl_code_end'] . '</td>
                                <td class="align-middle p-2">' . toPersianNum($read['tbl_code_number']) . '</td>
                                <td class="align-middle p-2">' . ($read['tbl_code_class'] != 0 || $read['tbl_code_workshopcode'] != 0 ? 'اختصاصی' : 'عمومی') . '</td>
                                <td class="align-middle p-2">' . ($read['tbl_code_discount'] == 1 ? 'درصدی' : 'تومانی') . '</td>
                                <td class="align-middle p-2"><a href="register.php?discount=' . $read['tbl_code_code'] . '" target="_blank">' . toPersianNum($read['tbl_code_count']) . '</a></td>
                                <td class="align-middle p-2">
                                <a href="?do=edit&id=' . $read['tbl_code_id'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                                <a href="?do=delete&id=' . $read['tbl_code_id'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>
                                </td>
                                </tr>';
                            $i++;
                        }

                        echo '</tbody></table>';
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
                            <td class="col-lg-9">
                                <ul class="pagination">
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-3">
                                <form action="code.php?do=gotopage" class="search-form" method="POST">
                                    <div class="form-group">
                                        <button type="submit" name="gotopage" class="btn btn-success"><?= $lang[76] ?></button>
                                        <input type="text" class="form-control go-fld" name="page_number">
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </table>
                    <?php
                        }
                    }
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: code.php?page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = do add
                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-6 col-12 p-3">
                    <form action="?do=additem" method="POST" >';
                        echo '<div class="form-group"><label>'.$lang[1194].'</label><select name="class" class="form-control select2"><option value="0">'.$lang[1194].'</option>';
                        $result_access = Query("Select * from tbl_class where tbl_class_status='1' order by tbl_class_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_class_code'] . '">' . $read_access['tbl_class_title'] . '</option>';
                        }
                        echo '</select>
                        </div>';
                        echo '<div class="form-group"><label>'.$lang[1455].'</label><select name="workshopcode" class="form-control select2"><option value="0">'.$lang[1455].'</option>';
                        $result_access = Query("Select * from tbl_workshop where tbl_workshop_status='1' order by tbl_workshop_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_workshop_code'] . '">' . $read_access['tbl_workshop_title'] . '</option>';
                        }
                        echo '</select></div>
                            <div class="form-group">
                            <label>'.$lang[50].'</label>
                            <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="form-group">
                            <label>'.$lang[1328].'</label>
                            <input type="text" name="code" class="form-control" required>
                            </div>
                            <div class="form-group">
                            <label>'.$lang[1456].'</label>
                            <input type="text" name="number" class="form-control" required>
                            </div>
                            <div class="form-group">
                            <label>'.$lang[1457].'</label>
                            <input type="text" name="count" class="form-control" required>
                            </div>
                            <div class="form-group">
                            <label>'.$lang[1458].'</label>
                            <input type="text" name="start" class="form-control datepicker" required>
                            </div><div class="form-group">
                            <label>'.$lang[1459].'</label>
                            <input type="text" name="end" class="form-control datepicker" required>
                            </div>
                            <hr>
                            <div class="form-group check-style">
                            <label><input type="radio" name="discount" value="1" required> '.$lang[1460].'
                            </label>
                            <label><input type="radio" name="discount" value="2" required> '.$lang[1461].'
                            
                            </label>
                            </div>
                            <hr>
                            <div class="form-group check-style">
                            <label><input type="checkbox" name="workshop" value="1" >'.$lang[1462].' </label>
                            </div>
                            <hr>
                            <div class="form-group">
                            <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary mt-3">
                            </div>
                            </form></div>';
                    }
                    // start = add record to db
                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $code = strtolower($_POST['code']);
                            $title = $_POST['title'];
                            $workshop = $_POST['workshop'];
                            $number = $_POST['number'];
                            $discount = $_POST['discount'];
                            $count = $_POST['count'];
                            $start = $_POST['start'];
                            $workshopcode = $_POST['workshopcode'];
                            $class = $_POST['class'];
                            $end = $_POST['end'];
                            if (empty($_POST['post'])) {
                                $post = 0;
                            } else {
                                $post = $_POST['post'];
                            }

                            if (empty($_POST['finalprice'])) {
                                $finalprice = 0;
                            } else {
                                $finalprice = $_POST['finalprice'];
                            }

                            $cat = $_POST['cat'];
                            $without = $_POST['without'];

                            $cat_all = '';

                            foreach ($cat as $key => $value) {
                                $cat_all .= $value . ',';
                            }

                            if (empty($title)) {
                                $_SESSION['error'] = $lang[56];
                                header("Location: code.php");
                                exit();
                            }
                            Query("INSERT INTO tbl_code (
                                    tbl_code_code,
                                    tbl_code_title,
                                    tbl_code_discount,
                                    tbl_code_number,
                                    tbl_code_count,
                                    tbl_code_cat,
                                    tbl_code_post,
                                    tbl_code_start,
                                    tbl_code_end,
                                    tbl_code_without,
                                    tbl_code_finalprice,
                                    tbl_code_admin,
                                    tbl_code_workshop,
                                    tbl_code_class,
                                    tbl_code_workshopcode) 
                                    values(
                                        '$code',
                                        '$title',
                                        '$discount',
                                        '$number',
                                        '$count',
                                        '$cat_all',
                                        '$post',
                                        '$start',
                                        '$end',
                                        '$without',
                                        '$finalprice',
                                        '0',
                                        '$workshop',
                                        '$class',
                                        '$workshopcode')");

                            $_SESSION['error'] = $lang[32];
                            header("Location: code.php");
                        }
                    }
                    // end  = add record          
                    // start = delete record
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_code Where tbl_code_id = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6 col-12 p-3"><p>' . $lang[25] . '</p><h3>' . $read['tbl_code_title'] . '</h3><br />
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $id . '" name="id" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
                        if (isset($_POST['submit'])) {
                            $id = $_POST['id'];
                            Query("DELETE from tbl_code Where tbl_code_id = '$id' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: code.php");
                            exit();
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {
                            header("Location: code.php");
                            exit();
                        }
                    }

                    // start = edit record
                    if (isset($do) && $do == 'edit') {
                        // this function for read multi language content - check kardan chand zaban baraye value field ha 
                        function read_fild($language_id = NULL, $field = NULL)
                        {
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "and tbl_code_lng = '$language_id'";
                            }
                            $id = $_GET['id'];
                            $result = Query("SELECT * FROM tbl_code Where tbl_code_id =  '$id'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }
                        // end function
                        // form for edit
                        echo '<div class="col-lg-6 col-12 p-3">
                            <form action="?do=edititem" method="POST" >
                            <input type="hidden" name="id" value="' . $id . '" >';
                        echo '<div class="form-group"><label>'.$lang[1194].'</label><select name="class" class="form-control select2"><option value="0">'.$lang[1194].'</option>';
                        $result_access = Query("Select * from tbl_class where tbl_class_status='1' order by tbl_class_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_class_code'] . '" ' . (read_fild(NULL, 'tbl_code_class') == $read_access['tbl_class_code'] ? 'selected' : '') . '>' . $read_access['tbl_class_title'] . '</option>';
                        }
                        echo '</select></div>';
                        echo '<div class="form-group"><label>'.$lang[1455].'</label><select name="workshopcode" class="form-control select2"><option value="0">'.$lang[1455].'</option>';

                        $result_access = Query("Select * from tbl_workshop where tbl_workshop_status='1' order by tbl_workshop_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_workshop_code'] . '" ' . (read_fild(NULL, 'tbl_code_workshopcode') == $read_access['tbl_class_code'] ? 'selected' : '') . '>' . $read_access['tbl_workshop_title'] . '</option>';
                        }
                        echo '</select></div>';

                        echo '<div class="form-group">
                            <label>'.$lang[50].'</label>
                            <input type="text" name="title" value="' . read_fild(NULL, 'tbl_code_title') . '" class="form-control" required>
                            </div>
                            <div class="form-group">
                            <label>'.$lang[1328].' </label>
                            <input type="text" name="code" value="' . read_fild(NULL, 'tbl_code_code') . '" class="form-control" required>
                            </div>
                            <div class="form-group">
                            <label>'.$lang[1456].'</label>
                            <input type="text" name="number" value="' . read_fild(NULL, 'tbl_code_number') . '" class="form-control" required>
                            </div>
                            <div class="form-group">
                            <label>'.$lang[1457].'</label>
                            <input type="text" name="count" value="' . read_fild(NULL, 'tbl_code_count') . '" class="form-control" required>
                            </div>
                            <div class="form-group">
                            <label>'.$lang[1458].'</label>
                            <input type="text" name="start" value="' . read_fild(NULL, 'tbl_code_start') . '" class="form-control datepicker" required>
                            </div>
                            <div class="form-group">
                            <label>'.$lang[1459].'</label>
                            <input type="text" name="end" value="' . read_fild(NULL, 'tbl_code_end') . '" class="form-control datepicker" required>
                            </div><hr>
                            <div class="form-group check-style">
                            <label><input type="radio" name="discount" value="1" ' . (read_fild(NULL, 'tbl_code_discount') == 1 ? 'checked' : '') . ' required> '.$lang[1460].'</label>
                            <label><input type="radio" name="discount" value="2" ' . (read_fild(NULL, 'tbl_code_discount') == 2 ? 'checked' : '') . ' required> '.$lang[1461].'</label>
                            </div>
                            <hr>
                            <div class="form-group check-style">
                            <label><input type="checkbox" name="workshop" value="1" ' . (read_fild(NULL, 'tbl_code_workshop') == 1 ? 'checked' : '') . '> '.$lang[1462].' </label>
                            </div>
                            <hr>
                            <div class="form-group">
                            <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3">
                            </div></form></div>';
                    }
                    // end = edit item
                    // 
                    // start = update items
                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['updateitem'])) {
                            $id = $_POST['id'];
                            $discount = $_POST['discount'];
                            $number = $_POST['number'];
                            $workshop = $_POST['workshop'];
                            $title = $_POST['title'];
                            $code = strtolower($_POST['code']);
                            $count = $_POST['count'];
                            if (empty($_POST['post'])) {
                                $post = 0;
                            } else {
                                $post = $_POST['post'];
                            }

                            if (empty($_POST['finalprice'])) {
                                $finalprice = 0;
                            } else {
                                $finalprice = $_POST['finalprice'];
                            }
                            $start = $_POST['start'];
                            $end = $_POST['end'];
                            $cat = $_POST['cat'];
                            $without = $_POST['without'];
                            $workshopcode = $_POST['workshopcode'];
                            $class = $_POST['class'];
                            $cat_all = '';

                            foreach ($cat as $key => $value) {
                                $cat_all .= $value . ',';
                            }
                            if (empty($title)) {
                                $_SESSION['error'] = $lang[56];
                                header("Location: code.php");
                                exit();
                            }
                            $language_name = $language[$i];
                            // update query
                            Query("Update tbl_code SET tbl_code_workshopcode='$workshopcode',tbl_code_class='$class',tbl_code_workshop='$workshop',tbl_code_finalprice='$finalprice',tbl_code_without='$without',tbl_code_end='$end',tbl_code_start='$start',tbl_code_post='$post',tbl_code_cat='$cat_all',tbl_code_count='$count',tbl_code_number='$number',tbl_code_title='$title' , tbl_code_code = '$code' ,tbl_code_discount = '$discount'  Where tbl_code_id= '$id' ");
                            $_SESSION['error'] = $lang[21];
                            header("Location: code.php");
                        }
                    }
                    // end = update item

                    // start = search result               
                    if (isset($t) && isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: code.php?keyword=$keyword_search");
                        }
                    }
                    // end = search result            
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include './footer.inc.php';

?>