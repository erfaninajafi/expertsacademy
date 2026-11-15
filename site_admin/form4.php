<?php
include './includer.php';
$show_per_page = '10';
$pagetitle = ' فرم تماس سریع درخواست مشاوره  ';
$Tabpage = '6';
$thisaccess = '1';
include './header.inc.php';
?>

<div class="col-md-12 mb-12 admin-list-page">
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
                       </div>';

                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_form Where tbl_form_section='contact' and tbl_form_model='1' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_form Where tbl_form_section='contact' and tbl_form_model='1' ORDER BY tbl_form_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="admin-list-empty">' . $lang[57] . '</div>';
                        } else {
                            // start show list
                            echo '<div class="admin-list-toolbar">
                                <div class="admin-list-toolbar__field">
                                    <label for="admin-list-search">جست‌وجو در همین صفحه</label>
                                    <input type="search" id="admin-list-search" data-admin-list-search class="form-control" placeholder="نام یا تاریخ ارسال">
                                </div>
                                <div class="admin-list-toolbar__field admin-list-toolbar__field--status">
                                    <label for="admin-list-status">وضعیت</label>
                                    <select id="admin-list-status" data-admin-list-status class="form-select">
                                        <option value="">همه وضعیت‌ها</option>
                                        <option value="pending">بررسی نشده</option>
                                        <option value="reviewed">بررسی شده</option>
                                    </select>
                                </div>
                                <p class="admin-list-count" data-admin-list-count aria-live="polite"></p>
                            </div>
                            <table class="table table-bordered table-hover table-striped margin-top" data-admin-list="forms" style="text-align:right;">
                                <thead class="table-dark">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>نام و نام خانوادگی</th>
                                    <th>تاریخ ارسال</th>
                                    <th>وضعیت</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                            $i = 1;
                            while ($read = mysqli_fetch_array($result)) {
                                echo '<tr data-admin-status="' . ($read['tbl_form_status'] == '0' ? 'pending' : 'reviewed') . '">
                            <td style="width: 1%">' . $i . '</td>
                            <td class="col-sm-8"><a href="?do=edit&code=' . $read['tbl_form_id'] . '" >' . htmlspecialchars((string) $read['tbl_form_fld1'], ENT_QUOTES, 'UTF-8') . '</a></td>
                            <td class="col-sm-2">' . $read['tbl_form_date'] . '</td>
                            <td class="col-sm-2">' . ($read['tbl_form_status'] == '0' ? '<span class="admin-list-badge admin-list-badge--pending">بررسی نشده</span>' : '<span class="admin-list-badge admin-list-badge--reviewed">بررسی شده</span>') . '</td>
                            <td class="col-sm-2 admin-list-actions">
                            <a href="?do=edit&code=' . $read['tbl_form_id'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
                            <a href="?do=delete&code=' . $read['tbl_form_id'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>
                            </tr>';
                                $i++;
                            }

                            echo '</tbody></table><p class="admin-list-empty" data-admin-list-empty hidden>موردی مطابق جست‌وجو در این صفحه پیدا نشد.</p>';
                            // start pagination                
                            if ($page > $page_number) {
                                echo '<div class="admin-list-empty">' . $lang[57] . '</div>';
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
                                                <li><a class="page-link" href="?page=<?= ($back_page_number - 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[75] ?></a></li>
                                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a></li>
                                                <li><a class="page-link" href="?page=<?= min($page + 1, $page_number) . '' . $other_page_keyword . $keyword  ?>"><?= $lang[74] ?></a></li>
                                            </ul>
                                        </td>
                                        <td class="col-lg-6">

                                        </td>
                                    </tr>
                                </table>

                    <?php
                            }
                        }
                    }
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: form4.php?page=$page_number");
                        }
                    }
                    // end = pagination

                    // end  = add record          
                    // start = delete record
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_form Where tbl_form_id = '$code'  ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br /><h2>' . $read['tbl_form_fld1'] . '</h2><br />
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $code = $_POST['code'];

                            Query("DELETE from tbl_form Where tbl_form_id = '$code' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: form4.php");
                            exit();
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {
                            header("Location: form4.php");
                            exit();
                        }
                    }
                    // end = delete item
                    // start = edit record
                    if (isset($do) && $do == 'edit') {
                        $code = $_GET['code'];
                        $result = Query("SELECT * FROM tbl_form Where tbl_form_id =  '$code'  ");
                        $read = mysqli_fetch_array($result);
                        // this function for read multi language content - check kardan chand zaban baraye value field ha 
                        function read_fild($language_id = NULL, $field = null)
                        {
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "";
                            }
                            $code = $_GET['code'];
                            $result = Query("SELECT * FROM tbl_form Where tbl_form_id =  '$code'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }
                        // end function
                        // form for edit
                        echo '<div class="col-lg-6">
                          <form action="?do=edititem" method="POST" enctype="multipart/form-data">
                          <input type="hidden" name="code" value="' . $code . '" >';



                        echo '<div class="form-group">
                              <label>نام و نام خانوادگی</label>
                              <input type="text" value="' . read_fild(null, 'tbl_form_fld1') . '" class="form-control">
                              </div>';

                        echo '
                    <div class="form-group">
                    <label>ایمیل</label>
                        <input type="email" value="' . read_fild(null, 'tbl_form_fld2') . '" class="form-control" >
                    </div>
                    <div class="form-group">
                    <label>شماره همراه</label>
                        <input type="number" value="' . read_fild(null, 'tbl_form_fld3') . '"  class="form-control" >
                    </div>

                    <div class="request" ' . (read_fild(null, 'tbl_form_model') == '1' ? 'style="display:block;"' : 'style="display:none;"') . '>
                        <div class="form-group">
                        <label>سمت سازمانی</label>
                            <input type="text" value="' . read_fild(null, 'tbl_form_fld4') . '"  class="form-control">
                        </div>
                        <div class="form-group">
                        <label>زمینه کاری</label>
                            <input type="text" value="' . read_fild(null, 'tbl_form_fld5') . '" placeholder="" class="form-control">
                        </div>
                        <div class="form-group">
                        <label>نام شرکت / سازمان</label>
                            <input type="text" value="' . read_fild(null, 'tbl_form_fld6') . '" placeholder="" class="form-control">
                        </div>
                        <div class="form-group">
                        <label>شرح خدمات</label>
                            <textarea value="' . read_fild(null, 'tbl_form_fld7') . '" placeholder="" class="form-control" rows="5"></textarea>
                        </div>
                    </div>

                    <div class="job" ' . (read_fild(null, 'tbl_form_model') == '2' ? 'style="display:block;"' : 'style="display:none;"') . '>
                        <div class="form-group mb-2">
                            <label> <input type="radio" value="1" name="kind" > حقیقی </label>
                            <label> <input type="radio" value="2" name="kind"> حقوقی </label>
                        </div>
                    </div>

                    <div class="jobresult" ' . (read_fild(null, 'tbl_form_kind') == '1' ? 'style="display:block;"' : 'style="display:none;"') . '>
                     <div class="form-group">
                              <label>فایل ضمیمه 
                              
                              ' . (!empty(read_fild(null, 'tbl_form_file')) ? '<a href="../upload/' . read_fild(null, 'tbl_form_file') . '" target="_blank">دریاف فایل</a>' : 'ندارد') . '
                              </label>
                              </div>
                        <div class="form-group">
                        <label>شرح همکاری و مشارکت مورد نظر</label>
                            <textarea  placeholder="" class="form-control" rows="5">' . read_fild(null, 'tbl_form_fld8') . '</textarea>
                        </div>
                        <div class="form-group">
                        <label>تخصص ها</label>
                            <input type="text" value="' . read_fild(null, 'tbl_form_fld9') . '" placeholder="" class="form-control">
                        </div>
                    </div>

                    <div class="jobresulttwo" ' . (read_fild(null, 'tbl_form_kind') == '2' ? 'style="display:block;"' : 'style="display:none;"') . '>
                        <div class="form-group">
                        <label>نام شرکت / سازمان</label>
                            <input type="text" value="' . read_fild(null, 'tbl_form_fld10') . '" placeholder="" class="form-control">
                        </div>
                        <div class="form-group">
                        <label>زمینه کاری</label>
                            <input type="text" value="' . read_fild(null, 'tbl_form_fld11') . '" placeholder="" class="form-control">
                        </div>
                        <div class="form-group">
                        <label>شرح همکاری و مشارکت مورد نظر</label>
                            <textarea  placeholder="" class="form-control" rows="5">' . read_fild(null, 'tbl_form_fld12') . '</textarea>
                        </div>
                    </div>';




                        echo '<hr><div class="form-group">
                        <label><input type="radio" name="status" value="0" ' . (read_fild(null, 'tbl_form_status') == '0' ? 'checked' : '') . '> بررسی نشده</label>
                        <label><input type="radio" name="status" value="1" ' . (read_fild(null, 'tbl_form_status') == '1' ? 'checked' : '') . '> بررسی شده</label>
                        </div>
                                 
                                  <div class="form-group">
                                  <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary">
                                  </div>
                                  </form></div>';
                    }
                    // end = edit item
                    // 
                    // start = update items
                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['updateitem'])) {
                            $code = $_POST['code'];
                            $status = $_POST['status'];

                            Query("Update tbl_form SET tbl_form_status='$status'  Where tbl_form_id= '$code' ");

                            $_SESSION['error'] = $lang[21];
                            header("Location: form4.php");
                        }
                    }
                    // end = update item 
                    echo '</div></div></section>';
                    echo '<script defer src="asset/js/listing-ui.js"></script>';
                    include './footer.inc.php';
