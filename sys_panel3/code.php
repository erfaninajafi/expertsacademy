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
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    // list content
                    if (!isset($do)) {
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table></tr><td class="col-sm-9"><a class="btn btn-primary margin-left" href="?do=add" >' . $lang[31] . '</a></td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="?do=search" method="POST">
                       <input type="text" class="form-control" id="search_pages_title" name="keyword"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-primary">' . $lang[77] . '</button>
                       </form></td></tr></table>';

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
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
                        }
                        // start show list
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
    <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[50] . '</th>
                                    <th>کد</th>
                                    <th>شروع</th>
                                    <th>پایان</th>
                                    <th>میزان</th>
                                    <th>اختصاصی</th>
                                    <th>نوع</th>
                                    <th>تعداد مانده</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {

                            echo '<tr>
            <td style="width: 1%">' . $i . '</td>
            <td class="col-sm-1"><a href="?do=edit&id=' . $read['tbl_code_id'] . '" >' . $read['tbl_code_title'] . ($read['tbl_code_workshop'] == 1 ? ' [برای کارگاه ها]' : '') . '</a></td>
            <td class="col-sm-1">' . $read['tbl_code_code'] . '</td>
            <td class="col-sm-1">' . $read['tbl_code_start'] . '</td>
            <td class="col-sm-1">' . $read['tbl_code_end'] . '</td>
            <td class="col-sm-1">' . toPersianNum($read['tbl_code_number']) . '</td>
            <td class="col-sm-1">' . ($read['tbl_code_class'] != 0 || $read['tbl_code_workshopcode'] != 0 ? 'اختصاصی' : 'عمومی') . '</td>
            <td class="col-sm-1">' . ($read['tbl_code_discount'] == 1 ? 'درصدی' : 'تومانی') . '</td>
            <td class="col-sm-1"><a href="register.php?discount=' . $read['tbl_code_code'] . '" target="_blank">' . toPersianNum($read['tbl_code_count']) . '</a></td>
            </tr><tr>
            <td class="col-sm-1 btn-class" colspan="9">
            <a href="?do=edit&id=' . $read['tbl_code_id'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
            <a href="?do=delete&id=' . $read['tbl_code_id'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>
            </tr>';
                            $i++;
                        }

                        echo '</tbody></table>';
                        // start pagination                
                        if ($page > $page_number) {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
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
                            <td class="col-lg-6">
                                <ul class="pagination">
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="code.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
                                    <div class="form-group">
                                        <button type="submit" name="gotopage" class="btn btn-success" style="float: left;"><?= $lang[76] ?></button>
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
                        echo '<div class="col-lg-6">
            <form action="?do=additem" method="POST" >';
                        echo '<div class="form-group"><label>انتخاب دوره</label><select name="class" class="form-control select2"><option value="0">انتخاب کلاس</option>';

                        $result_access = Query("Select * from tbl_class where tbl_class_status='1' order by tbl_class_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_class_code'] . '">' . $read_access['tbl_class_title'] . '</option>';
                        }
                        echo '</select>
        </div>';


                        echo '<div class="form-group"><label>انتخاب کارگاه</label><select name="workshopcode" class="form-control select2"><option value="0">انتخاب کارگاه</option>';

                        $result_access = Query("Select * from tbl_workshop where tbl_workshop_status='1' order by tbl_workshop_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_workshop_code'] . '">' . $read_access['tbl_workshop_title'] . '</option>';
                        }
                        echo '</select>
        </div>';

                        echo '<div class="form-group">
                <label>عنوان</label>
                <input type="text" name="title" class="form-control" required>
                </div>';
                        echo '<div class="form-group">
                <label>کد تخفیف</label>
                <input type="text" name="code" class="form-control" required>
                </div>
                <div class="form-group">
                <label>میزان تخفیف</label>
                <input type="text" name="number" class="form-control" required>
                </div>
                <div class="form-group">
                <label>تعداد استفاده</label>
                <input type="text" name="count" class="form-control" required>
                </div>
                <div class="form-group">
                <label>تاریخ شروع</label>
                <input type="text" name="start" class="form-control datepicker" required>
                </div><div class="form-group">
                <label>تاریخ پایان</label>
                <input type="text" name="end" class="form-control datepicker" required>
                </div>
                <hr>
                <div class="form-group">
                <label>درصدی
                <input type="radio" name="discount" value="1" required>
                مقدار تومانی
                <input type="radio" name="discount" value="2" required>
                </label>
                </div>
                <div class="form-group check-style">
                  <label>کد برای کارگاه<input type="checkbox" name="workshop" value="1" > </label>
                  </div>
                <div class="form-group">
                <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary">
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
            tbl_code_workshopcode) values('$code','$title','$discount','$number','$count','$cat_all','$post','$start','$end','$without','$finalprice','0','$workshop'$class','$workshopcode')");

                            $_SESSION['error'] = $lang[32];
                            header("Location: code.php");
                        }
                    }
                    // end  = add record          
                    // start = delete record
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_code Where tbl_code_id = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br /><h2>' . $read['tbl_code_code'] . '</h2><br />
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $id . '" name="id" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
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

                    // end = delete item
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
                        echo '<div class="col-lg-6">
            <form action="?do=edititem" method="POST" >
            <input type="hidden" name="id" value="' . $id . '" >';

                        echo '<div class="form-group"><label>انتخاب دوره</label><select name="class" class="form-control select2"><option value="0">انتخاب کلاس</option>';

                        $result_access = Query("Select * from tbl_class where tbl_class_status='1' order by tbl_class_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_class_code'] . '" ' . (read_fild(NULL, 'tbl_code_class') == $read_access['tbl_class_code'] ? 'selected' : '') . '>' . $read_access['tbl_class_title'] . '</option>';
                        }
                        echo '</select>
        </div>';


                        echo '<div class="form-group"><label>انتخاب کارگاه</label><select name="workshopcode" class="form-control select2"><option value="0">انتخاب کارگاه</option>';

                        $result_access = Query("Select * from tbl_workshop where tbl_workshop_status='1' order by tbl_workshop_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_workshop_code'] . '" ' . (read_fild(NULL, 'tbl_code_workshopcode') == $read_access['tbl_class_code'] ? 'selected' : '') . '>' . $read_access['tbl_workshop_title'] . '</option>';
                        }
                        echo '</select>
        </div>';

                        echo '<div class="form-group">
            <label>عنوان</label>
            <input type="text" name="title" value="' . read_fild(NULL, 'tbl_code_title') . '" class="form-control" required>
            </div>';
                        echo '<div class="form-group">
            <label>کد تخفیف </label>
            <input type="text" name="code" value="' . read_fild(NULL, 'tbl_code_code') . '" class="form-control" required>
            </div><div class="form-group">
            <label>میزان تخفیف</label>
            <input type="text" name="number" value="' . read_fild(NULL, 'tbl_code_number') . '" class="form-control" required>
            </div>
            <div class="form-group">
            <label>تعداد استفاده</label>
            <input type="text" name="count" value="' . read_fild(NULL, 'tbl_code_count') . '" class="form-control" required>
            </div>
            <div class="form-group">
            <label>تاریخ شروع</label>
            <input type="text" name="start" value="' . read_fild(NULL, 'tbl_code_start') . '" class="form-control datepicker" required>
            </div><div class="form-group">
            <label>تاریخ پایان</label>
            <input type="text" name="end" value="' . read_fild(NULL, 'tbl_code_end') . '" class="form-control datepicker" required>
            </div><hr>
            <div class="form-group">
            <label>درصدی
            <input type="radio" name="discount" value="1" ' . (read_fild(NULL, 'tbl_code_discount') == 1 ? 'checked' : '') . ' required>
            مقدار تومانی
            <input type="radio" name="discount" value="2" ' . (read_fild(NULL, 'tbl_code_discount') == 2 ? 'checked' : '') . ' required>
            </label>
            </div>
            <div class="form-group check-style">
                  <label>کد برای کارگاه<input type="checkbox" name="workshop" value="1" ' . (read_fild(NULL, 'tbl_code_workshop') == 1 ? 'checked' : '') . '> </label>
                  </div>
            <div class="form-group">
            <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary">
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